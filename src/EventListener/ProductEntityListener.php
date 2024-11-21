<?php

namespace App\EventListener;

use App\Entity\Product;
use App\Service\RetailCrm\ProductsRetailcrmBus;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;


#[AsDoctrineListener(event: Events::postFlush, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postUpdate, priority: 500, connection: 'default')]
//#[AsDoctrineListener(event: Events::onFlush, priority: 500, connection: 'default')]
class ProductEntityListener
{

    private array $entites = [];

    public function __construct(
        private readonly ProductsRetailcrmBus $productsRetailcrmBus
    ) {
    }

    public function postFlush(PostFlushEventArgs $args): void
    {

        foreach ($this->entites as $entity) {
            $retailCrmMessage = json_encode(['product_id' => $entity->getId(),]);
            $this->productsRetailcrmBus->execute($retailCrmMessage);
        }
    }

    public function postUpdate(PostUpdateEventArgs $event): void
    {
        if ($event->getObject() instanceof Product) {
            $entity = $event->getObject();
            $this->entites[$entity->getId()] = $entity;
            // И далее эти $this->entites можно увидеть в событии postFlush выше по коду
        }

    }

}