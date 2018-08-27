<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class SidebarController
 *
 * @package App\Controller
 */
class SidebarController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categories()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('sidebar/categories.html.twig', compact('categories'));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function latestPosts()
    {
        $latestPosts = $this->getDoctrine()->getRepository(Post::class)->latest(5);

        return $this->render('sidebar/latest_post.html.twig', compact('latestPosts'));
    }
}
