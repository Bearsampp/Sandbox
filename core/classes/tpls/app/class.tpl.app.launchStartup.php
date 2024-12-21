<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppLaunchStartup
 *
 * This class provides methods to generate actions and menu items for launching the Bearsampp application at startup.
 * It includes functionalities for creating launch startup actions and processing launch startup commands.
 */
class TplAppLaunchStartup
{
    // Constant for the launch startup action identifier
    public const ACTION = 'launchStartup';

    /**
     * Generates the launch startup menu item and associated actions.
     *
     * This method creates a menu item for launching the application at startup and defines the actions to be taken
     * when the launch startup menu item is selected. It checks the current launch startup status and toggles it.
     * It uses the global language object to retrieve the localized string for the launch startup action.
     *
     * @global \BearsamppLang $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array The generated menu item and actions for launching the application at startup.
     */
    public static function process(): array
    {
        global $bearsamppLang;

        $isLaunchStartup = Util::isLaunchStartup();
        return TplApp::getActionMulti(
            self::ACTION, [$isLaunchStartup ? Config::DISABLED : Config::ENABLED],
            [$bearsamppLang->getValue(Lang::MENU_LAUNCH_STARTUP), $isLaunchStartup ? TplAestan::GLYPH_CHECK : ''],
            false, static::class
        );
    }

    /**
     * Generates the action to launch the application at startup.
     *
     * This method creates the action string for launching the application at startup. It includes commands to reload
     * the application configuration. The action string is used to define what happens when the launch startup action
     * is triggered.
     *
     * @param int $launchStartup The status to set for launch startup (enabled or disabled).
     *
     * @return string The generated action string for launching the application at startup.
     */
    public static function getActionLaunchStartup(int $launchStartup): string
    {
        return TplApp::getActionRun(Action::LAUNCH_STARTUP, [$launchStartup]) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }
}
