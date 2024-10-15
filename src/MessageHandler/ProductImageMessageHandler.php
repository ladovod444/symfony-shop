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
    // ... do some work - like sending an SMS message!
    //dd($checkUniqueTextJob);
    $productId = (int) $productImageMessage->getContent();
    $product = $this->productRepository->find($productId);

    // TODO сделать сохранение изображения, которое
    // будет храниться в сериализованных данных
    // А пока просто сделаем img + $productId
    $test_image = $productId . "img";
    $product->setImage($test_image);
    $this->entityManager->flush();

    $this->entityManager->flush();
  }
}