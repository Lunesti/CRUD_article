<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPostsController extends AbstractController
{
    /**
     * @Route("/admin/posts", name="admin_posts")
     */
    public function index(): Response
    {
        return $this->render('admin_posts/index.html.twig', [
            'controller_name' => 'AdminPostsController',
        ]);
    }
}
