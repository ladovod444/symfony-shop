<?php

namespace App\Service\RetailCrm;

use RetailCrm\Api\Client;

trait Helper
{
    const CATALOG_ID = 2;

    private $catalogMap = [
        'wallets' => 21,
        'nessesers' => 22,
        'covers' => 23,
    ];

}