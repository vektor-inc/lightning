<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit96ae27b311d604cd2c9e153dfb1dbfce
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit96ae27b311d604cd2c9e153dfb1dbfce', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit96ae27b311d604cd2c9e153dfb1dbfce', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit96ae27b311d604cd2c9e153dfb1dbfce::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
