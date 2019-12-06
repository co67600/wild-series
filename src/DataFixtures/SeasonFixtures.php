<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker  =  Faker\Factory::create('en_US');
        for ($i = 0; $i <= 50; $i++) {
            $season = new Season();
            $season->setDescription('Season : '.$faker->sentence);
            $season->setYear($faker->year);
            $season->setNumber($faker->numberBetween(1, 10));
            $this->addReference('season_' . $i, $season);
            $season->setProgram($this->getReference('program_'.rand(0, 5)));
            $manager->persist($season);
        }
        $manager->flush();

    }

    public function getDependencies()

    {
        return [ProgramFixtures::class];

    }


}