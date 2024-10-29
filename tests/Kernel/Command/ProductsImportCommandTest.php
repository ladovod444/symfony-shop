<?php

namespace App\Tests\Kernel\Command;


use App\Repository\UserRepository;
use App\Service\ProductImport;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;


class ProductsImportCommandTest extends KernelTestCase {

    public function testExecute(): void {
        self::bootKernel();

        // Создается "приложение", которое отвечает за консольные команды.
        $application = new Application(self::$kernel);

        $command = $application->find('shop:products:import');

        $productImport = $this->createMock(ProductImport::class);
        static::getContainer()->set(ProductImport::class, $productImport);
        $productImport->expects($this->once())->method('import')->with(10);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
          'count' => 10,
        ]);

        $commandTester->assertCommandIsSuccessful();
    }
}