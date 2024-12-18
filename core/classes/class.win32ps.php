<?php

/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

namespace Core\Classes;

use Core\Classes\Vbs;
use Core\Classes\Util;
use Core\Classes\Root;

/**
 * Class Win32Ps
 *
 * Provides utility functions for interacting with Windows processes.
 * Includes methods for retrieving process information, checking process existence,
 * finding processes by PID or path, and terminating processes.
 */
class Win32Ps
{
    public const NAME = 'Name';
    public const PROCESS_ID = 'ProcessID';
    public const EXECUTABLE_PATH = 'ExecutablePath';
    public const CAPTION = 'Caption';
    public const COMMAND_LINE = 'CommandLine';

    /**
     * Win32Ps constructor.
     */
    public function __construct()
    {
    }

    /**
     * Calls a specified Win32 process function if it exists.
     *
     * @param string $function The name of the function to call.
     * @return mixed The result of the function call, or false if the function does not exist.
     */
    private static function callWin32Ps(string $function): mixed
    {
        $result = false;

        if (function_exists($function)) {
            $result = @call_user_func($function);
        }

        return $result;
    }

    /**
     * Retrieves the keys used for process information.
     *
     * @return array An array of keys used for process information.
     */
    public static function getKeys(): array
    {
        return [
            self::NAME,
            self::PROCESS_ID,
            self::EXECUTABLE_PATH,
            self::CAPTION,
            self::COMMAND_LINE,
        ];
    }

    /**
     * Retrieves the current process ID.
     *
     * @return int The current process ID, or 0 if not found.
     */
    public static function getCurrentPid(): int
    {
        $procInfo = self::getStatProc();
        return isset($procInfo[self::PROCESS_ID]) ? intval($procInfo[self::PROCESS_ID]) : 0;
    }

    /**
     * Retrieves the status of the current process.
     *
     * @return array|null An array containing the process ID and executable path, or null on failure.
     */
    public static function getStatProc(): ?array
    {
        $statProc = self::callWin32Ps('win32_ps_stat_proc');

        if ($statProc !== false) {
            return [
                self::PROCESS_ID => $statProc['pid'],
                self::EXECUTABLE_PATH => $statProc['exe'],
            ];
        }

        return null;
    }

    /**
     * Retrieves a list of running processes.
     *
     * @return array|null An array of process information, or null on failure.
     */
    public static function getListProcs(): ?array
    {
        return Vbs::getListProcs(self::getKeys());
    }

    /**
     * Checks if a process with the specified PID exists.
     *
     * @param int $pid The process ID to check.
     * @return bool True if the process exists, false otherwise.
     */
    public static function exists(int $pid): bool
    {
        return self::findByPid($pid) !== false;
    }

    /**
     * Finds a process by its PID.
     *
     * @param int $pid The process ID to find.
     * @return array|false An array of process information, or false if not found.
     */
    public static function findByPid(int $pid): array|false
    {
        if ($pid > 0) {
            $procs = self::getListProcs();
            if ($procs !== null) {
                foreach ($procs as $proc) {
                    if (intval($proc[self::PROCESS_ID]) === $pid) {
                        return $proc;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Finds a process by its executable path.
     *
     * @param string $path The path to the executable.
     * @return array|false An array of process information, or false if not found.
     */
    public static function findByPath(string $path): array|false
    {
        $path = Util::formatUnixPath($path);
        if (!empty($path) && is_file($path)) {
            $procs = self::getListProcs();
            if ($procs !== null) {
                foreach ($procs as $proc) {
                    $unixExePath = Util::formatUnixPath($proc[self::EXECUTABLE_PATH] ?? '');
                    if ($unixExePath === $path) {
                        return $proc;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Terminates a process by its PID.
     *
     * @param int $pid The process ID to terminate.
     * @return void
     */
    public static function kill(int $pid): void
    {
        if ($pid > 0) {
            Vbs::killProc($pid);
        }
    }

    /**
     * Terminates all Bearsampp-related processes except the current one.
     *
     * @param bool $refreshProcs Whether to refresh the list of processes before terminating.
     * @return array An array of terminated processes.
     */
    public static function killBins(bool $refreshProcs = false): array
    {
        global $bearsamppRoot;

        $killed = [];

        $procs = $refreshProcs ? self::getListProcs() : $bearsamppRoot->getProcs();

        if ($procs !== null) {
            foreach ($procs as $proc) {
                $unixExePath = Util::formatUnixPath($proc[self::EXECUTABLE_PATH] ?? '');
                $unixCommandPath = Util::formatUnixPath($proc[self::COMMAND_LINE] ?? '');

                // Do not kill current process (PHP)
                if (intval($proc[self::PROCESS_ID] ?? 0) === self::getCurrentPid()) {
                    continue;
                }

                // Do not kill bearsampp.exe
                if ($unixExePath === $bearsamppRoot->getExeFilePath()) {
                    continue;
                }

                // Do not kill processes inside www directory
                if (
                    Util::startWith($unixExePath, $bearsamppRoot->getWwwPath() . '/') ||
                    Util::contains($unixCommandPath, $bearsamppRoot->getWwwPath() . '/')
                ) {
                    continue;
                }

                // Do not kill external processes
                if (
                    !Util::startWith($unixExePath, $bearsamppRoot->getRootPath() . '/') &&
                    !Util::contains($unixCommandPath, $bearsamppRoot->getRootPath() . '/')
                ) {
                    continue;
                }

                self::kill(intval($proc[self::PROCESS_ID]));
                $killed[] = $proc;
            }
        }

        return $killed;
    }
}
