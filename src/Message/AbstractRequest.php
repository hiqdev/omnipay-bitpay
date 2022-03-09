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

use BitPaySDKLight\Client;
use BitPaySDKLight\Env;
use BitPaySDKLight\Exceptions\BitPayException;

/**
 * PayPal Abstract Request.
 *
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    public function getToken()
    {
        return $this->getParameter('token');
    }

    public function setToken($value)
    {
        $this->setParameter('token', $value);
    }

    /**
     * @return Client
     * @throws BitPayException
     */
    protected function getClient(): Client
    {
        return new Client($this->getToken(), $this->getTestMode() ? Env::Test : Env::Prod);
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
}
