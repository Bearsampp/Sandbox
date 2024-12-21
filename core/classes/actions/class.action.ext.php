<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionExt handles the execution of various extended actions.
 */
class ActionExt
{
    // Constants for different actions
    public const START = 'start';
    public const STOP = 'stop';
    public const RELOAD = 'reload';
    public const REFRESH = 'refresh';

    // Constants for status codes
    public const STATUS_ERROR = 2;
    public const STATUS_WARNING = 1;
    public const STATUS_SUCCESS = 0;

    /**
     * Holds the current status of the action.
     *
     * @var int
     */
    private int $status = self::STATUS_SUCCESS;

    /**
     * Holds the logs generated during the action execution.
     *
     * @var string
     */
    private string $logs = '';

    /**
     * Constructor for the ActionExt class.
     *
     * @param array $args The command line arguments passed to the action.
     */
    public function __construct(array $args)
    {
        if (!isset($args[0]) || empty($args[0])) {
            $this->addLog('No args defined');
            $this->addLog('Available args:');
            foreach ($this->getProcs() as $proc) {
                $this->addLog('- ' . $proc);
            }
            $this->setStatus(self::STATUS_ERROR);
            $this->sendLogs();
            return;
        }

        $action = $args[0];

        $newArgs = [];
        foreach ($args as $key => $arg) {
            if ($key > 0) {
                $newArgs[] = $arg;
            }
        }

        $method = 'proc' . ucfirst($action);
        if (!method_exists($this, $method)) {
            $this->addLog('Unknown arg: ' . $action);
            $this->addLog('Available args:');
            foreach ($this->getProcs() as $procName => $procDesc) {
                $this->addLog('- ' . $procName . ': ' . $procDesc);
            }
            $this->setStatus(self::STATUS_ERROR);
            $this->sendLogs();
            return;
        }

        call_user_func([$this, $method], $newArgs);
        $this->sendLogs();
    }

    /**
     * Retrieves the list of available actions.
     *
     * @return array The list of available actions.
     */
    private function getProcs(): array
    {
        return [
            self::START,
            self::STOP,
            self::RELOAD,
            self::REFRESH
        ];
    }

    /**
     * Adds a log entry to the logs.
     *
     * @param string $data The log entry to add.
     */
    private function addLog(string $data): void
    {
        $this->logs .= $data . "\n";
    }

    /**
     * Sets the status of the action.
     *
     * @param int $status The status code to set.
     */
    private function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * Sends the logs as a JSON-encoded response.
     */
    private function sendLogs(): void
    {
        echo json_encode([
            'status' => $this->status,
            'response' => $this->logs
        ]);
    }

    /**
     * Starts the application.
     *
     * @param array $args The command line arguments passed to the action.
     */
    private function procStart(array $args): void
    {
        global $bearsamppRoot, $bearsamppWinbinder;

        if (!Util::isLaunched()) {
            $this->addLog('Starting ' . APP_TITLE);
            $bearsamppWinbinder->exec($bearsamppRoot->getExeFilePath(), null, false);
        } else {
            $this->addLog(APP_TITLE . ' already started');
            $this->setStatus(self::STATUS_WARNING);
        }
    }

    /**
     * Stops the application and removes services.
     *
     * @param array $args The command line arguments passed to the action.
     */
    private function procStop(array $args): void
    {
        global $bearsamppBins;

        if (Util::isLaunched()) {
            $this->addLog('Remove services');
            foreach ($bearsamppBins->getServices() as $sName => $service) {
                if ($service->delete()) {
                    $this->addLog('- ' . $sName . ': OK');
                } else {
                    $this->addLog('- ' . $sName . ': KO');
                    $this->setStatus(self::STATUS_ERROR);
                }
            }

            $this->addLog('Stop ' . APP_TITLE);
            Batch::exitAppStandalone();
        } else {
            $this->addLog(APP_TITLE . ' already stopped');
            $this->setStatus(self::STATUS_WARNING);
        }
    }

    /**
     * Reloads the application by stopping and starting services.
     *
     * @param array $args The command line arguments passed to the action.
     */
    private function procReload(array $args): void
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppWinbinder;

        if (!Util::isLaunched()) {
            $this->addLog(APP_TITLE . ' is not started.');
            $bearsamppWinbinder->exec($bearsamppRoot->getExeFilePath(), null, false);
            $this->addLog('Start ' . APP_TITLE);
            $this->setStatus(self::STATUS_WARNING);
            return;
        }

        $this->addLog('Remove services');
        foreach ($bearsamppBins->getServices() as $sName => $service) {
            if ($service->delete()) {
                $this->addLog('- ' . $sName . ': OK');
            } else {
                $this->addLog('- ' . $sName . ': KO');
                $this->setStatus(self::STATUS_ERROR);
            }
        }

        Win32Ps::killBins();

        $this->addLog('Start services');
        foreach ($bearsamppBins->getServices() as $sName => $service) {
            $service->create();
            if ($service->start()) {
                $this->addLog('- ' . $sName . ': OK');
            } else {
                $this->addLog('- ' . $sName . ': KO');
                $this->setStatus(self::STATUS_ERROR);
            }
        }
    }

    /**
     * Refreshes the application by calling the reload action.
     *
     * @param array $args The command line arguments passed to the action.
     */
    private function procRefresh(array $args): void
    {
        global $bearsamppAction;

        if (!Util::isLaunched()) {
            $this->addLog(APP_TITLE . ' is not started.');
            $this->setStatus(self::STATUS_ERROR);
            return;
        }

        $bearsamppAction->call(Action::RELOAD);
    }
}
