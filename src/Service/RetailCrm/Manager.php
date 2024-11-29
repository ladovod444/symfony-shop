<?php

namespace App\Service\RetailCrm;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use RetailCrm\Api\Client;
use RetailCrm\Api\Factory\SimpleClientFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


abstract class Manager
{
    protected Client $client;

    public function __construct(
        private string $url,
        public string $apiKey,
        protected LoggerInterface $logger,
        protected EntityManagerInterface $entityManager,
        protected ParameterBagInterface $parameterBag
    ) {
        $this->client = SimpleClientFactory::createClient($this->url, $this->apiKey);
    }

}