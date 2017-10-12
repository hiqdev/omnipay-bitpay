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

use Bitpay\InvoiceInterface;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * BitPay Complete Purchase Response.
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /**
     * @var CompletePurchaseRequest
     */
    public $request;

    /**
     * @var InvoiceInterface
     */
    protected $data;

    public function __construct(RequestInterface $request, InvoiceInterface $data)
    {
        parent::__construct($request, $data);

        $this->checkPosData();
    }

    /**
     * Whether the payment is successful.
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->data->getStatus() === InvoiceInterface::STATUS_CONFIRMED || $this->data->getStatus() === InvoiceInterface::STATUS_COMPLETE;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getTransactionId()
    {
        return isset($this->getPosData()['posData']['u']) ? $this->getPosData()['posData']['u'] : '';
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getTransactionReference()
    {
        return $this->data->getOrderId();
    }

    /**
     * Retruns the transatcion status.
     * @return string
     */
    public function getTransactionStatus()
    {
        return $this->data->getStatus();
    }

    /**
     * @return bool
     */
    public function isStatusExceptional()
    {
        return in_array($this->getData()->getExceptionStatus(), ['paidPartial', 'paidOver']);
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getAmount()
    {
        if ($this->isStatusExceptional()) {
            return (string)($this->getData()->getBtcPrice() * $this->getData()->getRate());
        }

        return (string)$this->data->getPrice();
    }

    /**
     * @return string
     */
    public function getRequestedAmount()
    {
        return $this->data->getPrice();
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getFee()
    {
        return '';
    }

    /**
     * Returns the currency.
     * @return string
     */
    public function getCurrency()
    {
        return strtoupper($this->data->getCurrency()->getCode());
    }

    /**
     * Returns the payer "name/email".
     * @return string
     */
    public function getPayer()
    {
        $buyer = $this->data->getBuyer();

        return $buyer->getFirstName() . ' ' . $buyer->getLastName() . ' / ' . $buyer->getEmail();
    }

    /**
     * Returns the payment date.
     * @return string
     */
    public function getTime()
    {
        $time = $this->data->getInvoiceTime();

        if ($time instanceof \DateTime) {
            return $time->format('c');
        }

        return date('c', $time/1000); // time in ms
    }

    /**
     * @return array
     */
    public function getPosData()
    {
        return @json_decode($this->data->getPosData(), true);
    }

    /**
     * @return InvoiceInterface
     */
    public function getData()
    {
        return parent::getData();
    }

    protected function checkPosData()
    {
        $data = $this->getPosData();
        if ($data === null || !isset($data['hash']) || !isset($data['posData'])) {
            throw new InvalidResponseException('Failed to decode JSON');
        }

        $posData = $data['posData'];
        $hash = $data['hash'];
        if ($hash !== $this->request->signPosData($posData)) {
            throw new InvalidResponseException('Invalid signature');
        }
    }
}
