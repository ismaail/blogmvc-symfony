<?php

namespace App\Tests;

/**
 * Class DoctrineMocker
 *
 * @package AppBundle\Traits
 *
 * @mixin \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
 * @property \Symfony\Bundle\FrameworkBundle\Client $client
 */
trait DoctrineMocker
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Doctrine\Bundle\DoctrineBundle\
     */
    public function mockDoctrine()
    {
        $mock = $this->getMockBuilder(\Doctrine\Bundle\DoctrineBundle\Registry::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository', 'clear'])
            ->getMock()
        ;

        $this->client->getContainer()->set('doctrine.orm.default_entity_manager', $mock);

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Doctrine\ORM\EntityManager
     */
    public function mockEntityManager()
    {
        $mock = $this->getMockBuilder(\Doctrine\ORM\EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getConfiguration'])
            ->getMock()
        ;

        $mock->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($this->mockConfiguration());

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Doctrine\ORM\Configuration
     */
    public function mockConfiguration()
    {
        $mock = $this->getMockBuilder(\Doctrine\ORM\Configuration::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultQueryHints', 'isSecondLevelCacheEnabled'])
            ->getMock();

        $mock->expects($this->any())
            ->method('getDefaultQueryHints')
            ->willReturn([]);

        $mock->expects($this->any())
            ->method('isSecondLevelCacheEnabled')
            ->willReturn(false);

        return $mock;
    }

    /**
     * @param array $items
     * @param int $maxResult
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function mockPaginator(array $items, int $maxResult)
    {
        $mock = $this->getMockBuilder(\Doctrine\ORM\Tools\Pagination\Paginator::class)
            ->disableOriginalConstructor()
            ->setMethods(['count', 'getIterator', 'getQuery'])
            ->getMock();

        $query = new \Doctrine\ORM\Query($this->mockEntityManager());
        $query->setMaxResults($maxResult);

        $mock->expects($this->any())->method('count')->willReturn(\count($items));
        $mock->expects($this->any())->method('getQuery')->willReturn($query);
        $mock->expects($this->any())->method('getIterator')->willReturn(new \ArrayIterator($items));

        return $mock;
    }
}
