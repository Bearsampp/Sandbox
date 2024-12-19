<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit74e55f7e18b4a4585e2b8b8489feadfc
{
    public static $files = array (
        '9b38cf48e83f5d8f60375221cd213eee' => __DIR__ . '/..' . '/phpstan/phpstan/bootstrap.php',
        '38143a9afc50997d55e4815db8489d1c' => __DIR__ . '/..' . '/rector/rector/bootstrap.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit74e55f7e18b4a4585e2b8b8489feadfc::$classMap;

        }, null, ClassLoader::class);
    }
}
