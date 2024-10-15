<?php

namespace App\MessageHandler;

use App\Message\ProductImageMessage;
use App\Repository\ProductRepository;
//use App\MessageHandler\ProductImageMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProductImageMessageHandler
{
  public function __construct(private readonly EntityManagerInterface $entityManager,
                              private readonly ProductRepository $productRepository) {

  }

  public function __invoke(ProductImageMessage $productImageMessage): void
  {

    $product_data = $productImageMessage->getContent();
    $product_data = json_decode($product_data, true);

   // $productId = (int) $productImageMessage->getContent();
    $productId = (int) $product_data['product_id'];
    $productImage = $product_data['product_image'];
    $product = $this->productRepository->find($productId);

    // TODO сделать сохранение изображения, которое
    // будет храниться в сериализованных данных

    $product->setImage($productImage);
    // TODO нужно будет сохранить уже изображение по ссылке вида
    // https://media.fortniteapi.io/images/displayAssets/v2/MAX/DAv2_Bundle_Featured_ElegantLilyCharm/MI_0.png
    // а потом уже сохранять ссылку уже сохраненного изображения.

    $this->entityManager->flush();

  }
}