<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ArticleRepository;
use App\Repository\SectionRepository;

class PublicController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ArticleRepository $ArticleRepository, SectionRepository $SectionRepository): Response
    {
        $user = $this->getUser();
        $articles = $ArticleRepository->findTenLastPublished();
        $sections = $SectionRepository->findAll();
        return $this->render('public/index.html.twig', [
            'controller_name' => 'PublicController',
            'articles' => $articles,
            'sections' => $sections,
            'user' => $user,
        ]);
    }
}
