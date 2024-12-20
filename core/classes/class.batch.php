<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Batch
 *
 * Provides utility functions for managing processes, services, and environment variables
 * within the Bearsampp application. Includes methods for finding executables by process ID,
 * checking which process is using a specific port, exiting and restarting the application,
 * managing services, and executing batch scripts.
 *
 * Key functionalities include:
 * - Finding executables by process ID.
 * - Checking which process is using a specific port.
 * - Exiting and restarting the application.
 * - Managing services (installing, uninstalling, setting descriptions, etc.).
 * - Executing batch scripts with optional output capture and timeout.
 * - Refreshing environment variables.
 * - Creating and removing symbolic links.
 * - Retrieving operating system information.
 *
 * Utilizes global variables to access application settings and paths, and logs operations for debugging purposes.
 */
class Batch
{
    public const END_PROCESS_STR = 'FINISHED!';
    public const CATCH_OUTPUT_FALSE = 'bearsamppCatchOutputFalse';

    /**
     * Constructor for the Batch class.
     */
    public function __construct()
    {
    }

    /**
     * Writes a log entry to the batch log file.
     *
     * @param string $log The log message to write.
     * @return void
     */
    private static function writeLog(string $log): void
    {
        global $bearsamppRoot;
        Util::logDebug($log, $bearsamppRoot->getBatchLogFilePath());
    }

    /**
     * Finds the executable name by its process ID (PID).
     *
     * @param int $pid The process ID to search for.
     * @return string|false The executable name if found, false otherwise.
     */
    public static function findExeByPid(int $pid): string|false
    {
        $result = self::exec('findExeByPid', 'TASKLIST /FO CSV /NH /FI "PID eq ' . $pid . '"', 5);
        if ($result !== false) {
            $expResult = explode('","', $result[0]);
            if (is_array($expResult) && count($expResult) > 2 && isset($expResult[0]) && !empty($expResult[0])) {
                return substr($expResult[0], 1);
            }
        }

        return false;
    }

    /**
     * Gets the process using a specific port.
     *
     * @param int $port The port number to check.
     * @return string|int|null The executable name and PID if found, the PID if executable not found, or null if no process is using the port.
     */
    public static function getProcessUsingPort(int $port): string|int|null
    {
        $result = self::exec('getProcessUsingPort', 'NETSTAT -aon', 4);
        if ($result !== false) {
            foreach ($result as $row) {
                if (!Util::startWith($row, 'TCP')) {
                    continue;
                }
                $rowExp = explode(' ', preg_replace('/\s+/', ' ', $row));
                if (count($rowExp) == 5 && Util::endWith($rowExp[1], ':' . $port) && $rowExp[3] == 'LISTENING') {
                    $pid = intval($rowExp[4]);
                    $exe = self::findExeByPid($pid);
                    if ($exe !== false) {
                        return $exe . ' (' . $pid . ')';
                    }
                    return $pid;
                }
            }
        }

        return null;
    }

    /**
     * Exits the application, optionally restarting it.
     *
     * @param bool $restart Whether to restart the application after exiting.
     * @return void
     */
    public static function exitApp(bool $restart = false): void
    {
        global $bearsamppRoot, $bearsamppCore;

        $content = 'PING 1.1.1.1 -n 1 -w 2000 > nul' . PHP_EOL;
        $content .= '"' . $bearsamppRoot->getExeFilePath() . '" -quit -id={bearsampp}' . PHP_EOL;
        if ($restart) {
            $basename = 'restartApp';
            Util::logInfo('Restart App');
            $content .= '"' . $bearsamppCore->getPhpExe() . '" "' . Core::isRoot_FILE . '" "' . Action::RESTART . '"' . PHP_EOL;
        } else {
            $basename = 'exitApp';
            Util::logInfo('Exit App');
        }

        Win32Ps::killBins();
        self::execStandalone($basename, $content);
    }

    /**
     * Restarts the application.
     *
     * @return void
     */
    public static function restartApp(): void
    {
        self::exitApp(true);
    }

    /**
     * Gets the version of PEAR installed.
     *
     * @return string|null The PEAR version if found, null otherwise.
     */
    public static function getPearVersion(): ?string
    {
        global $bearsamppBins;

        $result = self::exec('getPearVersion', 'CMD /C "' . $bearsamppBins->getPhp()->getPearExe() . '" -V', 5);
        if (is_array($result)) {
            foreach ($result as $row) {
                if (Util::startWith($row, 'PEAR Version:')) {
                    $expResult = explode(' ', $row);
                    if (count($expResult) == 3) {
                        return trim($expResult[2]);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Refreshes the environment variables.
     *
     * @return void
     */
    public static function refreshEnvVars(): void
    {
        global $bearsamppRoot, $bearsamppCore;
        self::execStandalone('refreshEnvVars', '"' . $bearsamppCore->getSetEnvExe() . '" -a ' . Registry::APP_PATH_REG_ENTRY . ' "' . Util::formatWindowsPath($bearsamppRoot->getRootPath()) . '"');
    }

    /**
     * Installs the FileZilla service.
     *
     * @return bool True if the service was installed successfully, false otherwise.
     */
    public static function installFilezillaService(): bool
    {
        global $bearsamppBins;

        self::exec('installFilezillaService', '"' . $bearsamppBins->getFilezilla()->getExe() . '" /install', true, false);

        if (!$bearsamppBins->getFilezilla()->getService()->isInstalled()) {
            return false;
        }

        self::setServiceDescription(BinFilezilla::SERVICE_NAME, $bearsamppBins->getFilezilla()->getService()->getDisplayName());

        return true;
    }

    /**
     * Uninstalls the FileZilla service.
     *
     * @return bool True if the service was uninstalled successfully, false otherwise.
     */
    public static function uninstallFilezillaService(): bool
    {
        global $bearsamppBins;

        self::exec('uninstallFilezillaService', '"' . $bearsamppBins->getFilezilla()->getExe() . '" /uninstall', true, false);
        return !$bearsamppBins->getFilezilla()->getService()->isInstalled();
    }

    /**
     * Initializes MySQL using a specified path.
     *
     * @param string $path The path to the MySQL initialization script.
     * @return void
     */
    public static function initializeMysql(string $path): void
    {
        if (!file_exists($path . '/init.bat')) {
            Util::logWarning($path . '/init.bat does not exist');
            return;
        }
        self::exec('initializeMysql', 'CMD /C "' . $path . '/init.bat"', 60);
    }

    /**
     * Installs the PostgreSQL service.
     *
     * @return bool True if the service was installed successfully, false otherwise.
     */
    public static function installPostgresqlService(): bool
    {
        global $bearsamppBins;

        $cmd = '"' . Util::formatWindowsPath($bearsamppBins->getPostgresql()->getCtlExe()) . '" register -N "' . BinPostgresql::SERVICE_NAME . '"';
        $cmd .= ' -U "LocalSystem" -D "' . Util::formatWindowsPath($bearsamppBins->getPostgresql()->getSymlinkPath()) . '\\data"';
        $cmd .= ' -l "' . Util::formatWindowsPath($bearsamppBins->getPostgresql()->getErrorLog()) . '" -w';
        self::exec('installPostgresqlService', $cmd, true, false);

        if (!$bearsamppBins->getPostgresql()->getService()->isInstalled()) {
            return false;
        }

        self::setServiceDisplayName(BinPostgresql::SERVICE_NAME, $bearsamppBins->getPostgresql()->getService()->getDisplayName());
        self::setServiceDescription(BinPostgresql::SERVICE_NAME, $bearsamppBins->getPostgresql()->getService()->getDisplayName());
        self::setServiceStartType(BinPostgresql::SERVICE_NAME, "demand");

        return true;
    }

    /**
     * Uninstalls the PostgreSQL service.
     *
     * @return bool True if the service was uninstalled successfully, false otherwise.
     */
    public static function uninstallPostgresqlService(): bool
    {
        global $bearsamppBins;

        $cmd = '"' . Util::formatWindowsPath($bearsamppBins->getPostgresql()->getCtlExe()) . '" unregister -N "' . BinPostgresql::SERVICE_NAME . '"';
        $cmd .= ' -l "' . Util::formatWindowsPath($bearsamppBins->getPostgresql()->getErrorLog()) . '" -w';
        self::exec('uninstallPostgresqlService', $cmd, true, false);
        return !$bearsamppBins->getPostgresql()->getService()->isInstalled();
    }

    /**
     * Initializes PostgreSQL using a specified path.
     *
     * @param string $path The path to the PostgreSQL initialization script.
     * @return void
     */
    public static function initializePostgresql(string $path): void
    {
        if (!file_exists($path . '/init.bat')) {
            Util::logWarning($path . '/init.bat does not exist');
            return;
        }
        self::exec('initializePostgresql', 'CMD /C "' . $path . '/init.bat"', 15);
    }

    /**
     * Creates a symbolic link.
     *
     * @param string $src The source path.
     * @param string $dest The destination path.
     * @return void
     */
    public static function createSymlink(string $src, string $dest): void
    {
        global $bearsamppCore;
        $src = Util::formatWindowsPath($src);
        $dest = Util::formatWindowsPath($dest);
        self::exec('createSymlink', '"' . $bearsamppCore->getLnExe() . '" --absolute --symbolic --traditional --1023safe "' . $src . '" ' . '"' . $dest . '"', true, false);
    }

    /**
     * Removes a symbolic link.
     *
     * @param string $link The path to the symbolic link.
     * @return void
     */
    public static function removeSymlink(string $link): void
    {
        self::exec('removeSymlink', 'rmdir /Q "' . Util::formatWindowsPath($link) . '"', true, false);
    }

    /**
     * Gets the operating system information.
     *
     * @return string The operating system information.
     */
    public static function getOsInfo(): string
    {
        $result = self::exec('getOsInfo', 'ver', 5);
        if (is_array($result)) {
            foreach ($result as $row) {
                if (Util::startWith($row, 'Microsoft')) {
                    return trim($row);
                }
            }
        }
        return '';
    }

    /**
     * Sets the display name of a service.
     *
     * @param string $serviceName The name of the service.
     * @param string $displayName The display name to set.
     * @return void
     */
    public static function setServiceDisplayName(string $serviceName, string $displayName): void
    {
        $cmd = 'sc config ' . $serviceName . ' DisplayName= "' . $displayName . '"';
        self::exec('setServiceDisplayName', $cmd, true, false);
    }

    /**
     * Sets the description of a service.
     *
     * @param string $serviceName The name of the service.
     * @param string $desc The description to set.
     * @return void
     */
    public static function setServiceDescription(string $serviceName, string $desc): void
    {
        $cmd = 'sc description ' . $serviceName . ' "' . $desc . '"';
        self::exec('setServiceDescription', $cmd, true, false);
    }

    /**
     * Sets the start type of a service.
     *
     * @param string $serviceName The name of the service.
     * @param string $startType The start type to set (e.g., "auto", "demand").
     * @return void
     */
    public static function setServiceStartType(string $serviceName, string $startType): void
    {
        $cmd = 'sc config ' . $serviceName . ' start= ' . $startType;
        self::exec('setServiceStartType', $cmd, true, false);
    }

    /**
     * Executes a standalone batch script.
     *
     * @param string $basename The base name for the script and result files.
     * @param string $content The content of the batch script.
     * @param bool $silent Whether to execute the script silently.
     * @return array|false The result of the execution, or false on failure.
     */
    public static function execStandalone(string $basename, string $content, bool $silent = true): array|false
    {
        return self::exec($basename, $content, false, false, true, $silent);
    }

    /**
     * Executes a batch script.
     *
     * @param string $basename The base name for the script and result files.
     * @param string $content The content of the batch script.
     * @param int|bool $timeout The timeout for the script execution in seconds, or true for default timeout, or false for no timeout.
     * @param bool $catchOutput Whether to capture the output of the script.
     * @param bool $standalone Whether the script is standalone.
     * @param bool $silent Whether to execute the script silently.
     * @param bool $rebuild Whether to rebuild the result array.
     * @return array|false The result of the execution, or false on failure.
     */
    public static function exec(string $basename, string $content, int|bool $timeout = true, bool $catchOutput = true, bool $standalone = false, bool $silent = true, bool $rebuild = true): array|false
    {
        global $bearsamppConfig, $bearsamppWinbinder;
        $result = false;

        $resultFile = self::getTmpFile('.tmp', $basename);
        $scriptPath = self::getTmpFile('.bat', $basename);
        $checkFile = self::getTmpFile('.tmp', $basename);

        // Redirect output
        if ($catchOutput) {
            $content .= '> "' . $resultFile . '"' . (!Util::endWith($content, '2') ? ' 2>&1' : '');
        }

        // Header
        $header = '@ECHO OFF' . PHP_EOL . PHP_EOL;

        // Footer
        $footer = PHP_EOL . (!$standalone ? PHP_EOL . 'ECHO ' . self::END_PROCESS_STR . ' > "' . $checkFile . '"' : '');

        // Process
        file_put_contents($scriptPath, $header . $content . $footer);
        $bearsamppWinbinder->exec($scriptPath, null, $silent);

        if (!$standalone) {
            $timeout = is_numeric($timeout) ? $timeout : ($timeout === true ? $bearsamppConfig->getScriptsTimeout() : false);
            $maxtime = time() + $timeout;
            $noTimeout = $timeout === false;
            while ($result === false || empty($result)) {
                if (file_exists($checkFile)) {
                    $check = file($checkFile);
                    if (!empty($check) && trim($check[0]) == self::END_PROCESS_STR) {
                        if ($catchOutput && file_exists($resultFile)) {
                            $result = file($resultFile);
                        } else {
                            $result = self::CATCH_OUTPUT_FALSE;
                        }
                    }
                }
                if ($maxtime < time() && !$noTimeout) {
                    break;
                }
            }
        }

        self::writeLog('Exec:');
        self::writeLog('-> basename: ' . $basename);
        self::writeLog('-> content: ' . str_replace(PHP_EOL, ' \\\\ ', $content));
        self::writeLog('-> checkFile: ' . $checkFile);
        self::writeLog('-> resultFile: ' . $resultFile);
        self::writeLog('-> scriptPath: ' . $scriptPath);

        if ($result !== false && !empty($result) && is_array($result)) {
            if ($rebuild) {
                $rebuildResult = [];
                foreach ($result as $row) {
                    $row = trim($row);
                    if (!empty($row)) {
                        $rebuildResult[] = $row;
                    }
                }
                $result = $rebuildResult;
            }
            self::writeLog('-> result: ' . substr(implode(' \\\\ ', $result), 0, 2048));
        } else {
            self::writeLog('-> result: N/A');
        }

        return $result;
    }

    /**
     * Gets a temporary file path with a specified extension and optional custom name.
     *
     * @param string $ext The file extension.
     * @param string|null $customName An optional custom name for the file.
     * @return string The temporary file path.
     */
    private static function getTmpFile(string $ext, ?string $customName = null): string
    {
        global $bearsamppCore;
        return Util::formatWindowsPath($bearsamppCore->getTmpPath() . '/' . (!empty($customName) ? $customName . '-' : '') . Util::random() . $ext);
    }
}
