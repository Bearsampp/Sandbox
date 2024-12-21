<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppMailhog
 *
 * This class provides methods to generate menus and actions for managing Mailhog within the Bearsampp application.
 * It includes functionalities for enabling/disabling Mailhog, switching versions, changing ports, and managing the service.
 */
class TplAppMailhog
{
    public const MENU = 'mailhog';
    public const MENU_VERSIONS = 'mailhogVersions';
    public const MENU_SERVICE = 'mailhogService';

    public const ACTION_ENABLE = 'enableMailhog';
    public const ACTION_SWITCH_VERSION = 'switchMailhogVersion';
    public const ACTION_CHANGE_PORT = 'changeMailhogPort';
    public const ACTION_INSTALL_SERVICE = 'installMailhogService';
    public const ACTION_REMOVE_SERVICE = 'removeMailhogService';

    /**
     * Processes the Mailhog menu.
     *
     * This method generates the menu for enabling or disabling Mailhog.
     * It uses the global language object to retrieve the localized string for Mailhog.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return array The generated menu for enabling or disabling Mailhog.
     */
    public static function process(): array
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable(
            $bearsamppLang->getValue(Lang::MAILHOG),
            self::MENU,
            static::class,
            $bearsamppBins->getMailhog()->isEnable()
        );
    }

    /**
     * Generates the Mailhog menu.
     *
     * This method creates the menu items and associated actions for Mailhog, including options for downloading,
     * enabling, switching versions, managing the service, and viewing logs.
     *
     * @global object $bearsamppRoot Provides access to the root path of the application.
     * @global object $bearsamppConfig Provides access to the application configuration.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated Mailhog menu items and actions.
     */
    public static function getMenuMailhog(): string
    {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppBins, $bearsamppLang;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getMailhog()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
            $bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
            Util::getWebsiteUrl('module/mailhog', '#releases'),
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
            $tplVersions = TplApp::getMenu($bearsamppLang->getValue(Lang::VERSIONS), self::MENU_VERSIONS, static::class);
            $resultItems .= $tplVersions[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplVersions[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Service
            $tplService = TplApp::getMenu($bearsamppLang->getValue(Lang::SERVICE), self::MENU_SERVICE, static::class);
            $resultItems .= $tplService[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplService[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Web page
            $resultItems .= TplAestan::getItemExe(
                $bearsamppLang->getValue(Lang::MAILHOG),
                $bearsamppConfig->getBrowser(),
                TplAestan::GLYPH_WEB_PAGE,
                $bearsamppRoot->getLocalUrl() . ':' . $bearsamppBins->getMailhog()->getUiPort()
            ) . PHP_EOL;

            // Log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_LOGS), $bearsamppBins->getMailhog()->getLog()) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Generates the Mailhog versions menu.
     *
     * This method creates the menu items and associated actions for switching between different versions of Mailhog.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated Mailhog versions menu items and actions.
     */
    public static function getMenuMailhogVersions(): string
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getMailhog()->getVersionList() as $version) {
            $tplSwitchMailhogVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION,
                [$version],
                [$version, $version === $bearsamppBins->getMailhog()->getVersion() ? TplAestan::GLYPH_CHECK : ''],
                false,
                static::class
            );

            // Item
            $items .= $tplSwitchMailhogVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchMailhogVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to enable or disable Mailhog.
     *
     * This method creates the action string for enabling or disabling Mailhog and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param int $enable The enable flag (1 to enable, 0 to disable).
     * @return string The generated action string for enabling or disabling Mailhog.
     */
    public static function getActionEnableMailhog(int $enable): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::ENABLE,
            [$bearsamppBins->getMailhog()->getName(), $enable]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }

    /**
     * Generates the action to switch the Mailhog version.
     *
     * This method creates the action string for switching the Mailhog version and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param string $version The version to switch to.
     * @return string The generated action string for switching the Mailhog version.
     */
    public static function getActionSwitchMailhogVersion(string $version): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::SWITCH_VERSION,
            [$bearsamppBins->getMailhog()->getName(), $version]
        ) . PHP_EOL . TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the Mailhog service menu.
     *
     * This method creates the menu items and associated actions for managing the Mailhog service, including starting, stopping,
     * restarting, changing ports, and installing or removing the service.
     *
     * @global object $bearsamppRoot Provides access to the root path of the application.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated Mailhog service menu items and actions.
     */
    public static function getMenuMailhogService(): string
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT,
            null,
            [$bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK],
            false,
            static::class
        );

        $isInstalled = $bearsamppBins->getMailhog()->getService()->isInstalled();

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getMailhog()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($bearsamppBins->getMailhog()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($bearsamppBins->getMailhog()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT,
                [$bearsamppBins->getMailhog()->getName(), $bearsamppBins->getMailhog()->getSmtpPort()],
                [sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getMailhog()->getSmtpPort()), TplAestan::GLYPH_LIGHT]
            ) . PHP_EOL .
            $tplChangePort[TplApp::SECTION_CALL] . PHP_EOL .
            TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_UPDATE_ENV_PATH), $bearsamppRoot->getRootPath() . '/nssmEnvPaths.dat') . PHP_EOL;

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
     * Generates the action to change the Mailhog port.
     *
     * This method creates the action string for changing the Mailhog port and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action string for changing the Mailhog port.
     */
    public static function getActionChangeMailhogPort(): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::CHANGE_PORT,
            [$bearsamppBins->getMailhog()->getName()]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }

    /**
     * Generates the action to install the Mailhog service.
     *
     * This method creates the action string for installing the Mailhog service and includes a command to reload the application.
     *
     * @return string The generated action string for installing the Mailhog service.
     */
    public static function getActionInstallMailhogService(): string
    {
        return TplApp::getActionRun(
            Action::SERVICE,
            [BinMailhog::SERVICE_NAME, ActionService::INSTALL]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }

    /**
     * Generates the action to remove the Mailhog service.
     *
     * This method creates the action string for removing the Mailhog service and includes a command to reload the application.
     *
     * @return string The generated action string for removing the Mailhog service.
     */
    public static function getActionRemoveMailhogService(): string
    {
        return TplApp::getActionRun(
            Action::SERVICE,
            [BinMailhog::SERVICE_NAME, ActionService::REMOVE]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }
}
