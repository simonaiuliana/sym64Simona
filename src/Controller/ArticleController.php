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
use App\Entity\User;

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
        $articles = $this->entityManager->getRepository(Article::class)->findBy([], ['article_date_create' => 'DESC'], 10);
        $sections = $this->entityManager->getRepository(Section::class)->findAll();

        return $this->render('home.html.twig', [
            'title' => $title,
            'articles' => $articles,
            'sections' => $sections,
        ]);
    }

    #[Route('/article/{slug}', name: 'article_show')]
    public function show(Article $article): Response
    {
        $sections = $this->entityManager->getRepository(Section::class)->findAll();
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'sections' => $sections,
        ]);
    }

    #[Route('/section/{slug}', name: 'section_show')]
    public function section(Section $section): Response
    {
        $title = 'Section';
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
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in to create an article.');
        }

        // Asociază utilizatorul cu articolul
        $article->setUser($user);
        $article->setArticleDateCreate(new \DateTime());
        $article->setPublished(0);

        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setPublished($form->get('published')->getData() ? 1 : 0);
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            return $this->redirectToRoute('article_show', ['slug' => $article->getTitleSlug()]);
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_REDAC')]
    #[Route('/admin/article/edit/{slug}', name: 'article_edit')]
    public function edit(Request $request, Article $article): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User || $article->getUser() !== $user) {
            throw $this->createAccessDeniedException('You cannot edit this article.');
        }

        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setPublished($form->get('published')->getData() ? 1 : 0);
            $this->entityManager->flush();
            return $this->redirectToRoute('article_show', ['slug' => $article->getTitleSlug()]);
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[IsGranted('ROLE_REDAC')]
    #[Route('/admin/article/delete/{slug}', name: 'article_delete')]
    public function delete(Article $article): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User || $article->getUser() !== $user) {
            throw $this->createAccessDeniedException('You cannot delete this article.');
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return $this->redirectToRoute('homepage');
    }
}
