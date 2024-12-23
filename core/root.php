<?php

/*
 * Bearsampp
 *
 * Updated for compatibility with PHP 8.2.26.
 *
 * Copyright (c) 2022 - 2024 Bearsampp
 * License: GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * GitHub: https://github.com/Bearsampp
 */

/**
 * Defines constants used throughout the Bearsampp application.
 *
 * These constants are used for application metadata, GitHub integration,
 * and other configuration settings.
 */
define('APP_AUTHOR_NAME', 'N6REJ');
define('APP_TITLE', 'Bearsampp');
define('APP_WEBSITE', 'https://bearsampp.com');
define('APP_LICENSE', 'GPL3 License');
define('APP_GITHUB_USER', 'Bearsampp');
define('APP_GITHUB_REPO', 'Bearsampp');
define('APP_GITHUB_USERAGENT', 'Bearsampp');
define('APP_GITHUB_LATEST_URL', 'https://api.github.com/repos/' . APP_GITHUB_USER . '/' . APP_GITHUB_REPO . '/releases/latest');
define('RETURN_TAB', "\t"); // Using double quotes for escape sequences

/**
 * Includes the Root class file and creates an instance of Root.
 * Registers the root directory of the application.
 *
 * Updated to ensure compatibility with PHP 8.2.26 by declaring all class properties.
 */
require_once __DIR__ . '/classes/class.root.php'; // Using __DIR__ for portability

$bearsamppRoot = new Root(__DIR__);
$bearsamppRoot->register();

/**
 * Creates an instance of the Action class and processes the action based on command line arguments.
 *
 * Updated for compatibility with PHP 8.2.26. Ensured that all class properties are properly declared.
 */
$bearsamppAction = new Action();
$bearsamppAction->process();

/**
 * Checks if the current script is being run as the root user and stops loading if true.
 *
 * This is to ensure that the application does not run with root privileges for security reasons.
 */
if ($bearsamppRoot->isRoot()) {
    Util::stopLoading();
}

/**
 * Creates an instance of the LangProc class to handle language-specific settings.
 * Retrieves the locale setting from the language data.
 *
 * Updated to be compatible with PHP 8.2.26 by ensuring all dynamic properties are declared.
 */
$langProc = new LangProc();
$locale = $langProc->getValue('locale');
