<?php

namespace App\Controller;

use App\Entity\Section;
use App\Form\SectionType;
use App\Repository\SectionRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/crud/section')]
final class CrudSectionController extends AbstractController
{
    #[Route(name: 'app_crud_section_index', methods: ['GET'])]
    public function index(SectionRepository $sectionRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !in_array("ROLE_ADMIN", $user->getRoles()))return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        return $this->render('crud_section/index.html.twig', [
            'sections' => $sectionRepository->findAll(),
            'user' => $user,
        ]);
    }

    #[Route('/new', name: 'app_crud_section_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SectionRepository $sectionRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !in_array("ROLE_ADMIN", $user->getRoles()))return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $section->setSectionSlug($slugify->slugify($section->getSectionTitle()));
            $entityManager->persist($section);
            $entityManager->flush();

            return $this->redirectToRoute('app_crud_section_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('crud_section/new.html.twig', [
            'sections' => $sectionRepository->findAll(),
            'section' => $section,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{slug}', name: 'app_crud_section_show', methods: ['GET'])]
    public function show(string $slug, SectionRepository $sectionRepository): Response
    {
        $user = $this->getUser();
        $section = $sectionRepository->getSectionBySlug($slug);
        if (!$user || !in_array("ROLE_ADMIN", $user->getRoles()))return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        return $this->render('crud_section/show.html.twig', [
            'sections' => $sectionRepository->findAll(),
            'section' => $section,
            'user' => $user,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_crud_section_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, string $slug, EntityManagerInterface $entityManager, SectionRepository $sectionRepository): Response
    {
        $user = $this->getUser();
        $section = $sectionRepository->getSectionBySlug($slug);
        if (!$user || !in_array("ROLE_ADMIN", $user->getRoles()))return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $section->setSectionSlug($slugify->slugify($section->getSectionTitle()));
            $entityManager->persist($section);
            $entityManager->flush();

            return $this->redirectToRoute('app_crud_section_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('crud_section/edit.html.twig', [
            'sections' => $sectionRepository->findAll(),
            'section' => $section,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_crud_section_delete', methods: ['POST'])]
    public function delete(Request $request, Section $section, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user || !in_array("ROLE_ADMIN", $user->getRoles()))return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        if ($this->isCsrfTokenValid('delete'.$section->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($section);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_crud_section_index', [], Response::HTTP_SEE_OTHER);
    }
}
