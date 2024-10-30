<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\SectionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthorController extends AbstractController
{
    #[Route('/author/{uniqid}', name: 'app_author')]
    public function index(string $uniqid, UserRepository $UserRepository, SectionRepository $SectionRepository, ArticleRepository $ArticleRepository): Response
    {
        $user = $this->getUser();
        $author = $UserRepository->GetUserByUniqid($uniqid);
        $sections = $SectionRepository->findAll();
        $articles = $ArticleRepository->findAllByAuthor($author->getId());
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
            'user' => $user,
            'author' => $author,
            'sections' => $sections,
            'articles' => $articles,
        ]);
    }
}
