<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

use Bearsampp\Core\Classes\Lang;
use Bearsampp\Core\Classes\WinBinder;
use Bearsampp\Core\Classes\Core;
use Bearsampp\Core\Classes\Util;

/**
 * Class ActionCheckVersion
 *
 * This class is responsible for checking the current version of the application and displaying a window
 * with the latest version information if an update is available. It also handles the user interaction with
 * the window, such as clicking on links or buttons.
 *
 * @package Bearsampp
 */
class ActionCheckVersion
{
    public const DISPLAY_OK = 'displayOk';

    private mixed $wbWindow;
    private mixed $wbImage;
    private mixed $wbLinkChangelog;
    private mixed $wbLinkFull;
    private mixed $wbBtnOk;

    private string $currentVersion;
    private string $latestVersion;
    private string $githubLatestVersionUrl;

    /**
     * Constructor for the ActionCheckVersion class.
     *
     * @param array $args Command line arguments passed to the script.
     */
    public function __construct(array $args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppWinbinder, $appGithubHeader;

        if (!file_exists($bearsamppCore->getExec())) {
            Util::startLoading();
            $this->currentVersion = $bearsamppCore->getAppVersion();

            // Assuming getLatestVersion now returns an array with version and URL
            $githubVersionData = Util::getLatestVersion(APP_GITHUB_LATEST_URL);

            if ($githubVersionData !== null && isset($githubVersionData['version'], $githubVersionData['html_url'])) {
                $githubLatestVersion = $githubVersionData['version'];
                $this->githubLatestVersionUrl = $githubVersionData['html_url']; // URL of the latest version
                if (version_compare($this->currentVersion, $githubLatestVersion, '<')) {
                    $this->showVersionUpdateWindow($bearsamppLang, $bearsamppWinbinder, $bearsamppCore, $githubLatestVersion);
                } elseif (!empty($args[0]) && $args[0] === self::DISPLAY_OK) {
                    $this->showVersionOkMessageBox($bearsamppLang, $bearsamppWinbinder);
                }
            }
        }
    }

    /**
     * Displays a window with the latest version information.
     *
     * @param Lang $lang Language processor instance.
     * @param WinBinder $winbinder WinBinder instance for creating windows and controls.
     * @param Core $core Core instance for accessing application resources.
     * @param string $githubLatestVersion The latest version available on GitHub.
     */
    private function showVersionUpdateWindow(Lang $lang, WinBinder $winbinder, Core $core, string $githubLatestVersion): void
    {
        $labelFullLink = $lang->getValue(Lang::DOWNLOAD) . ' ' . APP_TITLE . ' ' . $githubLatestVersion;

        $winbinder->reset();
        $this->wbWindow = $winbinder->createAppWindow($lang->getValue(Lang::CHECK_VERSION_TITLE), 380, 170, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

        $winbinder->createLabel($this->wbWindow, $lang->getValue(Lang::CHECK_VERSION_AVAILABLE_TEXT), 80, 35, 370, 120);

        $this->wbLinkFull = $winbinder->createHyperLink($this->wbWindow, $labelFullLink, 80, 87, 200, 20, WBC_LINES | WBC_RIGHT);

        $this->wbBtnOk = $winbinder->createButton($this->wbWindow, $lang->getValue(Lang::BUTTON_OK), 280, 103);
        $this->wbImage = $winbinder->drawImage($this->wbWindow, $core->getResourcesPath() . '/homepage/img/about.bmp');

        Util::stopLoading();
        $winbinder->setHandler($this->wbWindow, $this, 'processWindow');
        $winbinder->mainLoop();
        $winbinder->reset();
    }

    /**
     * Displays a message box indicating that the current version is the latest.
     *
     * @param Lang $lang Language processor instance.
     * @param WinBinder $winbinder WinBinder instance for creating windows and controls.
     */
    private function showVersionOkMessageBox(Lang $lang, WinBinder $winbinder): void
    {
        Util::stopLoading();
        $winbinder->messageBoxInfo(
            $lang->getValue(Lang::CHECK_VERSION_LATEST_TEXT),
            $lang->getValue(Lang::CHECK_VERSION_TITLE)
        );
    }

    /**
     * Processes window events and handles user interactions.
     *
     * @param mixed $window The window resource.
     * @param int $id The control ID that triggered the event.
     * @param mixed $ctrl The control resource.
     * @param mixed $param1 Additional parameter 1.
     * @param mixed $param2 Additional parameter 2.
     */
    public function processWindow(mixed $window, int $id, mixed $ctrl, mixed $param1, mixed $param2): void
    {
        global $bearsamppConfig, $bearsamppWinbinder;

        switch ($id) {
            case $this->wbLinkFull[WinBinder::CTRL_ID]:
                $latestVersionInfo = Util::getLatestVersion(APP_GITHUB_LATEST_URL);
                if ($latestVersionInfo && isset($latestVersionInfo['html_url'])) {
                    $browserPath = $bearsamppConfig->getBrowser();
                    if (!$bearsamppWinbinder->exec($browserPath, $latestVersionInfo['html_url'])) {
                        Util::logError("Failed to open browser at path: $browserPath with URL: " . $latestVersionInfo['html_url']);
                    }
                } else {
                    Util::logError("Failed to retrieve latest version info or 'html_url' not set.");
                }
                break;
            case IDCLOSE:
            case $this->wbBtnOk[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
            default:
                Util::logError("Unhandled window control ID: $id");
        }
    }
}
