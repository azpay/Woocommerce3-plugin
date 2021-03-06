<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit93fe59928be226a26f1a46577f6d8f05
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'Gateway\\API\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Gateway\\API\\' => 
        array (
            0 => __DIR__ . '/..' . '/azpay/sdk-php-7/src/gateway/API',
        ),
    );

    public static $prefixesPsr0 = array (
        'G' => 
        array (
            'Gateway\\API\\' => 
            array (
                0 => __DIR__ . '/..' . '/azpay/sdk-php-7/src/gateway/API',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit93fe59928be226a26f1a46577f6d8f05::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit93fe59928be226a26f1a46577f6d8f05::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit93fe59928be226a26f1a46577f6d8f05::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
