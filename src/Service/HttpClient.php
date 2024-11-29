<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;


class HttpClient
{
    /**
     * @throws GuzzleException
     */
    public function get(string $url, ?string $api_key): string
    {
        $client = new Client([
            // You can set any number of default request options.
            'timeout' => 25.0,
            'headers' => [
                'Authorization' => $api_key,
            ],
        ]);

        $response = $client->request('GET', $url);
        return $response->getBody()->getContents();
    }
}
