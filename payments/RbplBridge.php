<?php

namespace AppBundle\Payments;

use GuzzleHttp\ClientInterface;

final class RbplBridge implements RbplBridgeInterface
{
    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var string
     */
    private $crcKey;

    /**
     * @var string
     */
    private $environment = self::SANDBOX_ENVIRONMENT;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function setAuthorizationData(
        string $merchantId,
        string $crcKey,
        string $environment = self::SANDBOX_ENVIRONMENT
    ): void
    {
        $this->merchantId = $merchantId;
        $this->crcKey = $crcKey;
        $this->environment = $environment;
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentUrl(): string
    {
        return $this->getHostForEnvironment() . 'api/payments';
    }

    /**
     * {@inheritDoc}
     */
    public function getHostForEnvironment(): string
    {
        return self::SANDBOX_ENVIRONMENT === $this->environment ?
            self::SANDBOX_HOST : self::PRODUCTION_HOST
            ;
    }
}
