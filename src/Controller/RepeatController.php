<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RepeatController extends AbstractController
{
    #[Route('/repeat/{product}', name: 'app_repeat' , defaults: ['product' => 31])]
    public function index(Product $product): Response
    {
        dd($product);
        return $this->render('repeat/index.html.twig', [
            'controller_name' => 'RepeatController',
        ]);
    }
}
