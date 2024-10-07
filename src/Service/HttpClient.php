<?php

namespace App\Service;

use GuzzleHttp\Client;

class HttpClient
{
  public function get(string $url): string {
    $client = new Client([
      // You can set any number of default request options.
      'timeout' => 25.0,
    ]);

    $response = $client->get($url);

    return $response->getBody()->getContents();
  }
}