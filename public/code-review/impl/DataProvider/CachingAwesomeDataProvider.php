<?php

namespace src\DataProvider;

use src\DataProviderInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class CachingAwesomeDataProvider implements DataProviderInterface
{
    private $provider;
    private $logger;
    private $cache;

    public function __construct(
        AwesomeDataProvider $provider,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        $this->provider = $provider;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    private function getCacheKey(array $request)
    {
        return json_encode($request);
    }


    public function get(array $request)
    {
        $cacheKey = $this->getCacheKey($request);
        try {

            $cacheItem = $this->cache->getItem($cacheKey);

            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

        } catch (\Exception $e) {
            $this->logger->critical('Caching provider problem: ' . $e->getMessage());
        }

        $result = $this->provider->get($request);

        try {
            $cacheItem->set($result)->expiresAt((new \DateTime())->modify('+1 day'));
        } catch (\Exception $e) {
            $this->logger->critical('Caching provider problem: ' + $e->getMessage());
        }

        return $result;
    }
}