<?php

namespace Omnipay\BitPay\Tests\Message;

use BitPaySDKLight\Model\Invoice\Invoice;
use Omnipay\BitPay\Message\PurchaseRequest;
use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{
    private $request;
    private $token = '2g3S6HNsc9kJXMrrcHaGG2MdCzrcPLxt2ErPx2s44J2b';
    private $returnUrl = 'https://www.foodstore.com/success';
    private $notifyUrl = 'https://www.foodstore.com/notify';
    private $description = 'Test Transaction long description';
    private $transactionId = '12345ASD67890sd';
    private $amount = '14.65';
    private $currency = 'USD';
    private $testMode = true;

    public function setUp(): void
    {
        parent::setUp();

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'token' => $this->token,
            'returnUrl' => $this->returnUrl,
            'notifyUrl' => $this->notifyUrl,
            'description' => $this->description,
            'transactionId' => $this->transactionId,
            'amount' => $this->amount,
            'price' => $this->amount,
            'currency' => $this->currency,
            'testMode' => $this->testMode,
        ]);
    }

    public function testSuccess()
    {
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertStringStartsWith('https://test.bitpay.com/invoice', $response->getRedirectUrl());
        /** @var Invoice $invoice */
        $invoice = $response->getData();
        $this->assertSame($this->currency, $invoice->getCurrency());
        $this->assertSame($this->amount, (string)$invoice->getPrice());
    }
}
