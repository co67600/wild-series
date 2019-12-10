<?php
namespace App\DataFixtures;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
class CategoryFixtures extends Fixture
{
    CONST CATEGORIES = [
        'Horreur',
        'Action',
        'Aventure',
        'Thriller',
        'Animation',
        'Fantastique'
    ];
    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORIES as $key => $categoryname) {
            $category = new Category();
            $category->setName($categoryname);
            $this->addReference('categorie_' . $key, $category);
            $manager->persist($category);
        }
        $manager->flush();
    }
}