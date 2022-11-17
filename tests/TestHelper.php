<?php

declare(strict_types=1);

namespace App\Tests;

trait TestHelper
{
    public function handleTestError($response, $crawler): void
    {
        if (! $response->isSuccessful() && 500 === $response->getStatusCode()) {
            $block = $crawler->filter('h1.exception-message');

            if ($block->count()) {
                $this->fail($block->text());
            }
        }
    }
}
