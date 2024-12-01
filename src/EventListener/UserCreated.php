<?php

namespace App\EventListener;

// ...
use App\Entity\User;
use App\Service\Mailer;
use App\Service\RetailCrm\CustomerManager;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;

//#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Order::class)]
//#[AsDoctrineListener(event: Events::postFlush, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
class UserCreated
{
    private $entities = [];

    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly CustomerManager $customerManager,
        private readonly EntityManagerInterface $entityTypeManager,
        private readonly ParameterBagInterface $parameterBag,
    ) {

    }

//    public function postFlush(PostFlushEventArgs $args): void
//    {
//        //dd($this->entities);
//        foreach ($this->entities as $entity) {
////            //$this->bus->dispatch(new OrderMessage($entity->getId()));
//
////            $id = $this->customerManager->createCustomer($entity);
////            $entity->setCustomerId($id);
//
//        }
//        ///var_dump($this->entities);
//    }

    public function postPersist(PostPersistEventArgs $event): void
    {
        $env = $this->parameterBag->get('kernel.environment');
        if ($event->getObject() instanceof User && $env !== 'test') {
            $entity = $event->getObject();
            $id = $this->customerManager->createCustomer($entity);
            $entity->setCustomerId($id);
            $this->entityTypeManager->flush();
        }
    }
}
