<?php

namespace App\Tests\Controller\Api;

use App\Factory\UserFactory;
use App\Factory\ProductFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Helmich\JsonAssert\JsonAssertions;

class ProductControllerTest extends WebTestCase
{

    use ResetDatabase;
    use Factories;
    use JsonAssertions;

    public function testIndex(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user->_real());

        // Создадим тестовые товары
        ProductFactory::createMany(10, ['user' => $user]);

        $client->request('GET', '/api/v1/product/list');

        // Авторизация не нужна, используется $client->loginUser
        //$crawler = $client->request('GET', '/api/blog');
        //        $client->request('GET', '/api/v1/product/list', [], [], [
        //          'HTTP_ACCEPT' => 'application/json',
        //          'AUTHORIZATION' => 'Bearer ' . 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3Mjk4NTkzNDgsImV4cCI6MTcyOTg2Mjk0OCwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiIsIlJPTEVfU1VQRVJfQURNSU4iXSwidXNlcm5hbWUiOiJsYWRvdm9kQGdtYWlsLmNvbSJ9.CVeVnB5IH-YBy1OAudM_b4SbVenmryb34cINvx9GdkpQ-DwItYydAXbLu8Da-fmiqs2SpShX3k6ds5fK6h14dkhNfTcUQhajryC3wJCHBwTT644dm9MP9JUeHbrd1TQCThIAs_v1dH_rXfb0A1MLvaIPFqLG4Uh1eo1fS1wZvfUKvZWzpvCIBUbCTJOiZNAj-APcsgjK782xbN_V2u3Q-Z6YQAr-cwJTFF5w75hMIwIWMrRA-cvAAsHw_o9Kq7lL5LCRymV0pJEOQnjUs_2kEg6sjAyYfeydVek2GOXyz24yzYA87pupSiCQEYrHqL9xfYTCjFgsS3Rp-Z9wXaXCRw'
        //          ]);

        // получаем массив страниц
        $json = json_decode($client->getResponse()->getContent(), true);
        //dd($json);

        // Проверям кол-во
        $this->assertCount(10, $json);

        // Проверяем статус (успешность) запроса
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertResponseIsSuccessful();

        //dump($client->getResponse()->getContent());

        //    $this->assertSelectorTextContains('h1', 'Custom jumbotron');
        //    $this->assertCount(6, $crawler->filter('div.col-md-4.blog-item'));
    }

    public function testProduct(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user->_real());

        // Создадим тестовый товар
        ProductFactory::createOne();
        // Получим его
        $entity = ProductFactory::first();
        $title = $entity->getTitle();
        $current_price = $entity->getCurrentPrice();
        $description = $entity->getDescription();

        // Подготовим запрос
        $client->request(
          'GET',
          '/api/v1/product/' . $entity->getId(),
          server: ['CONTENT_TYPE' => 'application/json'],
        );

        // Получаем данные
        $json = json_decode($client->getResponse()->getContent(), true);

        // Проверим, что title, current_price, description соотв-ют созданному товару
        $this->assertJsonValueEquals($json, '$.title', $title);
        $this->assertJsonValueEquals($json, '$.current_price', $current_price);
        $this->assertJsonValueEquals($json, '$.description', $description);
        // Проверим, что запрос отдает 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertResponseIsSuccessful();
    }

    public function testAdd(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user->_real());

        // Подготовим содержимое
        $body = '{
            "title": "nnHalloween Town Meeting",
            "description": "nnnNew descr",
            "sku": "Test nnnnJBPID_RBLT_Townhall",
            "current_price": "400.00",
            "regular_price": "400.00",
            "image": "nnJBPID_RBLT_TownhallMI_0.png"
        }';

        // Подготовим запрос
        $client->request(
            'POST',
            '/api/v1/product/dto',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: $body
        );

        // Получим ответ
        $json = json_decode($client->getResponse()->getContent(), true);

        // Проверим, что title, current_price вновь созданного товара равны указанным значениям
        $this->assertJsonValueEquals($json, '$.title', 'nnHalloween Town Meeting');
        $this->assertJsonValueEquals($json, '$.current_price', 400.00);

        // Проверим, что запрос отдает 201 (created)
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $this->assertResponseIsSuccessful();
    }

    public function testUpdate(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user->_real());

        // Создадим тестовый товар
        ProductFactory::createOne();
        // Получим его
        $entity = ProductFactory::first();

        // Подготовим содержимое
        $body = '{
            "title": "Updated Halloween Town Meeting",
            "description": "Updated Halloween Town Meeting nnnNew descr",
            "sku": "Test nnnnJBPID_RBLT_Townhall",
            "current_price": "400.00",
            "regular_price": "400.00",
            "image": "nnJBPID_RBLT_TownhallMI_0.png"
        }';

        // Подготовим запрос
        $client->request(
            'PUT',
            '/api/v1/product/dto/' . $entity->getId(),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: $body
        );

        // Получаем данные
        $json = json_decode($client->getResponse()->getContent(), true);

        // Проверим, что title, current_price, description обновились на указанные данные
        $this->assertJsonValueEquals($json, '$.title', 'Updated Halloween Town Meeting');
        $this->assertJsonValueEquals($json, '$.current_price', 400.00);
        $this->assertJsonValueEquals($json, '$.description', 'Updated Halloween Town Meeting nnnNew descr');

        // Проверим, что запрос отдает 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user->_real());

        // Создадим тестовый товар
        ProductFactory::createOne();
        // Получим его
        $entity = ProductFactory::first();

        // Подготовим запрос
        $client->request(
            'DELETE',
            '/api/v1/product/' . $entity->getId(),
            server: ['CONTENT_TYPE' => 'application/json'],
        );

        // Проверим, что запрос отдает 204 - no content
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

}
