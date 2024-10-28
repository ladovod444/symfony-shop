<?php

namespace App\Command;

use App\Service\ProductImport;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'shop:products:import',
    description: 'Import products',
)]
class ShopProductsImportCommand extends Command
{
    use LockableTrait;
    public function __construct(private readonly ProductImport $productImport)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('count', InputArgument::OPTIONAL, 'Number of products to import', default: null);
        //            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return Command::SUCCESS;
        }
        $output->writeln('The command is executed');

        // Получение аргументов
        $count = $input->getArgument('count');
        //$input->getOption('count')->then(function () use ($count) {})

        $this->productImport->import($count);

        $this->release();
        return Command::SUCCESS;
    }
}
