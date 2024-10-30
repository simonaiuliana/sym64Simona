<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SectionRepository;

class CrudController extends AbstractController
{
    #[Route('/crud', name: 'crud')]
    public function index(SectionRepository $SectionRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !in_array("ROLE_ADMIN", $user->getRoles()) && !in_array("ROLE_REDAC", $user->getRoles()))return $this->redirectToRoute('app_login');
        if (in_array("ROLE_ADMIN", $user->getRoles())){
            $cruds = [
                ["app_comment_index", "Commentaires Crud"],
                ["app_crud_article_index", "Articles Crud"],
                ["app_crud_section_index", "Sections Crud"],
                ["app_crud_user_index", "Users Crud"],
            ];
        }else {
            $cruds = [
                ["app_crud_article_index", "Articles Crud"],
            ];
        }

        $sections = $SectionRepository->findAll();
        return $this->render('crud/index.html.twig', [
            'controller_name' => 'CrudController',
            'user' => $user,
            'sections' => $sections,
            'cruds' => $cruds,
        ]);
    }
}
