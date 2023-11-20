<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $repository
    ) {
    }

    #[Route('/blog', name: 'app_blog')]
    public function index(): Response
    {
        $posts = $this->repository->findAllOrderedByDate();
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

    #[Route('/blog/post/{postId}/edit', name: 'app_edit')]
    public function editPost(int $postId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = $this->repository->findOneBy(['id' => $postId]);
        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $editedPost */
            $postForm = $form->getData();

            $post->setTitle($postForm->getTitle());
            $post->setContent($postForm->getContent());
            $post->setImage($postForm->getImage());
            $post->setImageFile($postForm->getImageFile());
            $post->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute("app_blog");
        }

        return $this->render('blog/edit.html.twig', [
            'form' => $form,
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
            $newPost->setCreatedAt(new \DateTimeImmutable());
            $newPost->setUpdatedAt(null);
            $entityManager->persist($newPost);
            $entityManager->flush();

            return $this->redirectToRoute("app_blog");
        }

        return $this->render('blog/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/blog/post/{postId}/delete', name: 'app_delete')]
    public function deletePost(int $postId, EntityManagerInterface $entityManager): Response
    {
        $post = $this->repository->findOneBy(['id' => $postId]);
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->redirectToRoute("app_blog");
    }
}
