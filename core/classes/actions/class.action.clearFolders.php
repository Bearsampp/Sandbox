<?php

class ActionClearFolders
{
    public function __construct($args)
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppWinbinder;

        // Clear specific temporary folders in the root temporary path
        Util::clearFolder($bearsamppRoot->getTmpPath(), array('cachegrind', 'composer', 'openssl', 'mailhog', 'mailpit', 'xlight', 'npm-cache', 'pip', 'yarn', '.gitignore'));

        // Clear logs
        Util::clearFolder(
            $bearsamppRoot->getLogsPath(),
            array('mailpit.err.log', 'mailpit.out.log', 'memcached.err.log', 'memcached.out.log', 'xlight.err.log', 'xlight.log', '.gitignore')
        );

        // Clear the core temporary path
        Util::clearFolder($bearsamppCore->getTmpPath(), array('.gitignore'));

        // Handle the logs menu and reload
        $this->handleLogsAndReload();
    }

    private function handleLogsAndReload()
    {
        global $bearsamppWinbinder;

        // Generate menu items for each log file
        $window = TplAppLogs::process();

        // Destroy the logs window
        $this->destroyWindow($window);

        // Regenerate the logs menu items
        $logsMenuItems = TplAppLogs::process();

        // Regenerate the reload menu item
        $reloadMenuItem = TplAppReload::process();

        // Reload the application
        $args = []; // Define any necessary arguments
        new ActionReload($args);
    }

    /**
     * Destroys a window.
     *
     * @param   mixed  $window  The window object to destroy.
     */
    public function destroyWindow($window)
    {
        global $bearsamppWinbinder;
        $this->callWinBinder('wb_destroy_window', array($window), true);
    }

    /**
     * Calls a WinBinder function with the specified parameters.
     *
     * @param   string  $function            The name of the WinBinder function to call.
     * @param   array   $params              The parameters to pass to the function.
     * @param   bool    $removeErrorHandler  Whether to remove the error handler during the call.
     *
     * @return mixed The result of the function call.
     */
    private function callWinBinder($function, $params = array(), $removeErrorHandler = false)
    {
        $result = false;
        if (function_exists($function)) {
            if ($removeErrorHandler) {
                $result = @call_user_func_array($function, $params);
            } else {
                $result = call_user_func_array($function, $params);
            }
        }

        return $result;
    }
}
