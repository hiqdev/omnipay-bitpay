<?php

namespace Omnipay\BitPay\Tests\Message;

use Omnipay\BitPay\Message\PurchaseRequest;
use Omnipay\BitPay\Message\PurchaseResponse;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    private $request;
    private $token = '2g3S6HNsc9kJXMrrcHaGG2MdCzrcPLxt2ErPx2s44J2b';
    private $returnUrl = 'https://www.foodstore.com/success';
    private $notifyUrl = 'https://www.foodstore.com/notify';
    private $description = 'Test Transaction long description';
    private $transactionId = '12345ASD67890sd';
    private $amount = '4.65';
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
            'currency' => $this->currency,
            'testMode' => $this->testMode,
        ]);
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->amount, $data['amount']);
        $this->assertSame($this->returnUrl, $data['return']);
        $this->assertSame($this->notifyUrl, $data['notifyUrl']);
        $this->assertSame($this->description, $data['item_name']);
        $this->assertSame($this->transactionId, $data['item_number']);
    }

    public function testSendData()
    {
        $data = $this->request->getData();
        $response = $this->request->sendData($data);
        $this->assertSame(PurchaseResponse::class, get_class($response));
    }
}
