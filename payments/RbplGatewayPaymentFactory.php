<?php

namespace AppBundle\Payments;


use BitBag\SyliusPrzelewy24Plugin\Action\NotifyAction;
use GuzzleHttp\Client;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class RbplGatewayPaymentFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {

        $config->defaults([
            'payum.factory_name' => 'rbpl',
            'payum.factory_title' => 'rbpl',
            'payum.action.capture' => new CaptureAction(new RbplBridge(new Client())),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.status' => new StatusAction(new RbplBridge(new Client())),
            'payum.action.notify' => new \AppBundle\Payments\NotifyAction(new RbplBridge(new Client())),

        ]);

        if (false === (bool)$config['payum.api']) {
            $config['payum.default_options'] = [
                'crc_key' => '1',
                'merchant_id' => '1',
                'environment' => RbplBridgeInterface::SANDBOX_ENVIRONMENT,
            ];

            $config->defaults($config['payum.default_options']);

            $config['payum.required_options'] = [
                'crc_key',
                'merchant_id',
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return [
                    'crc_key' => $config['crc_key'],
                    'merchant_id' => $config['merchant_id'],
                    'environment' => $config['environment'],
                    'payum.http_client' => $config['payum.http_client'],
                ];
            };
        }
    }
}