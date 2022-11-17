<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use PHPUnit\Framework\MockObject\MockObject;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @mixin \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
 * @property \Symfony\Bundle\FrameworkBundle\KernelBrowser $client
 */
trait DoctrineMocker
{
    public function mockPaginator(array $items, int $maxResult): MockObject|Paginator
    {
        $mock = $this->getMockBuilder(\Doctrine\ORM\Tools\Pagination\Paginator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['count', 'getIterator', 'getQuery'])
            ->getMock();

        $query = new \Doctrine\ORM\Query($this->mockEntityManager());
        $query->setMaxResults($maxResult);

        $mock->method('count')->willReturn(\count($items));
        $mock->method('getQuery')->willReturn($query);
        $mock->method('getIterator')->willReturn(new \ArrayIterator($items));

        return $mock;
    }

    public function mockEntityManager(): MockObject|EntityManager
    {
        $mock = $this->getMockBuilder(\Doctrine\ORM\EntityManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConfiguration'])
            ->getMock()
        ;

        $mock->method('getConfiguration')
            ->willReturn($this->mockConfiguration());

        return $mock;
    }

    public function mockConfiguration(): MockObject|Configuration
    {
        $mock = $this->getMockBuilder(\Doctrine\ORM\Configuration::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDefaultQueryHints', 'isSecondLevelCacheEnabled'])
            ->getMock();

        $mock->method('getDefaultQueryHints')
            ->willReturn([]);

        $mock->method('isSecondLevelCacheEnabled')
            ->willReturn(false);

        return $mock;
    }
}
