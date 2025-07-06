<?php

declare(strict_types=1);

namespace Tests\Integration\Product\Presentation\Controller;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductPriceControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $em = $container->get('doctrine')->getManager();
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($em);
            $schemaTool->dropSchema($metadata);
            $schemaTool->createSchema($metadata);
        }
    }

    public function testGetAllPricesReturnsSuccess(): void
    {
        $this->client->request('GET', '/api/prices', [], [], [
            'HTTP_X-API-Key' => $_ENV['API_KEY'] ?? 'K4kP9wqX2YbV5nJm8tRv7sA6zQ3fH1gL',
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseFormatSame('json');
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('success', $data);
    }

    public function testGetAllPricesReturnsUnauthorizedWithoutApiKey(): void
    {
        $this->client->request('GET', '/api/prices');
        $this->assertResponseStatusCodeSame(401);
        $content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('Full authentication is required', $content);
    }
}
