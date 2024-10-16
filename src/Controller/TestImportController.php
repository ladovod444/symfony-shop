<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\HttpClient;
use App\Service\ProductImport;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestImportController extends AbstractController
{
    public function __construct(
        private readonly ProductImport $productImport,
        private HttpClient $httpClient,
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
    ) {
    }

    #[NoReturn] #[Route('/test-import', name: 'app_test_import')]
    public function importTest(): Response
    {
        // $user = $this->getUser();
        // $this->productImport->import($user);
        $count = 1;
        $this->productImport->import($count);
        exit;
        $p = 1;

        return new JsonResponse(
            $p
        );
    }

    #[Route('/test-image-save/{productId}', name: 'app_test_import')]
    public function testImageSave(int $productId): Response
    {
        $url = 'https://media.fortniteapi.io/images/displayAssets/v2/MAX/DAv2_Bundle_Featured_ElegantLilyCharm/MI_0.png';
        //    $ch = curl_init();
        //
        //    curl_setopt($ch, CURLOPT_HEADER, 0);
        //    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //    curl_setopt($ch, CURLOPT_URL, $url);
        //
        //    $data = curl_exec($ch);
        //    curl_close($ch);

        $filename = basename($url);
        // dd($filename);

        $data = $this->httpClient->get($url, null);

        $productImage = 'logo-1.png';
        $upload_dir = './uploads/images';
        $fp = $upload_dir.'/'.$productImage;
        file_put_contents($fp, $data);

        $product = $this->productRepository->find($productId);

        $product->setImage($productImage);
        // TODO нужно будет сохранить уже изображение по ссылке вида
        // https://media.fortniteapi.io/images/displayAssets/v2/MAX/DAv2_Bundle_Featured_ElegantLilyCharm/MI_0.png
        // а потом уже сохранять ссылку уже сохраненного изображения.
        $this->entityManager->flush();

        return new Response(
            // $data
            $filename
        );
        // return $data;
    }
}
