<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Autoloader class for dynamically loading classes based on their names.
 * This class handles the inclusion of class files from specific directories
 * based on naming conventions.
 */
class Autoloader
{
    /**
     * Constructs the Autoloader object.
     */
    public function __construct()
    {
    }

    /**
     * Attempts to load a class file based on the class name.
     * The method modifies the class name to match the expected file path and name,
     * then includes the file if it exists.
     *
     * @param string $class The name of the class to load.
     * @return bool Returns true if the file exists and was included, false otherwise.
     */
    public function load($class)
    {
        global $bearsamppRoot;

        $class = strtolower($class);
        $rootPath = $bearsamppRoot->getCorePath();

        $file = $rootPath . '/classes/class.' . $class . '.php';
        if (Util::startWith($class, 'bin')) {
            $class = $class != 'bins' ? substr_replace($class, '.', 3, 0) : $class;
            $file = $rootPath . '/classes/bins/class.' . $class . '.php';
        } elseif (Util::startWith($class, 'tool')) {
            $class = $class != 'tools' ? substr_replace($class, '.', 4, 0) : $class;
            $file = $rootPath . '/classes/tools/class.' . $class . '.php';
        } elseif (Util::startWith($class, 'app')) {
            $class = $class != 'apps' ? substr_replace($class, '.', 3, 0) : $class;
            $file = $rootPath . '/classes/apps/class.' . $class . '.php';
        } elseif (Util::startWith($class, 'action')) {
            $class = $class != 'action' ? substr_replace($class, '.', 6, 0) : $class;
            $file = $rootPath . '/classes/actions/class.' . $class . '.php';
        } elseif (Util::startWith($class, 'tplapp') && $class != 'tplapp') {
            $class = substr_replace(substr_replace($class, '.', 3, 0), '.', 7, 0);
            $file = $rootPath . '/classes/tpls/app/class.' . $class . '.php';
        } elseif (Util::startWith($class, 'tpl')) {
            $class = $class != 'tpls' ? substr_replace($class, '.', 3, 0) : $class;
            $file = $rootPath . '/classes/tpls/class.' . $class . '.php';
        }

        if (!file_exists($file)) {
            return false;
        }

        require_once $file;
        return true;
    }

    /**
     * Registers this Autoloader instance on the SPL autoload stack.
     *
     * @return bool Returns true on success, false on failure.
     */
    public function register()
    {
        return spl_autoload_register(array($this, 'load'));
    }

    /**
     * Unregisters this Autoloader instance from the SPL autoload stack.
     *
     * @return bool Returns true on success, false on failure.
     */
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'load'));
    }
}
