<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class CommentController
 *
 * @package App\Controller
 */
class CommentController extends AbstractController
{
    /**
     * @param string $slug
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(string $slug, Request $request)
    {
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $post = $postRepository->findBySlug($slug);

        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);

        if (! $form->isValid()) {
            return $this->render('default/show.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
            ]);
        }

        /** @var \App\Entity\Comment $comment */
        $comment = $form->getData();
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $postRepository->addComment($post, $comment);

        return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
    }
}
