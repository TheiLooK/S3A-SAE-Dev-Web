<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0bb65f8698a073c04e40355d8aaa174d
{
    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'touiteur\\app\\conf\\' => 18,
            'touiteur\\app\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'touiteur\\app\\conf\\' => 
        array (
            0 => __DIR__ . '/../..' . '/conf',
        ),
        'touiteur\\app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/classes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0bb65f8698a073c04e40355d8aaa174d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0bb65f8698a073c04e40355d8aaa174d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0bb65f8698a073c04e40355d8aaa174d::$classMap;

        }, null, ClassLoader::class);
    }
}
