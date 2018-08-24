<?php

namespace App\Controller;

use App\Entity\Post;
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $page = (int)$request->get('page') ?: 1;

        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $posts = $postRepository->paginate($page, 10);

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

        return $this->render('default/index.show.twig', compact('post'));
    }
}
