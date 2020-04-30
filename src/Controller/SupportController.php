<?php

namespace App\Controller;

use App\Repository\SupportRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SupportController extends AbstractController
{

    /**
     * @Route("/support", name="support")
     * @param ProgramRepository $programRepository
     *
     * @return Response
     */
    public function recentProjects(
        ProgramRepository $pprogramRepository,
        SupportRepository $supportRepo
    ): Response {
        $user = $this->getUser();
        $favoris = $supportRepo->findBy(['user'=>$user, 'support'=>2]);

        return $this->render('wild/_support.html.twig', [
            'favoris' => $favoris,

        ]);
    }



}
