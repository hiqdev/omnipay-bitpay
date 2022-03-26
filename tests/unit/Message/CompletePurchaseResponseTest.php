<?php

namespace Omnipay\BitPay\Tests\Message;

use BitPaySDKLight\Model\Invoice\Invoice;
use BitPaySDKLight\Model\Invoice\InvoiceStatus;
use Omnipay\BitPay\Message\CompletePurchaseRequest;
use Omnipay\BitPay\Message\CompletePurchaseResponse;
use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    private $request;

    private $token = '';
    private $description = 'Test Transaction long description';
    private $transactionId = 'someTransactionId';
    private $transactionReference = 'S4hVo7z6XZQ3yDUyHyUJD7';
    private $status = InvoiceStatus::Complete;
    private $amount = '14.01';
    private $currency = 'USD';
    private $payer = 'USD';
    private $testMode = true;

    public function setUp(): void
    {
        parent::setUp();

        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize([
            'token' => $this->token,
            'testMode' => $this->testMode,
            'transactionId' => $this->transactionId,
        ]);
        $this->request->setId($this->transactionId);
    }

    public function testSuccess()
    {
        $invoice = new Invoice($this->amount, $this->currency);
        $invoice->setFullNotifications(true);
        $invoice->setItemDesc($this->description);
        $invoice->setPosData(json_encode(['posData' => ['u' => $this->transactionId]]));
        $invoice->setStatus($this->status);
        $invoice->setId($this->transactionReference);
        $response = new CompletePurchaseResponse($this->request, $invoice);

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getCode());
        $this->assertSame($this->transactionId, $response->getTransactionId());
        $this->assertSame($this->transactionReference, $response->getTransactionReference());
        $this->assertSame('14.01', $response->getAmount());
        $this->assertStringContainsString($this->transactionReference, $response->getPayer());
        $this->assertSame($this->currency, $response->getCurrency());
    }
}
