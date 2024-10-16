<?php

namespace App\MessageHandler;

use App\Message\ProductImageMessage;
use App\Repository\ProductRepository;
use App\Service\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProductImageMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository $productRepository,
        private readonly HttpClient $httpClient,
    ) {
    }

    public function __invoke(ProductImageMessage $productImageMessage): void
    {
        $product_data = $productImageMessage->getContent();
        $product_data = json_decode($product_data, true);

        // $productId = (int) $productImageMessage->getContent();
        $productId = (int) $product_data['product_id'];
        $productImage = $product_data['product_image'];
        $product = $this->productRepository->find($productId);

        // Получаем изображение
        $data = $this->httpClient->get($productImage, null);

        // Формируем ему имя
        $productImage = $product->getSku().basename($productImage);

        // Cохраняем изображение
        // $upload_dir = './uploads/images';
        $upload_dir = './public/uploads/images';
        $fp = $upload_dir.'/'.$productImage;
        file_put_contents($fp, $data);

        // Записывем в БД название файла
        $product->setImage($productImage);

        $this->entityManager->flush();
    }
}
