<?php

namespace App\Controller;

use App\Entity\Section;
use App\Form\SectionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sections')]
class SectionsController extends AbstractController
{
    #[Route('/', name: 'section_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $sections = $em->getRepository(Section::class)->findAll();
        return $this->render('section/index.html.twig', ['sections' => $sections]);
    }

    #[Route('/new', name: 'section_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($section);
            $em->flush();
            return $this->redirectToRoute('section_index');
        }

        return $this->render('section/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'section_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, EntityManagerInterface $em, Section $section): Response
    {
        $form = $this->createForm(SectionType::class, $section);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // Just update the existing entity
            return $this->redirectToRoute('section_index');
        }

        return $this->render('section/edit.html.twig', ['form' => $form->createView(), 'section' => $section]);
    }

    #[Route('/{slug}', name: 'section_show')]
    public function show(EntityManagerInterface $em, string $slug): Response
    {
        $section = $em->getRepository(Section::class)->findOneBy(['section_slug' => $slug]);

        if (!$section) {
            throw $this->createNotFoundException('Section pas trouve');
        }

        return $this->render('section/show.html.twig', [
            'section' => $section,
            // You can also add associated articles if needed
        ]);
    }

    #[Route('/{id}', name: 'section_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, EntityManagerInterface $em, Section $section): Response
    {
        if ($this->isCsrfTokenValid('delete' . $section->getId(), $request->request->get('_token'))) {
            $em->remove($section);
            $em->flush();
        }

        return $this->redirectToRoute('section_index');
    }
}
