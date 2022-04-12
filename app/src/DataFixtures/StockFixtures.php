<?php

namespace App\DataFixtures;

use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StockFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $appleStock = new Stock('AAPL', 'USD', 15000, 'Apple Inc.');
        $manager->persist($appleStock);

        $teslaStock = new Stock('TSLA', 'USD', 19000, 'Tesla Inc.');
        $manager->persist($teslaStock);

        $shopifyStock = new Stock('SHOP', 'USD', 11500, 'Shopify Inc.');
        $manager->persist($shopifyStock);

        $amazonStock = new Stock('AMZN', 'USD', 17000, 'Amazon.com Inc.');
        $manager->persist($amazonStock);

        $manager->flush();
    }
}
