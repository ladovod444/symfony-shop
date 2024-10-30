<?php

namespace App\Tests\Controller\Api;

use App\Factory\OrderItemFactory;
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

    public function testIndex()
    {
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

    public function testAdd()
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user->_real());

        // Должен быть контент вида
        $contentT = '{
            "mail": "ladovod@gmail.com",
            "order": [
                {"id": 334, "title": "Hellfire Flare", "current_price": 500.00, "sku": "Wrap_ElbowCha", "quantity": 1},                
                {"id": 335, "title": "Fast & Furious Nissan Skyline Bundle", "current_price": 2500.00, "sku": "MelonPan_Bundle", "quantity": 2},               
                {"id": 331, "title": "Mephisto", "current_price": "1500.00", "sku": "Character_ElbowChat", "quantity": 1}
            ]
        }';

        // Создаем OrderItems
        OrderFactory::createOne();
        $order = OrderFactory::first();
        OrderItemFactory::createMany(3, ['ord' => $order]);
        $order_items = OrderItemFactory::all();

        $orderItems = [];
        foreach ($order_items as $order_item) {
            $orderItemReal = $order_item->_real();
            $orderItems[] = [
              'id' => $orderItemReal->getId(),
              //'title' => $orderItemReal->getTitle(),
              //'current_price' => $orderItemReal->getCurrentPrice(),
              //'sku' => $orderItemReal->getSku(),
              'quantity' => $orderItemReal->getQuantity(),
            ];
        }

        //dd($orderItems);
        $content = [
          'mail' => $user->getEmail(),
          'order' => $orderItems,
        ];

        $json_content = json_encode($content);

        $client->request(
            'POST',
            '/api/v1/order/create-order',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: $json_content
        );
        $json = json_decode($client->getResponse()->getContent(), true);

        // Проверим, что запрос отдает 201 (created)
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testDelete()
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user->_real());

        OrderFactory::createOne();
        $order = OrderFactory::first();

        $client->request('DELETE', '/api/v1/order/' . $order->getId());

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

}
