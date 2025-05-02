<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\TwilioSmsService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

class TestSmsCommand extends Command
{
    protected static $defaultName = 'app:test-reservation-sms';
    private $twilioSmsService;
    private $params;
    private $entityManager;

    public function __construct(
        TwilioSmsService $twilioSmsService,
        ParameterBagInterface $params,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->twilioSmsService = $twilioSmsService;
        $this->params = $params;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Test SMS confirmation for reservation using Twilio');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reservation = $this->entityManager->getRepository(Reservation::class)->find(25);
        
        if (!$reservation) {
            $output->writeln('ERROR: Reservation not found');
            return Command::FAILURE;
        }

        $trip = $reservation->getTrip();
        $phoneNumber = $this->params->get('test_phone_number');

        if (empty($phoneNumber)) {
            $output->writeln('ERROR: Test phone number not configured');
            return Command::FAILURE;
        }

       // Dans TestSmsCommand.php, réduisez le message à :
    $message = sprintf(
        "EasyGo - Votre réservation a été confirmée avec succès. 
        Accédez à votre compte pour plus de détails. 
        Merci pour votre confiance !",

        );

        $output->writeln("Sending to: " . $phoneNumber);
        $output->writeln("Message length: " . strlen($message) . " characters");
        $output->writeln("Message content:\n" . $message);

        if (!$this->twilioSmsService->isConfigured()) {
            $output->writeln('ERROR: Twilio is not properly configured');
            return Command::FAILURE;
        }

        try {
            $result = $this->twilioSmsService->sendSms($phoneNumber, $message);
            
            if ($result) {
                $output->writeln('SUCCESS: SMS sent via Twilio');
                return Command::SUCCESS;
            } else {
                $output->writeln('ERROR: Failed to send SMS (no exception thrown)');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $output->writeln('ERROR: ' . $e->getMessage());
            $output->writeln('TRACE: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}