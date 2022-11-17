<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SidebarController extends AbstractController
{
    public function categories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('sidebar/categories.html.twig', compact('categories'));
    }

    public function latestPosts(PostRepository $postRepository): Response
    {
        $latestPosts = $postRepository->latest(5);

        return $this->render('sidebar/latest_post.html.twig', compact('latestPosts'));
    }
}
