<?php

namespace App\Controller;

use App\Form\ArticleType;
use App\Entity\Article;
use App\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
        $articles = $this->entityManager->getRepository(Article::class)->findBy([], ['article_date_create' => 'DESC'], 10);
        $sections = $this->entityManager->getRepository(Section::class)->findAll();

        return $this->render('home.html.twig', [
            'title' => $title,
            'articles' => $articles,
            'sections' => $sections,
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
            'section' => $section,
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

    #[IsGranted('ROLE_REDAC')]
    #[Route('/admin/article/new', name: 'article_new')]
    public function new(Request $request): Response
    {
        $article = new Article();
        $article->setUserId($this->getUser()->getId());
        $article->setArticleDateCreate(new \DateTime()); // Set creation date
        $article->setPublished(0); // Default to unpublished

        // Create the form
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_REDAC')]
    #[Route('/admin/article/edit/{id}', name: 'article_edit')]
    public function edit(Request $request, Article $article): Response
    {
        // Ensure the user is the author of the article
        if ($article->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('You cannot edit this article.');
        }

        // Create the form
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[IsGranted('ROLE_REDAC')]
    #[Route('/admin/article/delete/{id}', name: 'article_delete')]
    public function delete(Article $article): Response
    {
        // Ensure the user is the author of the article
        if ($article->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('You cannot delete this article.');
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return $this->redirectToRoute('homepage');
    }
}
