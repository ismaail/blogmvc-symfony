<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Tests\DatabaseTestCase;

/**
 * Class CategoryTest
 *
 * @package App\Tests\Entity
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CategoryTest extends DatabaseTestCase
{
    /**
     * @test
     */
    public function it_creates_new_Category_with_the_correct_Slug()
    {
        $category = new Category();
        $category->setName('Foo Bar');

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->assertEquals('foo-bar', $category->getSlug(), 'Wrong Category slug value.');
    }
}
