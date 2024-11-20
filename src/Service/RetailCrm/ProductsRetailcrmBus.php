<?php

namespace App\Service\RetailCrm;

use App\Message\Retailcrm\ProductMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductsRetailcrmBus
{
    public function __construct(private readonly MessageBusInterface $bus)
    {

    }

    public function execute($message)
    {
        $this->bus->dispatch(ProductMessage::create($message));
    }
}
