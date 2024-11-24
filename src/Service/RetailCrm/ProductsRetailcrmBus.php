<?php

namespace App\Service\RetailCrm;

use App\Message\Retailcrm\ProductMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductsRetailcrmBus
{
    public function __construct(private readonly MessageBusInterface $bus)
    {

    }

    /**
     * @param string $message
     * @return void
     */
    public function execute(string $message): void
    {
        $this->bus->dispatch(ProductMessage::create($message));
    }
}
