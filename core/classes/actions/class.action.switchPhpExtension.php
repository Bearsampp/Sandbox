<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionSwitchPhpExtension
 *
 * This class is responsible for enabling or disabling PHP extensions by modifying the php.ini configuration file.
 */
class ActionSwitchPhpExtension
{
    public const SWITCH_ON = 'on';
    public const SWITCH_OFF = 'off';

    /**
     * ActionSwitchPhpExtension constructor.
     *
     * Initializes the class with the provided arguments and modifies the php.ini file
     * to enable or disable the specified PHP extension.
     *
     * @param array $args An array containing the extension name and the action (on/off).
     */
    public function __construct(array $args)
    {
        global $bearsamppBins;

        if (!empty($args[0]) && !empty($args[1])) {
            $onContent = 'extension=' . $args[0];
            $offContent = ';extension=' . $args[0];

            if (version_compare($bearsamppBins->getPhp()->getVersion(), '7.2', '<')) {
                $onContent = 'extension=php_' . $args[0] . '.dll';
                $offContent = ';extension=php_' . $args[0] . '.dll';
            }

            $phpiniContent = file_get_contents($bearsamppBins->getPhp()->getConf());
            if ($args[1] === self::SWITCH_ON) {
                $phpiniContent = str_replace($offContent, $onContent, $phpiniContent);
            } elseif ($args[1] === self::SWITCH_OFF) {
                $phpiniContent = str_replace($onContent, $offContent, $phpiniContent);
            }

            $phpiniContentOr = file_get_contents($bearsamppBins->getPhp()->getConf());
            if ($phpiniContent === $phpiniContentOr && file_exists($bearsamppBins->getPhp()->getSymlinkPath() . '/ext/php_' . $args[0] . '.dll')) {
                $extsIni = $bearsamppBins->getPhp()->getExtensionsFromConf();
                $latestExt = (end($extsIni) === '0' ? ';' : '');
                if (version_compare($bearsamppBins->getPhp()->getVersion(), '7.2', '<')) {
                    $latestExt .= 'extension=php_' . key($extsIni) . '.dll';
                } else {
                    $latestExt .= 'extension=' . key($extsIni);
                }
                $phpiniContent = str_replace(
                    $latestExt,
                    $latestExt . PHP_EOL . $onContent,
                    $phpiniContent
                );
            }

            file_put_contents($bearsamppBins->getPhp()->getConf(), $phpiniContent);
        }
    }
}
