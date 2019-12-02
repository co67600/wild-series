<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Category;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Tests\Compiler\E;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class WildController extends AbstractController
{

    public function showByProgram($program) {
        return $program->getSeasons();
    }
    public function showBySeason($season) {
        return $season->getEpisodes();
    }
    public function getAllPrograms() {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        return $programs;
    }
    public function getAllCategories(){
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        return $categories;
    }




    /**
     * @Route("/wild/",
     *     name="wild_index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this ->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * @param string $slug The slugger
     * @Route("/wild/show/{slug}",
     *     requirements={"slug"="[a-z0-9-]+"},
     *     defaults={"slug"="Aucune série sélectionnée, veuillez choisir une série"},
     *     name="wild_show")
     * @return Response
     */
    public function show(?string $slug): Response
    {
        if (!$slug){
            throw $this
            ->createNotFoundException('No slug has been sent to find a program in program\'s table .');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }
        $seasons = $this->showByProgram($program);
        $programs = $this->getAllPrograms();
        $categories =$this->getAllCategories();


        return $this->render('wild/show.html.twig',
            ['program' => $program,
                'slug'  => $slug,
                'seasons' => $seasons,
                'programs' => $programs,
                'categories' => $categories
                ]);
    }

    /**
     * @param string $categoryName
     * @Route("/wild/category/{categoryName}",
     *     requirements={"category"="[a-z0-9-]+"},
     *     name="show_category")
     * @return Response
     */
    public function showByCategory(string $categoryName) : Response
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find a program in program\'s table.');
        }

        $category = $this ->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name'=>$categoryName]);

        $program =  $this ->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category'=>$category->getId()], ['id'=>'DESC'], 3);

            return $this->render('wild/category.html.twig',
                ['category' => $categoryName,
                    'programs' => $program,
                ]);

    }
    /**
     * @Route("/wild/season/{id}-{season}", name="wild_season")
     * @param Season $season
     * @param Program $id
     * @return Response
     */
    public function season(Program $id, Season $season):Response {

        $episodes = $this->showBySeason($season);
        $programs = $this->getAllPrograms();
        $categories =$this->getAllCategories();

        return $this->render('wild/season.html.twig', ['episodes' => $episodes,
            'program' => $id, 'programs' => $programs,'categories' => $categories]);
    }


    /**
     * @Route("/wild/episode/{id}", name="wild_episode")
     * @param Episode $episode
     * @return Response
     */

    public function showEpisode(Episode $episode): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();


        return $this->render('wild/episode.html.twig', [
                'episode' => $episode,
                'season' => $season,
                'program'=> $program,
            ]
        );
    }




    /**
     * @Route("/wild/delete/{id}", name="wild_delete", methods={"DELETE"}))
     */
    public function delete($id): Response
    {
        return $this->render('wild/delete.html.twig', ['id' => $id]);
    }


}