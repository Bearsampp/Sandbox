<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Nssm
 *
 * This class provides methods to manage Windows services using NSSM (Non-Sucking Service Manager).
 * It includes functionalities to create, delete, start, stop, and retrieve the status of services.
 * The class also logs operations and errors.
 */
class Nssm
{
    // Start params
    public const SERVICE_AUTO_START = 'SERVICE_AUTO_START';
    public const SERVICE_DELAYED_START = 'SERVICE_DELAYED_START';
    public const SERVICE_DEMAND_START = 'SERVICE_DEMAND_START';
    public const SERVICE_DISABLED = 'SERVICE_DISABLED';

    // Type params
    public const SERVICE_WIN32_OWN_PROCESS = 'SERVICE_WIN32_OWN_PROCESS';
    public const SERVICE_INTERACTIVE_PROCESS = 'SERVICE_INTERACTIVE_PROCESS';

    // Status
    public const STATUS_CONTINUE_PENDING = 'SERVICE_CONTINUE_PENDING';
    public const STATUS_PAUSE_PENDING = 'SERVICE_PAUSE_PENDING';
    public const STATUS_PAUSED = 'SERVICE_PAUSED';
    public const STATUS_RUNNING = 'SERVICE_RUNNING';
    public const STATUS_START_PENDING = 'SERVICE_START_PENDING';
    public const STATUS_STOP_PENDING = 'SERVICE_STOP_PENDING';
    public const STATUS_STOPPED = 'SERVICE_STOPPED';
    public const STATUS_NOT_EXIST = 'SERVICE_NOT_EXIST';
    public const STATUS_NA = '-1';

    // Infos keys
    public const INFO_APP_DIRECTORY = 'AppDirectory';
    public const INFO_APPLICATION = 'Application';
    public const INFO_APP_PARAMETERS = 'AppParameters';
    public const INFO_APP_STDERR = 'AppStderr';
    public const INFO_APP_STDOUT = 'AppStdout';
    public const INFO_APP_ENVIRONMENT_EXTRA = 'AppEnvironmentExtra';

    public const PENDING_TIMEOUT = 10;
    public const SLEEP_TIME = 500000;

    private string $name;
    private string $displayName;
    private string $binPath;
    private string $params;
    private string $start;
    private string $stdout;
    private string $stderr;
    private string $environmentExtra;
    private ?string $latestError = null;
    private string $latestStatus;

    /**
     * Nssm constructor.
     * Initializes the Nssm class and logs the initialization.
     *
     * @param string $name The name of the service.
     */
    public function __construct(string $name)
    {
        Util::logInitClass($this);
        $this->name = $name;
    }

    /**
     * Writes a log entry.
     *
     * @param string $log The log message to write.
     */
    private function writeLog(string $log): void
    {
        global $bearsamppRoot;
        Util::logDebug($log, $bearsamppRoot->getNssmLogFilePath());
    }

    /**
     * Writes an informational log entry.
     *
     * @param string $log The log message to write.
     */
    private function writeLogInfo(string $log): void
    {
        global $bearsamppRoot;
        Util::logInfo($log, $bearsamppRoot->getNssmLogFilePath());
    }

    /**
     * Writes an error log entry.
     *
     * @param string $log The log message to write.
     */
    private function writeLogError(string $log): void
    {
        global $bearsamppRoot;
        Util::logError($log, $bearsamppRoot->getNssmLogFilePath());
    }

    /**
     * Executes an NSSM command.
     *
     * @param string $args The arguments for the NSSM command.
     *
     * @return array|false The result of the execution, or false on failure.
     */
    private function exec(string $args): array|false
    {
        global $bearsamppCore;

        $command = '"' . $bearsamppCore->getNssmExe() . '" ' . $args;
        $this->writeLogInfo('Cmd: ' . $command);

        $result = Batch::exec('nssm', $command, 10);
        if (is_array($result)) {
            $rebuildResult = [];
            foreach ($result as $row) {
                $row = trim($row);
                if (!empty($row)) {
                    $rebuildResult[] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row);
                }
            }
            $result = $rebuildResult;
            if (count($result) > 1) {
                $this->latestError = implode(' ; ', $result);
            }

            return $result;
        }

        return false;
    }

    /**
     * Retrieves the status of the service.
     *
     * @param bool $timeout Whether to apply a timeout for the status check.
     *
     * @return string The status of the service.
     */
    public function status(bool $timeout = true): string
    {
        usleep(self::SLEEP_TIME);

        $this->latestStatus = self::STATUS_NA;
        $maxtime = time() + self::PENDING_TIMEOUT;

        while ($this->latestStatus == self::STATUS_NA || $this->isPending($this->latestStatus)) {
            $exec = $this->exec('status ' . $this->getName());
            if ($exec !== false) {
                if (count($exec) > 1) {
                    $this->latestStatus = self::STATUS_NOT_EXIST;
                } else {
                    $this->latestStatus = $exec[0];
                }
            }
            if ($timeout && $maxtime < time()) {
                break;
            }
        }

        if ($this->latestStatus == self::STATUS_NOT_EXIST) {
            $this->latestError = 'Error 3: The specified service does not exist as an installed service.';
            $this->latestStatus = self::STATUS_NA;
        }

        return $this->latestStatus;
    }

    /**
     * Creates a new service.
     *
     * @return bool True if the service was created successfully, false otherwise.
     */
    public function create(): bool
    {
        $this->writeLog('Create service');
        $this->writeLog('-> service: ' . $this->getName());
        $this->writeLog('-> display: ' . $this->getDisplayName());
        $this->writeLog('-> description: ' . $this->getDisplayName());
        $this->writeLog('-> path: ' . $this->getBinPath());
        $this->writeLog('-> params: ' . $this->getParams());
        $this->writeLog('-> stdout: ' . $this->getStdout());
        $this->writeLog('-> stderr: ' . $this->getStderr());
        $this->writeLog('-> environment extra: ' . $this->getEnvironmentExtra());
        $this->writeLog('-> start_type: ' . ($this->getStart() !== null ? $this->getStart() : self::SERVICE_DEMAND_START));

        // Install bin
        $exec = $this->exec('install ' . $this->getName() . ' "' . $this->getBinPath() . '"');
        if ($exec === false) {
            return false;
        }

        // Params
        $exec = $this->exec('set ' . $this->getName() . ' AppParameters "' . $this->getParams() . '"');
        if ($exec === false) {
            return false;
        }

        // DisplayName
        $exec = $this->exec('set ' . $this->getName() . ' DisplayName "' . $this->getDisplayName() . '"');
        if ($exec === false) {
            return false;
        }

        // Description
        $exec = $this->exec('set ' . $this->getName() . ' Description "' . $this->getDisplayName() . '"');
        if ($exec === false) {
            return false;
        }

        // No AppNoConsole to fix nssm problems with Windows 10 Creators update.
        $exec = $this->exec('set ' . $this->getName() . ' AppNoConsole "1"');
        if ($exec === false) {
            return false;
        }

        // Start
        $exec = $this->exec('set ' . $this->getName() . ' Start "' . ($this->getStart() !== null ? $this->getStart() : self::SERVICE_DEMAND_START) . '"');
        if ($exec === false) {
            return false;
        }

        // Stdout
        $exec = $this->exec('set ' . $this->getName() . ' AppStdout "' . $this->getStdout() . '"');
        if ($exec === false) {
            return false;
        }

        // Stderr
        $exec = $this->exec('set ' . $this->getName() . ' AppStderr "' . $this->getStderr() . '"');
        if ($exec === false) {
            return false;
        }

        // Environment Extra
        $exec = $this->exec('set ' . $this->getName() . ' AppEnvironmentExtra ' . $this->getEnvironmentExtra());
        if ($exec === false) {
            return false;
        }

        if (!$this->isInstalled()) {
            $this->latestError = null;

            return false;
        }

        return true;
    }

    /**
     * Deletes the service.
     *
     * @return bool True if the service was deleted successfully, false otherwise.
     */
    public function delete(): bool
    {
        $this->stop();

        $this->writeLog('Delete service ' . $this->getName());
        $exec = $this->exec('remove ' . $this->getName() . ' confirm');
        if ($exec === false) {
            return false;
        }

        if ($this->isInstalled()) {
            $this->latestError = null;

            return false;
        }

        return true;
    }

    /**
     * Starts the service.
     *
     * @return bool True if the service was started successfully, false otherwise.
     */
    public function start(): bool
    {
        $this->writeLog('Start service ' . $this->getName());

        $exec = $this->exec('start ' . $this->getName());
        if ($exec === false) {
            return false;
        }

        if (!$this->isRunning()) {
            $this->latestError = null;

            return false;
        }

        return true;
    }

    /**
     * Stops the service.
     *
     * @return bool True if the service was stopped successfully, false otherwise.
     */
    public function stop(): bool
    {
        $this->writeLog('Stop service ' . $this->getName());

        $exec = $this->exec('stop ' . $this->getName());
        if ($exec === false) {
            return false;
        }

        if (!$this->isStopped()) {
            $this->latestError = null;

            return false;
        }

        return true;
    }

    /**
     * Restarts the service.
     *
     * @return bool True if the service was restarted successfully, false otherwise.
     */
    public function restart(): bool
    {
        if ($this->stop()) {
            return $this->start();
        }

        return false;
    }

    /**
     * Retrieves information about the service.
     *
     * @return array|false The service information, or false on failure.
     */
    public function infos(): array|false
    {
        global $bearsamppRegistry;

        $infos = Vbs::getServiceInfos($this->getName());
        if ($infos === false) {
            return false;
        }

        $infosNssm = [];
        $infosKeys = [
            self::INFO_APPLICATION,
            self::INFO_APP_PARAMETERS,
        ];

        foreach ($infosKeys as $infoKey) {
            $value = null;
            $exists = $bearsamppRegistry->exists(
                Registry::HKEY_LOCAL_MACHINE,
                'SYSTEM\CurrentControlSet\Services\\' . $this->getName() . '\Parameters',
                $infoKey
            );
            if ($exists) {
                $value = $bearsamppRegistry->getValue(
                    Registry::HKEY_LOCAL_MACHINE,
                    'SYSTEM\CurrentControlSet\Services\\' . $this->getName() . '\Parameters',
                    $infoKey
                );
            }
            $infosNssm[$infoKey] = $value;
        }

        if (!isset($infosNssm[self::INFO_APPLICATION])) {
            return $infos;
        }

        $infos[Win32Service::VBS_PATH_NAME] = $infosNssm[Nssm::INFO_APPLICATION] . ' ' . $infosNssm[Nssm::INFO_APP_PARAMETERS];

        return $infos;
    }

    /**
     * Checks if the service is installed.
     *
     * @return bool True if the service is installed, false otherwise.
     */
    public function isInstalled(): bool
    {
        $status = $this->status();
        $this->writeLog('isInstalled ' . $this->getName() . ': ' . ($status != self::STATUS_NA ? 'YES' : 'NO') . ' (status: ' . $status . ')');

        return $status != self::STATUS_NA;
    }

    /**
     * Checks if the service is running.
     *
     * @return bool True if the service is running, false otherwise.
     */
    public function isRunning(): bool
    {
        $status = $this->status();
        $this->writeLog('isRunning ' . $this->getName() . ': ' . ($status == self::STATUS_RUNNING ? 'YES' : 'NO') . ' (status: ' . $status . ')');

        return $status == self::STATUS_RUNNING;
    }

    /**
     * Checks if the service is stopped.
     *
     * @return bool True if the service is stopped, false otherwise.
     */
    public function isStopped(): bool
    {
        $status = $this->status();
        $this->writeLog('isStopped ' . $this->getName() . ': ' . ($status == self::STATUS_STOPPED ? 'YES' : 'NO') . ' (status: ' . $status . ')');

        return $status == self::STATUS_STOPPED;
    }

    /**
     * Checks if the service is paused.
     *
     * @return bool True if the service is paused, false otherwise.
     */
    public function isPaused(): bool
    {
        $status = $this->status();
        $this->writeLog('isPaused ' . $this->getName() . ': ' . ($status == self::STATUS_PAUSED ? 'YES' : 'NO') . ' (status: ' . $status . ')');

        return $status == self::STATUS_PAUSED;
    }

    /**
     * Checks if the service status is pending.
     *
     * @param string $status The status to check.
     *
     * @return bool True if the status is pending, false otherwise.
     */
    public function isPending(string $status): bool
    {
        return $status == self::STATUS_START_PENDING || $status == self::STATUS_STOP_PENDING
            || $status == self::STATUS_CONTINUE_PENDING || $status == self::STATUS_PAUSE_PENDING;
    }

    /**
     * Retrieves the description of the service status.
     *
     * @param string $status The status to describe.
     *
     * @return string|null The description of the status, or null if not recognized.
     */
    private function getServiceStatusDesc(string $status): ?string
    {
        return match ($status) {
            self::STATUS_CONTINUE_PENDING => 'The service continue is pending.',
            self::STATUS_PAUSE_PENDING => 'The service pause is pending.',
            self::STATUS_PAUSED => 'The service is paused.',
            self::STATUS_RUNNING => 'The service is running.',
            self::STATUS_START_PENDING => 'The service is starting.',
            self::STATUS_STOP_PENDING => 'The service is stopping.',
            self::STATUS_STOPPED => 'The service is not running.',
            self::STATUS_NA => 'Cannot retrieve service status.',
            default => null,
        };
    }

    /**
     * Gets the name of the service.
     *
     * @return string The name of the service.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of the service.
     *
     * @param string $name The name to set.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Gets the display name of the service.
     *
     * @return string The display name of the service.
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * Sets the display name of the service.
     *
     * @param string $displayName The display name to set.
     */
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * Gets the binary path of the service.
     *
     * @return string The binary path of the service.
     */
    public function getBinPath(): string
    {
        return $this->binPath;
    }

    /**
     * Sets the binary path of the service.
     *
     * @param string $binPath The binary path to set.
     */
    public function setBinPath(string $binPath): void
    {
        $this->binPath = str_replace('"', '', Util::formatWindowsPath($binPath));
    }

    /**
     * Gets the parameters of the service.
     *
     * @return string The parameters of the service.
     */
    public function getParams(): string
    {
        return $this->params;
    }

    /**
     * Sets the parameters of the service.
     *
     * @param string $params The parameters to set.
     */
    public function setParams(string $params): void
    {
        $this->params = $params;
    }

    /**
     * Gets the start type of the service.
     *
     * @return string The start type of the service.
     */
    public function getStart(): string
    {
        return $this->start;
    }

    /**
     * Sets the start type of the service.
     *
     * @param string $start The start type to set.
     */
    public function setStart(string $start): void
    {
        $this->start = $start;
    }

    /**
     * Gets the stdout path of the service.
     *
     * @return string The stdout path of the service.
     */
    public function getStdout(): string
    {
        return $this->stdout;
    }

    /**
     * Sets the stdout path of the service.
     *
     * @param string $stdout The stdout path to set.
     */
    public function setStdout(string $stdout): void
    {
        $this->stdout = $stdout;
    }

    /**
     * Gets the stderr path of the service.
     *
     * @return string The stderr path of the service.
     */
    public function getStderr(): string
    {
        return $this->stderr;
    }

    /**
     * Sets the stderr path of the service.
     *
     * @param string $stderr The stderr path to set.
     */
    public function setStderr(string $stderr): void
    {
        $this->stderr = $stderr;
    }

    /**
     * Gets the additional environment variables for the service.
     *
     * @return string The additional environment variables.
     */
    public function getEnvironmentExtra(): string
    {
        return $this->environmentExtra;
    }

    /**
     * Sets the additional environment variables for the service.
     *
     * @param string $environmentExtra The additional environment variables to set.
     */
    public function setEnvironmentExtra(string $environmentExtra): void
    {
        $this->environmentExtra = Util::formatWindowsPath($environmentExtra);
    }

    /**
     * Gets the latest status of the service.
     *
     * @return string The latest status of the service.
     */
    public function getLatestStatus(): string
    {
        return $this->latestStatus;
    }

    /**
     * Gets the latest error message related to the service.
     *
     * @return string|null The latest error message, or null if no error.
     */
    public function getLatestError(): ?string
    {
        return $this->latestError;
    }

    /**
     * Retrieves the error message or status description of the service.
     *
     * @return string|null The error message or status description, or null if no error or status is available.
     */
    public function getError(): ?string
    {
        global $bearsamppLang;

        if (!empty($this->latestError)) {
            return $bearsamppLang->getValue(Lang::ERROR) . ' ' . $this->latestError;
        } elseif ($this->latestStatus != self::STATUS_NA) {
            return $bearsamppLang->getValue(Lang::STATUS) . ' ' . $this->latestStatus . ' : ' . $this->getServiceStatusDesc($this->latestStatus);
        }

        return null;
    }
}
