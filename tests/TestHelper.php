<?php

namespace App\Tests;

/**
 * Trait TestHelper
 *
 * @package App\Tests
 */
trait TestHelper
{
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     */
    public function handleTestError($response, $crawler)
    {
        if (! $response->isSuccessful() && 500 === $response->getStatusCode()) {
            $block = $crawler->filter('h1.exception-message');
            if ($block->count()) {
                $this->fail($block->text());
            }
        }
    }
}
