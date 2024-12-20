<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplApp
 *
 * This class provides various methods to generate and manage menu items, actions, and sections
 * within the Bearsampp application. It includes functionalities for creating and processing
 * different sections, running actions, and generating menus with various options.
 */
class TplApp
{
    // Constants for item and section identifiers
    public const ITEM_CAPTION = 0;
    public const ITEM_GLYPH = 1;

    public const SECTION_CALL = 0;
    public const SECTION_CONTENT = 1;

    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Processes and generates the main sections of the application.
     *
     * This method generates the main sections of the application, including configuration,
     * services, messages, startup actions, and menu settings.
     *
     * @global object $bearsamppCore Provides access to core functionalities and configurations.
     *
     * @return string The generated sections as a concatenated string.
     */
    public static function process(): string
    {
        global $bearsamppCore;

        return TplAestan::getSectionConfig() . PHP_EOL .
            self::getSectionServices() . PHP_EOL .
            TplAestan::getSectionMessages() . PHP_EOL .
            self::getSectionStartupAction() . PHP_EOL .
            TplAestan::getSectionMenuRightSettings() . PHP_EOL .
            TplAestan::getSectionMenuLeftSettings(APP_TITLE . ' ' . $bearsamppCore->getAppVersion()) . PHP_EOL .
            self::getSectionMenuRight() . PHP_EOL .
            self::getSectionMenuLeft() . PHP_EOL;
    }

    /**
     * Processes and generates a lighter version of the main sections.
     *
     * This method generates a lighter version of the main sections, excluding some menu settings.
     *
     * @return string The generated sections as a concatenated string.
     */
    public static function processLight(): string
    {
        return TplAestan::getSectionConfig() . PHP_EOL .
            self::getSectionServices() . PHP_EOL .
            TplAestan::getSectionMessages() . PHP_EOL .
            self::getSectionStartupAction() . PHP_EOL;
    }

    /**
     * Generates a section name based on the provided name and arguments.
     *
     * @param string $name The base name of the section.
     * @param array $args Optional arguments to include in the section name.
     *
     * @return string The generated section name.
     */
    public static function getSectionName(string $name, array $args = []): string
    {
        return ucfirst($name) . (!empty($args) ? '-' . md5(serialize($args)) : '');
    }

    /**
     * Generates the content of a section based on the provided name, class, and arguments.
     *
     * @param string $name The base name of the section.
     * @param string $class The class name containing the method to generate the section content.
     * @param array $args Optional arguments to pass to the method.
     *
     * @return string The generated section content.
     */
    public static function getSectionContent(string $name, string $class, array $args = []): string
    {
        $baseMethod = 'get' . ucfirst($name);
        return '[' . self::getSectionName($name, $args) . ']' . PHP_EOL .
            call_user_func_array([$class, $baseMethod], $args);
    }

    /**
     * Generates an action string to run a specific action.
     *
     * @param string $action The action to run.
     * @param array $args Optional arguments for the action.
     * @param array $item Optional item details for the action.
     * @param bool $waitUntilTerminated Whether to wait until the action is terminated.
     *
     * @global object $bearsamppRoot Provides access to the root directory of the application.
     * @global object $bearsamppCore Provides access to core functionalities and configurations.
     *
     * @return string The generated action string.
     */
    public static function getActionRun(string $action, array $args = [], array $item = [], bool $waitUntilTerminated = true): string
    {
        global $bearsamppRoot, $bearsamppCore;

        $argImp = '';
        foreach ($args as $arg) {
            $argImp .= ' ' . base64_encode($arg);
        }

        $result = 'Action: run; ' .
            'FileName: "' . $bearsamppCore->getPhpExe(true) . '"; ' .
            'Parameters: "' . Core::IS_ROOT_FILE . ' ' . $action . $argImp . '"; ' .
            'WorkingDir: "' . $bearsamppRoot->getCorePath(true) . '"';

        if (!empty($item)) {
            $result = 'Type: item; ' . $result .
                '; Caption: "' . $item[self::ITEM_CAPTION] . '"' .
                (!empty($item[self::ITEM_GLYPH]) ? '; Glyph: "' . $item[self::ITEM_GLYPH] . '"' : '');
        } elseif ($waitUntilTerminated) {
            $result .= '; Flags: waituntilterminated';
        }

        return $result;
    }

    /**
     * Generates a multi-action string for a specific action.
     *
     * @param string $action The action to run.
     * @param array $args Optional arguments for the action.
     * @param array $item Optional item details for the action.
     * @param bool $disabled Whether the action is disabled.
     * @param string $class The class name containing the method to generate the section content.
     *
     * @return array An array containing the call string and the section content.
     */
    public static function getActionMulti(string $action, array $args = [], array $item = [], bool $disabled = false, string $class = ''): array
    {
        $action = 'action' . ucfirst($action);
        $sectionName = self::getSectionName($action, $args);

        $call = 'Action: multi; Actions: ' . $sectionName;

        if (!empty($item)) {
            $call = 'Type: item; ' . $call .
            '; Caption: "' . $item[self::ITEM_CAPTION] . '"' .
            (!empty($item[self::ITEM_GLYPH]) ? '; Glyph: "' . $item[self::ITEM_GLYPH] . '"' : '');
        } else {
            $call .= '; Flags: waituntilterminated';
        }

        return [$call, self::getSectionContent($action, $class, $args)];
    }

    /**
     * Generates an action string to execute a specific action.
     *
     * @return string The generated action string.
     */
    public static function getActionExec(): string
    {
        return self::getActionRun(Action::EXEC, [], [], false);
    }

    /**
     * Generates a menu with the specified caption, menu name, and class.
     *
     * @param string $caption The caption for the menu.
     * @param string $menu The name of the menu.
     * @param string $class The class name containing the method to generate the menu content.
     *
     * @return array An array containing the call string and the menu content.
     */
    public static function getMenu(string $caption, string $menu, string $class): array
    {
        $menu = 'menu' . ucfirst($menu);

        $call = 'Type: submenu; ' .
            'Caption: "' . $caption . '"; ' .
            'SubMenu: ' . self::getSectionName($menu) . '; ' .
            'Glyph: ' . TplAestan::GLYPH_FOLDER_CLOSE;

        return [$call, self::getSectionContent($menu, $class)];
    }

    /**
     * Generates a menu with the specified caption, menu name, class, and enabled state.
     *
     * @param string $caption The caption for the menu.
     * @param string $menu The name of the menu.
     * @param string $class The class name containing the method to generate the menu content.
     * @param bool $enabled Whether the menu is enabled.
     *
     * @return array An array containing the call string and the menu content.
     */
    public static function getMenuEnable(string $caption, string $menu, string $class, bool $enabled = true): array
    {
        $menu = 'menu' . ucfirst($menu);

        $call = 'Type: submenu; ' .
            'Caption: "' . $caption . '"; ' .
            'SubMenu: ' . self::getSectionName($menu) . '; ' .
            'Glyph: ' . ($enabled ? TplAestan::GLYPH_FOLDER_CLOSE : TplAestan::GLYPH_FOLDER_DISABLED);

        return [$call, self::getSectionContent($menu, $class)];
    }

    /**
     * Generates the services section.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated services section.
     */
    private static function getSectionServices(): string
    {
        global $bearsamppBins;

        $result = '[Services]' . PHP_EOL;
        foreach ($bearsamppBins->getServices() as $service) {
            $result .= 'Name: ' . $service->getName() . PHP_EOL;
        }

        return $result;
    }

    /**
     * Generates the startup action section.
     *
     * @return string The generated startup action section.
     */
    private static function getSectionStartupAction(): string
    {
        return '[StartupAction]' . PHP_EOL .
            self::getActionRun(Action::STARTUP) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL .
            self::getActionRun(Action::CHECK_VERSION) . PHP_EOL .
            self::getActionExec() . PHP_EOL;
    }

    /**
     * Generates the right menu section.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated right menu section.
     */
    private static function getSectionMenuRight(): string
    {
        global $bearsamppLang;

        $tplReload = TplAppReload::process();
        $tplBrowser = TplAppBrowser::process();
        $tplLang = TplAppLang::process();
        $tplLogsVerbose = TplAppLogsVerbose::process();
        $tplLaunchStartup = TplAppLaunchStartup::process();
        $tplExit = TplAppExit::process();

        return
            // Items
            '[Menu.Right]' . PHP_EOL .
            self::getActionRun(Action::ABOUT, null, [$bearsamppLang->getValue(Lang::MENU_ABOUT), TplAestan::GLYPH_ABOUT]) . PHP_EOL .
            self::getActionRun(
                Action::CHECK_VERSION,
                [ActionCheckVersion::DISPLAY_OK],
                [$bearsamppLang->getValue(Lang::MENU_CHECK_UPDATE), TplAestan::GLYPH_UPDATE]
            ) . PHP_EOL .
            TplAestan::getItemLink($bearsamppLang->getValue(Lang::HELP), Util::getWebsiteUrl('faq')) . PHP_EOL .

            TplAestan::getItemSeparator() . PHP_EOL .
            $tplReload[self::SECTION_CALL] . PHP_EOL .
            TplAppClearFolders::process() . PHP_EOL .
            TplAppRebuildIni::process() . PHP_EOL .
            $tplBrowser[self::SECTION_CALL] . PHP_EOL .
            TplAppEditConf::process() . PHP_EOL .

            TplAestan::getItemSeparator() . PHP_EOL .
            $tplLang[self::SECTION_CALL] . PHP_EOL .
            $tplLogsVerbose[self::SECTION_CALL] . PHP_EOL .
            $tplLaunchStartup[self::SECTION_CALL] . PHP_EOL .

            TplAestan::getItemSeparator() . PHP_EOL .
            $tplExit[self::SECTION_CALL] . PHP_EOL .

            // Actions
            PHP_EOL . $tplReload[self::SECTION_CONTENT] . PHP_EOL .
            PHP_EOL . $tplBrowser[self::SECTION_CONTENT] . PHP_EOL .
            PHP_EOL . $tplLang[self::SECTION_CONTENT] .
            PHP_EOL . $tplLogsVerbose[self::SECTION_CONTENT] .
            PHP_EOL . $tplLaunchStartup[self::SECTION_CONTENT] .
            PHP_EOL . $tplExit[self::SECTION_CONTENT] . PHP_EOL;
    }

    /**
     * Generates the left menu section.
     *
     * @global object $bearsamppRoot Provides access to the root directory of the application.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated left menu section.
     */
    private static function getSectionMenuLeft(): string
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppLang;

        $tplNodejs = TplAppNodejs::process();
        $tplApache = TplAppApache::process();
        $tplPhp = TplAppPhp::process();
        $tplMysql = TplAppMysql::process();
        $tplMariadb = TplAppMariadb::process();
        $tplPostgresql = TplAppPostgresql::process();
        $tplMailhog = TplAppMailhog::process();
        $tplMailpit = TplAppMailpit::process();
        $tplMemcached = TplAppMemcached::process();
        $tplFilezilla = TplAppFilezilla::process();
        $tplXlight = TplAppXlight::process();

        $tplLogs = TplAppLogs::process();
        $tplApps = TplAppApps::process();
        $tplTools = TplAppTools::process();

        $tplServices = TplAppServices::process();

        $tplOnline = TplAppOnline::process();

        $httpUrl = 'http://localhost' . ($bearsamppBins->getApache()->getPort() != 80 ? ':' . $bearsamppBins->getApache()->getPort() : '');
        $httpsUrl = 'https://localhost' . ($bearsamppBins->getApache()->getSslPort() != 443 ? ':' . $bearsamppBins->getApache()->getSslPort() : '');

        return
            // Items
            '[Menu.Left]' . PHP_EOL .
            TplAestan::getItemLink($bearsamppLang->getValue(Lang::MENU_LOCALHOST), $httpUrl) . PHP_EOL .
            TplAestan::getItemLink($bearsamppLang->getValue(Lang::MENU_LOCALHOST) . ' (SSL)', $httpsUrl) . PHP_EOL .
            TplAestan::getItemExplore($bearsamppLang->getValue(Lang::MENU_WWW_DIRECTORY), $bearsamppRoot->getWwwPath()) . PHP_EOL .

            //// Bins menus
            TplAestan::getItemSeparator() . PHP_EOL .
            $tplNodejs[self::SECTION_CALL] . PHP_EOL .
            $tplApache[self::SECTION_CALL] . PHP_EOL .
            $tplPhp[self::SECTION_CALL] . PHP_EOL .
            $tplMysql[self::SECTION_CALL] . PHP_EOL .
            $tplMariadb[self::SECTION_CALL] . PHP_EOL .
            $tplPostgresql[self::SECTION_CALL] . PHP_EOL .
            $tplMailhog[self::SECTION_CALL] . PHP_EOL .
            $tplMailpit[self::SECTION_CALL] . PHP_EOL .
            $tplMemcached[self::SECTION_CALL] . PHP_EOL .
            $tplXlight[self::SECTION_CALL] . PHP_EOL .
            $tplFilezilla[self::SECTION_CALL] . PHP_EOL .

            //// Stuff menus
            TplAestan::getItemSeparator() . PHP_EOL .
            $tplLogs[self::SECTION_CALL] . PHP_EOL .
            $tplTools[self::SECTION_CALL] . PHP_EOL .
            $tplApps[self::SECTION_CALL] . PHP_EOL .

            //// Services
            TplAestan::getItemSeparator() . PHP_EOL .
            $tplServices[self::SECTION_CALL] .

            //// Put online/offline
            TplAestan::getItemSeparator() . PHP_EOL .
            $tplOnline[self::SECTION_CALL] . PHP_EOL .

            // Actions
            PHP_EOL . $tplNodejs[self::SECTION_CONTENT] .
            PHP_EOL . $tplApache[self::SECTION_CONTENT] .
            PHP_EOL . $tplPhp[self::SECTION_CONTENT] .
            PHP_EOL . $tplMysql[self::SECTION_CONTENT] .
            PHP_EOL . $tplMariadb[self::SECTION_CONTENT] .
            PHP_EOL . $tplPostgresql[self::SECTION_CONTENT] .
            PHP_EOL . $tplMailhog[self::SECTION_CONTENT] .
            PHP_EOL . $tplMailpit[self::SECTION_CONTENT] .
            PHP_EOL . $tplMemcached[self::SECTION_CONTENT] .
            PHP_EOL . $tplFilezilla[self::SECTION_CONTENT] .
            PHP_EOL . $tplXlight[self::SECTION_CONTENT] .
            PHP_EOL . $tplLogs[self::SECTION_CONTENT] .
            PHP_EOL . $tplTools[self::SECTION_CONTENT] .
            PHP_EOL . $tplApps[self::SECTION_CONTENT] .
            PHP_EOL . $tplServices[self::SECTION_CONTENT] .
            PHP_EOL . $tplOnline[self::SECTION_CONTENT];
    }
}
