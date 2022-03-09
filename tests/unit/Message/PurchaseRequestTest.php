<?php

namespace Omnipay\BitPay\Message;

use Guzzle\Tests\Service\Mock\MockClient;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    private $request;
    private $token = 'CFJCZH3VitcEER9Uybx8LMvkPsSWzpSWvN4vhNEJp47b';
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

        $this->request = new PurchaseRequest(new MockClient(), $this->getHttpRequest());
        $this->request->initialize([
                                       'token'         => $this->token,
                                       'returnUrl'     => $this->returnUrl,
                                       'notifyUrl'     => $this->notifyUrl,
                                       'description'   => $this->description,
                                       'transactionId' => $this->transactionId,
                                       'amount'        => $this->amount,
                                       'currency'      => $this->currency,
                                       'testMode'      => $this->testMode,
                                   ]);
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->amount, $this->request->getAmount());
        $this->assertSame($this->returnUrl, $data['return']);
        $this->assertSame($this->notifyUrl, $data['notify_url']);
        $this->assertSame($this->description, $data['item_name']);
        $this->assertSame($this->transactionId, $data['item_number']);
    }

    public function testSendData()
    {
        $data = $this->request->getData();
        $response = $this->request->sendData($data);
        $this->assertSame('Omnipay\PayPal\Message\PurchaseResponse', get_class($response));
    }
}
