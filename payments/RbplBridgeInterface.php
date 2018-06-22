<?php


namespace AppBundle\Payments;

interface RbplBridgeInterface
{
    const SANDBOX_ENVIRONMENT = 'sandbox';
    const PRODUCTION_ENVIRONMENT = 'production';
    const SANDBOX_HOST = 'https://r-pay.herokuapp.com/';
    const PRODUCTION_HOST = 'https://r-pay.herokuapp.com/';
    const COMPLETED_STATUS = 'completed';
    const FAILED_STATUS = 'failed';
    const CANCELLED_STATUS = 'cancelled';

    /**
     * @return string
     */
    public function getPaymentUrl(): string;

    /**
     * @return string
     */
    public function getHostForEnvironment(): string;

    /**
     * @param string $merchantId
     * @param string $crcKey
     * @param string $environment
     */
    public function setAuthorizationData(
        string $merchantId,
        string $crcKey,
        string $environment = self::SANDBOX_ENVIRONMENT
    ): void;

}
