<?php

namespace App\Service;

use GuzzleHttp\Client;

class HttpClient
{
  public function get(string $url, ?string $api_key ): string {
    $client = new Client([
      // You can set any number of default request options.
      'timeout' => 25.0,
      'headers' => [
        'Authorization' => $api_key,
      ]
    ]);
    
    $response = $client->get($url);

    return $response->getBody()->getContents();
  }
}