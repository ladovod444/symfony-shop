<?php

namespace App\Tests\Controller\Api;

use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use App\Factory\OrderFactory;
use App\Controller\Api\OrderController;
use Helmich\JsonAssert\JsonAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class OrderControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;
    use JsonAssertions;

    public function testIndex() {
        $client = static::createClient();
        $user = UserFactory::createOne();

        // Подготовим запрос
        $client->loginUser($user->_real());

        // Создадим тестовые Заказы
        OrderFactory::createMany(10, ['owner' => $user]);

        $client->request('GET', '/api/v1/order/list');

        // получаем массив страниц
        $json = json_decode($client->getResponse()->getContent(), true);
        //dd($json);

        // Проверям кол-во
        $this->assertCount(10, $json);

        // Проверяем статус (успешность) запроса
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();
    }

}
