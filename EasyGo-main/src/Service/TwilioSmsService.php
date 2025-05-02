<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;
use Twilio\Rest\Client;

class TwilioSmsService
{
    private string $accountSid;
    private string $authToken;
    private string $fromNumber;
    private LoggerInterface $logger;

    public function __construct(
        ParameterBagInterface $params,
        LoggerInterface $logger
    ) {
        $this->accountSid = $params->get('twilio_account_sid');
        $this->authToken = $params->get('twilio_auth_token');
        $this->fromNumber = $params->get('twilio_from_number');
        $this->logger = $logger;
    }

    // UNE SEULE méthode sendSms !
    public function sendSms(string $to, string $message): bool
    {
        if (!$this->isConfigured()) {
            $this->logger->error('Twilio not properly configured');
            return false;
        }

        try {
            $client = new Client($this->accountSid, $this->authToken);
            $result = $client->messages->create(
                $to,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Twilio SMS sending failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function isConfigured(): bool
    {
        return !empty($this->accountSid) && 
               !empty($this->authToken) && 
               !empty($this->fromNumber);
    }
}