<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppPython
 *
 * This class provides methods to generate menu items and actions for managing Python tools
 * within the Bearsampp application. It includes functionalities for accessing the Python console,
 * IDLE, and other Python-related executables.
 */
class TplAppPython
{
    // Constant for the Python menu identifier
    public const MENU = 'python';

    /**
     * Generates the main Python menu with options to access Python tools.
     *
     * This method uses the global language object to retrieve the localized string for Python.
     *
     * @global LangProc $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated menu items and actions for Python.
     */
    public static function process(): string
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::PYTHON), self::MENU, static::class);
    }

    /**
     * Generates the Python menu with options for accessing the Python console, IDLE, and other tools.
     *
     * This method creates menu items for the Python console and IDLE, utilizing global objects
     * for language support and tool configurations.
     *
     * @global LangProc $bearsamppLang Provides language support for retrieving language-specific values.
     * @global Tools $bearsamppTools Provides access to various tools and their configurations.
     *
     * @return string The generated menu items and actions for Python tools.
     */
    public static function getMenuPython(): string
    {
        global $bearsamppLang, $bearsamppTools;

        // Generate menu item for Python console
        $resultItems = TplAestan::getItemConsoleZ(
            $bearsamppLang->getValue(Lang::PYTHON_CONSOLE),
            TplAestan::GLYPH_PYTHON,
            $bearsamppTools->getConsoleZ()->getTabTitlePython()
        ) . PHP_EOL;

        // Generate menu item for Python IDLE
        $resultItems .= TplAestan::getItemExe(
            $bearsamppLang->getValue(Lang::PYTHON) . ' IDLE',
            $bearsamppTools->getPython()->getIdleExe(),
            TplAestan::GLYPH_PYTHON
        ) . PHP_EOL;

        return $resultItems;
    }
}
