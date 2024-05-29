<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Config manages configuration settings for the application.
 */
class Config
{
    const CFG_MAX_LOGS_ARCHIVES = 'maxLogsArchives';
    const CFG_LOGS_VERBOSE = 'logsVerbose';
    const CFG_LANG = 'lang';
    const CFG_TIMEZONE = 'timezone';
    const CFG_NOTEPAD = 'notepad';
    const CFG_SCRIPTS_TIMEOUT = 'scriptsTimeout';

    const CFG_DEFAULT_LANG = 'defaultLang';
    const CFG_HOSTNAME = 'hostname';
    const CFG_BROWSER = 'browser';
    const CFG_ONLINE = 'online';
    const CFG_LAUNCH_STARTUP = 'launchStartup';

    const ENABLED = 1;
    const DISABLED = 0;

    const VERBOSE_SIMPLE = 0;
    const VERBOSE_REPORT = 1;
    const VERBOSE_DEBUG = 2;
    const VERBOSE_TRACE = 3;

    private $raw;

    /**
     * Constructor for the Config class.
     * Loads configuration settings from an ini file and sets the default timezone.
     */
    public function __construct()
    {
        global $bearsamppRoot;

        $this->raw = parse_ini_file($bearsamppRoot->getConfigFilePath());
        date_default_timezone_set($this->getTimezone());
    }

    /**
     * Retrieves a configuration value by key.
     *
     * @param string $key The configuration key.
     * @return mixed The value associated with the configuration key.
     */
    public function getRaw($key)
    {
        return $this->raw[$key];
    }

    /**
     * Updates a single configuration setting.
     *
     * @param string $key The configuration key to update.
     * @param mixed $value The new value for the configuration key.
     */
    public function replace($key, $value)
    {
        $this->replaceAll(array($key => $value));
    }

    /**
     * Updates multiple configuration settings.
     *
     * @param array $params An associative array of configuration keys and their new values.
     */
    public function replaceAll($params)
    {
        global $bearsamppRoot;

        Util::logTrace('Replace config:');
        $content = file_get_contents($bearsamppRoot->getConfigFilePath());
        foreach ($params as $key => $value) {
            $content = preg_replace('/^' . $key . '\s=\s.*/m', $key . ' = ' . '"' . $value.'"', $content, -1, $count);
            Util::logTrace('## ' . $key . ': ' . $value . ' (' . $count . ' replacements done)');
            $this->raw[$key] = $value;
        }

        file_put_contents($bearsamppRoot->getConfigFilePath(), $content);
    }

    /**
     * Retrieves the configured language.
     *
     * @return string The configured language.
     */
    public function getLang()
    {
        return $this->raw[self::CFG_LANG];
    }

    /**
     * Retrieves the default language setting.
     *
     * @return string The default language.
     */
    public function getDefaultLang()
    {
        return $this->raw[self::CFG_DEFAULT_LANG];
    }

    /**
     * Retrieves the configured timezone.
     *
     * @return string The configured timezone.
     */
    public function getTimezone()
    {
        return $this->raw[self::CFG_TIMEZONE];
    }

    /**
     * Checks if the application is set to online mode.
     *
     * @return bool True if online mode is enabled, false otherwise.
     */
    public function isOnline()
    {
        return $this->raw[self::CFG_ONLINE] == self::ENABLED;
    }

    /**
     * Checks if the application is set to launch at startup.
     *
     * @return bool True if launch at startup is enabled, false otherwise.
     */
    public function isLaunchStartup()
    {
        return $this->raw[self::CFG_LAUNCH_STARTUP] == self::ENABLED;
    }

    /**
     * Retrieves the configured browser.
     *
     * @return string The configured browser.
     */
    public function getBrowser()
    {
        return $this->raw[self::CFG_BROWSER];
    }

    /**
     * Retrieves the configured hostname.
     *
     * @return string The configured hostname.
     */
    public function getHostname()
    {
        return $this->raw[self::CFG_HOSTNAME];
    }

    /**
     * Retrieves the configured script timeout value.
     *
     * @return int The script timeout value in seconds.
     */
    public function getScriptsTimeout()
    {
        return intval($this->raw[self::CFG_SCRIPTS_TIMEOUT]);
    }

    /**
     * Retrieves the configured notepad application.
     *
     * @return string The configured notepad application.
     */
    public function getNotepad()
    {
        return $this->raw[self::CFG_NOTEPAD];
    }

    /**
     * Retrieves the verbosity level for logs.
     *
     * @return int The verbosity level.
     */
    public function getLogsVerbose()
    {
        return intval($this->raw[self::CFG_LOGS_VERBOSE]);
    }

    /**
     * Retrieves the maximum number of log archives.
     *
     * @return int The maximum number of log archives.
     */
    public function getMaxLogsArchives()
    {
        return intval($this->raw[self::CFG_MAX_LOGS_ARCHIVES]);
    }
}
