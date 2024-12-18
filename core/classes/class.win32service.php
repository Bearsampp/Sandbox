<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

use Bearsampp\Core\Util;
use Bearsampp\Core\Batch;
use Bearsampp\Core\Bins\BinFilezilla;
use Bearsampp\Core\Bins\BinPostgresql;
use Bearsampp\Core\Bins\BinMysql;
use Bearsampp\Core\Bins\BinMailhog;
use Bearsampp\Core\Bins\BinMailpit;
use Bearsampp\Core\Bins\BinMemcached;
use Bearsampp\Core\Bins\BinXlight;
use Bearsampp\Core\Bins\BinApache;
use Bearsampp\Core\Bins\BinMariadb;

/**
 * Class Win32Service
 *
 * This class provides an interface to manage Windows services. It includes methods to create, delete, start, stop, and query the status of services.
 * It also handles logging and error reporting for service operations.
 */
class Win32Service
{
    // Win32Service Service Status Constants
    public const WIN32_SERVICE_CONTINUE_PENDING = '5';
    public const WIN32_SERVICE_PAUSE_PENDING = '6';
    public const WIN32_SERVICE_PAUSED = '7';
    public const WIN32_SERVICE_RUNNING = '4';
    public const WIN32_SERVICE_START_PENDING = '2';
    public const WIN32_SERVICE_STOP_PENDING = '3';
    public const WIN32_SERVICE_STOPPED = '1';
    public const WIN32_SERVICE_NA = '0';

    // Win32 Error Codes
    public const WIN32_ERROR_ACCESS_DENIED = '5';
    public const WIN32_ERROR_CIRCULAR_DEPENDENCY = '423';
    public const WIN32_ERROR_DATABASE_DOES_NOT_EXIST = '429';
    public const WIN32_ERROR_DEPENDENT_SERVICES_RUNNING = '41B';
    public const WIN32_ERROR_DUPLICATE_SERVICE_NAME = '436';
    public const WIN32_ERROR_FAILED_SERVICE_CONTROLLER_CONNECT = '427';
    public const WIN32_ERROR_INSUFFICIENT_BUFFER = '7A';
    public const WIN32_ERROR_INVALID_DATA = 'D';
    public const WIN32_ERROR_INVALID_HANDLE = '6';
    public const WIN32_ERROR_INVALID_LEVEL = '7C';
    public const WIN32_ERROR_INVALID_NAME = '7B';
    public const WIN32_ERROR_INVALID_PARAMETER = '57';
    public const WIN32_ERROR_INVALID_SERVICE_ACCOUNT = '421';
    public const WIN32_ERROR_INVALID_SERVICE_CONTROL = '41C';
    public const WIN32_ERROR_PATH_NOT_FOUND = '3';
    public const WIN32_ERROR_SERVICE_ALREADY_RUNNING = '420';
    public const WIN32_ERROR_SERVICE_CANNOT_ACCEPT_CTRL = '425';
    public const WIN32_ERROR_SERVICE_DATABASE_LOCKED = '41F';
    public const WIN32_ERROR_SERVICE_DEPENDENCY_DELETED = '433';
    public const WIN32_ERROR_SERVICE_DEPENDENCY_FAIL = '42C';
    public const WIN32_ERROR_SERVICE_DISABLED = '422';
    public const WIN32_ERROR_SERVICE_DOES_NOT_EXIST = '424';
    public const WIN32_ERROR_SERVICE_EXISTS = '431';
    public const WIN32_ERROR_SERVICE_LOGON_FAILED = '42D';
    public const WIN32_ERROR_SERVICE_MARKED_FOR_DELETE = '430';
    public const WIN32_ERROR_SERVICE_NO_THREAD = '41E';
    public const WIN32_ERROR_SERVICE_NOT_ACTIVE = '426';
    public const WIN32_ERROR_SERVICE_REQUEST_TIMEOUT = '41D';
    public const WIN32_ERROR_SHUTDOWN_IN_PROGRESS = '45B';
    public const WIN32_NO_ERROR = '0';

    public const SERVER_ERROR_IGNORE = '0';
    public const SERVER_ERROR_NORMAL = '1';

    public const SERVICE_AUTO_START = '2';
    public const SERVICE_DEMAND_START = '3';
    public const SERVICE_DISABLED = '4';

    public const PENDING_TIMEOUT = 20;
    public const SLEEP_TIME = 500000;

    public const VBS_NAME = 'Name';
    public const VBS_DISPLAY_NAME = 'DisplayName';
    public const VBS_DESCRIPTION = 'Description';
    public const VBS_PATH_NAME = 'PathName';
    public const VBS_STATE = 'State';

    private string $name;
    private string $displayName;
    private string $binPath;
    private string $params;
    private string $startType;
    private string $errorControl;
    private ?Nssm $nssm = null;

    private string $latestStatus;
    private string $latestError;

    /**
     * Constructor for the Win32Service class.
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
     * @param string $log The log message.
     */
    private function writeLog(string $log): void
    {
        global $bearsamppRoot;
        Util::logDebug($log, $bearsamppRoot->getServicesLogFilePath());
    }

    /**
     * Returns an array of VBS keys used for service information.
     *
     * @return array The array of VBS keys.
     */
    public static function getVbsKeys(): array
    {
        return [
            self::VBS_NAME,
            self::VBS_DISPLAY_NAME,
            self::VBS_DESCRIPTION,
            self::VBS_PATH_NAME,
            self::VBS_STATE
        ];
    }

    /**
     * Calls a Win32 service function.
     *
     * @param string $function The function name.
     * @param mixed $param The parameter to pass to the function.
     * @param bool $checkError Whether to check for errors.
     *
     * @return mixed The result of the function call.
     */
    private function callWin32Service(string $function, mixed $param, bool $checkError = false): mixed
    {
        $result = false;
        if (function_exists($function)) {
            $result = call_user_func($function, $param);
            if ($checkError && dechex($result) != self::WIN32_NO_ERROR) {
                $this->latestError = dechex($result);
            }
        }

        return $result;
    }

    /**
     * Queries the status of the service.
     *
     * @param bool $timeout Whether to use a timeout.
     *
     * @return string The status of the service.
     */
    public function status(bool $timeout = true): string
    {
        usleep(self::SLEEP_TIME);

        $this->latestStatus = self::WIN32_SERVICE_NA;
        $maxtime = time() + self::PENDING_TIMEOUT;

        while ($this->latestStatus == self::WIN32_SERVICE_NA || $this->isPending($this->latestStatus)) {
            $this->latestStatus = $this->callWin32Service('win32_query_service_status', $this->getName());
            if (is_array($this->latestStatus) && isset($this->latestStatus['CurrentState'])) {
                $this->latestStatus = dechex($this->latestStatus['CurrentState']);
            } elseif (dechex($this->latestStatus) == self::WIN32_ERROR_SERVICE_DOES_NOT_EXIST) {
                $this->latestStatus = dechex($this->latestStatus);
            }
            if ($timeout && $maxtime < time()) {
                break;
            }
        }

        if ($this->latestStatus == self::WIN32_ERROR_SERVICE_DOES_NOT_EXIST) {
            $this->latestError = $this->latestStatus;
            $this->latestStatus = self::WIN32_SERVICE_NA;
        }

        return $this->latestStatus;
    }

    /**
     * Creates the service.
     *
     * @return bool True if the service was created successfully, false otherwise.
     */
    public function create(): bool
    {
        global $bearsamppBins;

        if ($this->getName() == BinFilezilla::SERVICE_NAME) {
            $bearsamppBins->getFilezilla()->rebuildConf();

            return Batch::installFilezillaService();
        } elseif ($this->getName() == BinPostgresql::SERVICE_NAME) {
            $bearsamppBins->getPostgresql()->rebuildConf();
            $bearsamppBins->getPostgresql()->initData();

            return Batch::installPostgresqlService();
        }
        if ($this->getNssm() instanceof Nssm) {
            $nssmEnvPath = Util::getAppBinsRegKey(false);
            $nssmEnvPath .= Util::getNssmEnvPaths();
            $nssmEnvPath .= '%SystemRoot%/system32;';
            $nssmEnvPath .= '%SystemRoot%;';
            $nssmEnvPath .= '%SystemRoot%/system32/Wbem;';
            $nssmEnvPath .= '%SystemRoot%/system32/WindowsPowerShell/v1.0';
            $this->getNssm()->setEnvironmentExtra('PATH=' . $nssmEnvPath);

            return $this->getNssm()->create();
        }

        $create = dechex($this->callWin32Service('win32_create_service', [
            'service' => $this->getName(),
            'display' => $this->getDisplayName(),
            'description' => $this->getDisplayName(),
            'path' => $this->getBinPath(),
            'params' => $this->getParams(),
            'start_type' => $this->getStartType() != null ? $this->getStartType() : self::SERVICE_DEMAND_START,
            'error_control' => $this->getErrorControl() != null ? $this->getErrorControl() : self::SERVER_ERROR_NORMAL,
        ], true));

        $this->writeLog('Create service: ' . $create . ' (status: ' . $this->status() . ')');
        $this->writeLog('-> service: ' . $this->getName());
        $this->writeLog('-> display: ' . $this->getDisplayName());
        $this->writeLog('-> description: ' . $this->getDisplayName());
        $this->writeLog('-> path: ' . $this->getBinPath());
        $this->writeLog('-> params: ' . $this->getParams());
        $this->writeLog('-> start_type: ' . ($this->getStartType() != null ? $this->getStartType() : self::SERVICE_DEMAND_START));
        $this->writeLog('-> service: ' . ($this->getErrorControl() != null ? $this->getErrorControl() : self::SERVER_ERROR_NORMAL));

        if ($create != self::WIN32_NO_ERROR) {
            return false;
        } elseif (!$this->isInstalled()) {
            $this->latestError = self::WIN32_NO_ERROR;

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
        if (!$this->isInstalled()) {
            return true;
        }

        $this->stop();

        if ($this->getName() == BinFilezilla::SERVICE_NAME) {
            return Batch::uninstallFilezillaService();
        } elseif ($this->getName() == BinPostgresql::SERVICE_NAME) {
            return Batch::uninstallPostgresqlService();
        }

        $delete = dechex($this->callWin32Service('win32_delete_service', $this->getName(), true));
        $this->writeLog('Delete service ' . $this->getName() . ': ' . $delete . ' (status: ' . $this->status() . ')');

        if ($delete != self::WIN32_NO_ERROR && $delete != self::WIN32_ERROR_SERVICE_DOES_NOT_EXIST) {
            return false;
        } elseif ($this->isInstalled()) {
            $this->latestError = self::WIN32_NO_ERROR;

            return false;
        }

        return true;
    }

    /**
     * Resets the service by deleting and recreating it.
     *
     * @return bool True if the service was reset successfully, false otherwise.
     */
    public function reset(): bool
    {
        if ($this->delete()) {
            usleep(self::SLEEP_TIME);

            return $this->create();
        }

        return false;
    }

    /**
     * Starts the service.
     *
     * @return bool True if the service was started successfully, false otherwise.
     */
    public function start(): bool
    {
        global $bearsamppBins;

        Util::logInfo('Attempting to start service: ' . $this->getName());

        if ($this->getName() == BinFilezilla::SERVICE_NAME) {
            $bearsamppBins->getFilezilla()->rebuildConf();
        } elseif ($this->getName() == BinMysql::SERVICE_NAME) {
            $bearsamppBins->getMysql()->initData();
        } elseif ($this->getName() == BinMailhog::SERVICE_NAME) {
            $bearsamppBins->getMailhog()->rebuildConf();
        } elseif ($this->getName() == BinMailpit::SERVICE_NAME) {
            $bearsamppBins->getMailpit()->rebuildConf();
        } elseif ($this->getName() == BinMemcached::SERVICE_NAME) {
            $bearsamppBins->getMemcached()->rebuildConf();
        } elseif ($this->getName() == BinPostgresql::SERVICE_NAME) {
            $bearsamppBins->getPostgresql()->rebuildConf();
            $bearsamppBins->getPostgresql()->initData();
        } elseif ($this->getName() == BinXlight::SERVICE_NAME) {
            $bearsamppBins->getXlight()->rebuildConf();
        }

        $start = dechex($this->callWin32Service('win32_start_service', $this->getName(), true));
        Util::logDebug('Start service ' . $this->getName() . ': ' . $start . ' (status: ' . $this->status() . ')');

        if ($start != self::WIN32_NO_ERROR && $start != self::WIN32_ERROR_SERVICE_ALREADY_RUNNING) {

            // Write error to log
            Util::logError('Failed to start service: ' . $this->getName() . ' with error code: ' . $start);

            if ($this->getName() == BinApache::SERVICE_NAME) {
                $cmdOutput = $bearsamppBins->getApache()->getCmdLineOutput(BinApache::CMD_SYNTAX_CHECK);
                if (!$cmdOutput['syntaxOk']) {
                    file_put_contents(
                        $bearsamppBins->getApache()->getErrorLog(),
                        '[' . date('Y-m-d H:i:s') . '] [error] ' . $cmdOutput['content'] . PHP_EOL,
                        FILE_APPEND
                    );
                }
            } elseif ($this->getName() == BinMysql::SERVICE_NAME) {
                $cmdOutput = $bearsamppBins->getMysql()->getCmdLineOutput(BinMysql::CMD_SYNTAX_CHECK);
                if (!$cmdOutput['syntaxOk']) {
                    file_put_contents(
                        $bearsamppBins->getMysql()->getErrorLog(),
                        '[' . date('Y-m-d H:i:s') . '] [error] ' . $cmdOutput['content'] . PHP_EOL,
                        FILE_APPEND
                    );
                }
            } elseif ($this->getName() == BinMariadb::SERVICE_NAME) {
                $cmdOutput = $bearsamppBins->getMariadb()->getCmdLineOutput(BinMariadb::CMD_SYNTAX_CHECK);
                if (!$cmdOutput['syntaxOk']) {
                    file_put_contents(
                        $bearsamppBins->getMariadb()->getErrorLog(),
                        '[' . date('Y-m-d H:i:s') . '] [error] ' . $cmdOutput['content'] . PHP_EOL,
                        FILE_APPEND
                    );
                }
            }

            return false;
        } elseif (!$this->isRunning()) {
            $this->latestError = self::WIN32_NO_ERROR;
            Util::logError('Service ' . $this->getName() . ' is not running after start attempt.');
            $this->latestError = null;
            return false;
        }

        Util::logInfo('Service ' . $this->getName() . ' started successfully.');
        return true;
    }

    /**
     * Stops the service.
     *
     * @return bool True if the service was stopped successfully, false otherwise.
     */
    public function stop(): bool
    {
        $stop = dechex($this->callWin32Service('win32_stop_service', $this->getName(), true));
        $this->writeLog('Stop service ' . $this->getName() . ': ' . $stop . ' (status: ' . $this->status() . ')');

        if ($stop != self::WIN32_NO_ERROR) {
            return false;
        } elseif (!$this->isStopped()) {
            $this->latestError = self::WIN32_NO_ERROR;

            return false;
        }

        return true;
    }

    /**
     * Restarts the service by stopping and then starting it.
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
     * @return array The service information.
     */
    public function infos(): array
    {
        if ($this->getNssm() instanceof Nssm) {
            return $this->getNssm()->infos();
        }

        return Vbs::getServiceInfos($this->getName());
    }

    /**
     * Checks if the service is installed.
     *
     * @return bool True if the service is installed, false otherwise.
     */
    public function isInstalled(): bool
    {
        $status = $this->status();
        $this->writeLog('isInstalled ' . $this->getName() . ': ' . ($status != self::WIN32_SERVICE_NA ? 'YES' : 'NO') . ' (status: ' . $status . ')');

        return $status != self::WIN32_SERVICE_NA;
    }

    /**
     * Checks if the service is running.
     *
     * @return bool True if the service is running, false otherwise.
     */
    public function isRunning(): bool
    {
        $status = $this->status();
        $this->writeLog('isRunning ' . $this->getName() . ': ' . ($status == self::WIN32_SERVICE_RUNNING ? 'YES' : 'NO') . ' (status: ' . $status . ')');

        return $status == self::WIN32_SERVICE_RUNNING;
    }

    /**
     * Checks if the service is stopped.
     *
     * @return bool True if the service is stopped, false otherwise.
     */
    public function isStopped(): bool
    {
        $status = $this->status();
        $this->writeLog('isStopped ' . $this->getName() . ': ' . ($status == self::WIN32_SERVICE_STOPPED ? 'YES' : 'NO') . ' (status: ' . $status . ')');

        return $status == self::WIN32_SERVICE_STOPPED;
    }

    /**
     * Checks if the service is paused.
     *
     * @return bool True if the service is paused, false otherwise.
     */
    public function isPaused(): bool
    {
        $status = $this->status();
        $this->writeLog('isPaused ' . $this->getName() . ': ' . ($status == self::WIN32_SERVICE_PAUSED ? 'YES' : 'NO') . ' (status: ' . $status . ')');

        return $status == self::WIN32_SERVICE_PAUSED;
    }

    /**
     * Checks if the service is in a pending state.
     *
     * @param string $status The status to check.
     *
     * @return bool True if the service is in a pending state, false otherwise.
     */
    public function isPending(string $status): bool
    {
        return $status == self::WIN32_SERVICE_START_PENDING || $status == self::WIN32_SERVICE_STOP_PENDING
            || $status == self::WIN32_SERVICE_CONTINUE_PENDING || $status == self::WIN32_SERVICE_PAUSE_PENDING;
    }

    /**
     * Returns a description of the Win32 service status.
     *
     * @param string $status The status code.
     *
     * @return string|null The status description.
     */
    private function getWin32ServiceStatusDesc(string $status): ?string
    {
        return match ($status) {
            self::WIN32_SERVICE_CONTINUE_PENDING => 'The service continue is pending.',
            self::WIN32_SERVICE_PAUSE_PENDING => 'The service pause is pending.',
            self::WIN32_SERVICE_PAUSED => 'The service is paused.',
            self::WIN32_SERVICE_RUNNING => 'The service is running.',
            self::WIN32_SERVICE_START_PENDING => 'The service is starting.',
            self::WIN32_SERVICE_STOP_PENDING => 'The service is stopping.',
            self::WIN32_SERVICE_STOPPED => 'The service is not running.',
            self::WIN32_SERVICE_NA => 'Cannot retrieve service status.',
            default => null,
        };
    }

    /**
     * Returns a description of the Win32 error code.
     *
     * @param string $code The error code.
     *
     * @return string|null The description of the error code, or null if the code is not recognized.
     */
    private function getWin32ErrorCodeDesc(string $code): ?string
    {
        return match ($code) {
            self::WIN32_ERROR_ACCESS_DENIED => 'The handle to the SCM database does not have the appropriate access rights.',
            // ... other cases ...
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
     * Gets the parameters for the service.
     *
     * @return string The parameters for the service.
     */
    public function getParams(): string
    {
        return $this->params;
    }

    /**
     * Sets the parameters for the service.
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
    public function getStartType(): string
    {
        return $this->startType;
    }

    /**
     * Sets the start type of the service.
     *
     * @param string $startType The start type to set.
     */
    public function setStartType(string $startType): void
    {
        $this->startType = $startType;
    }

    /**
     * Gets the error control setting of the service.
     *
     * @return string The error control setting of the service.
     */
    public function getErrorControl(): string
    {
        return $this->errorControl;
    }

    /**
     * Sets the error control setting of the service.
     *
     * @param string $errorControl The error control setting to set.
     */
    public function setErrorControl(string $errorControl): void
    {
        $this->errorControl = $errorControl;
    }

    /**
     * Gets the NSSM instance associated with the service.
     *
     * @return Nssm|null The NSSM instance.
     */
    public function getNssm(): ?Nssm
    {
        return $this->nssm;
    }

    /**
     * Sets the NSSM instance associated with the service.
     *
     * @param Nssm $nssm The NSSM instance to set.
     */
    public function setNssm(Nssm $nssm): void
    {
        $this->setDisplayName($nssm->getDisplayName());
        $this->setBinPath($nssm->getBinPath());
        $this->setParams($nssm->getParams());
        $this->setStartType($nssm->getStart());
        $this->nssm = $nssm;
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
     * Gets the latest error encountered by the service.
     *
     * @return string The latest error encountered by the service.
     */
    public function getLatestError(): string
    {
        return $this->latestError;
    }

    /**
     * Gets a detailed error message for the latest error encountered by the service.
     *
     * @return string|null The detailed error message, or null if no error.
     */
    public function getError(): ?string
    {
        global $bearsamppLang;
        if ($this->latestError != self::WIN32_NO_ERROR) {
            return $bearsamppLang->getValue(Lang::ERROR) . ' ' .
                $this->latestError . ' (' . hexdec($this->latestError) . ' : ' . $this->getWin32ErrorCodeDesc($this->latestError) . ')';
        } elseif ($this->latestStatus != self::WIN32_SERVICE_NA) {
            return $bearsamppLang->getValue(Lang::STATUS) . ' ' .
                $this->latestStatus . ' (' . hexdec($this->latestStatus) . ' : ' . $this->getWin32ServiceStatusDesc($this->latestStatus) . ')';
        }

        return null;
    }
}
