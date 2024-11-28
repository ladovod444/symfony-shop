<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Service\RetailCrm\ProductsRetailcrmBus;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[WithMonologChannel('import')]
class ProductImport
{

    public function __construct(
        private readonly HttpClient $httpClient,
        private string $fortnite_api_url,
        private string $fortnite_api_key,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private LoggerInterface $logger,
        private readonly MessageBusInterface $bus,
        private readonly ProductsBus $productsBus,
        private readonly ProductsRetailcrmBus $productsRetailcrmBus,
        private readonly ParameterBagInterface $parameterBag,
        private readonly CategoryRepository $categoryRepository
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function import($count): void
    {
        $products_data = $this->httpClient->get(
            $this->fortnite_api_url,
            $this->fortnite_api_key
        );

       // dd($products_data);

        $products = json_decode($products_data, true);
        $products = $products['shop'];
        //dd($products);

        $batchSize = 20;
        $data_count = 0;
        $this->logger->info('Start importing products');
        foreach ($products as $product_data) {
             dd($product_data); exit;
            $this->createProduct($product_data);
            ++$data_count;
            if (($data_count % $batchSize) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear(
                ); // Detaches all objects from Doctrine!
            }

            // Если задано кол-во.
            if (null !== $count && $data_count == $count) {
                break;
            }
        }
        $this->entityManager->flush(
        ); // Persist objects that did not make up an entire batch
        $this->entityManager->clear();
        $this->logger->info('Finish importing products');
    }

    public function createProduct(array $product_data): void
    {
        // $user = $this->userRepository->findOneBy(['email' => 'ladovod@gmail.com']);
        $user = $this->userRepository->find(
            $this->parameterBag->get('app:import_product_author')
        );

        $product = new Product();
        $product->setTitle($product_data['displayName'])
          ->setDescription($product_data['displayDescription'])
          ->setCurrentPrice($product_data['price']['finalPrice'])
          ->setRegularPrice($product_data['price']['regularPrice'])
          ->setSku($product_data['mainId'])
          ->setUser($user);
        $category = $this->randomCategory();
        $product->setCategory($category);
        $this->entityManager->persist($product);

        // Отправить json сообщение, c id и урлом изображения.
        $message = json_encode(
            [
            'product_id' => $product->getId(),
            'product_image' => $product_data['displayAssets'][0]['url'],
          ]
        );

        //$message = "test mess " . $product->getId();

        ////$this->bus->dispatch(ProductImageMessage::create($message));
        ///
        $this->productsBus->execute($message);


        // Создаем message для последующего создания товара в Retail crm
        $retailCrmMessage = json_encode(
            [
                'product_id' => $product->getId(),
                'is_new' => true,
            ]
        );

        $this->productsRetailcrmBus->execute($retailCrmMessage);
        // $this->bus->dispatch(ProductImageMessage::create($product->getId()));
        $this->logger->info('Creating product '.$product->getTitle());
    }

    /**
     * Adds Random category during import.
     *
     * @return \App\Entity\Category
     */
    private function randomCategory(): Category
    {
        $categories = $this->categoryRepository->findAll();

        $category_count = count($categories);
        $rand = mt_rand(0, $category_count - 1);

        return $categories[$rand];
    }

}
