<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppMysql
 *
 * This class provides methods to generate and manage menu items, actions, and sections
 * related to MySQL within the Bearsampp application. It includes functionalities for
 * enabling/disabling MySQL, switching versions, changing ports, managing services, and debugging.
 */
class TplAppMysql
{
    // Constants for menu and action identifiers
    public const MENU = 'mysql';
    public const MENU_VERSIONS = 'mysqlVersions';
    public const MENU_SERVICE = 'mysqlService';
    public const MENU_DEBUG = 'mysqlDebug';

    public const ACTION_ENABLE = 'enableMysql';
    public const ACTION_SWITCH_VERSION = 'switchMysqlVersion';
    public const ACTION_CHANGE_PORT = 'changeMysqlPort';
    public const ACTION_CHANGE_ROOT_PWD = 'changeMysqlRootPwd';
    public const ACTION_INSTALL_SERVICE = 'installMysqlService';
    public const ACTION_REMOVE_SERVICE = 'removeMysqlService';

    /**
     * Processes and generates the MySQL menu.
     *
     * This method generates the MySQL menu and determines if MySQL is enabled.
     *
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return array The generated MySQL menu.
     */
    public static function process(): array
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable(
            $bearsamppLang->getValue(Lang::MYSQL),
            self::MENU,
            static::class,
            $bearsamppBins->getMysql()->isEnable()
        );
    }

    /**
     * Generates the MySQL menu items and actions.
     *
     * This method creates menu items and actions for MySQL, including download links, enabling/disabling,
     * version switching, service management, debugging, and configuration file access.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     * @global \BearsamppTools $bearsamppTools Provides access to various tools used in the application.
     *
     * @return string The generated MySQL menu items and actions.
     */
    public static function getMenuMysql(): string
    {
        global $bearsamppBins, $bearsamppLang, $bearsamppTools;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getMysql()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
            $bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
            Util::getWebsiteUrl('module/mysql', '#releases'),
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

            // Debug
            $tplDebug = TplApp::getMenu(
                $bearsamppLang->getValue(Lang::DEBUG),
                self::MENU_DEBUG,
                static::class
            );
            $resultItems .= $tplDebug[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplDebug[TplApp::SECTION_CONTENT];

            // Console
            $resultItems .= TplAestan::getItemConsoleZ(
                $bearsamppLang->getValue(Lang::CONSOLE),
                TplAestan::GLYPH_CONSOLEZ,
                $bearsamppTools->getConsoleZ()->getTabTitleMysql()
            ) . PHP_EOL;

            // Conf
            $resultItems .= TplAestan::getItemNotepad(
                basename($bearsamppBins->getMysql()->getConf()),
                $bearsamppBins->getMysql()->getConf()
            ) . PHP_EOL;

            // Errors log
            $resultItems .= TplAestan::getItemNotepad(
                $bearsamppLang->getValue(Lang::MENU_ERROR_LOGS),
                $bearsamppBins->getMysql()->getErrorLog()
            ) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Generates the MySQL versions menu.
     *
     * This method creates menu items and actions for switching between different MySQL versions.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated MySQL versions menu items and actions.
     */
    public static function getMenuMysqlVersions(): string
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getMysql()->getVersionList() as $version) {
            $tplSwitchMysqlVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION,
                [$version],
                [$version, $version === $bearsamppBins->getMysql()->getVersion() ? TplAestan::GLYPH_CHECK : ''],
                false,
                static::class
            );

            // Item
            $items .= $tplSwitchMysqlVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchMysqlVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to enable or disable MySQL.
     *
     * This method creates the action string for enabling or disabling MySQL.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param int $enable The enable/disable flag (1 for enable, 0 for disable).
     * @return string The generated action string for enabling/disabling MySQL.
     */
    public static function getActionEnableMysql(int $enable): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::ENABLE,
            [$bearsamppBins->getMysql()->getName(), $enable]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }

    /**
     * Generates the action to switch MySQL version.
     *
     * This method creates the action string for switching to a different MySQL version.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param string $version The version to switch to.
     * @return string The generated action string for switching MySQL version.
     */
    public static function getActionSwitchMysqlVersion(string $version): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::SWITCH_VERSION,
            [$bearsamppBins->getMysql()->getName(), $version]
        ) . PHP_EOL . TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the MySQL service menu.
     *
     * This method creates menu items and actions for managing MySQL services, including starting, stopping,
     * restarting, changing ports, and managing root passwords.
     *
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated MySQL service menu items and actions.
     */
    public static function getMenuMysqlService(): string
    {
        global $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT,
            null,
            [$bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK],
            false,
            static::class
        );

        $isInstalled = $bearsamppBins->getMysql()->getService()->isInstalled();

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getMysql()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($bearsamppBins->getMysql()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($bearsamppBins->getMysql()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT,
                [$bearsamppBins->getMysql()->getName(), $bearsamppBins->getMysql()->getPort()],
                [sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getMysql()->getPort()), TplAestan::GLYPH_LIGHT]
            ) . PHP_EOL .
            $tplChangePort[TplApp::SECTION_CALL] . PHP_EOL;

        $tplChangeRootPwd = null;
        if ($isInstalled) {
            $tplChangeRootPwd = TplApp::getActionMulti(
                self::ACTION_CHANGE_ROOT_PWD,
                null,
                [$bearsamppLang->getValue(Lang::MENU_CHANGE_ROOT_PWD), TplAestan::GLYPH_PASSWORD],
                !$isInstalled,
                static::class
            );

            $result .= $tplChangeRootPwd[TplApp::SECTION_CALL] . PHP_EOL;
        }

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

        $result .= $tplChangePort[TplApp::SECTION_CONTENT] . PHP_EOL .
            ($tplChangeRootPwd !== null ? $tplChangeRootPwd[TplApp::SECTION_CONTENT] . PHP_EOL : '');

        return $result;
    }

    /**
     * Generates the MySQL debug menu.
     *
     * This method creates menu items and actions for debugging MySQL, including checking version,
     * variables, and syntax.
     *
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated MySQL debug menu items and actions.
     */
    public static function getMenuMysqlDebug(): string
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::DEBUG_MYSQL,
            [BinMysql::CMD_VERSION],
            [$bearsamppLang->getValue(Lang::DEBUG_MYSQL_VERSION), TplAestan::GLYPH_DEBUG]
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_MYSQL,
            [BinMysql::CMD_VARIABLES],
            [$bearsamppLang->getValue(Lang::DEBUG_MYSQL_VARIABLES), TplAestan::GLYPH_DEBUG]
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_MYSQL,
            [BinMysql::CMD_SYNTAX_CHECK],
            [$bearsamppLang->getValue(Lang::DEBUG_MYSQL_SYNTAX_CHECK), TplAestan::GLYPH_DEBUG]
        ) . PHP_EOL;
    }

    /**
     * Generates the action to change MySQL port.
     *
     * This method creates the action string for changing the MySQL port.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action string for changing MySQL port.
     */
    public static function getActionChangeMysqlPort(): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::CHANGE_PORT,
            [$bearsamppBins->getMysql()->getName()]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }

    /**
     * Generates the action to change MySQL root password.
     *
     * This method creates the action string for changing the MySQL root password.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action string for changing MySQL root password.
     */
    public static function getActionChangeMysqlRootPwd(): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::CHANGE_DB_ROOT_PWD,
            [$bearsamppBins->getMysql()->getName()]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }

    /**
     * Generates the action to install MySQL service.
     *
     * This method creates the action string for installing the MySQL service.
     *
     * @return string The generated action string for installing MySQL service.
     */
    public static function getActionInstallMysqlService(): string
    {
        return TplApp::getActionRun(
            Action::SERVICE,
            [BinMysql::SERVICE_NAME, ActionService::INSTALL]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }

    /**
     * Generates the action to remove MySQL service.
     *
     * This method creates the action string for removing the MySQL service.
     *
     * @return string The generated action string for removing MySQL service.
     */
    public static function getActionRemoveMysqlService(): string
    {
        return TplApp::getActionRun(
            Action::SERVICE,
            [BinMysql::SERVICE_NAME, ActionService::REMOVE]
        ) . PHP_EOL . TplAppReload::getActionReload();
    }
}
