<?php

namespace App\Command;

use App\Entity\Mission;
use App\Indexation\MissionIndexation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:regenerate-index-meili-search',
    description: 'Régénerer les indexes de meili search',
)]
class RegenerateIndexMeiliSearchCommand extends Command
{
    public function __construct(
       private EntityManagerInterface $entityManager,
       private MissionIndexation $missionIndexation
    ) {
        parent::__construct(null);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $missions = $this->entityManager->getRepository(Mission::class)->findBy([
            'archived' => false,
                'published' => true,
                'booked' => false
            ], ['started' => 'ASC']
        );

        foreach ($missions as $mission) {
            $io->note(sprintf('Mission: %s', $mission->getTitle()));

            // suppression de l'index
            $this->missionIndexation->delete($mission);

            // creation de l'index
            $this->missionIndexation->create($mission);
        }

        $io->success('Population terminé !');

        return Command::SUCCESS;
    }
}
