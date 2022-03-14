<?php

namespace Omnipay\BitPay\Message;

use BitPaySDKLight\Exceptions\BitPayException;
use BitPaySDKLight\Exceptions\InvoiceQueryException;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * BitPay Complete Purchase Request.
 */
class CompletePurchaseRequest extends AbstractRequest
{
    /**
     * Get the data for this request.
     *
     * @return void request data
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('transactionId', 'token');

        return $this->httpRequest->request->all();
    }

    public function setId($value)
    {
        $this->setTransactionId($value);
    }

    /**
     * Send the request with specified data.
     *
     * @param mixed $data The data to send
     *
     * @return CompletePurchaseResponse
     * @throws BitPayException
     * @throws InvoiceQueryException
     */
    public function sendData($data)
    {
        $invoice = $this->getClient()->getInvoice($this->getTransactionId());

        return $this->response = new CompletePurchaseResponse($this, $invoice);
    }
}
