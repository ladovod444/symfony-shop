<?php

namespace App\Service\RetailCrm;

use App\Entity\User;
use Psr\Http\Client\ClientExceptionInterface;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Model\Entity\Customers\Customer;
use RetailCrm\Api\Model\Request\Customers\CustomersCreateRequest;

class CustomerManager extends Manager
{
    use Helper;

//    public function createCustomer($customers_data): string
    public function createCustomer(User $user): string
    {
        $request = new CustomersCreateRequest();
        $request->customer = new Customer();

        //$request->site = 'aliexpress';
        $request->customer->email = $user->getEmail();
        $request->customer->firstName = $user->getFirstName();
        $request->customer->lastName = $user->getLastName();

        try {
            $response = $this->client->customers->create($request);
        } catch (ApiExceptionInterface|ClientExceptionInterface $exception) {
            echo $exception; // Every ApiExceptionInterface instance should implement __toString() method.
            exit(-1);
        }

        return $response->id;
    }
}