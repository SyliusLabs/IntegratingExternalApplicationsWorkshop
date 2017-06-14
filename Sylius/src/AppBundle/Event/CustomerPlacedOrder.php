<?php

declare(strict_types=1);

namespace AppBundle\Event;

final class CustomerPlacedOrder
{
    /**
     * @var string
     */
    private $customerEmail;

    /**
     * @var string
     */
    private $orderToken;

    /**
     * @var array
     */
    private $productsCodes;

    /**
     * @param string $customerEmail
     * @param string $orderToken
     * @param array $productsCodes
     */
    private function __construct(string $customerEmail, string $orderToken, array $productsCodes)
    {
        $this->customerEmail = $customerEmail;
        $this->orderToken = $orderToken;
        $this->productsCodes = $productsCodes;
    }

    /**
     * @param string $customerEmail
     * @param string $orderToken
     * @param array $productsCodes
     *
     * @return CustomerPlacedOrder
     */
    public static function occur(string $customerEmail, string $orderToken, array $productsCodes): self
    {
        return new self($customerEmail, $orderToken, $productsCodes);
    }

    /**
     * @return string Email
     */
    public function customerEmail(): string
    {
        return $this->customerEmail;
    }

    /**
     * @return string
     */
    public function orderToken(): string
    {
        return $this->orderToken;
    }

    /**
     * @return array
     */
    public function productsCodes(): array
    {
        return $this->productsCodes;
    }
}
