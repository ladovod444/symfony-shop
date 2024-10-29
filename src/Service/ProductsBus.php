<?php

namespace App\Service;

use App\Message\ProductImageMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductsBus
{
    public function __construct(private readonly MessageBusInterface $bus)
    {

    }

    public function execute($message)
    {
        $this->bus->dispatch(ProductImageMessage::create($message));
    }
}
