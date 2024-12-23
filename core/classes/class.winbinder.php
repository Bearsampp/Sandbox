<?php

/**
 * Class WinBinder
 *
 * Provides an interface to the WinBinder library, allowing for the creation and management
 * of Windows GUI elements in PHP. Includes methods for creating windows, controls, handling events,
 * and executing system commands.
 */
class WinBinder
{
    // Constants for control IDs and objects
    public const CTRL_ID = 0;
    public const CTRL_OBJ = 1;

    // Constants for progress bar increment and new line
    public const INCR_PROGRESS_BAR = '++';
    public const NEW_LINE = '@nl@';

    // Constants for message box types
    public const BOX_INFO = WBC_INFO;
    public const BOX_OK = WBC_OK;
    public const BOX_OKCANCEL = WBC_OKCANCEL;
    public const BOX_QUESTION = WBC_QUESTION;
    public const BOX_ERROR = WBC_STOP;
    public const BOX_WARNING = WBC_WARNING;
    public const BOX_YESNO = WBC_YESNO;
    public const BOX_YESNOCANCEL = WBC_YESNOCANCEL;

    // Constants for cursor types
    public const CURSOR_ARROW = 'arrow';
    public const CURSOR_CROSS = 'cross';
    public const CURSOR_FINGER = 'finger';
    public const CURSOR_FORBIDDEN = 'forbidden';
    public const CURSOR_HELP = 'help';
    public const CURSOR_IBEAM = 'ibeam';
    public const CURSOR_NONE = null;
    public const CURSOR_SIZEALL = 'sizeall';
    public const CURSOR_SIZENESW = 'sizenesw';
    public const CURSOR_SIZENS = 'sizens';
    public const CURSOR_SIZENWSE = 'sizenwse';
    public const CURSOR_SIZEWE = 'sizewe';
    public const CURSOR_UPARROW = 'uparrow';
    public const CURSOR_WAIT = 'wait';
    public const CURSOR_WAITARROW = 'waitarrow';

    // Constants for system information types
    public const SYSINFO_SCREENAREA = 'screenarea';
    public const SYSINFO_WORKAREA = 'workarea';

    private string $defaultTitle;
    private int $countCtrls;

    public array $callback;
    public array $gauge;

    /**
     * Initializes the WinBinder class, sets the default window title, and resets control counters.
     */
    public function __construct()
    {
        global $bearsamppCore;
        Util::logInitClass($this);

        $this->defaultTitle = APP_TITLE . ' ' . $bearsamppCore->getAppVersion();
        $this->reset();
    }

    /**
     * Writes a log message to the WinBinder log file.
     *
     * @param   string  $log  The log message to write.
     */
    private static function writeLog(string $log): void
    {
        global $bearsamppRoot;
        Util::logDebug($log, $bearsamppRoot->getWinbinderLogFilePath());
    }

    /**
     * Resets the control counter and callback array.
     */
    public function reset(): void
    {
        $this->countCtrls = 1000;
        $this->callback   = [];
    }

    /**
     * Calls a WinBinder function with the specified parameters.
     *
     * @param   string  $function            The name of the WinBinder function to call.
     * @param   array   $params              The parameters to pass to the function.
     * @param   bool    $removeErrorHandler  Whether to remove the error handler during the call.
     *
     * @return mixed The result of the function call.
     *
     * @throws \Throwable If an exception occurs and error handling is not suppressed.
     */
    private function callWinBinder(string $function, array $params = [], bool $removeErrorHandler = false): mixed
    {
        $result = false;
        if (function_exists($function)) {
            try {
                $result = call_user_func_array($function, $params);
            } catch (\Throwable $e) {
                if (!$removeErrorHandler) {
                    throw $e;
                }
            }
        }

        return $result;
    }

    /**
     * Creates a new window.
     *
     * @param   mixed       $parent   The parent window or null for a top-level window.
     * @param   string      $wclass   The window class.
     * @param   string      $caption  The window caption.
     * @param   int         $xPos     The x-coordinate of the window.
     * @param   int         $yPos     The y-coordinate of the window.
     * @param   int         $width    The width of the window.
     * @param   int         $height   The height of the window.
     * @param   mixed|null  $style    The window style.
     * @param   mixed|null  $params   Additional parameters for the window.
     *
     * @return mixed The created window object.
     */
    public function createWindow(
        mixed $parent,
        string $wclass,
        string $caption,
        int $xPos,
        int $yPos,
        int $width,
        int $height,
        mixed $style = null,
        mixed $params = null
    ): mixed {
        global $bearsamppCore;

        $caption = empty($caption) ? $this->defaultTitle : $this->defaultTitle . ' - ' . $caption;
        $window  = $this->callWinBinder('wb_create_window', [$parent, $wclass, $caption, $xPos, $yPos, $width, $height, $style, $params]);

        // Set tiny window icon
        $this->setImage($window, $bearsamppCore->getIconsPath() . '/app.ico');

        return $window;
    }

    /**
     * Creates a new control.
     *
     * @param   mixed       $parent    The parent window or control.
     * @param   string      $ctlClass  The control class.
     * @param   string      $caption   The control caption.
     * @param   int         $xPos      The x-coordinate of the control.
     * @param   int         $yPos      The y-coordinate of the control.
     * @param   int         $width     The width of the control.
     * @param   int         $height    The height of the control.
     * @param   mixed|null  $style     The control style.
     * @param   mixed|null  $params    Additional parameters for the control.
     *
     * @return array An array containing the control ID and object.
     */
    public function createControl(
        mixed $parent,
        string $ctlClass,
        string $caption,
        int $xPos,
        int $yPos,
        int $width,
        int $height,
        mixed $style = null,
        mixed $params = null
    ): array {
        $this->countCtrls++;

        return [
            self::CTRL_ID  => $this->countCtrls,
            self::CTRL_OBJ => $this->callWinBinder('wb_create_control', [
                $parent,
                $ctlClass,
                $caption,
                $xPos,
                $yPos,
                $width,
                $height,
                $this->countCtrls,
                $style,
                $params
            ]),
        ];
    }

    /**
     * Creates a new application window.
     *
     * @param   string      $caption  The window caption.
     * @param   int         $width    The width of the window.
     * @param   int         $height   The height of the window.
     * @param   mixed|null  $style    The window style.
     * @param   mixed|null  $params   Additional parameters for the window.
     *
     * @return mixed The created window object.
     */
    public function createAppWindow(
        string $caption,
        int $width,
        int $height,
        mixed $style = null,
        mixed $params = null
    ): mixed {
        return $this->createWindow(null, AppWindow, $caption, WBC_CENTER, WBC_CENTER, $width, $height, $style, $params);
    }

    /**
     * Creates a new naked window.
     *
     * @param   string      $caption  The window caption.
     * @param   int         $width    The width of the window.
     * @param   int         $height   The height of the window.
     * @param   mixed|null  $style    The window style.
     * @param   mixed|null  $params   Additional parameters for the window.
     *
     * @return mixed The created window object.
     */
    public function createNakedWindow(
        string $caption,
        int $width,
        int $height,
        mixed $style = null,
        mixed $params = null
    ): mixed {
        $window = $this->createWindow(null, NakedWindow, $caption, WBC_CENTER, WBC_CENTER, $width, $height, $style, $params);
        $this->setArea($window, $width, $height);

        return $window;
    }

    /**
     * Destroys a window.
     *
     * @param   mixed  $window  The window object to destroy.
     */
    public function destroyWindow(mixed $window): void
    {
        $this->callWinBinder('wb_destroy_window', [$window], true);
        exit();
    }

    /**
     * Starts the main event loop.
     *
     * @return mixed The result of the main loop.
     */
    public function mainLoop(): mixed
    {
        return $this->callWinBinder('wb_main_loop');
    }

    /**
     * Refreshes a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to refresh.
     *
     * @return mixed The result of the refresh operation.
     */
    public function refresh(mixed $wbobject): mixed
    {
        return $this->callWinBinder('wb_refresh', [$wbobject, true]);
    }

    /**
     * Retrieves system information.
     *
     * @param   string  $info  The type of system information to retrieve.
     *
     * @return mixed The retrieved system information.
     */
    public function getSystemInfo(string $info): mixed
    {
        return $this->callWinBinder('wb_get_system_info', [$info]);
    }

    /**
     * Draws an image on a WinBinder object.
     *
     * @param   mixed   $wbobject  The WinBinder object to draw on.
     * @param   string  $path      The path to the image file.
     * @param   int     $xPos      The x-coordinate of the image.
     * @param   int     $yPos      The y-coordinate of the image.
     * @param   int     $width     The width of the image.
     * @param   int     $height    The height of the image.
     *
     * @return mixed The result of the draw operation.
     */
    public function drawImage(
        mixed $wbobject,
        string $path,
        int $xPos = 0,
        int $yPos = 0,
        int $width = 0,
        int $height = 0
    ): mixed {
        $image = $this->callWinBinder('wb_load_image', [$path]);

        return $this->callWinBinder('wb_draw_image', [$wbobject, $image, $xPos, $yPos, $width, $height]);
    }

    /**
     * Draws text on a WinBinder object.
     *
     * @param   mixed       $parent   The parent WinBinder object.
     * @param   string      $caption  The text to draw.
     * @param   int         $xPos     The x-coordinate of the text.
     * @param   int         $yPos     The y-coordinate of the text.
     * @param   int|null    $width    The width of the text area.
     * @param   int|null    $height   The height of the text area.
     * @param   mixed|null  $font     The font to use for the text.
     *
     * @return mixed The result of the draw operation.
     */
    public function drawText(
        mixed $parent,
        string $caption,
        int $xPos,
        int $yPos,
        ?int $width = null,
        ?int $height = null,
        mixed $font = null
    ): mixed {
        $caption = str_replace(self::NEW_LINE, PHP_EOL, $caption);
        $width   = $width ?? 120;
        $height  = $height ?? 25;

        return $this->callWinBinder('wb_draw_text', [$parent, $caption, $xPos, $yPos, $width, $height, $font]);
    }

    /**
     * Draws a rectangle on a WinBinder object.
     *
     * @param   mixed  $parent  The parent WinBinder object.
     * @param   int    $xPos    The x-coordinate of the rectangle.
     * @param   int    $yPos    The y-coordinate of the rectangle.
     * @param   int    $width   The width of the rectangle.
     * @param   int    $height  The height of the rectangle.
     * @param   int    $color   The color of the rectangle.
     * @param   bool   $filled  Whether the rectangle should be filled.
     *
     * @return mixed The result of the draw operation.
     */
    public function drawRect(
        mixed $parent,
        int $xPos,
        int $yPos,
        int $width,
        int $height,
        int $color = 15790320,
        bool $filled = true
    ): mixed {
        return $this->callWinBinder('wb_draw_rect', [$parent, $xPos, $yPos, $width, $height, $color, $filled]);
    }

    /**
     * Draws a line on a WinBinder object.
     *
     * @param   mixed  $wbobject   The WinBinder object to draw on.
     * @param   int    $xStartPos  The starting x-coordinate of the line.
     * @param   int    $yStartPos  The starting y-coordinate of the line.
     * @param   int    $xEndPos    The ending x-coordinate of the line.
     * @param   int    $yEndPos    The ending y-coordinate of the line.
     * @param   int    $color      The color of the line.
     * @param   int    $height     The height of the line.
     *
     * @return mixed The result of the draw operation.
     */
    public function drawLine(
        mixed $wbobject,
        int $xStartPos,
        int $yStartPos,
        int $xEndPos,
        int $yEndPos,
        int $color,
        int $height = 1
    ): mixed {
        return $this->callWinBinder('wb_draw_line', [$wbobject, $xStartPos, $yStartPos, $xEndPos, $yEndPos, $color, $height]);
    }

    /**
     * Creates a font for use in WinBinder controls.
     *
     * @param   string      $fontName  The name of the font.
     * @param   int|null    $size      The size of the font.
     * @param   int|null    $color     The color of the font.
     * @param   mixed|null  $style     The style of the font.
     *
     * @return mixed The created font object.
     */
    public function createFont(
        string $fontName,
        ?int $size = null,
        ?int $color = null,
        mixed $style = null
    ): mixed {
        return $this->callWinBinder('wb_create_font', [$fontName, $size, $color, $style]);
    }

    /**
     * Waits for an event on a WinBinder object.
     *
     * @param   mixed|null  $wbobject  The WinBinder object to wait on.
     *
     * @return mixed The result of the wait operation.
     */
    public function wait(mixed $wbobject = null): mixed
    {
        return $this->callWinBinder('wb_wait', [$wbobject], true);
    }

    /**
     * Creates a timer for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to create the timer for.
     * @param   int    $wait      The wait time in milliseconds.
     *
     * @return array{int, mixed} An array containing the timer ID and object.
     */
    public function createTimer(mixed $wbobject, int $wait = 1000): array
    {
        $this->countCtrls++;

        return [
            self::CTRL_ID  => $this->countCtrls,
            self::CTRL_OBJ => $this->callWinBinder('wb_create_timer', [$wbobject, $this->countCtrls, $wait])
        ];
    }

    /**
     * Destroys a timer for a WinBinder object.
     *
     * @param   mixed  $wbobject     The WinBinder object to destroy the timer for.
     * @param   mixed  $timerobject  The timer object to destroy.
     *
     * @return mixed The result of the destroy operation.
     */
    public function destroyTimer(mixed $wbobject, mixed $timerobject): mixed
    {
        return $this->callWinBinder('wb_destroy_timer', [$wbobject, $timerobject]);
    }

    /**
     * Executes a system command.
     *
     * @param   string       $cmd     The command to execute.
     * @param   string|null  $params  The parameters to pass to the command.
     * @param   bool         $silent  Whether to execute the command silently.
     *
     * @return mixed The result of the command execution.
     */
    public function exec(string $cmd, ?string $params = null, bool $silent = false): mixed
    {
        global $bearsamppCore;

        if ($silent) {
            $silentScript = '"' . $bearsamppCore->getScript(Core::SCRIPT_EXEC_SILENT) . '" "' . $cmd . '"';
            $cmd          = 'wscript.exe';
            $params       = !empty($params) ? $silentScript . ' "' . $params . '"' : $silentScript;
        }

        $this->writeLog('exec: ' . $cmd . ' ' . $params);

        return $this->callWinBinder('wb_exec', [$cmd, $params]);
    }

    /**
     * Finds a file using WinBinder.
     *
     * @param   string  $filename  The name of the file to find.
     *
     * @return mixed The result of the find operation.
     */
    public function findFile(string $filename): mixed
    {
        $result = $this->callWinBinder('wb_find_file', [$filename]);
        $this->writeLog('findFile ' . $filename . ': ' . $result);

        return $result !== $filename ? $result : false;
    }

    /**
     * Sets an event handler for a WinBinder object.
     *
     * @param   mixed       $wbobject        The WinBinder object to set the handler for.
     * @param   mixed       $classCallback   The class callback for the handler.
     * @param   mixed       $methodCallback  The method callback for the handler.
     * @param   mixed|null  $launchTimer     The timer to launch for the handler.
     *
     * @return mixed The result of the set handler operation.
     */
    public function setHandler(
        mixed $wbobject,
        mixed $classCallback,
        mixed $methodCallback,
        mixed $launchTimer = null
    ): mixed {
        if ($launchTimer !== null) {
            $launchTimer = $this->createTimer($wbobject, (int)$launchTimer);
        }

        $this->callback[$wbobject] = [$classCallback, $methodCallback, $launchTimer];

        return $this->callWinBinder('wb_set_handler', [
            $wbobject,
            function ($window, $id, $ctrl, $param1, $param2) {
                $this->__winbinderEventHandler($window, $id, $ctrl, $param1, $param2);
            }
        ]);
    }

    /**
     * Sets an image for a WinBinder object.
     *
     * @param   mixed   $wbobject  The WinBinder object to set the image for.
     * @param   string  $path      The path to the image file.
     *
     * @return mixed The result of the set image operation.
     */
    public function setImage(mixed $wbobject, string $path): mixed
    {
        if ($wbobject === null) {
            error_log('Error: $wbobject is null.');

            return false;
        }

        if (!file_exists($path)) {
            error_log('Error: Image file does not exist at path: ' . $path);

            return false;
        }

        return $this->callWinBinder('wb_set_image', [$wbobject, $path]);
    }

    /**
     * Sets the maximum length for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the maximum length for.
     * @param   int    $length    The maximum length to set.
     *
     * @return mixed The result of the set maximum length operation.
     */
    public function setMaxLength(mixed $wbobject, int $length): mixed
    {
        return $this->callWinBinder('wb_send_message', [$wbobject, 0x00c5, $length, 0]);
    }

    /**
     * Sets the area of a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the area for.
     * @param   int    $width     The width of the area.
     * @param   int    $height    The height of the area.
     *
     * @return mixed The result of the set area operation.
     */
    public function setArea(mixed $wbobject, int $width, int $height): mixed
    {
        return $this->callWinBinder('wb_set_area', [$wbobject, WBC_TITLE, 0, 0, $width, $height]);
    }

    /**
     * Retrieves the text from a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to get the text from.
     *
     * @return mixed The retrieved text.
     */
    public function getText(mixed $wbobject): mixed
    {
        return $this->callWinBinder('wb_get_text', [$wbobject]);
    }

    /**
     * Sets the text for a WinBinder object.
     *
     * @param   mixed   $wbobject  The WinBinder object to set the text for.
     * @param   string  $content   The text content to set.
     *
     * @return mixed The result of the set text operation.
     */
    public function setText(mixed $wbobject, string $content): mixed
    {
        $content = str_replace(self::NEW_LINE, PHP_EOL, $content);

        return $this->callWinBinder('wb_set_text', [$wbobject, $content]);
    }

    /**
     * Retrieves the value from a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to get the value from.
     *
     * @return mixed The retrieved value.
     */
    public function getValue(mixed $wbobject): mixed
    {
        return $this->callWinBinder('wb_get_value', [$wbobject]);
    }

    /**
     * Sets the value for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the value for.
     * @param   mixed  $content   The value to set.
     *
     * @return mixed The result of the set value operation.
     */
    public function setValue(mixed $wbobject, mixed $content): mixed
    {
        return $this->callWinBinder('wb_set_value', [$wbobject, $content]);
    }

    /**
     * Retrieves the focus from a WinBinder object.
     *
     * @return mixed The WinBinder object that has the focus.
     */
    public function getFocus(): mixed
    {
        return $this->callWinBinder('wb_get_focus');
    }

    /**
     * Sets the focus to a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the focus to.
     *
     * @return mixed The result of the set focus operation.
     */
    public function setFocus(mixed $wbobject): mixed
    {
        return $this->callWinBinder('wb_set_focus', [$wbobject]);
    }

    /**
     * Sets the cursor type for a WinBinder object.
     *
     * @param   mixed   $wbobject  The WinBinder object to set the cursor for.
     * @param   string  $type      The cursor type to set.
     *
     * @return mixed The result of the set cursor operation.
     */
    public function setCursor(mixed $wbobject, string $type = self::CURSOR_ARROW): mixed
    {
        return $this->callWinBinder('wb_set_cursor', [$wbobject, $type]);
    }

    /**
     * Checks if a WinBinder object is enabled.
     *
     * @param   mixed  $wbobject  The WinBinder object to check.
     *
     * @return mixed True if the object is enabled, false otherwise.
     */
    public function isEnabled(mixed $wbobject): mixed
    {
        return $this->callWinBinder('wb_get_enabled', [$wbobject]);
    }

    /**
     * Sets the enabled state for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the enabled state for.
     * @param   bool   $enabled   True to enable the object, false to disable it.
     *
     * @return mixed The result of the set enabled state operation.
     */
    public function setEnabled(mixed $wbobject, bool $enabled = true): mixed
    {
        return $this->callWinBinder('wb_set_enabled', [$wbobject, $enabled]);
    }

    /**
     * Disables a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to disable.
     *
     * @return mixed The result of the disable operation.
     */
    public function setDisabled(mixed $wbobject): mixed
    {
        return $this->setEnabled($wbobject, false);
    }

    /**
     * Sets the style for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the style for.
     * @param   mixed  $style     The style to set.
     *
     * @return mixed The result of the set style operation.
     */
    public function setStyle(mixed $wbobject, mixed $style): mixed
    {
        return $this->callWinBinder('wb_set_style', [$wbobject, $style]);
    }

    /**
     * Sets the range for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the range for.
     * @param   int    $min       The minimum value of the range.
     * @param   int    $max       The maximum value of the range.
     *
     * @return mixed The result of the set range operation.
     */
    public function setRange(mixed $wbobject, int $min, int $max): mixed
    {
        return $this->callWinBinder('wb_set_range', [$wbobject, $min, $max]);
    }

    /**
     * Opens a system dialog to select a path.
     *
     * @param   mixed        $parent  The parent window for the dialog.
     * @param   string       $title   The title of the dialog.
     * @param   string|null  $path    The initial path for the dialog.
     *
     * @return mixed The selected path.
     */
    public function sysDlgPath(mixed $parent, string $title, ?string $path = null): mixed
    {
        return $this->callWinBinder('wb_sys_dlg_path', [$parent, $title, $path]);
    }

    /**
     * Opens a system dialog to open a file.
     *
     * @param   mixed        $parent  The parent window for the dialog.
     * @param   string       $title   The title of the dialog.
     * @param   string|null  $filter  The file filter for the dialog.
     * @param   string|null  $path    The initial path for the dialog.
     *
     * @return mixed The selected file path.
     */
    public function sysDlgOpen(mixed $parent, string $title, ?string $filter = null, ?string $path = null): mixed
    {
        return $this->callWinBinder('wb_sys_dlg_open', [$parent, $title, $filter, $path]);
    }

    /**
     * Creates a label control.
     *
     * @param   mixed     $parent   The parent window or control.
     * @param   string    $caption  The caption for the label.
     * @param   int       $xPos     The x-coordinate of the label.
     * @param   int       $yPos     The y-coordinate of the label.
     * @param   int|null  $width    The width of the label.
     * @param   int|null  $height   The height of the label.
     * @param   mixed     $style    The style for the label.
     * @param   mixed     $params   Additional parameters for the label.
     *
     * @return array{int, mixed} An array containing the control ID and object.
     */
    public function createLabel(mixed $parent, string $caption, int $xPos, int $yPos, ?int $width = null, ?int $height = null, mixed $style = null, mixed $params = null): array
    {
        $caption = str_replace(self::NEW_LINE, PHP_EOL, $caption);
        $width   = $width ?? 120;
        $height  = $height ?? 25;

        return $this->createControl($parent, Label, $caption, $xPos, $yPos, $width, $height, $style, $params);
    }

    /**
     * Creates an input text control.
     *
     * @param   mixed     $parent     The parent window or control.
     * @param   string    $value      The initial value for the input text.
     * @param   int       $xPos       The x-coordinate of the input text.
     * @param   int       $yPos       The y-coordinate of the input text.
     * @param   int|null  $width      The width of the input text.
     * @param   int|null  $height     The height of the input text.
     * @param   int|null  $maxLength  The maximum length of the input text.
     * @param   mixed     $style      The style for the input text.
     * @param   mixed     $params     Additional parameters for the input text.
     *
     * @return array{int, mixed} An array containing the control ID and object.
     */
    public function createInputText(
        mixed $parent,
        string $value,
        int $xPos,
        int $yPos,
        ?int $width = null,
        ?int $height = null,
        ?int $maxLength = null,
        mixed $style = null,
        mixed $params = null
    ): array {
        $value     = str_replace(self::NEW_LINE, PHP_EOL, $value);
        $width     = $width ?? 120;
        $height    = $height ?? 25;
        $inputText = $this->createControl($parent, EditBox, $value, $xPos, $yPos, $width, $height, $style, $params);
        if (is_numeric($maxLength) && $maxLength > 0) {
            $this->setMaxLength($inputText[self::CTRL_OBJ], $maxLength);
        }

        return $inputText;
    }

    /**
     * Creates an edit box control.
     *
     * @param   mixed     $parent  The parent window or control.
     * @param   string    $value   The initial value for the edit box.
     * @param   int       $xPos    The x-coordinate of the edit box.
     * @param   int       $yPos    The y-coordinate of the edit box.
     * @param   int|null  $width   The width of the edit box.
     * @param   int|null  $height  The height of the edit box.
     * @param   mixed     $style   The style for the edit box.
     * @param   mixed     $params  Additional parameters for the edit box.
     *
     * @return array{int, mixed} An array containing the control ID and object.
     */
    public function createEditBox(mixed $parent, string $value, int $xPos, int $yPos, ?int $width = null, ?int $height = null, mixed $style = null, mixed $params = null): array
    {
        $value   = str_replace(self::NEW_LINE, PHP_EOL, $value);
        $width   = $width ?? 540;
        $height  = $height ?? 340;
        $editBox = $this->createControl($parent, RTFEditBox, $value, $xPos, $yPos, $width, $height, $style, $params);

        return $editBox;
    }

    /**
     * Creates a hyperlink control.
     *
     * @param   mixed     $parent   The parent window or control.
     * @param   string    $caption  The caption for the hyperlink.
     * @param   int       $xPos     The x-coordinate of the hyperlink.
     * @param   int       $yPos     The y-coordinate of the hyperlink.
     * @param   int|null  $width    The width of the hyperlink.
     * @param   int|null  $height   The height of the hyperlink.
     * @param   mixed     $style    The style for the hyperlink.
     * @param   mixed     $params   Additional parameters for the hyperlink.
     *
     * @return array{int, mixed} An array containing the control ID and object.
     */
    public function createHyperLink(mixed $parent, string $caption, int $xPos, int $yPos, ?int $width = null, ?int $height = null, mixed $style = null, mixed $params = null): array
    {
        $caption   = str_replace(self::NEW_LINE, PHP_EOL, $caption);
        $width     = $width ?? 120;
        $height    = $height ?? 15;
        $hyperLink = $this->createControl($parent, HyperLink, $caption, $xPos, $yPos, $width, $height, $style, $params);
        $this->setCursor($hyperLink[self::CTRL_OBJ], self::CURSOR_FINGER);

        return $hyperLink;
    }

    /**
     * Creates a radio button control.
     *
     * @param   mixed     $parent      The parent window or control.
     * @param   string    $caption     The caption for the radio button.
     * @param   bool      $checked     Whether the radio button is checked.
     * @param   int       $xPos        The x-coordinate of the radio button.
     * @param   int       $yPos        The y-coordinate of the radio button.
     * @param   int|null  $width       The width of the radio button.
     * @param   int|null  $height      The height of the radio button.
     * @param   bool      $startGroup  Whether this radio button starts a new group.
     *
     * @return array{int, mixed} An array containing the control ID and object.
     */
    public function createRadioButton(mixed $parent, string $caption, bool $checked, int $xPos, int $yPos, ?int $width = null, ?int $height = null, bool $startGroup = false): array
    {
        $caption = str_replace(self::NEW_LINE, PHP_EOL, $caption);
        $width   = $width ?? 120;
        $height  = $height ?? 25;

        return $this->createControl($parent, RadioButton, $caption, $xPos, $yPos, $width, $height, $startGroup ? WBC_GROUP : null, $checked ? 1 : 0);
    }

    /**
     * Creates a button control.
     *
     * @param   mixed     $parent   The parent window or control.
     * @param   string    $caption  The caption for the button.
     * @param   int       $xPos     The x-coordinate of the button.
     * @param   int       $yPos     The y-coordinate of the button.
     * @param   int|null  $width    The width of the button.
     * @param   int|null  $height   The height of the button.
     * @param   mixed     $style    The style for the button.
     * @param   mixed     $params   Additional parameters for the button.
     *
     * @return array{int, mixed} An array containing the control ID and object.
     */
    public function createButton(mixed $parent, string $caption, int $xPos, int $yPos, ?int $width = null, ?int $height = null, mixed $style = null, mixed $params = null): array
    {
        $width  = $width ?? 80;
        $height = $height ?? 25;

        return $this->createControl($parent, PushButton, $caption, $xPos, $yPos, $width, $height, $style, $params);
    }

    /**
     * Creates a progress bar control.
     *
     * @param   mixed     $parent  The parent window or control.
     * @param   int       $max     The maximum value for the progress bar.
     * @param   int       $xPos    The x-coordinate of the progress bar.
     * @param   int       $yPos    The y-coordinate of the progress bar.
     * @param   int|null  $width   The width of the progress bar.
     * @param   int|null  $height  The height of the progress bar.
     * @param   mixed     $style   The style for the progress bar.
     * @param   mixed     $params  Additional parameters for the progress bar.
     *
     * @return array{int, mixed} An array containing the control ID and object.
     */
    public function createProgressBar(mixed $parent, int $max, int $xPos, int $yPos, ?int $width = null, ?int $height = null, mixed $style = null, mixed $params = null): array
    {
        global $bearsamppLang;

        $width       = $width ?? 200;
        $height      = $height ?? 15;
        $progressBar = $this->createControl($parent, Gauge, $bearsamppLang->getValue(Lang::LOADING), $xPos, $yPos, $width, $height, $style, $params);

        $this->setRange($progressBar[self::CTRL_OBJ], 0, $max);
        $this->gauge[$progressBar[self::CTRL_OBJ]] = 0;

        return $progressBar;
    }

    /**
     * Increments the value of a progress bar.
     *
     * @param   array  $progressBar  The progress bar control.
     */
    public function incrProgressBar(array $progressBar): void
    {
        $this->setProgressBarValue($progressBar, self::INCR_PROGRESS_BAR);
    }

    /**
     * Resets the value of a progress bar to zero.
     *
     * @param   array  $progressBar  The progress bar control.
     */
    public function resetProgressBar(array $progressBar): void
    {
        $this->setProgressBarValue($progressBar, 0);
    }

    /**
     * Sets the value of a progress bar.
     *
     * @param   array  $progressBar  The progress bar control.
     * @param   mixed  $value        The value to set.
     */
    public function setProgressBarValue(array $progressBar, mixed $value): void
    {
        if ($progressBar !== null && isset($progressBar[self::CTRL_OBJ]) && isset($this->gauge[$progressBar[self::CTRL_OBJ]])) {
            if (strval($value) === self::INCR_PROGRESS_BAR) {
                $value = $this->gauge[$progressBar[self::CTRL_OBJ]] + 1;
            }
            if (is_numeric($value)) {
                $this->gauge[$progressBar[self::CTRL_OBJ]] = $value;
                $this->setValue($progressBar[self::CTRL_OBJ], $value);
            }
        }
    }

    /**
     * Sets the maximum value of a progress bar.
     *
     * @param   array  $progressBar  The progress bar control.
     * @param   int    $max          The maximum value to set.
     */
    public function setProgressBarMax(array $progressBar, int $max): void
    {
        $this->setRange($progressBar[self::CTRL_OBJ], 0, $max);
    }

    /**
     * Displays a message box.
     *
     * @param   string       $message  The message to display.
     * @param   int          $type     The type of message box.
     * @param   string|null  $title    The title of the message box.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBox(string $message, int $type, ?string $title = null): mixed
    {
        global $bearsamppCore;

        $message    = str_replace(self::NEW_LINE, PHP_EOL, $message);
        $messageBox = $this->callWinBinder('wb_message_box', [
            null,
            strlen($message) < 64 ? str_pad($message, 64) : $message,
            $title === null ? $this->defaultTitle : $this->defaultTitle . ' - ' . $title,
            $type
        ]);

        if ($messageBox === null) {
            error_log('Error: $messageBox is null.');

            return null;
        }

        $iconPath = $bearsamppCore->getIconsPath() . '/app.ico';
        if (!file_exists($iconPath)) {
            error_log('Error: Icon file does not exist at path: ' . $iconPath);

            return null;
        }

        $this->setImage($messageBox, $iconPath);

        return $messageBox;
    }

    /**
     * Displays an informational message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxInfo(string $message, ?string $title = null): mixed
    {
        return $this->messageBox($message, self::BOX_INFO, $title);
    }

    /**
     * Displays an OK message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxOk(string $message, ?string $title = null): mixed
    {
        return $this->messageBox($message, self::BOX_OK, $title);
    }

    /**
     * Displays an OK/Cancel message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxOkCancel(string $message, ?string $title = null): mixed
    {
        return $this->messageBox($message, self::BOX_OKCANCEL, $title);
    }

    /**
     * Displays a question message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box. If null, the default title will be used.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxQuestion(string $message, ?string $title = null): mixed
    {
        return $this->messageBox($message, self::BOX_QUESTION, $title);
    }

    /**
     * Displays an error message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box. If null, the default title will be used.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxError(string $message, ?string $title = null): mixed
    {
        return $this->messageBox($message, self::BOX_ERROR, $title);
    }

    /**
     * Displays a warning message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box. If null, the default title will be used.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxWarning(string $message, ?string $title = null): mixed
    {
        return $this->messageBox($message, self::BOX_WARNING, $title);
    }

    /**
     * Displays a Yes/No message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box. If null, the default title will be used.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxYesNo(string $message, ?string $title = null): mixed
    {
        return $this->messageBox($message, self::BOX_YESNO, $title);
    }

    /**
     * Displays a Yes/No/Cancel message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box. If null, the default title will be used.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxYesNoCancel(string $message, ?string $title = null): mixed
    {
        return $this->messageBox($message, self::BOX_YESNOCANCEL, $title);
    }

    /**
     * Event handler for WinBinder events.
     *
     * @param   mixed  $window  The window object where the event occurred.
     * @param   int    $id      The ID of the event.
     * @param   mixed  $ctrl    The control that triggered the event.
     * @param   mixed  $param1  The first parameter of the event.
     * @param   mixed  $param2  The second parameter of the event.
     */
    private function __winbinderEventHandler(mixed $window, int $id, mixed $ctrl, mixed $param1, mixed $param2): void
    {
        if (isset($this->callback[$window][2])) {
            $this->destroyTimer($window, $this->callback[$window][2][0]);
        }

        call_user_func_array(
            [$this->callback[$window][0], $this->callback[$window][1]],
            [$window, $id, $ctrl, $param1, $param2]
        );
    }
}
