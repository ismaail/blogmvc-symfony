<?php

namespace App\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\DBAL\Schema\SchemaException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DatabaseTestCase
 *
 * @package App\Tests
 */
class DatabaseTestCase extends WebTestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected static $container;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    protected $client;

    /**
     * @var array
     */
    private $metadata;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null, array $data = [], $dataName = '')
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

    /**
     * Create Doctrine Schemas.
     */
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

    /**
     * Returns the doctrine orm entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager(): \Doctrine\ORM\EntityManager
    {
        return static::$container->get('doctrine.orm.entity_manager');
    }

    /**
     * Create Schema from Entities Metadata.
     *
     * @throws SchemaException
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected function generateSchema(): void
    {
        if (empty($this->metadata)) {
            throw new SchemaException('No Metadata Classes to process.');
        }

        $tool = new SchemaTool($this->getEntityManager());
        $tool->createSchema($this->metadata);
    }

    /**
     * Drop Doctrine Schemas.
     */
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
