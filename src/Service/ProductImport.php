<?php

namespace App\Service;

use App\Entity\Product;
use App\Service\HttpClient;

class ProductImport
{
  public function __construct(private readonly HttpClient $httpClient) {

  }

  public function import(array $products_data): void {
    //$products_data = $this->httpClient->get();
  }

  public function createProduct(array $product_data): Product {

  }
}