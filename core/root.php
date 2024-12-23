<?php

/*
 * Copyright (c) 2022 - 2024 Bearsampp
 * License: GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Defines constants used throughout the Bearsampp application.
 */
define('APP_AUTHOR_NAME', 'N6REJ');
define('APP_TITLE', 'Bearsampp');
define('APP_WEBSITE', 'https://bearsampp.com');
define('APP_LICENSE', 'GPL3 License');
define('APP_GITHUB_USER', 'Bearsampp');
define('APP_GITHUB_REPO', 'Bearsampp');
define('APP_GITHUB_USERAGENT', 'Bearsampp');
define('APP_GITHUB_LATEST_URL', 'https://api.github.com/repos/' . APP_GITHUB_USER . '/' . APP_GITHUB_REPO . '/releases/latest');
define('RETURN_TAB', "\t"); // Use double quotes for escape sequences

/**
 * Includes the Root class file and creates an instance of Root.
 * Registers the root directory of the application.
 */
require_once __DIR__ . '/classes/class.root.php'; // Use __DIR__ instead of dirname(__FILE__)
$bearsamppRoot = new Root(__DIR__);
$bearsamppRoot->register();

/**
 * Creates an instance of the Action class and processes the action based on command line arguments.
 */
$bearsamppAction = new Action();
$bearsamppAction->process();

/**
 * Checks if the current user has root privileges and stops loading if true.
 */
if ($bearsamppRoot->isRoot()) {
    Util::stopLoading();
}

/**
 * Creates an instance of the LangProc class to handle language-specific settings.
 * Retrieves the locale setting from the language data.
 */
$langProc = new LangProc();
$locale = $langProc->getValue('locale');
