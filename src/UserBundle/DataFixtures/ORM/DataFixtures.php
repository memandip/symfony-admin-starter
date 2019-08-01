<?php
namespace UserBundle\DataFixtures\ORM;

use UserBundle\Entity\TestUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class DataFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {
            $product = new TestUser();
            $product->setName('product '.$i);
            $product->setAdress('address '.$i);
            $product->setNumber(mt_rand(10, 100));
            $manager->persist($product);
        }

        $manager->flush();
    }
}