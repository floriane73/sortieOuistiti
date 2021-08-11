<?php

namespace App\Command;

use App\Repository\SortieRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateEtats extends Command {

    private $sortieRepository;

    protected static $defaultName = 'app:etats:update';

    public function __construct(SortieRepository $sortieRepository)
    {
        $this->$sortieRepository = $sortieRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Change l\'état des sorties selon la date du jour')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');

            $count = $this->sortieRepository->updateAllEtats();
        } else {
            $count = $this->sortieRepository->updateAllEtats();
        }

        $io->success(sprintf('Etats mis à jour', $count));

        return 0;
    }

}