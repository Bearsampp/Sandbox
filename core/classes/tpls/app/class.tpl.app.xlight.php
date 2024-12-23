<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class TplAppXlight
{
    public const MENU = 'xlight';
    public const MENU_VERSIONS = 'xlightVersions';
    public const MENU_SERVICE = 'xlightService';

    public const ACTION_ENABLE = 'enableXlight';
    public const ACTION_SWITCH_VERSION = 'switchXlightVersion';
    public const ACTION_CHANGE_PORT = 'changeXlightPort';
    public const ACTION_INSTALL_SERVICE = 'installXlightService';
    public const ACTION_REMOVE_SERVICE = 'removeXlightService';

    /**
     * Processes the Xlight menu.
     *
     * This method generates the menu for enabling or disabling Xlight.
     * It uses the global language object to retrieve the localized string for Xlight.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return array The generated menu for enabling or disabling Xlight.
     */
    public static function process(): array
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable(
            $bearsamppLang->getValue(Lang::XLIGHT),
            self::MENU,
            static::class,
            $bearsamppBins->getXlight()->isEnable()
        );
    }

    /**
     * Generates the Xlight menu.
     *
     * This method creates the menu items and associated actions for Xlight, including options for downloading,
     * enabling, switching versions, managing the service, and viewing logs.
     *
     * @global object $bearsamppRoot Provides access to the root path of the application.
     * @global object $bearsamppConfig Provides access to the application configuration.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated Xlight menu items and actions.
     */
    public static function getMenuXlight(): string
    {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppBins, $bearsamppLang;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getXlight()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
            $bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
            Util::getWebsiteUrl('module/xlight', '#releases'),
            false,
            TplAestan::GLYPH_BROWSER
        ) . PHP_EOL;

        // Enable
        $tplEnable = TplApp::getActionMulti(
            self::ACTION_ENABLE,
            [$isEnabled ? Config::DISABLED : Config::ENABLED],
            [$bearsamppLang->getValue(Lang::MENU_ENABLE), $isEnabled ? TplAestan::GLYPH_CHECK : ''],
            false,
            static::class
        );
        $resultItems .= $tplEnable[TplApp::SECTION_CALL] . PHP_EOL;
        $resultActions .= $tplEnable[TplApp::SECTION_CONTENT] . PHP_EOL;

        if ($isEnabled) {
            $resultItems .= TplAestan::getItemSeparator() . PHP_EOL;

            // Versions
            $tplVersions = TplApp::getMenu(
                $bearsamppLang->getValue(Lang::VERSIONS),
                self::MENU_VERSIONS,
                static::class
            );
            $resultItems .= $tplVersions[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplVersions[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Service
            $tplService = TplApp::getMenu(
                $bearsamppLang->getValue(Lang::SERVICE),
                self::MENU_SERVICE,
                static::class
            );
            $resultItems .= $tplService[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplService[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Log
            $resultItems .= TplAestan::getItemNotepad(
                $bearsamppLang->getValue(Lang::MENU_LOGS),
                $bearsamppBins->getXlight()->getLog()
            ) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Generates the Xlight versions menu.
     *
     * This method creates the menu items and associated actions for switching between different versions of Xlight.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated Xlight versions menu items and actions.
     */
    public static function getMenuXlightVersions(): string
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getXlight()->getVersionList() as $version) {
            $tplSwitchXlightVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION,
                [$version],
                [$version, $version === $bearsamppBins->getXlight()->getVersion() ? TplAestan::GLYPH_CHECK : ''],
                false,
                static::class
            );

            // Item
            $items .= $tplSwitchXlightVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchXlightVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to enable or disable Xlight.
     *
     * This method creates the action string for enabling or disabling Xlight and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param int $enable The enable flag (1 to enable, 0 to disable).
     * @return string The generated action string for enabling or disabling Xlight.
     */
    public static function getActionEnableXlight(int $enable): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::ENABLE,
            [$bearsamppBins->getXlight()->getName(), $enable]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }

    /**
     * Generates the action to switch the Xlight version.
     *
     * This method creates the action string for switching the Xlight version and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param string $version The version to switch to.
     * @return string The generated action string for switching the Xlight version.
     */
    public static function getActionSwitchXlightVersion(string $version): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::SWITCH_VERSION,
            [$bearsamppBins->getXlight()->getName(), $version]
        ) . PHP_EOL . TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the Xlight service menu.
     *
     * This method creates the menu items and associated actions for managing the Xlight service, including starting, stopping,
     * restarting, changing ports, and installing or removing the service.
     *
     * @global object $bearsamppRoot Provides access to the root path of the application.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated Xlight service menu items and actions.
     */
    public static function getMenuXlightService(): string
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT,
            null,
            [$bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK],
            false,
            static::class
        );

        $isInstalled = $bearsamppBins->getXlight()->getService()->isInstalled();

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getXlight()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($bearsamppBins->getXlight()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($bearsamppBins->getXlight()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT,
                [$bearsamppBins->getXlight()->getName(), $bearsamppBins->getXlight()->getPort()],
                [sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getXlight()->getPort()), TplAestan::GLYPH_LIGHT]
            ) . PHP_EOL .
            $tplChangePort[TplApp::SECTION_CALL] . PHP_EOL .
            TplAestan::getItemNotepad(
                $bearsamppLang->getValue(Lang::MENU_UPDATE_ENV_PATH),
                $bearsamppRoot->getRootPath() . '/nssmEnvPaths.dat'
            ) . PHP_EOL;

        if (!$isInstalled) {
            $tplInstallService = TplApp::getActionMulti(
                self::ACTION_INSTALL_SERVICE,
                null,
                [$bearsamppLang->getValue(Lang::MENU_INSTALL_SERVICE), TplAestan::GLYPH_SERVICE_INSTALL],
                $isInstalled,
                static::class
            );

            $result .= $tplInstallService[TplApp::SECTION_CALL] . PHP_EOL . PHP_EOL .
                $tplInstallService[TplApp::SECTION_CONTENT] . PHP_EOL;
        } else {
            $tplRemoveService = TplApp::getActionMulti(
                self::ACTION_REMOVE_SERVICE,
                null,
                [$bearsamppLang->getValue(Lang::MENU_REMOVE_SERVICE), TplAestan::GLYPH_SERVICE_REMOVE],
                !$isInstalled,
                static::class
            );

            $result .= $tplRemoveService[TplApp::SECTION_CALL] . PHP_EOL . PHP_EOL .
                $tplRemoveService[TplApp::SECTION_CONTENT] . PHP_EOL;
        }

        $result .= $tplChangePort[TplApp::SECTION_CONTENT] . PHP_EOL;

        return $result;
    }

    /**
     * Generates the action to change the Xlight port.
     *
     * This method creates the action string for changing the Xlight port and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action string for changing the Xlight port.
     */
    public static function getActionChangeXlightPort(): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::CHANGE_PORT,
            [$bearsamppBins->getXlight()->getName()]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }

    /**
     * Generates the action to install the Xlight service.
     *
     * This method creates the action string for installing the Xlight service and includes a command to reload the application.
     *
     * @return string The generated action string for installing the Xlight service.
     */
    public static function getActionInstallXlightService(): string
    {
        return TplApp::getActionRun(
            Action::SERVICE,
            [BinXlight::SERVICE_NAME, ActionService::INSTALL]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }

    /**
     * Generates the action to remove the Xlight service.
     *
     * This method creates the action string for removing the Xlight service and includes a command to reload the application.
     *
     * @return string The generated action string for removing the Xlight service.
     */
    public static function getActionRemoveXlightService(): string
    {
        return TplApp::getActionRun(
            Action::SERVICE,
            [BinXlight::SERVICE_NAME, ActionService::REMOVE]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }
}
