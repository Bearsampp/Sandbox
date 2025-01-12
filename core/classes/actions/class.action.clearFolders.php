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
        global $bearsamppAction, $bearsamppWinbinder;

        // Correctly purges old log entries from bearsampp.ini
        $args = [];
        new ActionRebuildini($args);

        $logs = TplAppLogs::process();

        // Trigger the reload action
        new ActionReload($args);

        $reloadActions = TplAppReload::process();
    }
}
