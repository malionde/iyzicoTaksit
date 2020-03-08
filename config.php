<?php

require_once('IyzipayBootstrap.php');

IyzipayBootstrap::init();

class Config
{
    public static function options()
    {
        $options = new \Iyzipay\Options();
        $options->setApiKey('sandbox-LoDo2D258h1uXyjopUvRXj1ir4BiWyAI');
        $options->setSecretKey('sandbox-VU1fLYVj3wyKWQ66DMEoBBjlreVUVflx');
        $options->setBaseUrl('https://sandbox-api.iyzipay.com');

        return $options;
    }
}