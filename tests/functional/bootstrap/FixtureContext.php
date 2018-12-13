<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Fidry\AliceDataFixtures\LoaderInterface;

class FixtureContext implements Context
{
    use SharedContextTrait;

    /** @var LoaderInterface */
    private $loader;

    /** @var string */
    private $fixturesBasePath;

    /** @var array */
    private $fixtures;

    public function __construct(
        Registry $doctrine,
        LoaderInterface $loader,
        string $fixturesBasePath
    ) {
        $this->loader = $loader;
        $this->fixturesBasePath = $fixturesBasePath;

        /** @var Connection[] $connections */
        $connections = $doctrine->getConnections();

        foreach ($connections as $connection) {
            if ('pdo_sqlite' !== $connection->getDriver()->getName()) {
                throw new \RuntimeException(sprintf(
                    'Fixtures must be loaded in an sqlite database, current database driver is %s,
                there must be an issue with test configuration.',
                    $connection->getDriver()->getName()
                ));
            }
        }

        /** @var ObjectManager[] $managers */
        $managers = $doctrine->getManagers(); // Note that currently, FidryAliceDataFixturesBundle does not support multiple managers

        foreach ($managers as $manager) {
            if ($manager instanceof EntityManagerInterface) {
                $schemaTool = new SchemaTool($manager);
                $schemaTool->dropDatabase();
                $schemaTool->createSchema($manager->getMetadataFactory()->getAllMetadata());
            }
        }
    }

    /**
     * @Given the fixtures file :fixturesFile is loaded
     *
     * @param string $fixturesFile Path to the fixtures
     */
    public function thereAreFixtures(string $fixturesFile): void
    {
        $this->fixtures = $this->loader->load([$this->fixturesBasePath.$fixturesFile]);

        $this->sharingContext->merge($this->fixtures);
    }
}
