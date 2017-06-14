<?php

declare(strict_types=1);

namespace Tests\RecommendationEngine\Infrastructure\Amqp\Denormalizer;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use RecommendationEngine\Domain\Event\CustomerPlacedOrder;
use RecommendationEngine\Infrastructure\Amqp\Denormalizer\CustomerPlacedOrderDenormalizer;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizationFailedException;

final class CustomerPlacedOrderDenormalizerTest extends TestCase
{
    /**
     * @test
     */
    public function it_denormalize_customer_placed_order_event(): void
    {
        $amqpMessage = new AMQPMessage(json_encode([
            'type' => 'customer_placed_order',
            'payload' => [
                'customerEmail' => 'rocketarminek@test.com',
                'orderToken' => 'xyz',
                'productsCodes' => ['awesome-sylius-mug', 'awesome-sylius-t_shirt'],
            ],
        ]));

        $denormalizer = new CustomerPlacedOrderDenormalizer();

        $event = $denormalizer->denormalize($amqpMessage);

        $this->assertEquals(new CustomerPlacedOrder('rocketarminek@test.com', 'xyz', ['awesome-sylius-mug', 'awesome-sylius-t_shirt']), $event);
    }

    /**
     * @test
     */
    public function it_cannot_denormalize_when_customer_email_is_missing(): void
    {
        $this->expectException(DenormalizationFailedException::class);

        $amqpMessage = new AMQPMessage(json_encode([
            'type' => 'customer_placed_order',
            'payload' => [
                'orderToken' => 'xyz',
                'productsCodes' => ['awesome-sylius-mug', 'awesome-sylius-t_shirt'],
            ],
        ]));

        $denormalizer = new CustomerPlacedOrderDenormalizer();

        $denormalizer->denormalize($amqpMessage);
    }

    /**
     * @test
     */
    public function it_cannot_denormalize_when_order_token_is_missing(): void
    {
        $this->expectException(DenormalizationFailedException::class);

        $amqpMessage = new AMQPMessage(json_encode([
            'type' => 'customer_placed_order',
            'payload' => [
                'customerEmail' => 'rocketarminek@test.com',
                'productsCodes' => ['awesome-sylius-mug', 'awesome-sylius-t_shirt'],
            ],
        ]));

        $denormalizer = new CustomerPlacedOrderDenormalizer();

        $denormalizer->denormalize($amqpMessage);
    }

    /**
     * @test
     */
    public function it_cannot_denormalize_when_product_codes_are_missing(): void
    {
        $this->expectException(DenormalizationFailedException::class);

        $amqpMessage = new AMQPMessage(json_encode([
            'type' => 'customer_placed_order',
            'payload' => [
                'customerEmail' => 'rocketarminek@test.com',
                'orderToken' => 'xyz',
            ],
        ]));

        $denormalizer = new CustomerPlacedOrderDenormalizer();

        $denormalizer->denormalize($amqpMessage);
    }

    /**
     * @test
     */
    public function it_cannot_denormalize_when_message_type_is_missing(): void
    {
        $this->expectException(DenormalizationFailedException::class);

        $amqpMessage = new AMQPMessage(json_encode([
            'payload' => [
                'customerEmail' => 'rocketarminek@test.com',
                'orderToken' => 'xyz',
                'productsCodes' => ['awesome-sylius-mug', 'awesome-sylius-t_shirt'],
            ],
        ]));

        $denormalizer = new CustomerPlacedOrderDenormalizer();

        $denormalizer->denormalize($amqpMessage);
    }

    /**
     * @test
     */
    public function it_cannot_denormalize_when_payload_is_missing(): void
    {
        $this->expectException(DenormalizationFailedException::class);

        $amqpMessage = new AMQPMessage(json_encode([
            'type' => 'customer_placed_order',
        ]));

        $denormalizer = new CustomerPlacedOrderDenormalizer();

        $denormalizer->denormalize($amqpMessage);
    }

    /**
     * @test
     */
    public function it_does_not_support_message_which_cannot_be_denormalized(): void
    {
        $amqpMessage = new AMQPMessage(json_encode([
            'payload' => [
                'customerEmail' => 'rocketarminek@test.com',
                'orderToken' => 'xyz',
                'productsCodes' => ['awesome-sylius-mug', 'awesome-sylius-t_shirt'],
            ],
        ]));
        $denormalizer = new CustomerPlacedOrderDenormalizer();

        $this->assertEquals($denormalizer->supports($amqpMessage), false);
    }

    /**
     * @test
     */
    public function it_supports_message_which_cannot_be_denormalized(): void
    {
        $amqpMessage = new AMQPMessage(json_encode([
            'type' => 'customer_placed_order',
            'payload' => [
                'customerEmail' => 'rocketarminek@test.com',
                'orderToken' => 'xyz',
                'productsCodes' => ['awesome-sylius-mug', 'awesome-sylius-t_shirt'],
            ],
        ]));
        $denormalizer = new CustomerPlacedOrderDenormalizer();

        $this->assertEquals($denormalizer->supports($amqpMessage), true);
    }
}
