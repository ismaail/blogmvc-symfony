<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(PostRepository $postRepository, Request $request)
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
    public function byCategory(PostRepository $postRepository, string $slug, Request $request)
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
    public function byAuthor(PostRepository $postRepository, string $username, Request $request)
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
    private function listPosts(PostRepository $postRepository, Request $request, array $filters = [])
    {
        $page = (int)$request->get('page') ?: 1;

        $posts = $postRepository->paginate($page, 10, $filters);

        return $this->render('default/index.html.twig', compact('posts'));
    }

    /**
     * @param string $slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(string $slug)
    {
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $post = $postRepository->findBySlug($slug);

        $form = $this->createForm(CommentType::class)->createView();

        return $this->render('default/show.html.twig', compact('post', 'form'));
    }
}
