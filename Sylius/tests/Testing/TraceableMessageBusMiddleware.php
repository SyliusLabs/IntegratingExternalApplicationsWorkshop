<?php

declare(strict_types=1);

namespace Tests\AppBundle\Testing;

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;

final class TraceableMessageBusMiddleware implements MessageBusMiddleware
{
    /**
     * @var array
     */
    private $messages = [];

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next): void
    {
        $this->messages[] = $message;

        $next();
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return $this->messages;
    }
}
