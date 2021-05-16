<?php

namespace App\Controller\frontend;

use  App\Entity\Post;
use Twig\Environment;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PostController extends AbstractController
{

    public function __construct(Environment $twig, PostRepository $repository) {
        $this->twig = $twig;
        $this->repository = $repository;
    }
    
    /**
     * index
     * @Route("/posts", name="posts")
     * @return Response
     */
    public function index(): Response
    {
        $posts = $this->repository->findAll();
        return $this->render('frontend/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
