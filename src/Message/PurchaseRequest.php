<?php

/*
 * PayPal driver for Omnipay PHP payment library
 *
 * @link      https://github.com/hiqdev/omnipay-paypal
 * @package   omnipay-paypal
 * @license   MIT
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace Omnipay\BitPay\Message;

use BitPaySDKLight\Model\Invoice\Invoice;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * PayPal Complete Purchase Request.
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * Get the data for this request.
     *
     * @return array request data
     */
    public function getData()
    {
        $this->validate(
            'token',
            'transactionId', 'description',
            'amount', 'currency',
            'returnUrl', 'notifyUrl'
        );
    }

    /**
     * Send the request with specified data.
     *
     * @param mixed $data The data to send
     * @return PurchaseResponse
     */
    public function sendData($data)
    {
        $bitpay = $this->getClient();
        $basicInvoice = new Invoice((float)$this->getAmount(), $this->getCurrency());
        $basicInvoice->setFullNotifications(true);
        $basicInvoice->setRedirectURL($this->getReturnUrl());
        $basicInvoice->setNotificationURL($this->getNotifyUrl());
        $basicInvoice->setPosData(json_encode($this->buildPosData()));
        $basicInvoice->setItemCode($this->getTransactionReference());
        $basicInvoice->setItemDesc($this->getDescription());

        $invoice = $bitpay->createInvoice($basicInvoice);

        return $this->response = new PurchaseResponse($this, $invoice);
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws InvalidRequestException
     */
    protected function buildPosData($data = [])
    {
        return parent::buildPosData([
            'c' => $this->getDescription(),
            's' => $this->getAmount(),
            'u' => $this->getTransactionId(),
        ]);
    }
}
