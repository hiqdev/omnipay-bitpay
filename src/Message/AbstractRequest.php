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

use Bitpay\Client\Adapter\CurlAdapter;
use Bitpay\Client\Client;
use Bitpay\Client\ClientInterface;
use Bitpay\Network\Livenet;
use Bitpay\Network\Testnet;
use Bitpay\Token;
use Omnipay\BitPay\Factory\StringStorage;

/**
 * PayPal Abstract Request.
 *
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected function getKeyStorage()
    {
        return new StringStorage();
    }

    public function getToken()
    {
        return $this->getParameter('token');
    }

    public function setToken($value)
    {
        $this->setParameter('token', $value);
    }

    /**
     * @return ClientInterface
     */
    protected function getClient()
    {
        $storage = new StringStorage();
        $privateKey = $storage->load($this->getPrivateKey());
        $publicKey = $storage->load($this->getPublicKey());

        $token = new Token();
        $token->setToken($this->getToken());

        $client = new Client();
        $client->setToken($token);
        $client->setPrivateKey($privateKey);
        $client->setPublicKey($publicKey);
        $client->setNetwork($this->getTestMode() ? new Testnet() : new Livenet());
        $client->setAdapter(new CurlAdapter());

        return $client;
    }

    public function getPrivateKeyObject()
    {
        return $this->getKeyStorage()->load($this->getPrivateKey());
    }

    public function getPublicKeyObject()
    {
        return $this->getKeyStorage()->load($this->getPublicKey());
    }

    /**
     * @param array $data
     * @return array
     */
    protected function buildPosData($data = [])
    {
        $array = array_merge([
            'd' => time(),
        ], $data);

        return [
            'hash' => $this->signPosData($array),
            'posData' => $array,
        ];
    }

    /**
     * @param array $data
     * @return string the signature
     */
    public function signPosData($data)
    {
        return crypt(md5(serialize($data)), $this->getPublicKeyObject()->getHex());
    }

    public function getPrivateKey()
    {
        return $this->getParameter('privateKey');
    }

    public function setPrivateKey($value)
    {
        return $this->setParameter('privateKey', $value);
    }

    public function getPublicKey()
    {
        return $this->getParameter('publicKey');
    }

    public function setPublicKey($value)
    {
        return $this->setParameter('publicKey', $value);
    }
}
