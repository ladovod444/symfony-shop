<?php

namespace App\Command\Retailcrm;

use App\Repository\ProductRepository;
use App\Service\RetailCrm\OffersManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:retailcrm:update-offers',
    description: 'Add a short description for your command',
)]
class UpdateOffersCommand extends Command
{
    public function __construct(
        private readonly OffersManager $offersManager,
        private readonly ProductRepository $productRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('count', null, InputOption::VALUE_OPTIONAL, 'Количество товаров для обновления');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $input->getOption('count') ? (int)$input->getOption('count') : 0;
        $this->offersManager->UpdateOffers($this->productRepository, $count);
        return Command::SUCCESS;
    }
}
