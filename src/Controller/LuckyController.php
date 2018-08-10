<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class LuckyController
 *
 * @package App\Controller
 */
class LuckyController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function number()
    {
        $number = random_int(0, 100);

        return new Response("<html><body>Lucky Number: {$number}</body></html>");
    }
}
