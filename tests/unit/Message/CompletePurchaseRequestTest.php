<?php

namespace Omnipay\BitPay\Tests\Message;

use Omnipay\BitPay\Message\CompletePurchaseRequest;
use Omnipay\BitPay\Message\CompletePurchaseResponse;
use Omnipay\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class CompletePurchaseRequestTest extends TestCase
{
    private $request;
    private $token = '8ZerGR1iWwH3iPchhCsSQba6rvHyPQw9Dx1jiYR2J4vX';
    private $description = 'Test Transaction long description';
    private $transactionId = 'S4hVo7z6XZQ3yDUyHyUJD7';
    private $amount = '14.01';
    private $currency = 'USD';
    private $testMode = true;

    public function setUp(): void
    {
        parent::setUp();

        $httpRequest = new HttpRequest([], [
            'item_name' => $this->description,
            'item_number' => $this->transactionId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'test' => $this->testMode,
        ]);

        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $httpRequest);
        $this->request->initialize([
            'token' => $this->token,
            'testMode' => $this->testMode,
            'transactionId' => $this->transactionId,
        ]);
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->description, $data['item_name']);
        $this->assertSame($this->transactionId, $data['item_number']);
        $this->assertSame($this->amount, $data['amount']);
        $this->assertSame($this->currency, $data['currency']);
    }

    public function testSendData()
    {
        $data = $this->request->getData();
        $response = $this->request->sendData($data);
        $this->assertSame(CompletePurchaseResponse::class, get_class($response));
    }
}
