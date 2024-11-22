<?php

namespace App\Service\RetailCrm;

use Psr\Http\Client\ClientExceptionInterface;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Model\Entity\Customers\Customer;
use RetailCrm\Api\Model\Request\Customers\CustomersCreateRequest;

class CustomerManager extends Manager
{
    use Helper;

    public function createCustomer($customers_data): string
    {
        $request = new CustomersCreateRequest();
        $request->customer = new Customer();

        //$request->site = 'aliexpress';
        $request->customer->email = $customers_data['email'] ;
        $request->customer->firstName = $customers_data['first_name'];
        $request->customer->lastName = $customers_data['last_name'] ;

        try {
            $response = $this->client->customers->create($request);
        } catch (ApiExceptionInterface|ClientExceptionInterface $exception) {
            echo $exception; // Every ApiExceptionInterface instance should implement __toString() method.
            exit(-1);
        }

        return $response->id;
    }
}