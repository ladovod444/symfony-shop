<?php

namespace App\Service\RetailCrm;

use App\Entity\Order;
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
use RetailCrm\Api\Model\Entity\Loyalty\SerializedOrder;
use RetailCrm\Api\Model\Entity\Loyalty\SerializedOrderProduct;
use RetailCrm\Api\Model\Entity\Loyalty\SerializedOrderProductOffer;
use RetailCrm\Api\Model\Entity\Orders\SerializedRelationCustomer;
use RetailCrm\Api\Model\Request\Orders\OrdersCreateRequest;
use RetailCrm\Api\Model\Request\Orders\OrdersRequest;
use RetailCrm\Api\Model\Request\Store\ProductsBatchCreateRequest;

#[WithMonologChannel('retailcrm')]
class OrderManager extends Manager
{

    /**
     * @throws ApiErrorException
     * @throws ClientExceptionInterface
     * @throws HandlerException
     * @throws MissingCredentialsException
     * @throws AccountDoesNotExistException
     * @throws ApiExceptionInterface
     * @throws HttpClientException
     * @throws ValidationException
     * @throws MissingParameterException
     */
    public function createOrder(Order $order)
    {
        $countryIso = $this->parameterBag->get('app:retailcrm:country_iso');
        $orderType = $this->parameterBag->get('app:retailcrm:order_type');
        // Пока сделать для имеющихся пользоватлей
        $orderUser = $order->getOwner();
        $customer_id = $orderUser->getCustomerId();

        $request = new OrdersCreateRequest();

        $request->order = new SerializedOrder();

        $request->order->countryIso = $countryIso;
        $request->order->orderType = $orderType;
        //$request->order->firstName = 'Dmitri'; // @todo переделать
        $request->order->customer = new SerializedRelationCustomer();
        $request->order->customer->id = $customer_id;

        $request->order->items = [];

        foreach ($order->getOrderItems() as $item) {
            $orderItem = new SerializedOrderProduct();
            $orderItem->initialPrice = $item->getProduct()->getCurrentPrice();
            $orderItem->quantity = $item->getQuantity();

            $offer = new SerializedOrderProductOffer();
            $offer->id = $item->getProduct()->getOfferId();
            $orderItem->offer = $offer;

            $request->order->items[] = $orderItem;
        }
        
        try {
            $response = $this->client->orders->create($request);

            $this->logger->info(sprintf('Created order %s', $response->order->id));
            //return $response->order->id;
        } catch (ApiExceptionInterface $exception) {
            // @todo добавить в лог
            $this->logger->info(sprintf('Order error %s', $exception->getErrorResponse()->errors[0]->code));
            echo sprintf(
                'Error from RetailCRM API (status code: %d): %s %s',
                $exception->getStatusCode(),
                $exception->getMessage(),
                $exception->getErrorResponse()->errors[0]->code
            );

        } catch (ClientExceptionInterface $exception) {
            $this->logger->info(sprintf('Order error %s', $exception->getErrorResponse()->errors[0]->code));
            echo $exception; // Every ApiExceptionInterface instance should implement __toString() method.
            exit(-1);
        }
        //$request->order->
    }
}