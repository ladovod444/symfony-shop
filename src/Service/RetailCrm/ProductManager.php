<?php

namespace App\Service\RetailCrm;

use Monolog\Attribute\WithMonologChannel;
use RetailCrm\Api\Client;
use RetailCrm\Api\Enum\Product\ProductType;
use RetailCrm\Api\Factory\SimpleClientFactory;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Interfaces\ClientExceptionInterface;
use RetailCrm\Api\Model\Entity\Store\ProductCreateInput;
use RetailCrm\Api\Model\Entity\Store\ProductEditGroupInput;
use RetailCrm\Api\Model\Entity\Store\ProductEditInput;
use RetailCrm\Api\Model\Filter\Store\OfferFilterType;
use RetailCrm\Api\Model\Request\Store\OffersRequest;
use RetailCrm\Api\Model\Request\Store\ProductBatchEditRequest;
use RetailCrm\Api\Model\Request\Store\ProductsBatchCreateRequest;
use RetailCrm\Api\Model\Request\Store\ProductsRequest;
use RetailCrm\Api\Model\Response\Store\OffersResponse;

#[WithMonologChannel('retailcrm')]
class ProductManager extends Manager
{
    use Helper;


    public function getOffers(): OffersResponse|string
    {
        //$client = SimpleClientFactory::createClient('https://ladovod.retailcrm.ru', 'XQeQMSyPu4Z55O6S2wnnt6MODXaYF3ZH');

        $request = new OffersRequest();
        //$r = new OfferProduct();
        //$r->id

//        $request->filter = new OfferFilterType();
//        $request->filter->name = "testName";
        //$request->filter->ids[] = 77;

        $response = $this->client->store->offers($request);

        return $response;

        //dd($response);
    }

    /**
     * @param $products_data
     * @return void
     *
     * @todo пока сделать сохранение только одного товара
     * Далее сделать до 20
     */
    public function createProducts($products_data): array {

        $productInput = new ProductCreateInput();
        $productInput->name = $products_data['name'];
        $productInput->description = $products_data['description'];
        $productInput->active = true;
        $productInput->url = 'https://symfony-shop.ddev.site/';
        //$productInput->article = 'testArticle';

        // https://ladovod.retailcrm.ru/admin/sites/2/edit#t-main
        // ЭТО Id Магазина !!!!!!!
        $productInput->catalogId = self::CATALOG_ID;


        $productInput->externalId = $products_data['id'];
        $productInput->manufacturer = 'Symfony shop';
        /*
        $productInput->markable = true;
        $productInput->novelty = true;
        $productInput->popular = true;
        $productInput->recommended = true;
        */
        $productInput->stock = true;
        $productInput->type = ProductType::PRODUCT;
        
        $productEditGroupInput = new ProductEditGroupInput();
        $productEditGroupInput->externalId = $products_data['groupName'];
        // @todo сделать соотв. поле для Category
        $productEditGroupInput->id = $this->catalogMap[$products_data['groupName']];
        $productInput->groups[] = $productEditGroupInput;

        try {
            $this->logger->info(sprintf('Created product %s', $productInput->name));
            $response = $this->client->store->productsBatchCreate(new ProductsBatchCreateRequest([$productInput]));

            return $response->addedProducts;
        } catch (ApiExceptionInterface $exception) {
            // @todo добавить в лог
            echo sprintf(
                'Error from RetailCRM API (status code: %d): %s %s',
                $exception->getStatusCode(),
                $exception->getMessage(),
                $exception->getErrorResponse()->errors[0]->code
            );

        } catch (ClientExceptionInterface $exception) {
            echo $exception; // Every ApiExceptionInterface instance should implement __toString() method.
            exit(-1);
        }
    }

    /**
     * @param $products_data
     * @return void
     *
     */
    public function updateProduct($products_data): void {
        $productInput = new ProductEditInput();
        $productInput->name = $products_data['name'];
        $productInput->description = $products_data['description'];
        $productInput->active = true;
        $productInput->id = $products_data['retailcrm_id'];

        try {
            $response = $this->client->store->productBatchEdit(new ProductBatchEditRequest([$productInput]));
            $this->logger->info(sprintf('Updated product %s', $productInput->name));
        } catch (ApiExceptionInterface $exception) {
            // @todo добавить в лог
            echo sprintf(
                'Error from RetailCRM API (status code: %d): %s %s',
                $exception->getStatusCode(),
                $exception->getMessage(),
                $exception->getErrorResponse()->errors[0]->code
            );

        } catch (ClientExceptionInterface $exception) {
            echo $exception; // Every ApiExceptionInterface instance should implement __toString() method.
            exit(-1);
        }
    }

}