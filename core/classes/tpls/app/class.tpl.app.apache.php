<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppApache
 *
 * This class provides methods to generate menu items and actions for managing Apache services
 * within the Bearsampp application. It includes functionalities for enabling/disabling Apache,
 * switching versions, changing ports, managing modules, aliases, and virtual hosts.
 */
class TplAppApache
{
    // Constants for menu and action identifiers
    public const MENU = 'apache';
    public const MENU_VERSIONS = 'apacheVersions';
    public const MENU_SERVICE = 'apacheService';
    public const MENU_DEBUG = 'apacheDebug';
    public const MENU_MODULES = 'apacheModules';
    public const MENU_ALIAS = 'apacheAlias';
    public const MENU_VHOSTS = 'apacheVhosts';

    public const ACTION_ENABLE = 'enableApache';
    public const ACTION_SWITCH_VERSION = 'switchApacheVersion';
    public const ACTION_CHANGE_PORT = 'changeApachePort';
    public const ACTION_INSTALL_SERVICE = 'installApacheService';
    public const ACTION_REMOVE_SERVICE = 'removeApacheService';
    public const ACTION_SWITCH_MODULE = 'switchApacheModule';
    public const ACTION_ADD_ALIAS = 'addAlias';
    public const ACTION_EDIT_ALIAS = 'editAlias';
    public const ACTION_ADD_VHOST = 'addVhost';
    public const ACTION_EDIT_VHOST = 'editVhost';

    /**
     * Generates the main Apache menu with options to enable/disable Apache and access submenus.
     *
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for Apache.
     */
    public static function process(): string
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable(
            $bearsamppLang->getValue(Lang::APACHE),
            self::MENU,
            static::class,
            $bearsamppBins->getApache()->isEnable()
        );
    }

    /**
     * Generates the Apache menu with options for versions, service, debug, modules, aliases, and virtual hosts.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated menu items and actions for Apache.
     */
    public static function getMenuApache(): string
    {
        global $bearsamppBins, $bearsamppLang;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getApache()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
            $bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
            Util::getWebsiteUrl('module/apache', '#releases'),
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

            // Debug
            $tplDebug = TplApp::getMenu($bearsamppLang->getValue(Lang::DEBUG), self::MENU_DEBUG, static::class);
            $resultItems .= $tplDebug[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplDebug[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Modules
            $tplModules = TplApp::getMenu($bearsamppLang->getValue(Lang::MODULES), self::MENU_MODULES, static::class);
            $resultItems .= $tplModules[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplModules[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Alias
            $tplAlias = TplApp::getMenu($bearsamppLang->getValue(Lang::ALIASES), self::MENU_ALIAS, static::class);
            $resultItems .= $tplAlias[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplAlias[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Vhosts
            $tplVhosts = TplApp::getMenu($bearsamppLang->getValue(Lang::VIRTUAL_HOSTS), self::MENU_VHOSTS, static::class);
            $resultItems .= $tplVhosts[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplVhosts[TplApp::SECTION_CONTENT];

            // Conf
            $resultItems .= TplAestan::getItemNotepad(basename($bearsamppBins->getApache()->getConf()), $bearsamppBins->getApache()->getConf()) . PHP_EOL;

            // Access log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_ACCESS_LOGS), $bearsamppBins->getApache()->getAccessLog()) . PHP_EOL;

            // Rewrite log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_REWRITE_LOGS), $bearsamppBins->getApache()->getRewriteLog()) . PHP_EOL;

            // Error log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_ERROR_LOGS), $bearsamppBins->getApache()->getErrorLog()) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Generates the Apache versions menu with options to switch between different versions.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for Apache versions.
     */
    public static function getMenuApacheVersions(): string
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getApache()->getVersionList() as $version) {
            $glyph = '';
            $apachePhpModule = $bearsamppBins->getPhp()->getApacheModule($version);
            if ($apachePhpModule === false) {
                $glyph = TplAestan::GLYPH_WARNING;
            } elseif ($version === $bearsamppBins->getApache()->getVersion()) {
                $glyph = TplAestan::GLYPH_CHECK;
            }

            $tplSwitchApacheVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION,
                [$version],
                [$version, $glyph],
                false,
                static::class
            );

            // Item
            $items .= $tplSwitchApacheVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchApacheVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to enable or disable Apache.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param int $enable The flag to enable (1) or disable (0) Apache.
     * @return string The generated action to enable or disable Apache.
     */
    public static function getActionEnableApache(int $enable): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, [$bearsamppBins->getApache()->getName(), $enable]) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to switch the Apache version.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param string $version The version to switch to.
     * @return string The generated action to switch the Apache version.
     */
    public static function getActionSwitchApacheVersion(string $version): string
    {
        global $bearsamppBins;
        return TplApp::getActionRun(Action::SWITCH_VERSION, [$bearsamppBins->getApache()->getName(), $version]) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the Apache service menu with options to start, stop, restart, and manage the service.
     *
     * @global \BearsamppRoot $bearsamppRoot Provides access to the root path of the application.
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for Apache service.
     */
    public static function getMenuApacheService(): string
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT,
            null,
            [$bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK],
            false,
            static::class
        );

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getApache()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($bearsamppBins->getApache()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($bearsamppBins->getApache()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT,
                [$bearsamppBins->getApache()->getName(), $bearsamppBins->getApache()->getPort()],
                [sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getApache()->getPort()), TplAestan::GLYPH_LIGHT]
            ) . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT,
                [$bearsamppBins->getApache()->getName(), $bearsamppBins->getApache()->getSslPort(), true],
                [sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getApache()->getSslPort()) . ' (SSL)', TplAestan::GLYPH_RED_LIGHT]
            ) . PHP_EOL .
            $tplChangePort[TplApp::SECTION_CALL] . PHP_EOL .
            TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_UPDATE_ENV_PATH), $bearsamppRoot->getRootPath() . '/nssmEnvPaths.dat') . PHP_EOL;

        $isInstalled = $bearsamppBins->getApache()->getService()->isInstalled();
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
     * Generates the action to change the Apache port.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action to change the Apache port.
     */
    public static function getActionChangeApachePort(): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_PORT, [$bearsamppBins->getApache()->getName()]) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to install the Apache service.
     *
     * @return string The generated action to install the Apache service.
     */
    public static function getActionInstallApacheService(): string
    {
        return TplApp::getActionRun(Action::SERVICE, [BinApache::SERVICE_NAME, ActionService::INSTALL]) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to remove the Apache service.
     *
     * @return string The generated action to remove the Apache service.
     */
    public static function getActionRemoveApacheService(): string
    {
        return TplApp::getActionRun(Action::SERVICE, [BinApache::SERVICE_NAME, ActionService::REMOVE]) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the Apache debug menu with options to run various debug commands.
     *
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated menu items and actions for Apache debug.
     */
    public static function getMenuApacheDebug(): string
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::DEBUG_APACHE,
            [BinApache::CMD_VERSION_NUMBER],
            [$bearsamppLang->getValue(Lang::DEBUG_APACHE_VERSION_NUMBER), TplAestan::GLYPH_DEBUG]
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE,
            [BinApache::CMD_COMPILE_SETTINGS],
            [$bearsamppLang->getValue(Lang::DEBUG_APACHE_COMPILE_SETTINGS), TplAestan::GLYPH_DEBUG]
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE,
            [BinApache::CMD_COMPILED_MODULES],
            [$bearsamppLang->getValue(Lang::DEBUG_APACHE_COMPILED_MODULES), TplAestan::GLYPH_DEBUG]
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE,
            [BinApache::CMD_CONFIG_DIRECTIVES],
            [$bearsamppLang->getValue(Lang::DEBUG_APACHE_CONFIG_DIRECTIVES), TplAestan::GLYPH_DEBUG]
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE,
            [BinApache::CMD_VHOSTS_SETTINGS],
            [$bearsamppLang->getValue(Lang::DEBUG_APACHE_VHOSTS_SETTINGS), TplAestan::GLYPH_DEBUG]
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE,
            [BinApache::CMD_LOADED_MODULES],
            [$bearsamppLang->getValue(Lang::DEBUG_APACHE_LOADED_MODULES), TplAestan::GLYPH_DEBUG]
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE,
            [BinApache::CMD_SYNTAX_CHECK],
            [$bearsamppLang->getValue(Lang::DEBUG_APACHE_SYNTAX_CHECK), TplAestan::GLYPH_DEBUG]
        ) . PHP_EOL;
    }

    /**
     * Generates the Apache modules menu with options to switch modules on or off.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for Apache modules.
     */
    public static function getMenuApacheModules(): string
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getApache()->getModulesFromConf() as $module => $switch) {
            $tplSwitchApacheModule = TplApp::getActionMulti(
                self::ACTION_SWITCH_MODULE,
                [$module, $switch],
                [$module, ($switch === ActionSwitchApacheModule::SWITCH_ON ? TplAestan::GLYPH_CHECK : '')],
                false,
                static::class
            );

            // Item
            $items .= $tplSwitchApacheModule[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchApacheModule[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to switch an Apache module on or off.
     *
     * @param string $module The module to switch.
     * @param string $switch The current switch state of the module.
     * @return string The generated action to switch the Apache module.
     */
    public static function getActionSwitchApacheModule(string $module, string $switch): string
    {
        $switch = $switch === ActionSwitchApacheModule::SWITCH_OFF ? ActionSwitchApacheModule::SWITCH_ON : ActionSwitchApacheModule::SWITCH_OFF;
        return TplApp::getActionRun(Action::SWITCH_APACHE_MODULE, [$module, $switch]) . PHP_EOL .
            TplService::getActionRestart(BinApache::SERVICE_NAME) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the Apache Alias menu with options to add and edit aliases.
     *
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for Apache aliases.
     */
    public static function getMenuApacheAlias(): string
    {
        global $bearsamppLang, $bearsamppBins;

        $tplAddAlias = TplApp::getActionMulti(
            self::ACTION_ADD_ALIAS,
            null,
            [$bearsamppLang->getValue(Lang::MENU_ADD_ALIAS), TplAestan::GLYPH_ADD],
            false,
            static::class
        );

        // Items
        $items = $tplAddAlias[TplApp::SECTION_CALL] . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL;

        // Actions
        $actions = PHP_EOL . $tplAddAlias[TplApp::SECTION_CONTENT];

        foreach ($bearsamppBins->getApache()->getAlias() as $alias) {
            $tplEditAlias = TplApp::getActionMulti(
                self::ACTION_EDIT_ALIAS,
                [$alias],
                [sprintf($bearsamppLang->getValue(Lang::MENU_EDIT_ALIAS), $alias), TplAestan::GLYPH_FILE],
                false,
                static::class
            );

            // Items
            $items .= $tplEditAlias[TplApp::SECTION_CALL] . PHP_EOL;

            // Actions
            $actions .= PHP_EOL . PHP_EOL . $tplEditAlias[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to add an Apache alias.
     *
     * @return string The generated action to add an Apache alias.
     */
    public static function getActionAddAlias(): string
    {
        return TplApp::getActionRun(Action::ADD_ALIAS) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to edit an Apache alias.
     *
     * @param string $alias The alias to edit.
     * @return string The generated action to edit an Apache alias.
     */
    public static function getActionEditAlias(string $alias): string
    {
        return TplApp::getActionRun(Action::EDIT_ALIAS, [$alias]) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the Apache Virtual Hosts (Vhosts) menu with options to add and edit Vhosts.
     *
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for Apache Vhosts.
     */
    public static function getMenuApacheVhosts(): string
    {
        global $bearsamppLang, $bearsamppBins;

        $tplAddVhost = TplApp::getActionMulti(
            self::ACTION_ADD_VHOST,
            null,
            [$bearsamppLang->getValue(Lang::MENU_ADD_VHOST), TplAestan::GLYPH_ADD],
            false,
            static::class
        );

        // Items
        $items = $tplAddVhost[TplApp::SECTION_CALL] . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL;

        // Actions
        $actions = PHP_EOL . $tplAddVhost[TplApp::SECTION_CONTENT];

        foreach ($bearsamppBins->getApache()->getVhosts() as $vhost) {
            $tplEditVhost = TplApp::getActionMulti(
                self::ACTION_EDIT_VHOST,
                [$vhost],
                [sprintf($bearsamppLang->getValue(Lang::MENU_EDIT_VHOST), $vhost), TplAestan::GLYPH_FILE],
                false,
                static::class
            );

            // Items
            $items .= $tplEditVhost[TplApp::SECTION_CALL] . PHP_EOL;

            // Actions
            $actions .= PHP_EOL . PHP_EOL . $tplEditVhost[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to add an Apache Virtual Host (Vhost).
     *
     * @return string The generated action to add an Apache Vhost.
     */
    public static function getActionAddVhost(): string
    {
        return TplApp::getActionRun(Action::ADD_VHOST) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to edit an Apache Virtual Host (Vhost).
     *
     * @param string $vhost The Vhost to edit.
     * @return string The generated action to edit an Apache Vhost.
     */
    public static function getActionEditVhost(string $vhost): string
    {
        return TplApp::getActionRun(Action::EDIT_VHOST, [$vhost]) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
