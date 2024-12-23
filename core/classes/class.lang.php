<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License: GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Lang
 *
 * This class contains constants used for localization and internationalization
 * within the Bearsampp application. The constants are categorized into various
 * sections such as General, Single, Menu, Bins, Apps, Tools, Errors, Actions,
 * Windows forms, and Homepage. Each constant represents a key that can be used
 * to retrieve localized strings from a language file or database.
 */
class Lang
{
    // General
    public const ALL_RUNNING_HINT = 'allRunningHint';
    public const SOME_RUNNING_HINT = 'someRunningHint';
    public const NONE_RUNNING_HINT = 'noneRunningHint';

    // Single
    public const ABOUT = 'about';
    public const ADMINISTRATION = 'administration';
    public const ALIASES = 'aliases';
    public const APPS = 'apps';
    public const BINS = 'bins';
    public const CHANGELOG = 'changelog';
    public const CONSOLE = 'console';
    public const DEBUG = 'debug';
    public const DISABLED = 'disabled';
    public const DISCORD = 'discord';
    public const DONATE = 'donate';
    public const DONATE_VIA = 'donateVia';
    public const DOWNLOAD = 'download';
    public const DOWNLOAD_MORE = 'downloadMore';
    public const ENABLED = 'enabled';
    public const ERROR = 'error';
    public const EXECUTABLE = 'executable';
    public const EXTENSIONS = 'extensions';
    public const FACEBOOK = 'facebook';
    public const GIT_CONSOLE = 'gitConsole';
    public const GITGUI = 'gitGui';
    public const GITHUB = 'github';
    public const HELP = 'help';
    public const HOSTSEDITOR = 'hostseditor';
    public const LANG = 'lang';
    public const LICENSE = 'license';
    public const LOCALE = 'locale';
    public const LOGS = 'logs';
    public const LOGS_VERBOSE = 'logsVerbose';
    public const MODULES = 'modules';
    public const NAME = 'name';
    public const PAYPAL = 'paypal';
    public const PYTHON_CONSOLE = 'pythonConsole';
    public const PWGEN = 'pwgen';
    public const QUIT = 'quit';
    public const READ_CHANGELOG = 'readChangelog';
    public const RELOAD = 'reload';
    public const REPOS = 'repos';
    public const RESTART = 'restart';
    public const SERVICE = 'service';
    public const SETTINGS = 'settings';
    public const SSL = 'ssl';
    public const STARTUP = 'startup';
    public const STATUS = 'status';
    public const STATUS_PAGE = 'statusPage';
    public const TARGET = 'target';
    public const TOOLS = 'tools';
    public const VERBOSE_DEBUG = 'verboseDebug';
    public const VERBOSE_REPORT = 'verboseReport';
    public const VERBOSE_SIMPLE = 'verboseSimple';
    public const VERBOSE_TRACE = 'verboseTrace';
    public const VERSION = 'version';
    public const VERSIONS = 'versions';
    public const VERSION_URL = 'https://github.com/Bearsampp/Bearsampp/releases/tag/';
    public const VIRTUAL_HOSTS = 'virtualHosts';
    public const WEBSITE = 'website';

    // Menu
    public const MENU_ABOUT = 'menuAbout';
    public const MENU_ACCESS_LOGS = 'menuAccessLogs';
    public const MENU_ADD_ALIAS = 'menuAddAlias';
    public const MENU_ADD_VHOST = 'menuAddVhost';
    public const MENU_CHANGE_PORT = 'menuChangePort';
    public const MENU_CHANGE_ROOT_PWD = 'menuChangeRootPwd';
    public const MENU_CHECK_PORT = 'menuCheckPort';
    public const MENU_CHECK_UPDATE = 'menuCheckUpdate';
    public const MENU_CLEAR_FOLDERS = 'menuClearFolders';
    public const MENU_EDIT_ALIAS = 'menuEditAlias';
    public const MENU_EDIT_CONF = 'menuEditConf';
    public const MENU_EDIT_VHOST = 'menuEditVhost';
    public const MENU_ENABLE = 'menuEnable';
    public const MENU_ERROR_LOGS = 'menuErrorLogs';
    public const MENU_GEN_SSL_CERTIFICATE = 'menuGenSslCertificate';
    public const MENU_INSTALL_SERVICE = 'menuInstallService';
    public const MENU_LAUNCH_STARTUP = 'menuLaunchStartup';
    public const MENU_LOCALHOST = 'menuLocalhost';
    public const MENU_LOGS = 'menuLogs';
    public const MENU_PUT_OFFLINE = 'menuPutOffline';
    public const MENU_PUT_ONLINE = 'menuPutOnline';
    public const MENU_REBUILD_INI = 'menuRebuildIni';
    public const MENU_REFRESH_REPOS = 'menuRefreshRepos';
    public const MENU_REMOVE_SERVICE = 'menuRemoveService';
    public const MENU_RESTART_SERVICE = 'menuRestartService';
    public const MENU_RESTART_SERVICES = 'menuRestartServices';
    public const MENU_REWRITE_LOGS = 'menuRewriteLogs';
    public const MENU_SCAN_REPOS_STARTUP = 'menuScanReposStartup';
    public const MENU_SESSION_LOGS = 'menuSessionLogs';
    public const MENU_START_SERVICE = 'menuStartService';
    public const MENU_START_SERVICES = 'menuStartServices';
    public const MENU_STATS_LOGS = 'menuStatsLogs';
    public const MENU_STOP_SERVICE = 'menuStopService';
    public const MENU_STOP_SERVICES = 'menuStopServices';
    public const MENU_TRANSFER_LOGS = 'menuTransferLogs';
    public const MENU_UPDATE_ENV_PATH = 'menuUpdateEnvPath';
    public const MENU_WWW_DIRECTORY = 'menuWwwDirectory';

    // Bins
    public const APACHE = 'apache';
    public const FILEZILLA = 'filezilla';
    public const PHP = 'php';
    public const PEAR = 'pear';
    public const MEMCACHED = 'memcached';
    public const MAILHOG = 'mailhog';
    public const MAILPIT = 'mailpit';
    public const MARIADB = 'mariadb';
    public const MYSQL = 'mysql';
    public const NODEJS = 'nodejs';
    public const POSTGRESQL = 'postgresql';
    public const XLIGHT = 'xlight';

    // Apps
    public const PHPMYADMIN = 'phpmyadmin';
    public const WEBGRIND = 'webgrind';
    public const ADMINER = 'adminer';
    public const PHPPGADMIN = 'phppgadmin';

    // Tools
    public const BRUNO = 'bruno';
    public const COMPOSER = 'composer';
    public const CONSOLEZ = 'consolez';
    public const GHOSTSCRIPT = 'ghostscript';
    public const GIT = 'git';
    public const NGROK = 'ngrok';
    public const PERL = 'perl';
    public const PYTHON = 'python';
    public const RUBY = 'ruby';
    public const XDC = 'xdc';
    public const YARN = 'yarn';

    // Errors
    public const ERROR_CONF_NOT_FOUND = 'errorConfNotFound';
    public const ERROR_EXE_NOT_FOUND = 'errorExeNotFound';
    public const ERROR_FILE_NOT_FOUND = 'errorFileNotFound';
    public const ERROR_INVALID_PARAMETER = 'errorInvalidParameter';

    // Action Switch version
    public const SWITCH_VERSION_TITLE = 'switchVersionTitle';
    public const SWITCH_VERSION_RELOAD_CONFIG = 'switchVersionReloadConfig';
    public const SWITCH_VERSION_RELOAD_BINS = 'switchVersionReloadBins';
    public const SWITCH_VERSION_REGISTRY = 'switchVersionRegistry';
    public const SWITCH_VERSION_RESET_SERVICES = 'switchVersionResetServices';
    public const SWITCH_VERSION_SAME_ERROR = 'switchVersionSameError';
    public const SWITCH_VERSION_OK = 'switchVersionOk';
    public const SWITCH_VERSION_OK_RESTART = 'switchVersionOkRestart';
    public const APACHE_INCPT = 'apacheIncpt';
    public const PHP_INCPT = 'phpIncpt';
    public const BEARSAMPP_CONF_NOT_FOUND_ERROR = 'bearsamppConfNotFoundError';
    public const BEARSAMPP_CONF_MALFORMED_ERROR = 'bearsamppConfMalformedError';

    // Action Switch PHP setting
    public const SWITCH_PHP_SETTING_TITLE = 'switchPhpSettingTitle';
    public const SWITCH_PHP_SETTING_NOT_FOUND = 'switchPhpSettingNotFound';

    // Action Check port
    public const CHECK_PORT_TITLE = 'checkPortTitle';
    public const PORT_USED_BY = 'portUsedBy';
    public const PORT_NOT_USED = 'portNotUsed';
    public const PORT_NOT_USED_BY = 'portNotUsedBy';
    public const PORT_USED_BY_ANOTHER_DBMS = 'portUsedByAnotherDbms';
    public const PORT_CHANGED = 'portChanged';

    // Action Install service
    public const INSTALL_SERVICE_TITLE = 'installServiceTitle';
    public const SERVICE_ALREADY_INSTALLED = 'serviceAlreadyInstalled';
    public const SERVICE_INSTALLED = 'serviceInstalled';
    public const SERVICE_INSTALL_ERROR = 'serviceInstallError';

    // Action Remove service
    public const REMOVE_SERVICE_TITLE = 'removeServiceTitle';
    public const SERVICE_NOT_EXIST = 'serviceNotExist';
    public const SERVICE_REMOVED = 'serviceRemoved';
    public const SERVICE_REMOVE_ERROR = 'serviceRemoveError';

    // Action Start service
    public const START_SERVICE_TITLE = 'startServiceTitle';
    public const START_SERVICE_ERROR = 'startServiceError';

    // Action Stop service
    public const STOP_SERVICE_TITLE = 'stopServiceTitle';
    public const STOP_SERVICE_ERROR = 'stopServiceError';

    // Action Restart service
    public const RESTART_SERVICE_TITLE = 'restartServiceTitle';
    public const RESTART_SERVICE_ERROR = 'restartServiceError';

    // Action Delete alias
    public const DELETE_ALIAS_TITLE = 'deleteAliasTitle';
    public const DELETE_ALIAS = 'deleteAlias';
    public const ALIAS_REMOVED = 'aliasRemoved';
    public const ALIAS_REMOVE_ERROR = 'aliasRemoveError';

    // Action Add/Edit alias
    public const ADD_ALIAS_TITLE = 'addAliasTitle';
    public const ALIAS_NAME_LABEL = 'aliasNameLabel';
    public const ALIAS_DEST_LABEL = 'aliasDestLabel';
    public const ALIAS_EXP_LABEL = 'aliasExpLabel';
    public const ALIAS_DEST_PATH = 'aliasDestPath';
    public const ALIAS_NOT_VALID_ALPHA = 'aliasNotValidAlpha';
    public const ALIAS_ALREADY_EXISTS = 'aliasAlreadyExists';
    public const ALIAS_CREATED = 'aliasCreated';
    public const ALIAS_CREATED_ERROR = 'aliasCreatedError';
    public const EDIT_ALIAS_TITLE = 'editAliasTitle';

    // Action Delete vhost
    public const DELETE_VHOST_TITLE = 'deleteVhostTitle';
    public const DELETE_VHOST = 'deleteVhost';
    public const VHOST_REMOVED = 'vhostRemoved';
    public const VHOST_REMOVE_ERROR = 'vhostRemoveError';

    // Action Add/Edit vhost
    public const ADD_VHOST_TITLE = 'addVhostTitle';
    public const VHOST_SERVER_NAME_LABEL = 'vhostServerNameLabel';
    public const VHOST_DOCUMENT_ROOT_LABEL = 'vhostDocumentRootLabel';
    public const VHOST_EXP_LABEL = 'vhostExpLabel';
    public const VHOST_DOC_ROOT_PATH = 'vhostDocRootPath';
    public const VHOST_NOT_VALID_DOMAIN = 'vhostNotValidDomain';
    public const VHOST_ALREADY_EXISTS = 'vhostAlreadyExists';
    public const VHOST_CREATED = 'vhostCreated';
    public const VHOST_CREATED_ERROR = 'vhostCreatedError';
    public const EDIT_VHOST_TITLE = 'editVhostTitle';

    // Action Change port
    public const CHANGE_PORT_TITLE = 'changePortTitle';
    public const CHANGE_PORT_CURRENT_LABEL = 'changePortCurrentLabel';
    public const CHANGE_PORT_NEW_LABEL = 'changePortNewLabel';
    public const CHANGE_PORT_SAME_ERROR = 'changePortSameError';

    // Action Change database root password
    public const CHANGE_DB_ROOT_PWD_TITLE = 'changeDbRootPwdTitle';
    public const CHANGE_DB_ROOT_PWD_CURRENTPWD_LABEL = 'changeDbRootPwdCurrentpwdLabel';
    public const CHANGE_DB_ROOT_PWD_NEWPWD1_LABEL = 'changeDbRootPwdNewpwd1Label';
    public const CHANGE_DB_ROOT_PWD_NEWPWD2_LABEL = 'changeDbRootPwdNewpwd2Label';
    public const CHANGE_DB_ROOT_PWD_NOTSAME_ERROR = 'changeDbRootPwdNotsameError';
    public const CHANGE_DB_ROOT_PWD_INCORRECT_ERROR = 'changeDbRootPwdIncorrectError';
    public const CHANGE_DB_ROOT_PWD_TEXT = 'changeDbRootPwdText';

    // Action Startup
    public const STARTUP_STARTING_TEXT = 'startupStartingText';
    public const STARTUP_ROTATION_LOGS_TEXT = 'startupRotationLogsText';
    public const STARTUP_KILL_OLD_PROCS_TEXT = 'startupKillOldProcsText';
    public const STARTUP_REFRESH_HOSTNAME_TEXT = 'startupRefreshHostnameText';
    public const STARTUP_CHECK_BROWSER_TEXT = 'startupCheckBrowserText';
    public const STARTUP_SYS_INFOS = 'startupSysInfos';
    public const STARTUP_CLEAN_TMP_TEXT = 'startupCleanTmpText';
    public const STARTUP_CLEAN_OLD_BEHAVIORS_TEXT = 'startupCleanOldBehaviorsText';
    public const STARTUP_REFRESH_ALIAS_TEXT = 'startupRefreshAliasText';
    public const STARTUP_REFRESH_VHOSTS_TEXT = 'startupRefreshVhostsText';
    public const STARTUP_CHECK_PATH_TEXT = 'startupCheckPathText';
    public const STARTUP_SCAN_FOLDERS_TEXT = 'startupScanFoldersText';
    public const STARTUP_CHANGE_PATH_TEXT = 'startupChangePathText';
    public const STARTUP_REGISTRY_TEXT = 'startupRegistryText';
    public const STARTUP_REGISTRY_ERROR_TEXT = 'startupRegistryErrorText';
    public const STARTUP_UPDATE_CONFIG_TEXT = 'startupUpdateConfigText';
    public const STARTUP_CHECK_SERVICE_TEXT = 'startupCheckServiceText';
    public const STARTUP_INSTALL_SERVICE_TEXT = 'startupInstallServiceText';
    public const STARTUP_START_SERVICE_TEXT = 'startupStartServiceText';
    public const STARTUP_PREPARE_RESTART_TEXT = 'startupPrepareRestartText';
    public const STARTUP_ERROR_TITLE = 'startupErrorTitle';
    public const STARTUP_SERVICE_ERROR = 'startupServiceError';
    public const STARTUP_SERVICE_CREATE_ERROR = 'startupServiceCreateError';
    public const STARTUP_SERVICE_START_ERROR = 'startupServiceStartError';
    public const STARTUP_SERVICE_SYNTAX_ERROR = 'startupServiceSyntaxError';
    public const STARTUP_SERVICE_PORT_ERROR = 'startupServicePortError';
    public const STARTUP_REFRESH_GIT_REPOS_TEXT = 'startupRefreshGitReposText';
    public const STARTUP_GEN_SSL_CRT_TEXT = 'startupGenSslCrtText';

    // Action Quit
    public const EXIT_LEAVING_TEXT = 'exitLeavingText';
    public const EXIT_REMOVE_SERVICE_TEXT = 'exitRemoveServiceText';
    public const EXIT_STOP_OTHER_PROCESS_TEXT = 'exitStopOtherProcessText';

    // Action Change browser
    public const CHANGE_BROWSER_TITLE = 'changeBrowserTitle';
    public const CHANGE_BROWSER_EXP_LABEL = 'changeBrowserExpLabel';
    public const CHANGE_BROWSER_OTHER_LABEL = 'changeBrowserOtherLabel';
    public const CHANGE_BROWSER_OK = 'changeBrowserOk';

    // Action About
    public const ABOUT_TITLE = 'aboutTitle';
    public const ABOUT_TEXT = 'aboutText';

    // Action Debug Apache
    public const DEBUG_APACHE_VERSION_NUMBER = 'debugApacheVersionNumber';
    public const DEBUG_APACHE_COMPILE_SETTINGS = 'debugApacheCompileSettings';
    public const DEBUG_APACHE_COMPILED_MODULES = 'debugApacheCompiledModules';
    public const DEBUG_APACHE_CONFIG_DIRECTIVES = 'debugApacheConfigDirectives';
    public const DEBUG_APACHE_VHOSTS_SETTINGS = 'debugApacheVhostsSettings';
    public const DEBUG_APACHE_LOADED_MODULES = 'debugApacheLoadedModules';
    public const DEBUG_APACHE_SYNTAX_CHECK = 'debugApacheSyntaxCheck';

    // Action Debug MySQL
    public const DEBUG_MYSQL_VERSION = 'debugMysqlVersion';
    public const DEBUG_MYSQL_VARIABLES = 'debugMysqlVariables';
    public const DEBUG_MYSQL_SYNTAX_CHECK = 'debugMysqlSyntaxCheck';

    // Action Debug MariaDB
    public const DEBUG_MARIADB_VERSION = 'debugMariadbVersion';
    public const DEBUG_MARIADB_VARIABLES = 'debugMariadbVariables';
    public const DEBUG_MARIADB_SYNTAX_CHECK = 'debugMariadbSyntaxCheck';

    // Action Debug PostgreSQL
    public const DEBUG_POSTGRESQL_VERSION = 'debugPostgresqlVersion';

    // Action others
    public const REGISTRY_SET_ERROR_TEXT = 'registrySetErrorText';

    // Action check version
    public const CHECK_VERSION_TITLE = 'checkVersionTitle';
    public const CHECK_VERSION_AVAILABLE_TEXT = 'checkVersionAvailableText';
    public const CHECK_VERSION_CHANGELOG_TEXT = 'checkVersionChangelogText';
    public const CHECK_VERSION_LATEST_TEXT = 'checkVersionLatestText';

    // Action gen SSL certificate
    public const GENSSL_TITLE = 'genSslTitle';
    public const GENSSL_PATH = 'genSslPath';
    public const GENSSL_CREATED = 'genSslCreated';
    public const GENSSL_CREATED_ERROR = 'genSslCreatedError';

    // Action restart
    public const RESTART_TITLE = 'restartTitle';
    public const RESTART_TEXT = 'restartText';

    // Action enable
    public const ENABLE_TITLE = 'enableTitle';
    public const ENABLE_BUNDLE_NOT_EXIST = 'enableBundleNotExist';

    // Windows forms
    public const BUTTON_OK = 'buttonOk';
    public const BUTTON_DELETE = 'buttonDelete';
    public const BUTTON_SAVE = 'buttonSave';
    public const BUTTON_FINISH = 'buttonFinish';
    public const BUTTON_CANCEL = 'buttonCancel';
    public const BUTTON_NEXT = 'buttonNext';
    public const BUTTON_BACK = 'buttonBack';
    public const BUTTON_BROWSE = 'buttonBrowse';
    public const LOADING = 'loading';

    // Homepage
    public const HOMEPAGE_OFFICIAL_WEBSITE = 'homepageOfficialWebsite';
    public const HOMEPAGE_SERVICE_STARTED = 'homepageServiceStarted';
    public const HOMEPAGE_SERVICE_STOPPED = 'homepageServiceStopped';
    public const HOMEPAGE_ABOUT_HTML = 'homepageAboutHtml';
    public const HOMEPAGE_LICENSE_TEXT = 'homepageLicenseText';
    public const HOMEPAGE_QUESTIONS_TITLE = 'homepageQuestionsTitle';
    public const HOMEPAGE_QUESTIONS_TEXT = 'homepageQuestionsText';
    public const HOMEPAGE_POST_ISSUE = 'homepagePostIssue';
    public const HOMEPAGE_PHPINFO_TEXT = 'homepagePhpinfoText';
    public const HOMEPAGE_APC_TEXT = 'homepageApcText';
    public const HOMEPAGE_MAILHOG_TEXT = 'homepageMailhogText';
    public const HOMEPAGE_MAILPIT_TEXT = 'homepageMailpitText';
    public const HOMEPAGE_XLIGHT_TEXT = 'homepageXlightText';
    public const HOMEPAGE_BACK_TEXT = 'homepageBackText';

    /**
     * Get all the keys defined in the Lang class.
     *
     * This method returns an array of all the constants defined in the Lang class.
     * These constants are used as keys for localization and internationalization
     * purposes within the Bearsampp application.
     *
     * @return array An array of all the localization keys.
     */
    public static function getKeys(): array
    {
        return [
            // General
            self::ALL_RUNNING_HINT,
            self::SOME_RUNNING_HINT,
            self::NONE_RUNNING_HINT,

            // Single
            self::ABOUT,
            self::ADMINISTRATION,
            self::ALIASES,
            self::APPS,
            self::BINS,
            self::CHANGELOG,
            self::CONSOLE,
            self::DEBUG,
            self::DISABLED,
            self::DONATE,
            self::DONATE_VIA,
            self::DOWNLOAD,
            self::DOWNLOAD_MORE,
            self::ENABLED,
            self::ERROR,
            self::EXECUTABLE,
            self::EXTENSIONS,
            self::GIT_CONSOLE,
            self::GITHUB,
            self::HELP,
            self::HOSTSEDITOR,
            self::LANG,
            self::LICENSE,
            self::LOGS,
            self::LOGS_VERBOSE,
            self::MODULES,
            self::NAME,
            self::PAYPAL,
            self::PYTHON_CONSOLE,
            self::PWGEN,
            self::QUIT,
            self::READ_CHANGELOG,
            self::RELOAD,
            self::REPOS,
            self::RESTART,
            self::SERVICE,
            self::SETTINGS,
            self::SSL,
            self::STARTUP,
            self::STATUS,
            self::STATUS_PAGE,
            self::TARGET,
            self::TOOLS,
            self::VERBOSE_DEBUG,
            self::VERBOSE_REPORT,
            self::VERBOSE_SIMPLE,
            self::VERBOSE_TRACE,
            self::VERSION,
            self::VERSIONS,
            self::VIRTUAL_HOSTS,
            self::WEBSITE,

            // Menu
            self::MENU_ABOUT,
            self::MENU_ACCESS_LOGS,
            self::MENU_ADD_ALIAS,
            self::MENU_ADD_VHOST,
            self::MENU_CHANGE_PORT,
            self::MENU_CHANGE_ROOT_PWD,
            self::MENU_CHECK_PORT,
            self::MENU_CHECK_UPDATE,
            self::MENU_CLEAR_FOLDERS,
            self::MENU_EDIT_ALIAS,
            self::MENU_EDIT_VHOST,
            self::MENU_ENABLE,
            self::MENU_ERROR_LOGS,
            self::MENU_GEN_SSL_CERTIFICATE,
            self::MENU_INSTALL_SERVICE,
            self::MENU_LAUNCH_STARTUP,
            self::MENU_LOCALHOST,
            self::MENU_LOGS,
            self::MENU_PUT_OFFLINE,
            self::MENU_PUT_ONLINE,
            self::MENU_REBUILD_INI,
            self::MENU_REFRESH_REPOS,
            self::MENU_REMOVE_SERVICE,
            self::MENU_RESTART_SERVICE,
            self::MENU_RESTART_SERVICES,
            self::MENU_REWRITE_LOGS,
            self::MENU_SCAN_REPOS_STARTUP,
            self::MENU_SESSION_LOGS,
            self::MENU_START_SERVICE,
            self::MENU_START_SERVICES,
            self::MENU_STATS_LOGS,
            self::MENU_STOP_SERVICE,
            self::MENU_STOP_SERVICES,
            self::MENU_TRANSFER_LOGS,
            self::MENU_UPDATE_ENV_PATH,
            self::MENU_WWW_DIRECTORY,

            // Bins
            self::APACHE,
            self::FILEZILLA,
            self::PHP,
            self::PEAR,
            self::MEMCACHED,
            self::MAILHOG,
            self::MAILPIT,
            self::MARIADB,
            self::MYSQL,
            self::NODEJS,
            self::POSTGRESQL,
            self::XLIGHT,

            // Apps
            self::PHPMYADMIN,
            self::WEBGRIND,
            self::ADMINER,
            self::PHPPGADMIN,

            // Tools
            self::BRUNO,
            self::COMPOSER,
            self::CONSOLEZ,
            self::GIT,
            self::NGROK,
            self::PERL,
            self::PYTHON,
            self::RUBY,
            self::XDC,
            self::YARN,

            // Errors
            self::ERROR_CONF_NOT_FOUND,
            self::ERROR_EXE_NOT_FOUND,
            self::ERROR_FILE_NOT_FOUND,
            self::ERROR_INVALID_PARAMETER,

            // Action Switch version
            self::SWITCH_VERSION_TITLE,
            self::SWITCH_VERSION_RELOAD_CONFIG,
            self::SWITCH_VERSION_RELOAD_BINS,
            self::SWITCH_VERSION_REGISTRY,
            self::SWITCH_VERSION_RESET_SERVICES,
            self::SWITCH_VERSION_SAME_ERROR,
            self::SWITCH_VERSION_OK,
            self::SWITCH_VERSION_OK_RESTART,
            self::APACHE_INCPT,
            self::PHP_INCPT,
            self::BEARSAMPP_CONF_NOT_FOUND_ERROR,
            self::BEARSAMPP_CONF_MALFORMED_ERROR,

            // Action Switch PHP setting
            self::SWITCH_PHP_SETTING_TITLE,
            self::SWITCH_PHP_SETTING_NOT_FOUND,

            // Action Check port
            self::CHECK_PORT_TITLE,
            self::PORT_USED_BY,
            self::PORT_NOT_USED,
            self::PORT_NOT_USED_BY,
            self::PORT_USED_BY_ANOTHER_DBMS,
            self::PORT_CHANGED,

            // Action Install service
            self::INSTALL_SERVICE_TITLE,
            self::SERVICE_ALREADY_INSTALLED,
            self::SERVICE_INSTALLED,
            self::SERVICE_INSTALL_ERROR,

            // Action Remove service
            self::REMOVE_SERVICE_TITLE,
            self::SERVICE_NOT_EXIST,
            self::SERVICE_REMOVED,
            self::SERVICE_REMOVE_ERROR,

            // Action Start service
            self::START_SERVICE_TITLE,
            self::START_SERVICE_ERROR,

            // Action Stop service
            self::STOP_SERVICE_TITLE,
            self::STOP_SERVICE_ERROR,

            // Action Restart service
            self::RESTART_SERVICE_TITLE,
            self::RESTART_SERVICE_ERROR,

            // Action Delete alias
            self::DELETE_ALIAS_TITLE,
            self::DELETE_ALIAS,
            self::ALIAS_REMOVED,
            self::ALIAS_REMOVE_ERROR,

            // Action Add/Edit alias
            self::ADD_ALIAS_TITLE,
            self::ALIAS_NAME_LABEL,
            self::ALIAS_DEST_LABEL,
            self::ALIAS_EXP_LABEL,
            self::ALIAS_DEST_PATH,
            self::ALIAS_NOT_VALID_ALPHA,
            self::ALIAS_ALREADY_EXISTS,
            self::ALIAS_CREATED,
            self::ALIAS_CREATED_ERROR,
            self::EDIT_ALIAS_TITLE,

            // Action Delete vhost
            self::DELETE_VHOST_TITLE,
            self::DELETE_VHOST,
            self::VHOST_REMOVED,
            self::VHOST_REMOVE_ERROR,

            // Action Add/Edit vhost
            self::ADD_VHOST_TITLE,
            self::VHOST_SERVER_NAME_LABEL,
            self::VHOST_DOCUMENT_ROOT_LABEL,
            self::VHOST_EXP_LABEL,
            self::VHOST_DOC_ROOT_PATH,
            self::VHOST_NOT_VALID_DOMAIN,
            self::VHOST_ALREADY_EXISTS,
            self::VHOST_CREATED,
            self::VHOST_CREATED_ERROR,
            self::EDIT_VHOST_TITLE,

            // Action Change port
            self::CHANGE_PORT_TITLE,
            self::CHANGE_PORT_CURRENT_LABEL,
            self::CHANGE_PORT_NEW_LABEL,
            self::CHANGE_PORT_SAME_ERROR,

            // Action Change database root password
            self::CHANGE_DB_ROOT_PWD_TITLE,
            self::CHANGE_DB_ROOT_PWD_CURRENTPWD_LABEL,
            self::CHANGE_DB_ROOT_PWD_NEWPWD1_LABEL,
            self::CHANGE_DB_ROOT_PWD_NEWPWD2_LABEL,
            self::CHANGE_DB_ROOT_PWD_NOTSAME_ERROR,
            self::CHANGE_DB_ROOT_PWD_INCORRECT_ERROR,
            self::CHANGE_DB_ROOT_PWD_TEXT,

            // Action Startup
            self::STARTUP_STARTING_TEXT,
            self::STARTUP_ROTATION_LOGS_TEXT,
            self::STARTUP_KILL_OLD_PROCS_TEXT,
            self::STARTUP_REFRESH_HOSTNAME_TEXT,
            self::STARTUP_CHECK_BROWSER_TEXT,
            self::STARTUP_SYS_INFOS,
            self::STARTUP_CLEAN_TMP_TEXT,
            self::STARTUP_CLEAN_OLD_BEHAVIORS_TEXT,
            self::STARTUP_REFRESH_ALIAS_TEXT,
            self::STARTUP_REFRESH_VHOSTS_TEXT,
            self::STARTUP_CHECK_PATH_TEXT,
            self::STARTUP_SCAN_FOLDERS_TEXT,
            self::STARTUP_CHANGE_PATH_TEXT,
            self::STARTUP_REGISTRY_TEXT,
            self::STARTUP_REGISTRY_ERROR_TEXT,
            self::STARTUP_UPDATE_CONFIG_TEXT,
            self::STARTUP_CHECK_SERVICE_TEXT,
            self::STARTUP_INSTALL_SERVICE_TEXT,
            self::STARTUP_START_SERVICE_TEXT,
            self::STARTUP_PREPARE_RESTART_TEXT,
            self::STARTUP_ERROR_TITLE,
            self::STARTUP_SERVICE_ERROR,
            self::STARTUP_SERVICE_CREATE_ERROR,
            self::STARTUP_SERVICE_START_ERROR,
            self::STARTUP_SERVICE_SYNTAX_ERROR,
            self::STARTUP_SERVICE_PORT_ERROR,
            self::STARTUP_REFRESH_GIT_REPOS_TEXT,
            self::STARTUP_GEN_SSL_CRT_TEXT,

            // Action Quit
            self::EXIT_LEAVING_TEXT,
            self::EXIT_REMOVE_SERVICE_TEXT,
            self::EXIT_STOP_OTHER_PROCESS_TEXT,

            // Action Change browser
            self::CHANGE_BROWSER_TITLE,
            self::CHANGE_BROWSER_EXP_LABEL,
            self::CHANGE_BROWSER_OTHER_LABEL,
            self::CHANGE_BROWSER_OK,

            // Action About
            self::ABOUT_TITLE,
            self::ABOUT_TEXT,

            // Action Debug Apache
            self::DEBUG_APACHE_VERSION_NUMBER,
            self::DEBUG_APACHE_COMPILE_SETTINGS,
            self::DEBUG_APACHE_COMPILED_MODULES,
            self::DEBUG_APACHE_CONFIG_DIRECTIVES,
            self::DEBUG_APACHE_VHOSTS_SETTINGS,
            self::DEBUG_APACHE_LOADED_MODULES,
            self::DEBUG_APACHE_SYNTAX_CHECK,

            // Action Debug MySQL
            self::DEBUG_MYSQL_VERSION,
            self::DEBUG_MYSQL_VARIABLES,
            self::DEBUG_MYSQL_SYNTAX_CHECK,

            // Action Debug MariaDB
            self::DEBUG_MARIADB_VERSION,
            self::DEBUG_MARIADB_VARIABLES,
            self::DEBUG_MARIADB_SYNTAX_CHECK,

            // Action Debug PostgreSQL
            self::DEBUG_POSTGRESQL_VERSION,

            // Action others
            self::REGISTRY_SET_ERROR_TEXT,

            // Action check version
            self::CHECK_VERSION_TITLE,
            self::CHECK_VERSION_AVAILABLE_TEXT,
            self::CHECK_VERSION_CHANGELOG_TEXT,
            self::CHECK_VERSION_LATEST_TEXT,

            // Action gen SSL certificate
            self::GENSSL_TITLE,
            self::GENSSL_PATH,
            self::GENSSL_CREATED,
            self::GENSSL_CREATED_ERROR,

            // Action restart
            self::RESTART_TITLE,
            self::RESTART_TEXT,

            // Action enable
            self::ENABLE_TITLE,
            self::ENABLE_BUNDLE_NOT_EXIST,

            // Windows forms
            self::BUTTON_OK,
            self::BUTTON_DELETE,
            self::BUTTON_SAVE,
            self::BUTTON_FINISH,
            self::BUTTON_CANCEL,
            self::BUTTON_NEXT,
            self::BUTTON_BACK,
            self::BUTTON_BROWSE,
            self::LOADING,

            // Homepage
            self::HOMEPAGE_OFFICIAL_WEBSITE,
            self::HOMEPAGE_SERVICE_STARTED,
            self::HOMEPAGE_SERVICE_STOPPED,
            self::HOMEPAGE_ABOUT_HTML,
            self::HOMEPAGE_LICENSE_TEXT,
            self::HOMEPAGE_QUESTIONS_TITLE,
            self::HOMEPAGE_QUESTIONS_TEXT,
            self::HOMEPAGE_POST_ISSUE,
            self::HOMEPAGE_PHPINFO_TEXT,
            self::HOMEPAGE_APC_TEXT,
            self::HOMEPAGE_MAILHOG_TEXT,
            self::HOMEPAGE_MAILPIT_TEXT,
            self::HOMEPAGE_XLIGHT_TEXT,
            self::HOMEPAGE_BACK_TEXT,
        ];
    }
}
