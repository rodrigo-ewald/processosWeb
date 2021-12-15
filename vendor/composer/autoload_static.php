<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit153e6f3825c3403c64ee02aae0038a58
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'saes\\' => 5,
        ),
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'saes\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-classes/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Slim' => 
            array (
                0 => __DIR__ . '/..' . '/slim/slim',
            ),
        ),
        'R' => 
        array (
            'Rain' => 
            array (
                0 => __DIR__ . '/..' . '/rain/raintpl/library',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit153e6f3825c3403c64ee02aae0038a58::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit153e6f3825c3403c64ee02aae0038a58::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit153e6f3825c3403c64ee02aae0038a58::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit153e6f3825c3403c64ee02aae0038a58::$classMap;

        }, null, ClassLoader::class);
    }
}
