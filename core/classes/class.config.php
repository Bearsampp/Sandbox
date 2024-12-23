<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Config
 *
 * This class handles the configuration settings for the Bearsampp application.
 * It reads the configuration from an INI file and provides methods to access and modify these settings.
 */
class Config
{
    public const CFG_MAX_LOGS_ARCHIVES = 'maxLogsArchives';
    public const CFG_LOGS_VERBOSE = 'logsVerbose';
    public const CFG_LANG = 'lang';
    public const CFG_TIMEZONE = 'timezone';
    public const CFG_NOTEPAD = 'notepad';
    public const CFG_SCRIPTS_TIMEOUT = 'scriptsTimeout';
    public const DOWNLOAD_ID = 'DownloadId';

    public const CFG_DEFAULT_LANG = 'defaultLang';
    public const CFG_HOSTNAME = 'hostname';
    public const CFG_BROWSER = 'browser';
    public const CFG_ONLINE = 'online';
    public const CFG_LAUNCH_STARTUP = 'launchStartup';

    public const ENABLED = 1;
    public const DISABLED = 0;

    public const VERBOSE_SIMPLE = 0;
    public const VERBOSE_REPORT = 1;
    public const VERBOSE_DEBUG = 2;
    public const VERBOSE_TRACE = 3;

    private array $raw;

    /**
     * Constructs a Config object and initializes the configuration settings.
     * Reads the configuration from the INI file and sets the default timezone.
     */
    public function __construct()
    {
        global $bearsamppRoot;

        // Set current timezone to match what's in .conf
        $this->raw = parse_ini_file($bearsamppRoot->getConfigFilePath());
        date_default_timezone_set($this->getTimezone());
    }

    /**
     * Retrieves the raw configuration value for the specified key.
     *
     * @param string $key The configuration key.
     * @return mixed The configuration value.
     */
    public function getRaw(string $key): mixed
    {
        return $this->raw[$key] ?? null;
    }

    /**
     * Replaces a single configuration value with the specified key and value.
     *
     * @param string $key The configuration key.
     * @param mixed $value The new configuration value.
     * @return void
     */
    public function replace(string $key, mixed $value): void
    {
        $this->replaceAll([$key => $value]);
    }

    /**
     * Replaces multiple configuration values with the specified key-value pairs.
     *
     * @param array $params An associative array of key-value pairs to replace.
     * @return void
     */
    public function replaceAll(array $params): void
    {
        global $bearsamppRoot;

        Util::logTrace('Replace config:');
        $content = file_get_contents($bearsamppRoot->getConfigFilePath());
        foreach ($params as $key => $value) {
            $content = preg_replace('/^' . preg_quote($key, '/') . '\s*=\s*.*/m', $key . ' = "' . $value . '"', $content, -1, $count);
            Util::logTrace('## ' . $key . ': ' . $value . ' (' . $count . ' replacements done)');
            $this->raw[$key] = $value;
        }

        file_put_contents($bearsamppRoot->getConfigFilePath(), $content);
    }

    /**
     * Retrieves the language setting from the configuration.
     *
     * @return string|null The language setting or null if not set.
     */
    public function getLang(): ?string
    {
        return $this->raw[self::CFG_LANG] ?? null;
    }

    /**
     * Retrieves the default language setting from the configuration.
     *
     * @return string|null The default language setting or null if not set.
     */
    public function getDefaultLang(): ?string
    {
        return $this->raw[self::CFG_DEFAULT_LANG] ?? null;
    }

    /**
     * Retrieves the timezone setting from the configuration.
     *
     * @return string|null The timezone setting or null if not set.
     */
    public function getTimezone(): ?string
    {
        return $this->raw[self::CFG_TIMEZONE] ?? null;
    }

    /**
     * Retrieves the license key from the configuration.
     *
     * @return string|null The license key or null if not set.
     */
    public function getDownloadId(): ?string
    {
        return $this->raw[self::DOWNLOAD_ID] ?? null;
    }

    /**
     * Checks if the application is set to be online.
     *
     * @return bool True if online, false otherwise.
     */
    public function isOnline(): bool
    {
        return ($this->raw[self::CFG_ONLINE] ?? self::DISABLED) == self::ENABLED;
    }

    /**
     * Checks if the application is set to launch at startup.
     *
     * @return bool True if set to launch at startup, false otherwise.
     */
    public function isLaunchStartup(): bool
    {
        return ($this->raw[self::CFG_LAUNCH_STARTUP] ?? self::DISABLED) == self::ENABLED;
    }

    /**
     * Retrieves the browser setting from the configuration.
     *
     * @return string|null The browser setting or null if not set.
     */
    public function getBrowser(): ?string
    {
        return $this->raw[self::CFG_BROWSER] ?? null;
    }

    /**
     * Retrieves the hostname setting from the configuration.
     *
     * @return string|null The hostname setting or null if not set.
     */
    public function getHostname(): ?string
    {
        return $this->raw[self::CFG_HOSTNAME] ?? null;
    }

    /**
     * Retrieves the scripts timeout setting from the configuration.
     *
     * @return int The scripts timeout setting.
     */
    public function getScriptsTimeout(): int
    {
        return intval($this->raw[self::CFG_SCRIPTS_TIMEOUT] ?? 0);
    }

    /**
     * Retrieves the notepad setting from the configuration.
     *
     * @return string|null The notepad setting or null if not set.
     */
    public function getNotepad(): ?string
    {
        return $this->raw[self::CFG_NOTEPAD] ?? null;
    }

    /**
     * Retrieves the logs verbosity setting from the configuration.
     *
     * @return int The logs verbosity setting.
     */
    public function getLogsVerbose(): int
    {
        return intval($this->raw[self::CFG_LOGS_VERBOSE] ?? self::VERBOSE_SIMPLE);
    }

    /**
     * Retrieves the maximum logs archives setting from the configuration.
     *
     * @return int The maximum logs archives setting.
     */
    public function getMaxLogsArchives(): int
    {
        return intval($this->raw[self::CFG_MAX_LOGS_ARCHIVES] ?? 0);
    }
}
