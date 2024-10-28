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
    #[IsGranted(('ROLE_ADMIN'))]
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

    // Add methods for edit, update, and delete
}
