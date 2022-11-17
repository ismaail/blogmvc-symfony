<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\DBAL\Schema\SchemaException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DatabaseTestCase extends WebTestCase
{
    protected static ContainerInterface $container;

    protected KernelBrowser $client;

    private array $metadata;

    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if (! static::$kernel) {
            static::$kernel = self::createKernel([
                'environment' => 'test',
                'debug' => true,
            ]);

            static::$kernel->boot();
        }

        static::$container = static::$kernel->getContainer();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        // Entities metadata.
        $this->metadata = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();

        $this->generateSchema();

        // Clear EntityManager from previous tests data.
        $this->getEntityManager()->clear();
    }

    protected function getEntityManager(): EntityManager
    {
        return static::$container->get('doctrine.orm.entity_manager');
    }

    protected function generateSchema(): void
    {
        if (empty($this->metadata)) {
            throw new SchemaException('No Metadata Classes to process.');
        }

        $tool = new SchemaTool($this->getEntityManager());
        $tool->createSchema($this->metadata);
    }

    protected function tearDown(): void
    {
        if (empty($this->metadata)) {
            throw new SchemaException('No Metadata Classes to process.');
        }

        $tool = new SchemaTool($this->getEntityManager());
        $tool->dropSchema($this->metadata);

        parent::tearDown();
    }
}
