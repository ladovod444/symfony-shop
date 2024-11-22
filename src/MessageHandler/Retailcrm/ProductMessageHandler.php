<?php

namespace App\MessageHandler\Retailcrm;

use App\Message\Retailcrm\ProductMessage;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use RetailCrm\Api\Exception\Api\AccountDoesNotExistException;
use RetailCrm\Api\Exception\Api\ApiErrorException;
use RetailCrm\Api\Exception\Api\MissingCredentialsException;
use RetailCrm\Api\Exception\Api\MissingParameterException;
use RetailCrm\Api\Exception\Api\ValidationException;
use RetailCrm\Api\Exception\Client\HandlerException;
use RetailCrm\Api\Exception\Client\HttpClientException;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Interfaces\ClientExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Service\RetailCrm\ProductManager as RetailCrmProduct;
use App\Service\RetailCrm\OffersManager as RetailCrmOffer;

#[AsMessageHandler]
class ProductMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository $productRepository,
        private readonly RetailCrmProduct $product,
        private readonly RetailCrmOffer $offer
    ) {
    }

    /**
     * @throws ApiErrorException
     * @throws ClientExceptionInterface
     * @throws HandlerException
     * @throws AccountDoesNotExistException
     * @throws MissingCredentialsException
     * @throws HttpClientException
     * @throws ApiExceptionInterface
     * @throws ValidationException
     * @throws MissingParameterException
     */
    public function __invoke(ProductMessage $productMessage): void {

        // 0. Получим содержимое сообщения
        $productData = $productMessage->getContent();
        $productData = json_decode($productData, true);


        // 1. Получить product
        $productId = (int) $productData['product_id'];
        $product = $this->productRepository->find($productId);


        $product_data = [
            'id' => $product->getId(),
            'name' => $product->getTitle(),
            'description' => $product->getDescription(),
            'groupName' => $product->getCategory()->getSlug(),
        ];

        // Если создание товара
        if (isset($productData['is_new'])) {
            // 2. Создать его в retailCRM, получить id из retailCRM
            $data = $this->product->createProducts($product_data);

            // Сохранить retailcrm_id для Product
            $product->setRetailcrmId($data[0]);
        }

        else {
            // Сделать обновление товара и цены
            $product_data['retailcrm_id'] = $product->getRetailcrmId();
            $this->product->updateProduct($product_data);
            $this->offer->updateOffer($this->productRepository, $product->getRetailcrmId());
        }
        $this->entityManager->flush();
        //$this->entityManager->persist($product);


    }

}