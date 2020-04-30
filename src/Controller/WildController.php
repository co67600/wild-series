<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Actor;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Support;
use App\Form\CategoryType;
use App\Form\ActorType;
use App\Form\CommentType;
use App\Form\ProgramSearchType;
use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SupportRepository;
use App\Service\Slugify;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpParser\Node\Expr\New_;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;




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
     *     name="wild_index", methods="GET|POST")
     * @return Response A response instance
     */
    public function index(Request $request): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this ->createNotFoundException(
                'No program found in program\'s table.'
            );
        };

        $form = $this->createForm(
            ProgramSearchType::class,
            null,
            ['method' => Request::METHOD_GET]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            // $data contains $_POST data
            // TODO : Faire une recherche dans la BDD avec les infos de $data…
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs,
                'form' => $form->createView(),
                //'category' => $category,
                ]
        );
    }

    /**
     * @Route("/category_navbar", name="category_navbar", methods={"GET"})
     */
    public function indexNavbar(CategoryRepository $categoryRepository): Response
    {
        return $this->render('wild/_category.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @param string $slug
     * @Route("/wild/show/{slug}",
     *     defaults={"slug"="Aucune série sélectionnée, veuillez choisir une série"},
     *     name="wild_show")
     * @return Response
     */
    public function show(string $slug): Response
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

    public function showEpisode(Episode $episode, Request $request, EntityManagerInterface $em ): Response
    {
        $comment = New Comment();
        $user = $this->getUser();
        $season = $episode->getSeason();
        $program = $season->getProgram();
        if (!$user) {
            $form = $this
                ->createForm(CommentType::class)
                ->remove('comment')
                ->remove('rate');
            return $this->render("wild/episode.html.twig", [
                'episode' => $episode,
                'season' => $season,
                'program'=> $program,
                'form' => $form->createView(),
            ]);
        }
        $form = $this
            ->createForm(CommentType::class, $comment)
            ->add('poster', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthorId($user->getId());
            $comment->setEpisode($episode);
            $program = $season->getProgram();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('wild_episode',['id' => $episode->getId()]);
        }
        $comment = $episode->getComments();
        return $this->render("wild/episode.html.twig", [
            'episode' => $episode,
            'season' => $season,
            'comments' => $comment,
            'program'=> $program,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/wild/actor/{id}", name="show_actor")
     * @param Actor $actor
     * @return Response
     */

    public function showActor(Actor $actor): Response
    {
        return $this->render('wild/showActor.html.twig', [
                'actor' => $actor,
            ]
        );
    }

    /**
     * @Route("/recherche", name="search")
     */
    public function recherche(Request $request, ProgramRepository $repo, PaginatorInterface $paginator) {

        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);

        $donnees = $repo->findAll();

        if ($searchForm->isSubmitted()) {

            $title = $searchForm->getData()->getTitle();
            $donnees = $repo->search($title);


            if ($donnees == null) {
                $this->addFlash('erreur', 'Aucune série contenant ce mot clé dans le titre n\'a été trouvée, essayez en une autre.');

            }

        }

        // Paginate the results of the query
        $programs = $paginator->paginate(
        // Doctrine Query, not results
            $donnees,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );

        return $this->render('wild/search.html.twig', [
            'programs' => $programs,
            'searchForm' => $searchForm->createView()
        ]);

    }

    /**
     * @Route("program/{slug}/favoris", name="program_show_favoris")
     * @param Program $program
     * @param SupportRepository $supProRepo
     * @return RedirectResponse
     */
    public function modifyToFavoris(Program $program, SupportRepository $supProRepo)
    {
        $user = $this->getUser();

        $favoris = $supProRepo -> findOneBy(['program'=>$program, 'user'=>$user, 'support'=>2]);
        $entityManager = $this->getDoctrine()->getManager();

        if (!$favoris) {
            $supportProgram = new Support;
            $supportProgram->setProgram($program);
            $supportProgram->setUser($user);
            $supportProgram->setSupport(2);

            $entityManager->persist($supportProgram);
            $entityManager->flush();
            $this->addFlash('success', 'Série ajoutée à vos favoris');
        } else {
            $entityManager->remove($favoris);
            $entityManager->flush();
            $this->addFlash('warning', 'Série supprimée de vos favoris');
        }

        return $this->redirectToRoute('wild_show', ['slug'=>$program->getSlug()]);
    }

    /**
     * @Route("/support_navbar", name="support_navbar", methods={"GET"})
     */
    public function supportNavbar(SupportRepository $supportRepository): Response
    {
        return $this->render('wild/_support.html.twig', [
            'favoris' => $supportRepository->findBy(),
        ]);
    }

}
