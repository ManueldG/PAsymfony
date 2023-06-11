<?php

namespace App\Controller;

use App\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_page')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/post/{id}', name: 'post')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $photo = $entityManager->getRepository(Photo::class)->find($id);

        if (!$photo) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        

        return $this->render('page/detail.html.twig', ['photo' => $photo]);
        
    }
}
