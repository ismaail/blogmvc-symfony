<?php

namespace App\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\DBAL\Schema\SchemaException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class DatabaseTestCase
 *
 * @package App\Tests
 */
class DatabaseTestCase extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    private $metadata;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Entities metadata.
        $this->metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $this->generateSchema();
    }

    /**
     * Create Schema from Entities Metadata.
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    private function generateSchema()
    {
        if (! empty($this->metadata)) {
            $tool = new SchemaTool($this->entityManager);
            $tool->createSchema($this->metadata);
        } else {
            throw new SchemaException('No Metadata Classes to process.');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
