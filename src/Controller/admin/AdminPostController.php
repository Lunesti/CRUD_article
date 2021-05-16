<?php

namespace App\Controller\admin;

use  App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPostController extends AbstractController
{

    public function __construct(PostRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/post/create", name="admin.post.create")
     */
    public function new(Request $request): Response
    {   
        $posts = new Post();

        $form = $this->createForm(PostType::class, $posts);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($posts);
            $this->em->flush();
            $this->addFlash('success', 'Article ajouté avec succès');

            return $this->redirectToRoute('posts');
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * edit
     * @Route("/admin/post/{id}", name="admin.post.edit")
     * @param  mixed $post
     * @param  mixed $request
     * @return Response
     */
    public function edit(Post $post, Request $request): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Article modifié avec succès');
            return $this->redirectToRoute('posts');
        }

        return $this->render("admin/edit.html.twig", [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }
}
