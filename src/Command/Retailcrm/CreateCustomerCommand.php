<?php

namespace App\Command\Retailcrm;

use App\Repository\UserRepository;
use App\Service\RetailCrm\CustomerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Command\LockableTrait;

#[AsCommand(
    name: 'app:retailcrm:create-customer',
    description: 'Create customers in retailcrm',
)]
class CreateCustomerCommand extends Command
{
    use LockableTrait;
    public function __construct(
        private readonly CustomerManager $manager,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityTypeManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return Command::SUCCESS;
        }

        $users = $this->userRepository->findAll();
//        $users_count = 0;
        foreach ($users as $user) {
//            $customers_data = [];
//            $customers_data['email'] = $user->getEmail();
//            $customers_data['first_name'] = $user->getFirstName();
//            $customers_data['last_name'] = $user->getLastName();
            $id = $this->manager->createCustomer($user);

            $user->setCustomerId($id);
            $this->entityTypeManager->flush();

            $output->writeln("Customer with $id was created.");
            //$users_count++;

//            if ($users_count == 1) {
//                break;
//            }
        }

        $this->release();
        return Command::SUCCESS;
    }
}
