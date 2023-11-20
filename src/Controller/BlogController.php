<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class BlogController extends AbstractController
{
    public function __construct(
        private PostRepository $repository
    ) {
    }

    #[Route('/blog', name: 'app_blog')]
    public function index(): Response
    {
        $posts = $this->repository->findAll();
        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/blog/post/{postId}', name: 'app_post')]
    public function showPost(int $postId): Response
    {
        $post = $this->repository->findOneBy(['id' => $postId]);
        return $this->render('blog/post.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/blog/new', name: 'app_new')]
    public function newPost(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostFormType::class, new Post());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $newPost */
            $newPost = $form->getData();

            $entityManager->persist($newPost);
            $entityManager->flush();

            return $this->redirectToRoute("app_blog");
        }

        return $this->render('blog/new.html.twig', [
            'form' => $form,
        ]);
    }
}
