<?php

namespace App\Controller;

use App\Service\ProductImport;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class TestImportController extends AbstractController
{
 public function __construct(private ProductImport  $productImport)
 {

 }

  #[Route('/test-import', name: 'app_test_import')]
 public function importTest(): Response {
   //$user = $this->getUser();
   //$this->productImport->import($user);
   $this->productImport->import();
   exit;
   $p = 1;
   return new JsonResponse(
     $p
   );

  }
}