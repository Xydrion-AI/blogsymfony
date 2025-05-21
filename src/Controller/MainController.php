<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(ArticlesRepository $articlesRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $findPagination = $articlesRepository->LimiteAticlesQuery(); //crée un dql qui met des parametre predefinit d'affichage.
        
        $pagination = $paginator->paginate(
            $findPagination,
            $request->query->getInt('page', 1),
            10,

        );

        return $this->render('main/index.html.twig', [
            'articles' => $pagination ,
        ]);

        
    }
}
