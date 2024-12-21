<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppServices
 *
 * This class provides methods to generate menu items and actions for managing multiple services
 * within the Bearsampp application. It includes functionalities for starting, stopping, and restarting
 * all services at once.
 */
class TplAppServices
{
    // Constants for action identifiers
    public const ACTION_START = 'startServices';
    public const ACTION_STOP = 'stopServices';
    public const ACTION_RESTART = 'restartServices';

    /**
     * Generates the main services menu with options to start, stop, and restart all services.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array An array containing the generated menu items and actions for services.
     */
    public static function process(): array
    {
        global $bearsamppLang;

        $tplStart = TplApp::getActionMulti(
            self::ACTION_START,
            null,
            [$bearsamppLang->getValue(Lang::MENU_START_SERVICES), TplAestan::GLYPH_SERVICES_START],
            false,
            static::class
        );

        $tplStop = TplApp::getActionMulti(
            self::ACTION_STOP,
            null,
            [$bearsamppLang->getValue(Lang::MENU_STOP_SERVICES), TplAestan::GLYPH_SERVICES_STOP],
            false,
            static::class
        );

        $tplRestart = TplApp::getActionMulti(
            self::ACTION_RESTART,
            null,
            [$bearsamppLang->getValue(Lang::MENU_RESTART_SERVICES), TplAestan::GLYPH_SERVICES_RESTART],
            false,
            static::class
        );

        // Items
        $items = $tplStart[TplApp::SECTION_CALL] . PHP_EOL .
            $tplStop[TplApp::SECTION_CALL] . PHP_EOL .
            $tplRestart[TplApp::SECTION_CALL] . PHP_EOL;

        // Actions
        $actions = PHP_EOL . $tplStart[TplApp::SECTION_CONTENT] .
            PHP_EOL . $tplStop[TplApp::SECTION_CONTENT] .
            PHP_EOL . $tplRestart[TplApp::SECTION_CONTENT];

        return [$items, $actions];
    }

    /**
     * Generates the actions to start all services.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated actions to start all services.
     */
    public static function getActionStartServices(): string
    {
        global $bearsamppBins;
        $actions = '';

        foreach ($bearsamppBins->getServices() as $sName => $service) {
            $actions .= TplService::getActionStart($service->getName()) . PHP_EOL;
        }

        return $actions;
    }

    /**
     * Generates the actions to stop all services.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated actions to stop all services.
     */
    public static function getActionStopServices(): string
    {
        global $bearsamppBins;
        $actions = '';

        foreach ($bearsamppBins->getServices() as $sName => $service) {
            $actions .= TplService::getActionStop($service->getName()) . PHP_EOL;
        }

        return $actions;
    }

    /**
     * Generates the actions to restart all services by stopping and then starting them.
     *
     * @return string The generated actions to restart all services.
     */
    public static function getActionRestartServices(): string
    {
        return self::getActionStopServices() . self::getActionStartServices();
    }
}
