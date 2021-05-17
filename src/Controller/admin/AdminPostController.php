<?php

namespace App\Controller\admin;

use  App\Entity\Post;
use App\Form\PostType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
    public function create(Request $request): Response
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
     * post
     * @Route("/posts/{slug}-{id}", name="post.show", requirements = {"slug": "[a-z0-9\-]*"})
     * @param  Post $post
     * @return Response
     */
    public function read(Post $post, string $slug, Request $request): Response
    {
        // Si notre méthode $post->getSlug (notre getter) est different du slug passé en paramètre à notre url, on redirige sur post.show avec en paramètre l'id et le slug. le slug vaut notre title.
        if($post->getSlug() !== $slug) {
            return $this->redirectToRoute('post.show', [
                'id' => $post->getId(),
                'slug' => $post->getSlug()
            ], 301);
        }

        if(!$post){
            // Si aucun article n'est trouvé, nous créons une exception
            throw $this->createNotFoundException('L\'article n\'existe pas');
        }

        $comment = new Comment();

        /*$comments = $this->getDoctrine()->getRepository(Comment::class)->findBy([
            'comment' => $comment->getComment(),
            'author' => $comment->getAuthor(),
        ]);*/

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($comment);
            $this->em->flush();
            $this->addFlash('success', 'Le commentaire à bien été envoyé.');
        }

        return $this->render('frontend/post.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            //'comment' => $comments
        ]);
    }

    
    /**
     * edit
     * @Route("/admin/post/{id}", name="admin.post.edit", methods="GET|POST")
     * @param  mixed $post
     * @param  mixed $request
     * @return Response
     */
    public function update(Post $post, Request $request): Response
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
    
    /**
     * delete
     * @Route("/admin/post/{id}", name="admin.post.delete", methods="DELETE")
     * @param  Post $post
     */
    public function delete(Post $post, Request $request): Response {
        $submittedToken = $request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $submittedToken)) {
            $this->em->remove($post);
            $this->em->flush();
            $this->addFlash('success', 'Bien supprimé avec succès');
        }
        return $this->redirectToRoute('posts');

    }
}
