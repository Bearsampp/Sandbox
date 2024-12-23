<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class TplService
{
    /**
     * Generates an action string to create a service.
     *
     * @param string $sName The name of the service to create.
     * @return string The generated action string for creating the service.
     */
    public static function getActionCreate(string $sName): string
    {
        return TplApp::getActionRun(Action::SERVICE, [$sName, ActionService::CREATE]);
    }

    /**
     * Generates an action string to start a service.
     *
     * @param string $sName The name of the service to start.
     * @return string The generated action string for starting the service.
     */
    public static function getActionStart(string $sName): string
    {
        return TplApp::getActionRun(Action::SERVICE, [$sName, ActionService::START]);
    }

    /**
     * Generates an action string to stop a service.
     *
     * @param string $sName The name of the service to stop.
     * @return string The generated action string for stopping the service.
     */
    public static function getActionStop(string $sName): string
    {
        return TplApp::getActionRun(Action::SERVICE, [$sName, ActionService::STOP]);
    }

    /**
     * Generates an action string to restart a service.
     *
     * @param string $sName The name of the service to restart.
     * @return string The generated action string for restarting the service.
     */
    public static function getActionRestart(string $sName): string
    {
        return TplApp::getActionRun(Action::SERVICE, [$sName, ActionService::RESTART]);
    }

    /**
     * Generates an action string to install a service.
     *
     * @param string $sName The name of the service to install.
     * @return string The generated action string for installing the service.
     */
    public static function getActionInstall(string $sName): string
    {
        return TplApp::getActionRun(Action::SERVICE, [$sName, ActionService::INSTALL]);
    }

    /**
     * Generates an action string to remove a service.
     *
     * @param string $sName The name of the service to remove.
     * @return string The generated action string for removing the service.
     */
    public static function getActionRemove(string $sName): string
    {
        return TplApp::getActionRun(Action::SERVICE, [$sName, ActionService::REMOVE]);
    }

    /**
     * Generates a menu item to start a service.
     *
     * @param string $sName The name of the service to start.
     * @return string The generated menu item string for starting the service.
     */
    public static function getItemStart(string $sName): string
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, [$sName, ActionService::START],
            [$bearsamppLang->getValue(Lang::MENU_START_SERVICE), TplAestan::GLYPH_START]
        );
    }

    /**
     * Generates a menu item to stop a service.
     *
     * @param string $sName The name of the service to stop.
     * @return string The generated menu item string for stopping the service.
     */
    public static function getItemStop(string $sName): string
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, [$sName, ActionService::STOP],
            [$bearsamppLang->getValue(Lang::MENU_STOP_SERVICE), TplAestan::GLYPH_STOP]
        );
    }

    /**
     * Generates a menu item to restart a service.
     *
     * @param string $sName The name of the service to restart.
     * @return string The generated menu item string for restarting the service.
     */
    public static function getItemRestart(string $sName): string
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, [$sName, ActionService::RESTART],
            [$bearsamppLang->getValue(Lang::MENU_RESTART_SERVICE), TplAestan::GLYPH_RELOAD]
        );
    }

    /**
     * Generates a menu item to install a service.
     *
     * @param string $sName The name of the service to install.
     * @return string The generated menu item string for installing the service.
     */
    public static function getItemInstall(string $sName): string
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, [$sName, ActionService::INSTALL],
            [$bearsamppLang->getValue(Lang::MENU_INSTALL_SERVICE), TplAestan::GLYPH_SERVICE_INSTALL]
        );
    }

    /**
     * Generates a menu item to remove a service.
     *
     * @param string $sName The name of the service to remove.
     * @return string The generated menu item string for removing the service.
     */
    public static function getItemRemove(string $sName): string
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, [$sName, ActionService::REMOVE],
            [$bearsamppLang->getValue(Lang::MENU_REMOVE_SERVICE), TplAestan::GLYPH_SERVICE_REMOVE]
        );
    }
}
