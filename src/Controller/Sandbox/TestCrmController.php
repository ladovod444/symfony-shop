<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\ProductRepository;
use RetailCrm\Api\Enum\Product\ProductType;
use RetailCrm\Api\Factory\SimpleClientFactory;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Interfaces\ClientExceptionInterface;
use RetailCrm\Api\Model\Entity\Customers\Customer;
use RetailCrm\Api\Model\Entity\Loyalty\OrderProduct;
use RetailCrm\Api\Model\Entity\Store\OfferProduct;
use RetailCrm\Api\Model\Entity\Store\PriceUploadInput;
use RetailCrm\Api\Model\Entity\Store\PriceUploadPricesInput;
use RetailCrm\Api\Model\Entity\Store\Product;
use RetailCrm\Api\Model\Entity\Store\ProductEditGroupInput;
use RetailCrm\Api\Model\Entity\Store\ProductGroup;
use RetailCrm\Api\Model\Entity\Store\ProductOffer;
use RetailCrm\Api\Model\Filter\Store\OfferFilterType;
use RetailCrm\Api\Model\Request\Customers\CustomersCreateRequest;
use RetailCrm\Api\Model\Request\Store\OffersRequest;
use RetailCrm\Api\Model\Request\Store\PricesUploadRequest;
use RetailCrm\Api\Model\Request\Store\ProductsBatchCreateRequest;
use RetailCrm\Api\Model\Request\Store\ProductsRequest;
use RetailCrm\Api\Model\Response\References\PriceTypesResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\RetailCrm\ProductManager as RetailCrmProduct;

use App\Entity\Product as ProductEntity;

use RetailCrm\Api\Model\Entity\Store\ProductCreateInput;

class TestCrmController extends AbstractController
{

    public function __construct(private readonly RetailCrmProduct $product,
    private readonly ProductRepository $productRepository,)
    {

    }

    #[Route('/test/crm', name: 'app_test_crm')]
    public function index(): Response
    {

        $client = SimpleClientFactory::createClient('https://ladovod.retailcrm.ru', 'XQeQMSyPu4Z55O6S2wnnt6MODXaYF3ZH');

//        $creds = $client->api->credentials();
//        dd($creds);
//        dd($client);

        // Клиент.
        $request = new CustomersCreateRequest();
        $request->customer = new Customer();

        //$request->site = 'aliexpress';
        $request->customer->email = 'john.doe@example.com';
        $request->customer->firstName = 'John';
        $request->customer->lastName = 'Doe';

        //        try {
//            $response = $client->customers->create($request);
//        } catch (ApiExceptionInterface | ClientExceptionInterface $exception) {
//            echo $exception; // Every ApiExceptionInterface instance should implement __toString() method.
//            exit(-1);
//        }

//        echo 'Customer ID: ' . $response->id;
//        die();


        //$request = new Re;
        // Создание массива товаров
        $requestProds = new ProductsBatchCreateRequest();
        //$requestProd = new ProductsRequest();


        ///////////////////////////////////////////////////////////////
        $productInput = new ProductCreateInput();
        $productInput->name = 'testName';
        $productInput->description = 'testDescription';
        $productInput->active = true;
        $productInput->url = 'https://symfony-shop.ddev.site/';
        $productInput->article = 'testArticle';

        // https://ladovod.retailcrm.ru/admin/sites/2/edit#t-main
        // ЭТО Id Магазина !!!!!!!
        $productInput->catalogId = 2;


        $productInput->externalId = 'testExternalId' . time();
        $productInput->manufacturer = 'testManufacturer';
        $productInput->markable = true;
        $productInput->novelty = true;
        $productInput->popular = true;
        $productInput->recommended = true;
        $productInput->stock = true;
        $productInput->type = ProductType::PRODUCT;

        $productEditGroupInput = new ProductEditGroupInput();
        $productEditGroupInput->externalId = 'testExternalId';
        $productEditGroupInput->id = 20;
        // $productInput->
        $productInput->groups[] = $productEditGroupInput;

        //$productInput->catalogId = 19;
        //$productInput->
        ///////////////////////////////////////////////////////////////
        //$product->
        //$product->
//        $product->quantity = 3;
//        $product->maxPrice = 1000;
//        $product->minPrice = 900;

        //$product->

        // https://ladovod.retailcrm.ru/product-groups/19/edit
        //$product->catalogId = 19;

        try {
            //$response = $client->store->productsBatchCreate($requestProds);
            //dd($productInput);
            $response = $client->store->productsBatchCreate(new ProductsBatchCreateRequest([$productInput]));

            dd($response);
        } catch (ApiExceptionInterface $exception) {
            //$response = $client->store->products($requestProd);

            var_dump($exception->getErrorResponse());

            echo sprintf(
                'Error from RetailCRM API (status code: %d): %s %s',
                $exception->getStatusCode(),
                $exception->getMessage(),
                $exception->getErrorResponse()->errors[0]->code

            //$exception->getTraceAsString()
            );

        } catch (ClientExceptionInterface $exception) {
            echo $exception; // Every ApiExceptionInterface instance should implement __toString() method.
            exit(-1);
        }

        //dd($response->addedProducts);


        return $this->render('test_crm/index.html.twig', [
            'controller_name' => 'TestCrmController',
        ]);
    }


    #[Route('/test/crm-prices', name: 'app_test_crm_prices')]
    public function priceUpload(): Response
    {
        $client = SimpleClientFactory::createClient('https://ladovod.retailcrm.ru', 'XQeQMSyPu4Z55O6S2wnnt6MODXaYF3ZH');


        $price = new PriceUploadInput();
        // $price->site = 'aliexpress';
        $price->xmlId = 64;
        $price->id = 77; // ЭТО id торгового предложения
        // Видимо это должно быть в Product
        $price->prices = [new PriceUploadPricesInput('base', 100.20)];
        // $price->externalId = '64';
        // $price->externalId = 'testExternalId';
        //$price->
        //$price->site = 'ladovod.retailcrm.ru';
        $request = new PricesUploadRequest([$price]);

        // ????????????????????????????????????????????????
        // ВИДИМО НУЖНО СОЗДАВАТЬ / ЗАГРУЖАТЬ в цикле ТОВАР, получить его [offerIds][]
        // а потом уже использовать этот offerId для указания PRICE


        try {
            //$response = $client->store->productsBatchCreate($requestProds);
            //dd($productInput);
            //$response = $client->store->pricesUpload(new ProductsBatchCreateRequest([$productInput]));

            $response = $client->store->pricesUpload($request);

            dd($response);
        } catch (ApiExceptionInterface $exception) {
            //$response = $client->store->products($requestProd);

            var_dump($exception->getErrorResponse());

            echo sprintf(
                'Error from RetailCRM API (status code: %d): %s %s',
                $exception->getStatusCode(),
                $exception->getMessage(),
                $exception->getErrorResponse()->errors[0]->code

            //$exception->getTraceAsString()
            );

        } catch (ClientExceptionInterface $exception) {
            echo $exception; // Every ApiExceptionInterface instance should implement __toString() method.
            exit(-1);
        }

        return $this->render('test_crm/index.html.twig', [
            'controller_name' => 'TestCrmController',
        ]);
    }

    #[Route('/test/crm-offers', name: 'app_test_crm_offers')]
    public function getOffers(): Response
    {
//        $client = SimpleClientFactory::createClient('https://ladovod.retailcrm.ru', 'XQeQMSyPu4Z55O6S2wnnt6MODXaYF3ZH');
//
//        $request = new OffersRequest();
//        //$r = new OfferProduct();
//        //$r->id
//
//        $request->filter = new OfferFilterType();
//
//        $request->filter->name = "testName";
//
//        //$request->filter->ids[] = 77;
//
//
//
//        $response = $client->store->offers($request);
//
//        dd($response);

        $response = $this->product->getOffers();
        dd($response);

        return $this->render('test_crm/index.html.twig', [
            'controller_name' => 'TestCrmController',
        ]);
    }


    #[Route('/test/crm-products', name: 'app_test_crm_products')]
    public function getProducts(): Response
    {
        $client = SimpleClientFactory::createClient('https://ladovod.retailcrm.ru', 'XQeQMSyPu4Z55O6S2wnnt6MODXaYF3ZH');
        $request = new ProductsRequest();

//        $request->filter = new OfferFilterType();
//        $request->filter->name = "testName";
        //$request->filter->ids[] = 77;



        $response = $client->store->products($request);

        dd($response);

        return $this->render('test_crm/index.html.twig', [
            'controller_name' => 'TestCrmController',
        ]);
    }

    #[Route('/test/crm-create-products', name: 'app_test_crm_create_products')]
    public function createProducts(): Response
    {

        // Получим товар
        $product = $this->productRepository->find(2);

        $product_data = [
            'id' => $product->getId(),
            'name' => $product->getTitle(),
            'description' => $product->getDescription(),
            'groupName' => $product->getCategory()->getSlug(),
        ];
        //dd($product_data);


        $data = $this->product->createProducts($product_data);
        //dd($data);
    }

    #[Route('/test/crm-get-prices-types', name: 'app_test_crm_prices_types')]
    public function getPrices(): Response
    {
        $client = SimpleClientFactory::createClient('https://ladovod.retailcrm.ru', 'XQeQMSyPu4Z55O6S2wnnt6MODXaYF3ZH');
        //$request = new PriceTypesResponse();

//        $request->filter = new OfferFilterType();
//        $request->filter->name = "testName";
        //$request->filter->ids[] = 77;



        $response = $client->references->priceTypes();

        dd($response);

    }

    #[Route('/test/order-test/{order}', name: 'app_test_order_test')]
    public function testOrder(Order $order): Response
    {


        //$response = $this->product->getOffers();
        //dd($response);

        return $this->render('notify/notify-order.html.twig', [
            'controller_name' => 'TestCrmController',
            'order' => $order,
        ]);
    }

}
