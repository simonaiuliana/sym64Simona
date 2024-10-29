<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\EditCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SectionRepository;

#[Route('/comment')]
final class CommentController extends AbstractController
{
    #[Route(name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository, SectionRepository $SectionRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !in_array("ROLE_ADMIN", $user->getRoles()) && !in_array("ROLE_REDAC", $user->getRoles())){
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        $sections = $SectionRepository->findAll();
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
            'sections' => $sections,
            'user' => $user,
        ]);
    }

    #[Route('/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SectionRepository $SectionRepository): Response
    {
        $user = $this->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        $comment = new Comment();
        $form = $this->createForm(EditCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        $sections = $SectionRepository->findAll();
        return $this->render('comment/new.html.twig', [
            'sections' => $sections,
            'comment' => $comment,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment, SectionRepository $SectionRepository): Response
    {
        $user = $this->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        $sections = $SectionRepository->findAll();
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
            'sections' => $sections,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager, SectionRepository $SectionRepository): Response
    {
        $user = $this->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(EditCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        $sections = $SectionRepository->findAll();
        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'sections' => $sections,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()){
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
