<?php

namespace App\Service;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoriesManagement {
    /**
     * @var EntityManagerInterface
     */
    private $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    public function getAll() {
        $categoriesAll =$this->em->getRepository(Category::class)->findAll();
        return $categoriesAll;
    }
}