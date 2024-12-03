<?php

namespace Kernel\Service;

use App\Entity\Contacts as ContactEntity;
use App\Contacts\Contacts;
use App\Kernel;
use App\Service\ContactsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

//class ContactsServiceTest extends KernelTestCase
class ContactsServiceTest extends TestCase
{

    const EMAIL = "test@gmail.com";
    const TITLE = "Title";
    const BODY = "Test Body";

    private EventDispatcherInterface $eventDispatcher;
    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testCreateContacts()
    {
//        self::bootKernel();
        // Мокаем сервис EventDispatcherInterface, т.к. сейчас мы не планируем отправлять диспатчить Event
//       $eventDispatcher =  $this->createMock(EventDispatcherInterface::class);
//       static::getContainer()->get(EventDispatcherInterface::class, $eventDispatcher);


        // Entity
        $expectedContactsEntity = (new ContactEntity())
            ->setEmail(self::EMAIL)
            ->setUser(null)
            ->setTitle(self::TITLE)
            ->setBody(self::BODY);

        // Contacts
        $requestContacts = (new Contacts())
            ->setEmail(self::EMAIL)
            ->setUser(null)
            ->setTitle(self::TITLE)
            ->setBody(self::BODY);

        // Мокаем сервис EntityManagerInterface
//        $entityManager = $this->createMock(EntityManagerInterface::class);
//        static::getContainer()->get(EntityManagerInterface::class, $entityManager);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($expectedContactsEntity);

        $this->entityManager->expects($this->once())
//            ->method('saveAndCommit')
            ->method('flush');

        (new ContactsService($this->entityManager, $this->eventDispatcher))->createContacts($requestContacts);
    }
}
