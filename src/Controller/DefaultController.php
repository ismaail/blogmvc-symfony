<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\CommentType;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    /**
     * List All Posts with pagination.
     */
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->listPosts($request);
    }

    /**
     * List All Posts by Category slug with pagination.
     */
    #[Route('/category/{slug}', name: 'category_posts', requirements: ['slug' => '[a-zA-Z0-9-]+'], methods: ['GET'])]
    public function byCategory(string $slug, Request $request): Response
    {
        return $this->listPosts($request, ['category' => $slug]);
    }

    /**
     * List All Posts by Author username with pagination.
     */
    #[Route('/author/{username}', name: 'author_posts', requirements: ['slug' => '[a-zA-Z0-9-]+'], methods: ['GET'])]
    public function byAuthor(string $username, Request $request): Response
    {
        return $this->listPosts($request, ['author' => $username]);
    }

    private function listPosts(Request $request, array $filters = []): Response
    {
        $page = (int)$request->get('page') ?: 1;

        $posts = $this->postRepository->paginate($page, 10, $filters);

        return $this->render('default/index.html.twig', compact('posts'));
    }

    /**
     * Show single Post by Slug.
     */
    #[Route('/post/{slug}', name: 'post_show', requirements: ['slug' => '[a-zA-Z0-9-]+'], methods: ['GET'])]
    public function show(string $slug): Response
    {
        $post = $this->postRepository->findBySlug($slug);

        $form = $this->createForm(CommentType::class)->createView();

        return $this->render('default/show.html.twig', compact('post', 'form'));
    }
}
