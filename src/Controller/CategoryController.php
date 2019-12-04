<?php

namespace App\Controller;


use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CategoryController extends AbstractController
{

    /**
     * @Route("/category/add", name="add_category", methods="GET|POST")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $em->persist($category);
            $em->flush();
        }
            return $this->render('category/index.html.twig', [
                'form' => $form->createView(),
            ]);
        }
}


