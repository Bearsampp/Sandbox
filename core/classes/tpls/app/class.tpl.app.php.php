<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppPhp
 *
 * This class provides methods to generate menu items and actions for managing PHP services
 * within the Bearsampp application. It includes functionalities for enabling/disabling PHP,
 * switching versions, changing settings, and managing extensions.
 */
class TplAppPhp
{
    public const MENU = 'php';
    public const MENU_VERSIONS = 'phpVersions';
    public const MENU_SETTINGS = 'phpSettings';
    public const MENU_EXTENSIONS = 'phpExtensions';

    public const ACTION_ENABLE = 'enablePhp';
    public const ACTION_SWITCH_VERSION = 'switchPhpVersion';
    public const ACTION_SWITCH_SETTING = 'switchPhpSetting';
    public const ACTION_SWITCH_EXTENSION = 'switchPhpExtension';

    /**
     * Processes the PHP menu and returns the menu items and actions.
     *
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return array The generated menu items and actions for the PHP menu.
     */
    public static function process(): array
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable(
            $bearsamppLang->getValue(Lang::PHP),
            self::MENU,
            static::class,
            $bearsamppBins->getPhp()->isEnable()
        );
    }

    /**
     * Generates the PHP menu items and actions.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated PHP menu items and actions.
     */
    public static function getMenuPhp(): string
    {
        global $bearsamppBins, $bearsamppLang;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getPhp()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
            $bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
            Util::getWebsiteUrl('module/php', '#releases'),
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

            // Settings
            $tplSettings = TplApp::getMenu($bearsamppLang->getValue(Lang::SETTINGS), self::MENU_SETTINGS, static::class);
            $resultItems .= $tplSettings[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplSettings[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Extensions
            $tplExtensions = TplApp::getMenu($bearsamppLang->getValue(Lang::EXTENSIONS), self::MENU_EXTENSIONS, static::class);
            $resultItems .= $tplExtensions[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplExtensions[TplApp::SECTION_CONTENT];

            // Conf
            $resultItems .= TplAestan::getItemNotepad(
                basename($bearsamppBins->getPhp()->getConf()),
                $bearsamppBins->getPhp()->getConf()
            ) . PHP_EOL;

            // Errors log
            $resultItems .= TplAestan::getItemNotepad(
                $bearsamppLang->getValue(Lang::MENU_ERROR_LOGS),
                $bearsamppBins->getPhp()->getErrorLog()
            ) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Generates the PHP versions menu items and actions.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated PHP versions menu items and actions.
     */
    public static function getMenuPhpVersions(): string
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getPhp()->getVersionList() as $version) {
            $glyph = '';
            $apachePhpModule = $bearsamppBins->getPhp()->getApacheModule($bearsamppBins->getApache()->getVersion(), $version);
            if ($apachePhpModule === false) {
                $glyph = TplAestan::GLYPH_WARNING;
            } elseif ($version === $bearsamppBins->getPhp()->getVersion()) {
                $glyph = TplAestan::GLYPH_CHECK;
            }

            $tplSwitchPhpVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION,
                [$version],
                [$version, $glyph],
                false,
                static::class
            );

            // Item
            $items .= $tplSwitchPhpVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchPhpVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to enable or disable PHP.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param int $enable The enable flag (1 to enable, 0 to disable).
     * @return string The generated action string to enable or disable PHP.
     */
    public static function getActionEnablePhp(int $enable): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, [$bearsamppBins->getPhp()->getName(), $enable]) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to switch the PHP version.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param string $version The PHP version to switch to.
     * @return string The generated action string to switch the PHP version.
     */
    public static function getActionSwitchPhpVersion(string $version): string
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::SWITCH_VERSION, [$bearsamppBins->getPhp()->getName(), $version]) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the PHP settings menu items and actions.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated PHP settings menu items and actions.
     */
    public static function getMenuPhpSettings(): string
    {
        global $bearsamppBins;

        $menuItems = '';
        $menuActions = '';
        foreach ($bearsamppBins->getPhp()->getSettings() as $key => $value) {
            if (is_array($value)) {
                $menuItems .= 'Type: submenu; ' .
                    'Caption: "' . $key . '"; ' .
                    'SubMenu: MenuPhpSetting-' . md5($key) . '; ' .
                    'Glyph: ' . TplAestan::GLYPH_FOLDER_CLOSE . PHP_EOL;
            } else {
                $glyph = '';
                $settingEnabled = $bearsamppBins->getPhp()->isSettingActive($value);
                if (!$bearsamppBins->getPhp()->isSettingExists($value)) {
                    $glyph = TplAestan::GLYPH_WARNING;
                } elseif ($settingEnabled) {
                    $glyph = TplAestan::GLYPH_CHECK;
                }
                $tplSwitchPhpSetting = TplApp::getActionMulti(
                    self::ACTION_SWITCH_SETTING,
                    [$value, $settingEnabled],
                    [$key, $glyph],
                    false,
                    static::class
                );

                $menuItems .= $tplSwitchPhpSetting[TplApp::SECTION_CALL] . PHP_EOL;
                $menuActions .= $tplSwitchPhpSetting[TplApp::SECTION_CONTENT];
            }
        }

        $submenusItems = '';
        $submenusActions = '';
        $submenuKeys = self::getSubmenuPhpSettings();
        foreach ($submenuKeys as $submenuKey) {
            $submenusItems .= PHP_EOL . '[MenuPhpSetting-' . md5($submenuKey) . ']' .
                PHP_EOL . self::getSubmenuPhpSettings($submenuKey);

            $submenusActions .= self::getSubmenuPhpSettings($submenuKey, [], [], false);
        }

        return $menuItems . $submenusItems . PHP_EOL . $menuActions . $submenusActions;
    }

    /**
     * Generates the submenu items and actions for PHP settings.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param array $passThr The pass-through array for nested settings.
     * @param array $result The result array to store submenu items.
     * @param array $settings The settings array to process.
     * @param bool $sectionCall Whether to generate section calls or content.
     * @return string The generated submenu items and actions for PHP settings.
     */
    private static function getSubmenuPhpSettings(array $passThr = [], array $result = [], array $settings = [], bool $sectionCall = true): string
    {
        global $bearsamppBins;
        $settings = empty($settings) ? $bearsamppBins->getPhp()->getSettings() : $settings;

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                if (is_array($passThr)) {
                    $result[] = $key;
                    $result = self::getSubmenuPhpSettings($passThr, $result, $value);
                } else {
                    $result = is_array($result) ? '' : $result;
                    if ($key === $passThr) {
                        foreach ($value as $key2 => $value2) {
                            if (is_array($value2) && $sectionCall) {
                                $result .= 'Type: submenu; ' .
                                    'Caption: "' . $key2 . '"; ' .
                                    'SubMenu: MenuPhpSetting-' . md5($key2) . '; ' .
                                    'Glyph: ' . TplAestan::GLYPH_FOLDER_CLOSE . PHP_EOL;
                            } elseif (!is_array($value2)) {
                                $glyph = '';
                                $settingEnabled = $bearsamppBins->getPhp()->isSettingActive($value2);
                                if (!$bearsamppBins->getPhp()->isSettingExists($value2)) {
                                    $glyph = TplAestan::GLYPH_WARNING;
                                } elseif ($settingEnabled) {
                                    $glyph = TplAestan::GLYPH_CHECK;
                                }
                                $tplSwitchPhpSetting = TplApp::getActionMulti(
                                    self::ACTION_SWITCH_SETTING,
                                    [$value2, $settingEnabled],
                                    [$key2, $glyph],
                                    false,
                                    static::class
                                );

                                if ($sectionCall) {
                                    $result .= $tplSwitchPhpSetting[TplApp::SECTION_CALL] . PHP_EOL;
                                } else {
                                    $result .= $tplSwitchPhpSetting[TplApp::SECTION_CONTENT] . PHP_EOL;
                                }
                            }
                        }
                    } else {
                        $result .= self::getSubmenuPhpSettings($passThr, null, $value, $sectionCall);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Generates the action to switch a PHP setting.
     *
     * @param string $setting The PHP setting to switch.
     * @param bool $enabled The current state of the setting (true if enabled, false otherwise).
     * @return string The generated action string to switch the PHP setting.
     */
    public static function getActionSwitchPhpSetting(string $setting, bool $enabled): string
    {
        $switch = $enabled ? ActionSwitchPhpParam::SWITCH_OFF : ActionSwitchPhpParam::SWITCH_ON;
        return TplApp::getActionRun(Action::SWITCH_PHP_PARAM, [$setting, $switch]) . PHP_EOL .
            TplService::getActionRestart(BinApache::SERVICE_NAME) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the PHP extensions menu items and actions.
     *
     * @global \BearsamppBins $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated PHP extensions menu items and actions.
     */
    public static function getMenuPhpExtensions(): string
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getPhp()->getExtensions() as $extension => $switch) {
            $tplSwitchPhpExtension = TplApp::getActionMulti(
                self::ACTION_SWITCH_EXTENSION,
                [$extension, $switch],
                [$extension, ($switch === ActionSwitchPhpExtension::SWITCH_ON ? TplAestan::GLYPH_CHECK : '')],
                false,
                static::class
            );

            // Item
            $items .= $tplSwitchPhpExtension[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchPhpExtension[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to switch a PHP extension.
     *
     * @param string $extension The PHP extension to switch.
     * @param string $switch The current state of the extension (on or off).
     * @return string The generated action string to switch the PHP extension.
     */
    public static function getActionSwitchPhpExtension(string $extension, string $switch): string
    {
        $switch = $switch === ActionSwitchPhpExtension::SWITCH_OFF ? ActionSwitchPhpExtension::SWITCH_ON : ActionSwitchPhpExtension::SWITCH_OFF;
        return TplApp::getActionRun(Action::SWITCH_PHP_EXTENSION, [$extension, $switch]) . PHP_EOL .
            TplService::getActionRestart(BinApache::SERVICE_NAME) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }
}
