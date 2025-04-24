<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class InfobipService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $baseUrl;
    private string $sender;
    private LoggerInterface $logger;

    public function __construct(
        HttpClientInterface $httpClient,
        ParameterBagInterface $params,
        LoggerInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $params->get('infobip_api_key');
        $this->baseUrl = $params->get('infobip_base_url');
        $this->sender = $params->get('infobip_sender');
        $this->logger = $logger;
    }

    public function sendSms(string $to, string $message): bool
    {
        try {
            $this->logger->debug('Attempting to send SMS', [
                'to' => $to,
                'sender' => $this->sender,
                'message' => $message
            ]);

            $response = $this->httpClient->request('POST', $this->baseUrl.'/sms/2/text/advanced', [
                'headers' => [
                    'Authorization' => 'App '.$this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'messages' => [
                        [
                            'from' => $this->sender,
                            'destinations' => [
                                ['to' => $to]
                            ],
                            'text' => $message
                        ]
                    ]
                ],
                'timeout' => 10 // Timeout en secondes
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);

            $this->logger->debug('Infobip API response', [
                'status' => $statusCode,
                'response' => $content
            ]);

            $isSuccess = $statusCode >= 200 && $statusCode < 300;
            
            if (!$isSuccess) {
                $this->storeFailedSms($to, $message, "HTTP Status $statusCode");
                $this->logger->error('Infobip API error', [
                    'status' => $statusCode,
                    'response' => $content
                ]);
            }

            return $isSuccess;

        } catch (\Exception $e) {
            $this->storeFailedSms($to, $message, $e->getMessage());
            $this->logger->error('SMS sending failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    private function storeFailedSms(string $to, string $message, string $error): void
    {
        // Créer le répertoire var/log s'il n'existe pas
        $logDir = __DIR__.'/../../var/log';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logEntry = sprintf(
            "[%s] TO: %s | ERROR: %s | MSG: %s\n",
            date('Y-m-d H:i:s'),
            $to,
            $error,
            $message
        );
        
        file_put_contents(
            $logDir.'/failed_sms.log',
            $logEntry,
            FILE_APPEND
        );
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->baseUrl) && !empty($this->sender);
    }
}