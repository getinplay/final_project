<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit75575c88fa0f0efa18777fe9f6f5f4b2
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

        spl_autoload_register(array('ComposerAutoloaderInit75575c88fa0f0efa18777fe9f6f5f4b2', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit75575c88fa0f0efa18777fe9f6f5f4b2', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit75575c88fa0f0efa18777fe9f6f5f4b2::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
