<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class SidebarController
 *
 * @package App\Controller
 */
class SidebarController extends AbstractController
{
    /**
     * @param \App\Repository\CategoryRepository $categoryRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('sidebar/categories.html.twig', compact('categories'));
    }

    /**
     * @param \App\Repository\PostRepository $postRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function latestPosts(PostRepository $postRepository): Response
    {
        $latestPosts = $postRepository->latest(5);

        return $this->render('sidebar/latest_post.html.twig', compact('latestPosts'));
    }
}
