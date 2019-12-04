<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb6afcf8de2dac709012f3390ab7cd4d2
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
        'P' => 
        array (
            'PDFShift\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
        'PDFShift\\' => 
        array (
            0 => __DIR__ . '/..' . '/pdfshift/pdfshift-php/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb6afcf8de2dac709012f3390ab7cd4d2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb6afcf8de2dac709012f3390ab7cd4d2::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
