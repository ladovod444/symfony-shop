<?php

namespace App\Service\RetailCrm;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use RetailCrm\Api\Client;
use RetailCrm\Api\Factory\SimpleClientFactory;


abstract class Manager
{
    protected Client $client;
    public function __construct(private string $url, public string $apiKey, protected LoggerInterface $logger)
    {
        $this->client = SimpleClientFactory::createClient($this->url, $this->apiKey);
    }

}