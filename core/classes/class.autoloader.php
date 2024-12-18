<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

declare(strict_types=1);

/**
 * Class Autoloader
 *
 * Handles the autoloading of classes within the Bearsampp application.
 * It registers itself with the SPL autoload stack and loads classes based on naming conventions.
 */
class Autoloader
{
    /**
     * Initializes the Autoloader object.
     */
    public function __construct()
    {
    }

    /**
     * Loads the specified class file based on the class name.
     *
     * @param string $class The name of the class to load.
     * @return bool True if the class file was successfully loaded, false otherwise.
     */
    public function load(string $class): bool
    {
        global $bearsamppRoot;

        $class    = strtolower($class);
        $rootPath = $bearsamppRoot->getCorePath();

        $file = $rootPath . '/classes/class.' . $class . '.php';

        if (Util::startWith($class, 'bin')) {
            $classFileName = ($class !== 'bins') ? substr_replace($class, '.', 3, 0) : $class;
            $file          = $rootPath . '/classes/bins/class.' . $classFileName . '.php';
        } elseif (Util::startWith($class, 'tool')) {
            $classFileName = ($class !== 'tools') ? substr_replace($class, '.', 4, 0) : $class;
            $file          = $rootPath . '/classes/tools/class.' . $classFileName . '.php';
        } elseif (Util::startWith($class, 'app')) {
            $classFileName = ($class !== 'apps') ? substr_replace($class, '.', 3, 0) : $class;
            $file          = $rootPath . '/classes/apps/class.' . $classFileName . '.php';
        } elseif (Util::startWith($class, 'action')) {
            $classFileName = ($class !== 'action') ? substr_replace($class, '.', 6, 0) : $class;
            $file          = $rootPath . '/classes/actions/class.' . $classFileName . '.php';
        } elseif (Util::startWith($class, 'tplapp') && $class !== 'tplapp') {
            $classFileName = substr_replace($class, '.', 3, 0);
            $classFileName = substr_replace($classFileName, '.', 7, 0);
            $file          = $rootPath . '/classes/tpls/app/class.' . $classFileName . '.php';
        } elseif (Util::startWith($class, 'tpl')) {
            $classFileName = ($class !== 'tpls') ? substr_replace($class, '.', 3, 0) : $class;
            $file          = $rootPath . '/classes/tpls/class.' . $classFileName . '.php';
        }

        if (!file_exists($file)) {
            return false;
        }

        require_once $file;
        return true;
    }

    /**
     * Registers the autoloader with the SPL autoload stack.
     *
     * @return bool True on success, false on failure.
     */
    public function register(): bool
    {
        return spl_autoload_register([$this, 'load']);
    }

    /**
     * Unregisters the autoloader from the SPL autoload stack.
     *
     * @return bool True on success, false on failure.
     */
    public function unregister(): bool
    {
        return spl_autoload_unregister([$this, 'load']);
    }
}
