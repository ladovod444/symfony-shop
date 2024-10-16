<?php

namespace App\Service;

use App\Entity\Product;
use App\Message\ProductImageMessage;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[WithMonologChannel('import')]
class ProductImport
{
    public function __construct(private readonly HttpClient $httpClient,
        private string $fortnite_api_url,
        private string $fortnite_api_key,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private LoggerInterface $logger,
        private readonly MessageBusInterface $bus,
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function import($count): void
    {
        $products_data = $this->httpClient->get($this->fortnite_api_url, $this->fortnite_api_key);

        $products = json_decode($products_data, true);
        $products = $products['shop'];

        $batchSize = 20;
        $data_count = 0;
        $this->logger->info('Start importing products');
        foreach ($products as $product_data) {
            // dd($product_data); exit;
            $this->createProduct($product_data);
            ++$data_count;
            if (($data_count % $batchSize) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine!
            }

            // Если задано кол-во.
            if (null !== $count && $data_count == $count) {
                break;
            }
        }
        $this->entityManager->flush(); // Persist objects that did not make up an entire batch
        $this->entityManager->clear();
        $this->logger->info('Finish importing products');
    }

    public function createProduct(array $product_data): void
    {
        $user = $this->userRepository->findOneBy(['email' => 'ladovod@gmail.com']);

        $product = new Product();
        $product->setTitle($product_data['displayName'])
          ->setDescription($product_data['displayDescription'])
          ->setCurrentPrice($product_data['price']['finalPrice'])
          ->setRegularPrice($product_data['price']['regularPrice'])
          ->setSku($product_data['mainId'])
          ->setUserId($user);
        $this->entityManager->persist($product);

        // Отправить json сообщение, c id и урлом изображения.
        $message = json_encode(
            [
                'product_id' => $product->getId(),
                'product_image' => $product_data['displayAssets'][0]['url'],
            ]
        );

        $this->bus->dispatch(ProductImageMessage::create($message));
        // $this->bus->dispatch(ProductImageMessage::create($product->getId()));
        $this->logger->info('Creating product '.$product->getTitle());
    }
}
