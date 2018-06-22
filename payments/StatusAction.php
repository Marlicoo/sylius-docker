<?php

namespace AppBundle\Payments;


use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;

use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class StatusAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var RbplBridgeInterface
     */
    private $rbplBridge;

    /**
     * @param RbplBridgeInterface $rbplBridge
     */
    public function __construct(RbplBridgeInterface $rbplBridge)
    {
        $this->rbplBridge = $rbplBridge;
    }

    /**
     * {@inheritDoc}
     */
    public function setApi($api): void
    {
        if (false === is_array($api)) {
            throw new UnsupportedApiException('Not supported.Expected to be set as array.');
        }

        $this->rbplBridge->setAuthorizationData($api['merchant_id'], $api['crc_key'], $api['environment']);
    }

    /**
     * {@inheritDoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        $details = $payment->getDetails();

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        if (isset($httpRequest->query['status']) &&
            $httpRequest->query['status'] === RbplBridgeInterface::CANCELLED_STATUS
        ) {
            $details['p24_status'] = RbplBridgeInterface::CANCELLED_STATUS;
            $request->markCanceled();
            return;
        }

        if (false === isset($details['p24_status'])) {
            $request->markNew();
            return;
        }

        if (RbplBridgeInterface::COMPLETED_STATUS === $details['p24_status']) {
            $request->markCaptured();
            return;
        }

        if (RbplBridgeInterface::FAILED_STATUS === $details['p24_status']) {
            $request->markFailed();
            return;
        }

        $request->markUnknown();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof PaymentInterface
            ;
    }
}