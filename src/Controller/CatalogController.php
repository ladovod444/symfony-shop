<?php

namespace App\Controller;

use App\Filter\ProductFilter;
use App\Form\ProductFilterType;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogController extends AbstractController
{
    #[Route('/catalog', name: 'app_catalog')]
    public function index(Request            $request,
        ProductRepository     $productRepository,
        PaginatorInterface $paginator): Response
    {

        $productFilter = new ProductFilter();
        $form = $this->createForm(ProductFilterType::class, $productFilter);
        $form->handleRequest($request);

        $pagination = $paginator->paginate(
        //$query, /* query NOT result */
            $productRepository->findByProductFilter($productFilter),
            $request->query->getInt('page', 1), /*page number*/
            18 /*limit per page*/
        );

        return $this->render('catalog/index.html.twig', [
//            'products' => $productRepository->findByProductFilter,
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);

//        return $this->render('catalog/index.html.twig', [
//            'controller_name' => 'CatalogController',
//        ]);
    }
}
