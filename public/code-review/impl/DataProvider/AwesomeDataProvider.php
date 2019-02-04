<?php

namespace src\DataProvider;

use src\DataProviderInterface;
use src\Exception\DataProviderException;

class AwesomeDataProvider implements DataProviderInterface
{
    private $host;
    private $user;
    private $password;

    public function __construct($host, $user, $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    public function get(array $request)
    {
        // Remote requests

        $response = 'response from remote';
        $something = mt_rand(0,1);
        if ($something) {
            throw new DataProviderException('Something wrong');
        }

        return $response;
    }
}