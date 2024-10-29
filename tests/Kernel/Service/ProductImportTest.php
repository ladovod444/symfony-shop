<?php

namespace App\Tests\Kernel\Service;

use App\Factory\UserFactory;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\HttpClient;
use App\Service\ProductImport;
use App\Service\ProductsBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductImportTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testProductImport()
    {
        self::bootKernel();

        // Задаем кол-во товаров для импорта
        $product_count = 2;

        // Создаем юзера для теста.
        $user = UserFactory::createOne();

        // Моккаем сервис ProductsBus
        $bus = $this->createMock(ProductsBus::class);
        static::getContainer()->set(ProductsBus::class, $bus);

        // Моккаем сервис userRepository
        $userRepository = $this->createMock(UserRepository::class);
        // Задаем ему метод (userRepository->find)
        // который возвратит ему выше созданного юзера ($user = UserFactory::createOne();)
        $userRepository->method('find')->willReturn($user->_real());
        // в контейнер "вставляем" нужный сервис (Мок)
        static::getContainer()->set(UserRepository::class, $userRepository);


        // Но "ходить" на внешний сервис не нужно...
        // @todo Поэтому нужно замокать httpClient (\App\Service\HttpClient \App\Service\ProductImport::__construct)
        // Моккаем сервис HttpClient
        $httpClient = $this->createMock(HttpClient::class);
        // Нужно чтобы этот сервис выдавал некие фейковые данные.
        $httpClient->method('get')->willReturnCallback(
            function () {
                // Здесь подложим данные, которые отдает \App\Service\HttpClient::get
                return file_get_contents('tests/DataProvider/import_data.json');
                //return '';
            }
        );
        static::getContainer()->set(HttpClient::class, $httpClient);


        // Далее получаем сервис ProductImport
        $productImport = static::getContainer()->get(ProductImport::class);
        assert($productImport instanceof ProductImport);
        // Выполняем импорт
        $productImport->import($product_count);

        // Получим productsRepository
        $productsRepository = static::getContainer()->get(ProductRepository::class);
        assert($productsRepository instanceof ProductRepository);
        // Получим товары, которые должны быть импортированы
        $products = $productsRepository->findAll();

        // Сравним количество
        $this->assertCount($product_count, $products);

    }
}
