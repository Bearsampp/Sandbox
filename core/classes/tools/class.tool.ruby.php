<?php

class ToolRuby extends Module
{
    const ROOT_CFG_VERSION = 'rubyVersion';

    const LOCAL_CFG_EXE = 'rubyExe';
    const LOCAL_CFG_CONSOLE_EXE = 'rubyConsoleExe';

    private $exe;
    private $consoleExe;

    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::RUBY);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->consoleExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONSOLE_EXE];
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
        if (!is_file($this->consoleExe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->consoleExe));
        }
    }

    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    public function getExe() {
        return $this->exe;
    }

    public function getConsoleExe() {
        return $this->consoleExe;
    }
}
