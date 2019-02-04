<?php

namespace src;

use src\DataProvider\AwesomeDataProvider;
use src\Exception\DataProviderException;

class Client {

    public function getProviderData (array $request, DataProviderInterface $provider) {
        try {
            return $provider->get($request);
        } catch (DataProviderException $e) {
            // For instance: because provider is unavailable
            // For example, add corresponding message to log
            //...
        }
    }

    public static function main() {

        $client = new Client();

        // Use provider without cache
        $provider = new AwesomeDataProvider('example-host.com', 'username', 'password');

        $response = $client->getProviderData(['some' => 'request'], $provider);

    }
}