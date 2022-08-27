<?php

namespace App\Controller;

use App\Form\CommentType;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DefaultController
 *
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * List All Posts with pagination.
     *
     * @param \App\Repository\PostRepository $postRepository
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(PostRepository $postRepository, Request $request): Response
    {
        return $this->listPosts($postRepository, $request);
    }

    /**
     * List All Posts by Category slug with pagination.
     *
     * @param \App\Repository\PostRepository $postRepository
     * @param string $slug
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/category/{slug}', name: 'category_posts', requirements: ['slug' => '[a-zA-Z0-9-]+'], methods: ['GET'])]
    public function byCategory(PostRepository $postRepository, string $slug, Request $request): Response
    {
        return $this->listPosts($postRepository, $request, ['category' => $slug]);
    }

    /**
     * List All Posts by Author username with pagination.
     *
     * @param \App\Repository\PostRepository $postRepository
     * @param string $username
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/author/{username}', name: 'author_posts', requirements: ['slug' => '[a-zA-Z0-9-]+'], methods: ['GET'])]
    public function byAuthor(PostRepository $postRepository, string $username, Request $request): Response
    {
        return $this->listPosts($postRepository, $request, ['author' => $username]);
    }

    /**
     * @param \App\Repository\PostRepository $postRepository
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $filters
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function listPosts(PostRepository $postRepository, Request $request, array $filters = []): Response
    {
        $page = (int)$request->get('page') ?: 1;

        $posts = $postRepository->paginate($page, 10, $filters);

        return $this->render('default/index.html.twig', compact('posts'));
    }

    /**
     * @param string $slug
     * @param \App\Repository\PostRepository $postRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/post/{slug}', name: 'post_show', requirements: ['slug' => '[a-zA-Z0-9-]+'], methods: ['GET'])]
    public function show(string $slug, PostRepository $postRepository): Response
    {
        $post = $postRepository->findBySlug($slug);

        $form = $this->createForm(CommentType::class)->createView();

        return $this->render('default/show.html.twig', compact('post', 'form'));
    }
}
