<?php

namespace App\Service;
use App\Entity\Actor;
use Doctrine\ORM\EntityManagerInterface;

class ActorsManagement {
    /**
     * @var EntityManagerInterface
     */
    private $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    public function getAll() {
        $actorsAll =$this->em->getRepository(Actor::class)->findAll();
        return $actorsAll;
    }
}