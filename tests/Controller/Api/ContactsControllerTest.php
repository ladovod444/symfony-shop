<?php

namespace Controller\Api;

use App\Contacts\Contacts;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Helmich\JsonAssert\JsonAssertions;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ContactsControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;
    use JsonAssertions;

    public function testSendContacts()
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user);

        $contacts = (new Contacts())
            ->setTitle('test title')
            ->setBody('test body')
            ->setEmail('test@test.com')
            ;

        $data = json_encode($contacts);
//        $data = json_encode(['email' => 'test@test.com', 'title' => 'test title', 'body' => 'test body']);

        $client->request('POST', '/api/v1/send-contacts',
            server: ['CONTENT_TYPE' => 'application/json'], // !!! Важно указывать тип контента
            content: $data);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $json = json_decode($client->getResponse()->getContent(), true);
        $this->assertJsonValueEquals($json, '$.title', 'test title');
    }
}
