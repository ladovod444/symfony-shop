<?php

namespace App\Controller\Sandbox;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestRoutingOnlyController extends AbstractController
{
//    #[Route('//test/routing/only', name: 'app__test_routing_only')]
    public function index(): Response
    {
        return $this->render('test_routing_only/index.html.twig', [
            'controller_name' => __CLASS__,
        ]);
    }

    #[Route('/test/routing/{optional}', name: 'app_test_routing_optional')]
    public function optional($admin_mail, $optional = 15): Response
    {
        dd($admin_mail);
        //dd($optional);
        return $this->render('test_routing_only/index.html.twig', [
            'controller_name' => __CLASS__,
        ]);
    }

    /**
     * This route has a greedy pattern and is defined first.
     */
    #[Route('/blog/{slug}', name: 'blog_show')]
    public function show(string $slug): Response
    {
        dd($slug);
        // ...
    }

    /**
     * This route could not be matched without defining a higher priority than 0.
     */
//    #[Route('/blog/list', name: 'blog_list', priority: 2)]
    #[Route('/blog/list', name: 'blog_list')]
    public function list(): Response
    {
        dd('list');
        // ...
    }
}
