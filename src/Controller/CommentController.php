<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @param \App\Repository\PostRepository $postRepository
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route('/post/{slug}/comment', name: 'post_comment_store', methods: ['POST'])]
    public function store(string $slug, Request $request, PostRepository $postRepository): RedirectResponse|Response
    {
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
        $postRepository->addComment($post, $comment);

        return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
    }
}
