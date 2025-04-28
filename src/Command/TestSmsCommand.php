<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\InfobipService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

class TestSmsCommand extends Command
{
    protected static $defaultName = 'app:test-reservation-sms';
    private $infobipService;
    private $params;
    private $entityManager;

    public function __construct(
        InfobipService $infobipService,
        ParameterBagInterface $params,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->infobipService = $infobipService;
        $this->params = $params;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Test SMS confirmation for reservation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
{
    $reservation = $this->entityManager->getRepository(Reservation::class)->find(51);
    
    if (!$reservation) {
        $output->writeln('ERROR: Reservation not found');
        return Command::FAILURE;
    }

    $trip = $reservation->getTrip();
    $phoneNumber = $this->params->get('test_phone_number');

    $message = sprintf(
        "EasyGo - Confirmation de réservation\n\n" .
        "Votre réservation N°%d est confirmée :\n" .
        " Départ: %s\n" .
        " Arrivée: %s\n" .
        " Date: %s\n" .
        " Détails: %d place(s) - %.2f DNT\n\n" .
        "Merci de voyager avec EasyGo!",
        $reservation->getId(),
        $trip->getDeparturePoint(),
        $trip->getDestination(),
        $trip->getTripDate()->format('d/m/Y H:i'),
        $reservation->getNombrePlaces(),
        $reservation->getMontantTotal()
    );

    $output->writeln("Sending to: " . $phoneNumber);
    $output->writeln("Message: " . $message);

    try {
        $result = $this->infobipService->sendSms($phoneNumber, $message);
        $output->writeln($result ? 'SUCCESS: SMS sent' : 'ERROR: Failed to send SMS');
        return $result ? Command::SUCCESS : Command::FAILURE;
    } catch (\Exception $e) {
        $output->writeln('ERROR: ' . $e->getMessage());
        return Command::FAILURE;
    }
}
}
