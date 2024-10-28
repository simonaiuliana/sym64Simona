<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $title = 'Home';

        // Fetch the latest articles
        $articles = $this->entityManager->getRepository(Article::class)->findBy([], ['article_data_create' => 'DESC'], 10);

        // Fetch all sections
        $sections = $this->entityManager->getRepository(Section::class)->findAll();

        return $this->render('home.html.twig', [
            'title' => $title,
            'articles' => $articles,
            'sections' => $sections // Now sections is properly defined
        ]);
    }

    #[Route('/article/{id}', name: 'article_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/section/{slug}', name: 'section_show')]
    public function section(Section $section): Response
    {
        $title = 'Section';

        // Fetch articles for the specific section
        $articles = $this->entityManager->getRepository(Article::class)->findBy(['section' => $section]);

        return $this->render('section/show.html.twig', [
            'title' => $title,
            'articles' => $articles,
            'section' => $section, // Pass the section to the view if needed
        ]);
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function adminDashboard(AuthenticationUtils $authenticationUtils): Response
    {
        $title = 'Log In';
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'title' => $title,
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
