<?php

namespace App\Controller;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class StockController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private CacheInterface $memcachedAdapter;

    private CacheInterface $redisAdapter;

    public function __construct(
        EntityManagerInterface $entityManager,
        CacheInterface $memcachedAdapter,
        CacheInterface $redisAdapter
    ) {
        $this->entityManager = $entityManager;
        $this->memcachedAdapter = $memcachedAdapter;
        $this->redisAdapter = $redisAdapter;
    }

    /**
     * @Route(path="/stock/memcached/{symbol}", name="redis-stock-by-symbol")
     */
    public function getFromMemcachedBySymbol(string $symbol): Response
    {
        $symbol = strtoupper($symbol);

        $entityManager = $this->entityManager;

        /** @var Stock $stock */
        $stock = $this->memcachedAdapter->get($symbol, function (ItemInterface $item) use ($symbol, $entityManager) {
            echo 'Cache miss for ' . $symbol . ' in memcached!<br>';
            return $entityManager->getRepository(Stock::class)->findOneBy(['symbol' => $symbol]);
        });

        if ($stock === null) {
            return new Response("Stock \"{$symbol}\" was not found.");
        }

        return new Response("{$stock->getName()} has a current value of {$stock->getPrice()}");
    }

    /**
     * @Route(path="/stock/redis/{symbol}", name="memcached-stock-by-symbol")
     */
    public function getFromRedisBySymbol(string $symbol): Response
    {
        $symbol = strtoupper($symbol);

        $entityManager = $this->entityManager;

        /** @var Stock $stock */
        $stock = $this->redisAdapter->get($symbol, function (ItemInterface $item) use ($symbol, $entityManager) {
            echo 'Cache miss for ' . $symbol . ' in Redis!<br>';
            return $entityManager->getRepository(Stock::class)->findOneBy(['symbol' => $symbol]);
        });

        if ($stock === null) {
            return new Response("Stock \"{$symbol}\" was not found.");
        }

        return new Response("{$stock->getName()} has a current value of {$stock->getPrice()}");
    }
}