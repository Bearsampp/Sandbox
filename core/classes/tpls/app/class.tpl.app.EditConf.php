<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppEditConf
 *
 * This class provides a method to process the request for editing the configuration file.
 * It generates a Notepad item string that allows the user to edit the `bearsampp.conf` file.
 */
class TplAppEditConf
{
    /**
     * Processes the request to edit the configuration file.
     *
     * This method generates a Notepad item string that allows the user to edit the `bearsampp.conf` file.
     * It utilizes global variables to access language settings and the root path of the application.
     *
     * @global LangProc $bearsamppLang The language processor for retrieving localized strings.
     * @global Root $bearsamppRoot The root object for accessing application paths.
     * @return string The Notepad item string for editing the configuration file.
     */
    public static function process(): string
    {
        global $bearsamppLang, $bearsamppRoot;

        return TplAestan::getItemNotepad(
            sprintf($bearsamppLang->getValue(Lang::MENU_EDIT_CONF), "bearsampp.conf"),
            $bearsamppRoot->getConfigFilePath()
        ) . PHP_EOL;
    }
}
