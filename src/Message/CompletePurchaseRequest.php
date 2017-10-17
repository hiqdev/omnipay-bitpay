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

/**
 * BitPay Complete Purchase Request.
 */
class CompletePurchaseRequest extends AbstractRequest
{
    /**
     * Get the data for this request.
     * @return array request data
     */
    public function getData()
    {
        $this->validate('transactionId', 'privateKey', 'publicKey', 'token');
    }

    public function setId($value)
    {
        $this->setTransactionId($value);
    }

    /**
     * Send the request with specified data.
     * @param mixed $data The data to send
     * @return CompletePurchaseResponse
     */
    public function sendData($data)
    {
        $invoice = $this->getClient()->getInvoice($this->getTransactionId());

        return $this->response = new CompletePurchaseResponse($this, $invoice);
    }
}
