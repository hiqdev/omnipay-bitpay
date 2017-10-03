<?php

namespace Omnipay\BitPay\Factory;

use Bitpay\PrivateKey;
use Bitpay\PublicKey;

class StringStorage implements \Bitpay\Storage\StorageInterface
{
    /**
     * @inheritdoc
     */
    public function persist(\Bitpay\KeyInterface $key)
    {
        return serialize($key);
    }

    /**
     * @return PublicKey|PrivateKey
     */
    public function load($serializedKey)
    {
        return unserialize($serializedKey);
    }
}
