<?php

namespace src;

use src\Exception\DataProviderException;

interface DataProviderInterface {

    /**
     * @param array $request
     * @return mixed
     * @throws DataProviderException
     */
    public function get(array $request);
}