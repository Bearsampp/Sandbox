<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Action handles the execution of various actions based on command line arguments.
 */
class Action
{
    // Constants for different actions
    public const ABOUT = 'about';
    public const ADD_ALIAS = 'addAlias';
    public const ADD_VHOST = 'addVhost';
    public const CHANGE_BROWSER = 'changeBrowser';
    public const CHANGE_DB_ROOT_PWD = 'changeDbRootPwd';
    public const CHANGE_PORT = 'changePort';
    public const CHECK_PORT = 'checkPort';
    public const CHECK_VERSION = 'checkVersion';
    public const CLEAR_FOLDERS = 'clearFolders';
    public const DEBUG_APACHE = 'debugApache';
    public const DEBUG_MARIADB = 'debugMariadb';
    public const DEBUG_MYSQL = 'debugMysql';
    public const DEBUG_POSTGRESQL = 'debugPostgresql';
    public const EDIT_ALIAS = 'editAlias';
    public const EDIT_VHOST = 'editVhost';
    public const ENABLE = 'enable';
    public const EXEC = 'exec';
    public const GEN_SSL_CERTIFICATE = 'genSslCertificate';
    public const LAUNCH_STARTUP = 'launchStartup';
    public const MANUAL_RESTART = 'manualRestart';
    public const LOADING = 'loading';
    public const QUIT = 'quit';
    public const REBUILD_INI = 'rebuildIni';
    public const REFRESH_REPOS = 'refreshRepos';
    public const REFRESH_REPOS_STARTUP = 'refreshReposStartup';
    public const RELOAD = 'reload';
    public const RESTART = 'restart';
    public const SERVICE = 'service';
    public const STARTUP = 'startup';
    public const SWITCH_APACHE_MODULE = 'switchApacheModule';
    public const SWITCH_LANG = 'switchLang';
    public const SWITCH_LOGS_VERBOSE = 'switchLogsVerbose';
    public const SWITCH_PHP_EXTENSION = 'switchPhpExtension';
    public const SWITCH_PHP_PARAM = 'switchPhpParam';
    public const SWITCH_ONLINE = 'switchOnline';
    public const SWITCH_VERSION = 'switchVersion';

    public const EXT = 'ext';

    /**
     * @var mixed Holds the current action instance.
     */
    private $current;

    /**
     * Constructor for the Action class.
     * Initializes the Action object.
     */
    public function __construct()
    {
        // Initialization code can be added here if needed
    }

    /**
     * Processes the action based on command line arguments.
     *
     * This method checks if an action exists in the command line arguments,
     * cleans the argument, constructs the action class name, and then
     * initializes the action class with the provided arguments.
     *
     * @return void
     */
    public function process(): void
    {
        if ($this->exists()) {
            $action = Util::cleanArgv(1);
            $actionClass = 'Action' . ucfirst($action);

            $args = [];
            foreach ($_SERVER['argv'] as $key => $arg) {
                if ($key > 1) {
                    $args[] = $action === self::EXT ? $arg : base64_decode($arg);
                }
            }

            $this->current = null;
            if (class_exists($actionClass)) {
                Util::logDebug('Start ' . $actionClass);
                $this->current = new $actionClass($args);
            }
        }
    }

    /**
     * Calls a specific action by name with optional arguments.
     *
     * This method constructs the action class name from the provided action name,
     * checks if the class exists, and then initializes the action class with the
     * provided arguments.
     *
     * @param string $actionName The name of the action to call.
     * @param mixed $actionArgs Optional arguments for the action.
     * @return void
     */
    public function call(string $actionName, mixed $actionArgs = null): void
    {
        $actionClass = 'Action' . ucfirst($actionName);
        if (class_exists($actionClass)) {
            Util::logDebug('Start ' . $actionClass);
            new $actionClass($actionArgs);
        }
    }

    /**
     * Checks if the action exists in the command line arguments.
     *
     * This method verifies if the command line arguments contain an action
     * by checking the presence and non-emptiness of the second argument.
     *
     * @return bool Returns true if the action exists, false otherwise.
     */
    public function exists(): bool
    {
        return isset($_SERVER['argv'])
            && isset($_SERVER['argv'][1])
            && !empty($_SERVER['argv'][1]);
    }
}
