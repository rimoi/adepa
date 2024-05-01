<?php

namespace App\Command;

use App\Constant\NotificationConstant;
use App\Entity\Exclusive;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-email-available-mission',
    description: "si
mission non prise en 2 jours, l’envoi de la mission est envoyé à toutes les
personnes ayant le profil",
)]
class SendEmailAvailableMissionCommand extends Command
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly NotificationService $notificationService
    )
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $exclusives = $this->entityManager->getRepository(Exclusive::class)->findAll();

        foreach ($exclusives as $exclusive) {
            if ($exclusive->isAlwaysOnTime()) {
                continue;
            }

            $this->notificationService->infoUserMission($exclusive->getMission(), NotificationConstant::EMAIL, [$exclusive->getUser()]);
            
            $this->entityManager->remove($exclusive);
        }
        
        $this->entityManager->flush();

        $io->success('Tous est okay !');

        return Command::SUCCESS;
    }
}
