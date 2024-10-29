<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\NewArticleType;
use App\Form\ArticleType;
use App\Form\RedacArticleType;
use App\Repository\ArticleRepository;
use App\Repository\SectionRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/crud/article')]
final class CrudArticleController extends AbstractController
{
    #[Route(name: 'app_crud_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, SectionRepository $SectionRepository): Response
    {
        $user = $this->getUser();
        if ($user && in_array("ROLE_ADMIN", $user->getRoles())){
            $articles = $articleRepository->findAll();
        }else if ($user && in_array("ROLE_REDAC", $user->getRoles())){
            $articles = $articleRepository->findAllByAuthor($user->getId());
        }else {
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        $sections = $SectionRepository->findAll();
        return $this->render('crud_article/index.html.twig', [
            'sections' => $sections,
            'articles' => $articles,
            'user' => $user,
        ]);
    }

    #[Route('/new', name: 'app_crud_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SectionRepository $SectionRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !in_array("ROLE_ADMIN", $user->getRoles()) && !in_array("ROLE_REDAC", $user->getRoles())){
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        $article = new Article();
        $form = $this->createForm(NewArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $article->setUser($this->getUser());
            $article->setTitleSlug($slugify->slugify($article->getTitle()));

            $is_going_to_be_published = $form->getData()->getPublished();
            if ($is_going_to_be_published){
                $article->setArticleDatePosted(new \DateTime());
            }

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_crud_article_index', [], Response::HTTP_SEE_OTHER);
        }

        $sections = $SectionRepository->findAll();
        return $this->render('crud_article/new.html.twig', [
            'sections' => $sections,
            'article' => $article,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{slug}', name: 'app_crud_article_show', methods: ['GET'])]
    public function show(string $slug, ArticleRepository $ArticleRepository, SectionRepository $SectionRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !in_array("ROLE_ADMIN", $user->getRoles()) && !in_array("ROLE_REDAC", $user->getRoles())){
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        $article = $ArticleRepository->getArticleBySlug($slug);
        if (!in_array("ROLE_ADMIN", $user->getRoles()) && $user->getId() != $article->getUser()->getId()){
            return $this->redirectToRoute('app_crud_article_index', [], Response::HTTP_SEE_OTHER);
        }
        $sections = $SectionRepository->findAll();
        return $this->render('crud_article/show.html.twig', [
            'sections' => $sections,
            'article' => $article,
            'user' => $user,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_crud_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, string $slug, ArticleRepository $ArticleRepository, EntityManagerInterface $entityManager, SectionRepository $SectionRepository): Response
    {
        $user = $this->getUser();
        // if not user or neither redac nor admin
        if (!$user || !in_array("ROLE_ADMIN", $user->getRoles()) && !in_array("ROLE_REDAC", $user->getRoles())){
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        $article = $ArticleRepository->getArticleBySlug($slug);
        $was_published = $article->getPublished();
        if (!in_array("ROLE_ADMIN", $user->getRoles()) && $user->getId() != $article->getUser()->getId()){
            return $this->redirectToRoute('app_crud_article_index', [], Response::HTTP_SEE_OTHER);
        }
        if (in_array("ROLE_ADMIN", $user->getRoles())){
            $form = $this->createForm(ArticleType::class, $article);
        }else {
            $form = $this->createForm(RedacArticleType::class, $article);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $is_going_to_be_published = $form->getData()->getPublished();
            if (!$was_published && $is_going_to_be_published){
                $article->setArticleDatePosted(new \DateTime());
            }else if ($was_published && !$is_going_to_be_published) {
                $article->setArticleDatePosted(null);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_crud_article_index', [], Response::HTTP_SEE_OTHER);
        }

        $sections = $SectionRepository->findAll();
        return $this->render('crud_article/edit.html.twig', [
            'article' => $article,
            'sections' => $sections,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_crud_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user || !in_array("ROLE_ADMIN", $user->getRoles()) && !in_array("ROLE_REDAC", $user->getRoles())){
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        if (!in_array("ROLE_ADMIN", $user->getRoles()) && $user->getId() != $article->getUser()->getId()){
            return $this->redirectToRoute('app_crud_article_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_crud_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
