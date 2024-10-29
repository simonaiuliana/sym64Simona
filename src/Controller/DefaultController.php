<?php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\Section;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(EntityManagerInterface $entityManager): Response
    {    $title = 'Home';
        // Preia secțiunile din baza de date
        $sections = $entityManager->getRepository(Section::class)->findAll();
        $articles = $entityManager->getRepository(Article::class)->findBy([], ['article_date_create' => 'DESC']);
        
        return $this->render('home.html.twig', [
            'title' => $title,
            'sections' => $sections,
            'articles' => $articles,
        ]);
    }

    
}
