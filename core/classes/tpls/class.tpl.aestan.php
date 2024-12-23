<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAestan
 *
 * This class provides various methods to generate configuration strings for the Bearsampp application.
 * It includes methods to create items for ConsoleZ, links, Notepad, executables, and explorer actions.
 * Additionally, it handles service actions such as start, stop, and restart, and generates configuration
 * sections for messages, config, and menu settings.
 *
 * Constants:
 * - Glyph constants for various icons used in the application.
 * - Service actions for starting, stopping, and restarting services.
 * - Image files used in the application.
 *
 * Methods:
 * - getGlyphFlah(string $lang): void
 * - getItemSeparator(): string
 * - getItemConsoleZ(string $caption, int $glyph, ?string $id, ?string $title, ?string $initDir, ?string $command): string
 * - getItemLink(string $caption, string $link, bool $local, int $glyph): string
 * - getItemNotepad(string $caption, string $path): string
 * - getItemExe(string $caption, string $exe, int $glyph, ?string $params): string
 * - getItemExplore(string $caption, string $path): string
 * - getActionService(?string $service, string $action, bool $item): string
 * - getActionServiceStart(string $service): string
 * - getItemActionServiceStart(string $service): string
 * - getActionServiceStop(string $service): string
 * - getItemActionServiceStop(string $service): string
 * - getActionServiceRestart(string $service): string
 * - getItemActionServiceRestart(string $service): string
 * - getActionServicesClose(): string
 * - getItemActionServicesClose(): string
 * - getSectionMessages(): string
 * - getSectionConfig(): string
 * - getSectionMenuRightSettings(): string
 * - getSectionMenuLeftSettings(string $caption): string
 */
class TplAestan
{
    // Glyph constants
    public const GLYPH_CONSOLEZ = 0;
    public const GLYPH_ADD = 1;
    public const GLYPH_FOLDER_OPEN = 2;
    public const GLYPH_FOLDER_CLOSE = 3;
    public const GLYPH_BROWSER = 5;
    public const GLYPH_FILE = 6;
    public const GLYPH_SERVICE_REMOVE = 7;
    public const GLYPH_SERVICE_INSTALL = 8;
    public const GLYPH_START = 9;
    public const GLYPH_PAUSE = 10;
    public const GLYPH_STOP = 11;
    public const GLYPH_RELOAD = 12;
    public const GLYPH_CHECK = 13;
    public const GLYPH_SERVICE_ALL_RUNNING = 16;
    public const GLYPH_SERVICE_SOME_RUNNING = 17;
    public const GLYPH_SERVICE_NONE_RUNNING = 18;
    public const GLYPH_WARNING = 19;
    public const GLYPH_EXIT = 20;
    public const GLYPH_ABOUT = 21;
    public const GLYPH_SERVICES_RESTART = 22;
    public const GLYPH_SERVICES_STOP = 23;
    public const GLYPH_SERVICES_START = 24;
    public const GLYPH_LIGHT = 25;
    public const GLYPH_GIT = 26;
    public const GLYPH_NODEJS = 28;
    public const GLYPH_NETWORK = 29;
    public const GLYPH_WEB_PAGE = 30;
    public const GLYPH_DEBUG = 31;
    public const GLYPH_TRASHCAN = 32;
    public const GLYPH_UPDATE = 33;
    public const GLYPH_RESTART = 34;
    public const GLYPH_SSL_CERTIFICATE = 35;
    public const GLYPH_RED_LIGHT = 36;
    public const GLYPH_COMPOSER = 37;
    public const GLYPH_PEAR = 38;
    public const GLYPH_HOSTSEDITOR = 39;
    public const GLYPH_IMAGEMAGICK = 41;
    public const GLYPH_NOTEPAD2 = 42;
    public const GLYPH_PASSWORD = 45;
    public const GLYPH_FILEZILLA = 47;
    public const GLYPH_FOLDER_DISABLED = 48;
    public const GLYPH_FOLDER_ENABLED = 49;
    public const GLYPH_PYTHON = 50;
    public const GLYPH_RUBY = 52;
    public const GLYPH_YARN = 54;
    public const GLYPH_PERL = 55;
    public const GLYPH_GHOSTSCRIPT = 56;
    public const GLYPH_NGROK = 57;
    public const GLYPH_PWGEN = 58;
    public const GLYPH_XLIGHT = 59;
    public const GLYPH_REBUILD_INI = 60;
    public const GLYPH_BRUNO = 61;

    // Service actions
    public const SERVICE_START = 'startresume';
    public const SERVICE_STOP = 'stop';
    public const SERVICE_RESTART = 'restart';
    public const SERVICES_CLOSE = 'closeservices';

    // Image files
    public const IMG_BAR_PICTURE = 'bar.dat';
    public const IMG_GLYPH_SPRITES = 'sprites.dat';

    /**
     * Retrieves the glyph flag for a given language.
     *
     * @param string $lang The language code.
     * @return void
     */
    public static function getGlyphFlah(string $lang): void
    {
    }

    /**
     * Returns a string representing a separator item.
     *
     * @return string The separator item string.
     */
    public static function getItemSeparator(): string
    {
        return 'Type: separator';
    }

    /**
     * Returns a string representing a ConsoleZ item.
     *
     * @param string $caption The caption for the item.
     * @param int $glyph The glyph index.
     * @param string|null $id The ID for the item.
     * @param string|null $title The title for the item.
     * @param string|null $initDir The initial directory for the item.
     * @param string|null $command The command to execute.
     * @return string The ConsoleZ item string.
     */
    public static function getItemConsoleZ(string $caption, int $glyph, ?string $id = null, ?string $title = null, ?string $initDir = null, ?string $command = null): string
    {
        global $bearsamppTools;

        $args = '';
        if ($id !== null) {
            $args .= ' -t ""' . $id . '""';
        }
        if ($title !== null) {
            $args .= ' -w ""' . $title . '""';
        }
        if ($initDir !== null) {
            $args .= ' -d ""' . $initDir . '""';
        }
        if ($command !== null) {
            $args .= ' -r ""' . $command . '""';
        }

        return self::getItemExe(
            $caption,
            $bearsamppTools->getConsoleZ()->getExe(),
            $glyph,
            $args
        );
    }

    /**
     * Returns a string representing a link item.
     *
     * @param string $caption The caption for the item.
     * @param string $link The URL for the link.
     * @param bool $local Whether the link is local.
     * @param int $glyph The glyph index.
     * @return string The link item string.
     */
    public static function getItemLink(string $caption, string $link, bool $local = false, int $glyph = self::GLYPH_WEB_PAGE): string
    {
        global $bearsamppRoot, $bearsamppConfig;

        if ($local) {
            $link = $bearsamppRoot->getLocalUrl($link);
        }

        return self::getItemExe(
            $caption,
            $bearsamppConfig->getBrowser(),
            $glyph,
            $link
        );
    }

    /**
     * Returns a string representing a Notepad item.
     *
     * @param string $caption The caption for the item.
     * @param string $path The path to the file.
     * @return string The Notepad item string.
     */
    public static function getItemNotepad(string $caption, string $path): string
    {
        global $bearsamppConfig;

        return self::getItemExe(
            $caption,
            $bearsamppConfig->getNotepad(),
            self::GLYPH_FILE,
            $path
        );
    }

    /**
     * Returns a string representing an executable item.
     *
     * @param string $caption The caption for the item.
     * @param string $exe The path to the executable.
     * @param int $glyph The glyph index.
     * @param string|null $params The parameters for the executable.
     * @return string The executable item string.
     */
    public static function getItemExe(string $caption, string $exe, int $glyph, ?string $params = null): string
    {
        return 'Type: item; ' .
            'Caption: "' . $caption . '"; ' .
            'Action: run; ' .
            'FileName: "' . $exe . '"; ' .
            (!empty($params) ? 'Parameters: "' . $params . '"; ' : '') .
            'Glyph: ' . $glyph;
    }

    /**
     * Returns a string representing an explorer item.
     *
     * @param string $caption The caption for the item.
     * @param string $path The path to explore.
     * @return string The explorer item string.
     */
    public static function getItemExplore(string $caption, string $path): string
    {
        return 'Type: item; ' .
            'Caption: "' . $caption . '"; ' .
            'Action: shellexecute; ' .
            'FileName: "' . $path . '"; ' .
            'Glyph: ' . self::GLYPH_FOLDER_OPEN;
    }

    /**
     * Returns a string representing a service action.
     *
     * @param string|null $service The service name.
     * @param string $action The action to perform.
     * @param bool $item Whether to return as an item.
     * @return string The service action string.
     */
    private static function getActionService(?string $service, string $action, bool $item = false): string
    {
        global $bearsamppLang;
        $result = 'Action: ' . $action;

        if ($service !== null) {
            $result = 'Action: service; ' .
                'Service: ' . $service . '; ' .
                'ServiceAction: ' . $action;
        }

        if ($item) {
            $result = 'Type: item; ' . $result;
            if ($action === self::SERVICE_START) {
                $result .= '; Caption: "' . $bearsamppLang->getValue(Lang::MENU_START_SERVICE) . '"' .
                    '; Glyph: ' . self::GLYPH_START;
            } elseif ($action === self::SERVICE_STOP) {
                $result .= '; Caption: "' . $bearsamppLang->getValue(Lang::MENU_STOP_SERVICE) . '"' .
                    '; Glyph: ' . self::GLYPH_STOP;
            } elseif ($action === self::SERVICE_RESTART) {
                $result .= '; Caption: "' . $bearsamppLang->getValue(Lang::MENU_RESTART_SERVICE) . '"' .
                    '; Glyph: ' . self::GLYPH_RELOAD;
            }
        } elseif ($action !== self::SERVICES_CLOSE) {
            $result .= '; Flags: ignoreerrors waituntilterminated';
        }

        return $result;
    }

    /**
     * Returns a string representing a service start action.
     *
     * @param string $service The service name.
     * @return string The service start action string.
     */
    public static function getActionServiceStart(string $service): string
    {
        return self::getActionService($service, self::SERVICE_START, false);
    }

    /**
     * Returns a string representing a service start item.
     *
     * @param string $service The service name.
     * @return string The service start item string.
     */
    public static function getItemActionServiceStart(string $service): string
    {
        return self::getActionService($service, self::SERVICE_STOP, true);
    }

    /**
     * Returns a string representing a service stop action.
     *
     * @param string $service The service name.
     * @return string The service stop action string.
     */
    public static function getActionServiceStop(string $service): string
    {
        return self::getActionService($service, self::SERVICE_STOP, false);
    }

    /**
     * Returns a string representing a service stop item.
     *
     * @param string $service The service name.
     * @return string The service stop item string.
     */
    public static function getItemActionServiceStop(string $service): string
    {
        return self::getActionService($service, self::SERVICE_START, true);
    }

    /**
     * Returns a string representing a service restart action.
     *
     * @param string $service The service name.
     * @return string The service restart action string.
     */
    public static function getActionServiceRestart(string $service): string
    {
        return self::getActionService($service, self::SERVICE_RESTART, false);
    }

    /**
     * Returns a string representing a service restart item.
     *
     * @param string $service The service name.
     * @return string The service restart item string.
     */
    public static function getItemActionServiceRestart(string $service): string
    {
        return self::getActionService($service, self::SERVICE_RESTART, true);
    }

    /**
     * Returns a string representing a close services action.
     *
     * @return string The close services action string.
     */
    public static function getActionServicesClose(): string
    {
        return self::getActionService(null, self::SERVICES_CLOSE, false);
    }

    /**
     * Returns a string representing a close services item.
     *
     * @return string The close services item string.
     */
    public static function getItemActionServicesClose(): string
    {
        return self::getActionService(null, self::SERVICES_CLOSE, true);
    }

    /**
     * Returns a string representing the messages section.
     *
     * @return string The messages section string.
     */
    public static function getSectionMessages(): string
    {
        global $bearsamppLang;

        return '[Messages]' . PHP_EOL .
            'AllRunningHint=' . $bearsamppLang->getValue(Lang::ALL_RUNNING_HINT) . PHP_EOL .
            'SomeRunningHint=' . $bearsamppLang->getValue(Lang::SOME_RUNNING_HINT) . PHP_EOL .
            'NoneRunningHint=' . $bearsamppLang->getValue(Lang::NONE_RUNNING_HINT) . PHP_EOL;
    }

    /**
     * Returns a string representing the config section.
     *
     * @return string The config section string.
     */
    public static function getSectionConfig(): string
    {
        global $bearsamppCore;
        return '[Config]' . PHP_EOL .
            'ImageList=' . self::IMG_GLYPH_SPRITES . PHP_EOL .
            'ServiceCheckInterval=1' . PHP_EOL .
            'TrayIconAllRunning=' . self::GLYPH_SERVICE_ALL_RUNNING . PHP_EOL .
            'TrayIconSomeRunning=' . self::GLYPH_SERVICE_SOME_RUNNING . PHP_EOL .
            'TrayIconNoneRunning=' . self::GLYPH_SERVICE_NONE_RUNNING . PHP_EOL .
            'ID={' . strtolower(APP_TITLE) . '}' . PHP_EOL .
            'AboutHeader=' . APP_TITLE . PHP_EOL .
            'AboutVersion=Version ' . $bearsamppCore->getAppVersion() . PHP_EOL;
    }

    /**
     * Returns a string representing the right menu settings section.
     *
     * @return string The right menu settings section string.
     */
    public static function getSectionMenuRightSettings(): string
    {
        return '[Menu.Right.Settings]' . PHP_EOL .
            'BarVisible=no' . PHP_EOL .
            'SeparatorsAlignment=center' . PHP_EOL .
            'SeparatorsFade=yes' . PHP_EOL .
            'SeparatorsFadeColor=clBtnShadow' . PHP_EOL .
            'SeparatorsFlatLines=yes' . PHP_EOL .
            'SeparatorsGradientEnd=clSilver' . PHP_EOL .
            'SeparatorsGradientStart=clGray' . PHP_EOL .
            'SeparatorsGradientStyle=horizontal' . PHP_EOL .
            'SeparatorsSeparatorStyle=shortline' . PHP_EOL;
    }

    /**
     * Returns a string representing the left menu settings section.
     *
     * @param string $caption The caption for the left menu.
     * @return string The left menu settings section string.
     */
    public static function getSectionMenuLeftSettings(string $caption): string
    {
        return '[Menu.Left.Settings]' . PHP_EOL .
            'AutoLineReduction=no' . PHP_EOL .
            'BarVisible=yes' . PHP_EOL .
            'BarCaptionAlignment=bottom' . PHP_EOL .
            'BarCaptionCaption=' . $caption . PHP_EOL .
            'BarCaptionDepth=1' . PHP_EOL .
            'BarCaptionDirection=downtoup' . PHP_EOL .
            'BarCaptionFont=Tahoma,14,clWhite' . PHP_EOL .
            'BarCaptionHighlightColor=clNone' . PHP_EOL .
            'BarCaptionOffsetY=0' . PHP_EOL .
            'BarCaptionShadowColor=clNone' . PHP_EOL .
            'BarPictureHorzAlignment=center' . PHP_EOL .
            'BarPictureOffsetX=0' . PHP_EOL .
            'BarPictureOffsetY=0' . PHP_EOL .
            'BarPicturePicture=' . self::IMG_BAR_PICTURE . PHP_EOL .
            'BarPictureTransparent=yes' . PHP_EOL .
            'BarPictureVertAlignment=bottom' . PHP_EOL .
            'BarBorder=clNone' . PHP_EOL .
            'BarGradientEnd=$00c07840' . PHP_EOL .
            'BarGradientStart=$00c07840' . PHP_EOL .
            'BarGradientStyle=horizontal' . PHP_EOL .
            'BarSide=left' . PHP_EOL .
            'BarSpace=0' . PHP_EOL .
            'BarWidth=32' . PHP_EOL .
            'SeparatorsAlignment=center' . PHP_EOL .
            'SeparatorsFade=yes' . PHP_EOL .
            'SeparatorsFadeColor=clBtnShadow' . PHP_EOL .
            'SeparatorsFlatLines=yes' . PHP_EOL .
            'SeparatorsFont=Arial,8,clWhite,bold' . PHP_EOL .
            'SeparatorsGradientEnd=$00FFAA55' . PHP_EOL .
            'SeparatorsGradientStart=$00550000' . PHP_EOL .
            'SeparatorsGradientStyle=horizontal' . PHP_EOL .
            'SeparatorsSeparatorStyle=caption' . PHP_EOL;
    }
}
