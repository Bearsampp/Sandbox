<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ToolNgrok
 *
 * This class represents the Ngrok tool module within the Bearsampp application.
 * It extends the abstract `Module` class and provides specific functionalities
 * for managing the Ngrok tool, including loading configurations, setting versions,
 * and retrieving executable paths.
 */
class ToolNgrok extends Module
{
    /**
     * Configuration key for the Ngrok version in the root configuration.
     */
    const ROOT_CFG_VERSION = 'ngrokVersion';

    /**
     * Configuration key for the Ngrok executable in the local configuration.
     */
    const LOCAL_CFG_EXE = 'ngrokExe';

    /**
     * @var string Path to the Ngrok executable.
     */
    private $exe;

    /**
     * Constructor for the ToolNgrok class.
     *
     * @param string $id The identifier for the Ngrok tool.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the Ngrok tool configuration and paths.
     *
     * @param string|null $id The identifier for the Ngrok tool. Defaults to null.
     * @param string|null $type The type of the module. Defaults to null.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::NGROK);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
        }

        if (!$this->enable) {
            Util::logInfo($this->name . ' is not enabled!');
            return;
        }
        if (!is_dir($this->currentPath)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $this->name . ' ' . $this->version, $this->currentPath));
        }
        if (!is_dir($this->symlinkPath)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $this->name . ' ' . $this->version, $this->symlinkPath));
            return;
        }
        if (!is_file($this->bearsamppConf)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->bearsamppConf));
        }
        if (!is_file($this->exe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->exe));
        }
    }

    /**
     * Sets the version of the Ngrok tool and updates the configuration.
     *
     * @param string $version The version to set for the Ngrok tool.
     */
    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    /**
     * Retrieves the path to the Ngrok executable.
     *
     * @return string The path to the Ngrok executable.
     */
    public function getExe() {
        return $this->exe;
    }
}
