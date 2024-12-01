<?php

namespace App\EventListener;

// ...
use App\Entity\Order;
use App\Entity\User;
use App\Message\OrderMessage;
use App\Service\Mailer;
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
#[AsDoctrineListener(event: Events::postFlush, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
class OrderCreated
{
    private $entities = [];

    public function __construct(
        private readonly MessageBusInterface $bus,
        private  readonly ParameterBagInterface $parameterBag,
    ) {

    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        foreach ($this->entities as $entity) {
            $this->bus->dispatch(new OrderMessage($entity->getId()));
        }
    }

    public function postPersist(PostPersistEventArgs $event): void
    {
        $env = $this->parameterBag->get('kernel.environment');
        if ($event->getObject() instanceof Order && $env !== 'test') {
            $entity = $event->getObject();
            ///dd($args);
            $this->entities[$entity->getId()] = $entity;
            // И далее эти $this->entites можно увидеть в событии postFlush выше по коду
        }
    }
}
