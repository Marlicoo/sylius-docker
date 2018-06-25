<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace AppBundle\Payments;


use AppBundle\Payments\RbplBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Payum\Core\Bridge\Spl\ArrayObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class NotifyAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
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
     * @param Notify $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());
        $details['status'] = RbplBridgeInterface::COMPLETED_STATUS;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request): bool
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
