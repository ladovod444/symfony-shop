<?php

namespace App\Service\RetailCrm;

use App\Repository\ProductRepository;
use Monolog\Attribute\WithMonologChannel;
use RetailCrm\Api\Exception\Api\AccountDoesNotExistException;
use RetailCrm\Api\Exception\Api\ApiErrorException;
use RetailCrm\Api\Exception\Api\MissingCredentialsException;
use RetailCrm\Api\Exception\Api\MissingParameterException;
use RetailCrm\Api\Exception\Api\ValidationException;
use RetailCrm\Api\Exception\Client\HandlerException;
use RetailCrm\Api\Exception\Client\HttpClientException;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Interfaces\ClientExceptionInterface;
use RetailCrm\Api\Model\Entity\Store\PriceUploadInput;
use RetailCrm\Api\Model\Entity\Store\PriceUploadPricesInput;
use RetailCrm\Api\Model\Filter\Store\ProductFilterType;
use RetailCrm\Api\Model\Request\Store\PricesUploadRequest;
use RetailCrm\Api\Model\Request\Store\ProductsRequest;

#[WithMonologChannel('retailcrm')]
class OffersManager extends Manager
{

//filter[name]

    /**
     * @throws ApiErrorException
     * @throws ClientExceptionInterface
     * @throws HandlerException
     * @throws AccountDoesNotExistException
     * @throws MissingCredentialsException
     * @throws ApiExceptionInterface
     * @throws HttpClientException
     * @throws MissingParameterException
     * @throws ValidationException
     */
    public function UpdateOffer(ProductRepository $productRepository, int $product_id) {

        $request = new ProductsRequest();
        $request->filter = new ProductFilterType();
        $request->filter->ids[] = $product_id;

        // Получаем ответ - товары от сервера.
        $response = $this->client->store->products($request);

        $site_product = $productRepository->findOneBy(['retailcrm_id' => $product_id]);

        if ($site_product && $response->products !== null) {
            $this->logger->info(
                "Starting update Retailcrm product offer with name= {$site_product->getTitle()} and id={$product_id}"
            );

            $product_current_price = $site_product->getCurrentPrice();
            $product_regular_price = $site_product->getRegularPrice();

            // Обновить торговое предложение...
            $price = new PriceUploadInput();
            $price->id = $response->products[0]->offers[0]->id; // ЭТО id торгового предложения !!!!

            $price->prices = [
                new PriceUploadPricesInput('base', $product_regular_price),
                new PriceUploadPricesInput('current', $product_current_price),
            ];
            $request = new PricesUploadRequest([$price]);

            try {
                $response = $this->client->store->pricesUpload($request);
                $this->logger->info("Updated Retailcrm product offer with offer_id= {$price->id}");
            } catch (ApiExceptionInterface $exception) {
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
            unset($site_product);
        }
    }

    public function UpdateOffers(ProductRepository $productRepository, int $count)
    {

        // Реквест на получение товаров
        $request = new ProductsRequest();

//        $request->filter = new OfferFilterType();
//        $request->filter->name = "testName";
        //$request->filter->ids[] = 77;


        // Получаем ответ - товары от сервера.
        $response = $this->client->store->products($request);

        $prod_count = 0;
        foreach ($response->products as $product) {

            if ($prod_count === $count) {
                break;
            }

            // Получим товар и цену (сайт)
            $site_product = $productRepository->findOneBy(['retailcrm_id' => $product->id]);

            if ($site_product) {
                $this->logger->info(
                    "Starting update Retailcrm product offer with name= {$site_product->getTitle()} and id={$product->id}"
                );

                $product_current_price = $site_product->getCurrentPrice();
                $product_regular_price = $site_product->getRegularPrice();

                // Обновить торговое предложение...
                $price = new PriceUploadInput();
                $price->id = $product->offers[0]->id; // ЭТО id торгового предложения !!!!

                $site_product->setOfferId($product->offers[0]->id);
                $this->entityManager->flush();

                $price->prices = [
                    new PriceUploadPricesInput('base', $product_regular_price),
                    new PriceUploadPricesInput('current', $product_current_price),
                ];
                $request = new PricesUploadRequest([$price]);

                try {
                    $response = $this->client->store->pricesUpload($request);
                    $this->logger->info("Updated Retailcrm product offer with offer_id= {$product->offers[0]->id}");
                } catch (ApiExceptionInterface $exception) {
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
                unset($site_product);
            }

            $prod_count++;
        }

        // Обратить внимание на пагинацию 20 элементов
        /**
         * +pagination: RetailCrm\Api\Model\Entity\Pagination^ {#3899
         * +limit: 20
         * +totalCount: 13
         * +currentPage: 1
         * +totalPageCount: 1
         * }
         */
    }
}