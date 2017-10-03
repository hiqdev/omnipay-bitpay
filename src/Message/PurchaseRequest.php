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

use Bitpay\Currency;
use Bitpay\Invoice;
use Bitpay\Item;
use Bitpay\ItemInterface;

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
            'privateKey', 'publicKey', 'token',
            'transactionReference', 'description',
            'price', 'currency',
            'returnUrl', 'cancelUrl', 'notifyUrl'
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
        $invoice = new Invoice();
        $invoice->setFullNotifications(true);
        $invoice->setItem($this->createItem());
        $invoice->setCurrency(new Currency($this->getCurrency()));
        $invoice->setRedirectUrl($this->getReturnUrl());
        $invoice->setNotificationUrl($this->getNotifyUrl());

        $response = $this->getClient()->createInvoice($invoice);

        return $this->response = new PurchaseResponse($this, $response);
    }

    /**
     * @return Item|ItemInterface
     */
    protected function createItem()
    {
        $item = new Item();
        $item->setCode($this->getTransactionReference());
        $item->setDescription($this->getDescription());
        $item->setPrice($this->getAmount());

        return $item;
    }
}
