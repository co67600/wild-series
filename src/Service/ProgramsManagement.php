<?php
namespace App\Service;
use App\Entity\Category;
use App\Entity\Program;
use Doctrine\ORM\EntityManagerInterface;

class ProgramsManagement {
    /**
     * @var EntityManagerInterface
     */
    private $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    public function getAll() {
        $programsAll =$this->em->getRepository(Program::class)->findAll();
        return $programsAll;
    }
}