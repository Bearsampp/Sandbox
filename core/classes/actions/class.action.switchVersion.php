<?php
/*
 *
 *  * Copyright (c) 2021-2024 Bearsampp
 *  * License:  GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class ActionSwitchVersion
 * Handles the switching of versions for various services and binaries in the Bearsampp application.
 */
class ActionSwitchVersion
{
    private Splash $bearsamppSplash;
    private string $version;
    private $bin; // Assuming $bin is an object, specify the class if known
    private string $currentVersion;
    private $service; // Assuming $service is an object, specify the class if known
    private bool $changePort;
    private string $boxTitle;

    const GAUGE_SERVICES = 1;
    const GAUGE_OTHERS = 7;

    /**
     * ActionSwitchVersion constructor.
     * Initializes the class with the provided arguments and sets up the splash screen.
     *
     * @param   array  $args  Command line arguments for switching versions.
     */
    public function __construct(array $args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        if (!empty($args[0]) && !empty($args[1])) {
            $this->pathsToScan = [];
            $this->version     = $args[1];

            if ($args[0] === $bearsamppBins->getApache()->getName()) {
                $this->bin            = $bearsamppBins->getApache();
                $this->currentVersion = $bearsamppBins->getApache()->getVersion();
                $this->service        = $bearsamppBins->getApache()->getService();
                $this->changePort     = true;
                $folderList           = Util::getFolderList($bearsamppBins->getApache()->getRootPath());
                foreach ($folderList as $folder) {
                    $this->pathsToScan[] = [
                        'path'      => $bearsamppBins->getApache()->getRootPath() . '/' . $folder,
                        'includes'  => ['.ini', '.conf'],
                        'recursive' => true
                    ];
                }
            }
            // Similar changes for other conditions...

            $this->boxTitle = sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_TITLE), $this->bin->getName(), $this->version);

            // Start splash screen
            $this->bearsamppSplash = new Splash();
            $this->bearsamppSplash->init(
                $this->boxTitle,
                self::GAUGE_SERVICES * count($bearsamppBins->getServices()) + self::GAUGE_OTHERS,
                $this->boxTitle
            );

            $bearsamppWinbinder->setHandler($this->bearsamppSplash->getWbWindow(), $this, 'processWindow', 1000);
            $bearsamppWinbinder->mainLoop();
            $bearsamppWinbinder->reset();
        }
    }

    /**
     * Processes the window events for the splash screen.
     *
     * @param   mixed  $window  The window handle.
     * @param   int    $id      The event ID.
     * @param   mixed  $ctrl    The control handle.
     * @param   mixed  $param1  The first parameter.
     * @param   mixed  $param2  The second parameter.
     */
    public function processWindow($window, int $id, $ctrl, $param1, $param2): void
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        if ($this->version === $this->currentVersion) {
            $bearsamppWinbinder->messageBoxWarning(sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_SAME_ERROR), $this->bin->getName(), $this->version), $this->boxTitle);
            $bearsamppWinbinder->destroyWindow($window);
            return;
        }

        // scan folder
        $this->bearsamppSplash->incrProgressBar();
        if (!empty($this->pathsToScan)) {
            Util::changePath(Util::getFilesToScan($this->pathsToScan));
        }

        // switch
        $this->bearsamppSplash->incrProgressBar();
        if ($this->bin->switchVersion($this->version, true) === false) {
            $this->bearsamppSplash->incrProgressBar(self::GAUGE_SERVICES * count($bearsamppBins->getServices()) + self::GAUGE_OTHERS);
            $bearsamppWinbinder->destroyWindow($window);
            return;
        }

        // stop service
        if ($this->service !== null) {
            $binName = $this->bin->getName() === $bearsamppLang->getValue(Lang::PHP) ? $bearsamppLang->getValue(Lang::APACHE) : $this->bin->getName();
            $this->bearsamppSplash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::STOP_SERVICE_TITLE), $binName));
            $this->bearsamppSplash->incrProgressBar();
            $this->service->stop();
        } else {
            $this->bearsamppSplash->incrProgressBar();
        }

        // reload config
        $this->bearsamppSplash->setTextLoading($bearsamppLang->getValue(Lang::SWITCH_VERSION_RELOAD_CONFIG));
        $this->bearsamppSplash->incrProgressBar();
        Root::loadConfig();

        // reload bins
        $this->bearsamppSplash->setTextLoading($bearsamppLang->getValue(Lang::SWITCH_VERSION_RELOAD_BINS));
        $this->bearsamppSplash->incrProgressBar();
        $bearsamppBins->reload();

        // change port
        if ($this->changePort) {
            $this->bin->reload();
            $this->bin->changePort($this->bin->getPort());
        }

        // start service
        if ($this->service !== null) {
            $binName = $this->bin->getName() === $bearsamppLang->getValue(Lang::PHP) ? $bearsamppLang->getValue(Lang::APACHE) : $this->bin->getName();
            $this->bearsamppSplash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::START_SERVICE_TITLE), $binName));
            $this->bearsamppSplash->incrProgressBar();
            $this->service->start();
        } else {
            $this->bearsamppSplash->incrProgressBar();
        }

        $this->bearsamppSplash->incrProgressBar(self::GAUGE_SERVICES * count($bearsamppBins->getServices()) + 1);
        $bearsamppWinbinder->messageBoxInfo(
            sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_OK), $this->bin->getName(), $this->version),
            $this->boxTitle
        );
        $bearsamppWinbinder->destroyWindow($window);

        $this->bearsamppSplash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_REGISTRY), Registry::APP_BINS_REG_ENTRY));
        $this->bearsamppSplash->incrProgressBar(2);
        Util::setAppBinsRegKey(Util::getAppBinsRegKey(false));

        $this->bearsamppSplash->setTextLoading($bearsamppLang->getValue(Lang::SWITCH_VERSION_RESET_SERVICES));
        foreach ($bearsamppBins->getServices() as $sName => $service) {
            $this->bearsamppSplash->incrProgressBar();
            $service->delete();
        }

        $bearsamppWinbinder->messageBoxInfo(
            sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_OK_RESTART), $this->bin->getName(), $this->version, APP_TITLE),
            $this->boxTitle
        );

        $bearsamppCore->setExec(ActionExec::RESTART);

        $bearsamppWinbinder->destroyWindow($window);
    }
}
