<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit87fb660ac669c77c29ba8186c67f7702
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Core\\' => 5,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Core',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit87fb660ac669c77c29ba8186c67f7702::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit87fb660ac669c77c29ba8186c67f7702::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit87fb660ac669c77c29ba8186c67f7702::$classMap;

        }, null, ClassLoader::class);
    }
}
