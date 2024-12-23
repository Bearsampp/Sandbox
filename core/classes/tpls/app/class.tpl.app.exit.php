<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppExit
 *
 * This class provides methods to handle the exit action within the Bearsampp application.
 * It includes functionalities to process the exit action and generate the necessary action strings.
 */
class TplAppExit
{
    /**
     * Constant representing the exit action.
     */
    public const ACTION = 'exit';

    /**
     * Processes the exit action and generates the necessary action strings.
     *
     * This method generates a multi-action string for the exit action, including the caption and glyph.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array An array containing the call string and the section content for the exit action.
     */
    public static function process(): array
    {
        global $bearsamppLang;

        return TplApp::getActionMulti(
            self::ACTION,
            null,
            [$bearsamppLang->getValue(Lang::QUIT), TplAestan::GLYPH_EXIT],
            false,
            static::class
        );
    }

    /**
     * Generates the action string to execute the exit action.
     *
     * This method generates a run action string for the quit action and appends the exit action.
     *
     * @return string The generated action string for the exit action.
     */
    public static function getActionExit(): string
    {
        return TplApp::getActionRun(Action::QUIT) . PHP_EOL . 'Action: exit';
    }
}
