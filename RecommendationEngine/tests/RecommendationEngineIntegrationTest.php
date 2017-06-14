<?php

declare(strict_types=1);

namespace Tests\RecommendationEngine;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RecommendationEngineIntegrationTest extends WebTestCase
{
    /**
     * @var ConsumerInterface
     */
    private $amqpConsumer;

    /**
     * @var Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        static::bootKernel();

        $this->amqpConsumer = static::$kernel->getContainer()->get('rabbitmq_simplebus.consumer');
        $this->client = static::createClient([], ['HTTP_ACCEPT' => 'application/json']);
    }

    /**
     * @test
     */
    public function it_returns_list_of_recommended_products(): void
    {
        $this->amqpConsumer->execute(new AMQPMessage(json_encode([
            'type' => 'customer_placed_order',
            'payload' => [
                'customerEmail' => 'customer@example.com',
                'orderToken' => 'order1',
                'productsCodes' => ['product1', 'product2']
            ],
        ])));
        $this->amqpConsumer->execute(new AMQPMessage(json_encode([
            'type' => 'customer_placed_order',
            'payload' => [
                'customerEmail' => 'customer@example.com',
                'orderToken' => 'order1',
                'productsCodes' => ['product1', 'product3']
            ],
        ])));

        $this->client->request('GET', '/recommended-products/product1', [], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertEquals(['product2', 'product3'], json_decode($response->getContent(), true));
    }

    /**
     * @test
     */
    public function it_returns_list_of_recommended_products_while_respecting_the_limit(): void
    {
        $this->amqpConsumer->execute(new AMQPMessage(json_encode([
            'type' => 'customer_placed_order',
            'payload' => [
                'customerEmail' => 'customer@example.com',
                'orderToken' => 'order1',
                'productsCodes' => ['product1', 'product2']
            ],
        ])));
        $this->amqpConsumer->execute(new AMQPMessage(json_encode([
            'type' => 'customer_placed_order',
            'payload' => [
                'customerEmail' => 'customer@example.com',
                'orderToken' => 'order1',
                'productsCodes' => ['product1', 'product3']
            ],
        ])));

        $this->client->request('GET', '/recommended-products/product1?limit=1', [], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertEquals(['product2'], json_decode($response->getContent(), true));
    }

    /** @before */
    protected function purgeDatabase(): void
    {
        static::$kernel->getContainer()->get('neo4j.entity_manager.default')->getDatabaseDriver()->run('MATCH (n) DETACH DELETE n;');
    }
}
