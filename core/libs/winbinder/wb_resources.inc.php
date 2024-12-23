<?php


/**
 * RC file parser: Converts Windows resource files to WinBinder commands.
 *
 * @author
 * @copyright
 * @license See LICENSE.TXT for details
 */

 // Constants for screen scaling
define('WB_KX_SCREEN', 1.498); // Determined through trial and error
define('WB_KY_SCREEN', 1.625); // Determined through trial and error

// Windows constants from win.h
define('WS_VISIBLE',          0x10000000);
define('WS_DISABLED',         0x08000000);
define('WS_GROUP',            0x00020000);
define('WS_EX_STATICEDGE',    0x00020000);

// Button styles
define('BS_PUSHBUTTON',       0x00);
define('BS_CHECKBOX',         0x02);
define('BS_AUTOCHECKBOX',     0x03);
define('BS_RADIOBUTTON',      0x04);
define('BS_GROUPBOX',         0x07);
define('BS_AUTORADIOBUTTON',  0x09);
define('BS_ICON',             0x40);
define('BS_BITMAP',           0x80);

// Edit control styles
define('ES_NUMBER',           0x2000);
define('ES_PASSWORD',         0x20);
define('ES_READONLY',         0x0800);
define('ES_UPPERCASE',        0x08);
define('ES_LEFT',             0x0);
define('ES_CENTER',           0x01);
define('ES_RIGHT',            0x02);
define('ES_MULTILINE',        0x04);

// Static styles
define('SS_LEFT',             0x00);
define('SS_CENTER',           0x01);
define('SS_RIGHT',            0x02);
define('SS_ETCHEDHORZ',       0x10);
define('SS_ETCHEDVERT',       0x11);
define('SS_ETCHEDFRAME',      0x12);
define('SS_ICON',             0x03);
define('SS_BITMAP',           0x0E);
define('SS_LEFTNOWORDWRAP',   0x0C);
define('SS_WORDELLIPSIS',     0xC000);

// Other styles
define('CBS_SORT',            0x100);
define('CBS_DROPDOWNLIST',    3);
define('LBS_SORT',            2);
define('LVS_NOSORTHEADER',    0x00008000);
define('LVS_GRIDLINES',       0x00800000); // Actually WS_BORDER
define('LVS_CHECKBOXES',      0x00000800); // Actually LVS_ALIGNLEFT
define('LVS_SINGLESEL',       0x00000004);
define('TBS_AUTOTICKS',       1);

// Initialize global variables
$_winclass = '';
$_usergeom = [];
$path_res = '';
$_tabN = 0;

/**
 * Parses RC (Windows resource) text and returns the corresponding WinBinder code.
 *
 * @param string      $rc       The resource text, usually read from a RC file.
 * @param string      $winvar   The window variable name (default '$mainwin').
 * @param string|null $parent   The parent window variable name.
 * @param string      $type     The window type (e.g., 'AppWindow', 'ModalDialog').
 * @param string|null $caption  The window caption (unused, kept for compatibility).
 * @param int|string  $x        The x-coordinate or predefined constant (default WBC_CENTER).
 * @param int|string  $y        The y-coordinate or predefined constant (default WBC_CENTER).
 * @param int|string  $width    The width or predefined constant (default WBC_CENTER).
 * @param int|string  $height   The height or predefined constant (default WBC_CENTER).
 * @param int         $style    The window style.
 * @param int         $lparam   Additional window parameters.
 * @param string      $respath  Path to resource files.
 * @return string               The generated WinBinder code.
 */
function parse_rc(
    string $rc,
    string $winvar = '$mainwin',
    ?string $parent = null,
    string $type = 'AppWindow',
    ?string $caption = null,
    int|string $x = 'WBC_CENTER',
    int|string $y = 'WBC_CENTER',
    int|string $width = 'WBC_CENTER',
    int|string $height = 'WBC_CENTER',
    int $style = 0,
    int $lparam = 0,
    string $respath = 'PATH_RES'
): string {
    global $_winclass, $_usergeom, $path_res, $_tabN;

    // Initialize variables
    $_usergeom = [$x, $y, $width, $height];
    $path_res = $respath;

    // Remove comments and unnecessary spaces
    $rc = preg_replace('/^\s*;.*$/m', '', $rc);
    $rc = preg_replace('/^\s*(.*)$/m', '$1', $rc);

    // Extract #define statements
    $def = '';
    if (preg_match_all('/^\s*#define.*$/m', $rc, $matches)) {
        $def = implode("\n", $matches[0]);

        // Remove #define lines from the main RC text
        $rc = preg_replace('/^\s*#define.*$/m', '', $rc);
    }

    // Remove blank lines from defines
    $def = preg_replace('/\n+/m', "\n", $def);

    // Convert C-style #define statements to PHP format
    $def = preg_replace('/#define\s+(\w+)\s+"(.*)"/', 'if(!defined("$1")) define("$1", "$2");', $def);
    $def = preg_replace("/#define\s+(\w+)\s+'(.+)'/", 'if(!defined("$1")) define("$1", "$2");', $def);
    $def = preg_replace('/#define\s+(\w+)\s+(\S+)/', 'if(!defined("$1")) define("$1", $2);', $def);
    $def = "// Control identifiers\n\n" . preg_replace("/(\r\n|\r|\n)+/sm", "\n", $def);

    // Set window class
    $_winclass = $type;

    // Token regex pattern
    $tok = '\s*(?:"[^"]*"|\'[^\']*\'|[^,\'"\s]+)\s*';

    // Process DIALOGEX entries
    $rc = "// Create window\n\n" . preg_replace_callback(
        "/^$tok\s+DIALOGEX$tok,$tok,$tok,$tok\s+CAPTION$tok\s+FONT$tok,$tok\s+STYLE$tok\s+EXSTYLE$tok/m",
        '_scale_dialog',
        $rc
    );

    // Process CONTROL entries
    $rc = preg_replace_callback(
        "/^\s*CONTROL\s+$tok,$tok,$tok,$tok,$tok,$tok,$tok,$tok,$tok/m",
        '_scale_controls',
        $rc
    );

    // Replace BEGIN and END statements with comments
    $rc = preg_replace("/^\s*BEGIN/m", "\n// Insert controls\n", $rc);
    $rc = preg_replace("/^\s*END/m", "\n// End controls", $rc);

    // Replace placeholders with actual variable names
    $rc = str_replace("%WINVAR%", $winvar, $rc);
    $rc = str_replace("%PARENT%", $parent ?? 'NULL', $rc);
    $rc = str_replace("%STYLE%", (string)$style, $rc);
    $rc = str_replace("%LPARAM%", (string)$lparam, $rc);

    return "$def\n$rc";
}

/**
 * Internal function to scale dialog dimensions and generate window creation code.
 *
 * @param array $matches The regex match results from the DIALOGEX pattern.
 * @return string        The generated code for creating the window.
 */
function _scale_dialog(array $matches): string {
    global $_winclass, $_usergeom, $_tabN;

    // Initialize variables
    $code = '';
    $_tabN = 0;

    if ($_winclass === 'TabControl') {
        $_tabN++;
        $code = "wbtemp_create_item(%PARENT%, {$matches[6]});\n";
    } else {
        // Width and height adjustments
        $_addx = 8; // width + 2xborder
        $_addy = 4 + 42 + 17 + 4; // border + caption + border

        switch (strtolower($_winclass)) {
            case 'appwindow':
                $_winclass = 'AppWindow';
                $_addx = 8;
                $_addy = 3 + 18 + 22 + 18 + 3;
                break;
            case 'resizablewindow':
                $_winclass = 'ResizableWindow';
                break;
            case 'modaldialog':
                $_winclass = 'ModalDialog';
                break;
            case 'modelessdialog':
                $_winclass = 'ModelessDialog';
                break;
            case 'tooldialog':
                $_winclass = 'ToolDialog';
                break;
            default:
                $_winclass = 'AppWindow';
        }

        // Determine window geometry
        if (!($_usergeom[0] === 'WBC_CENTER' && $_usergeom[1] === 'WBC_CENTER' &&
            $_usergeom[2] === 'WBC_CENTER' && $_usergeom[3] === 'WBC_CENTER')) {
            $code = "%WINVAR% = wb_create_window(%PARENT%, {$_winclass}, {$matches[6]}, {$_usergeom[0]}, {$_usergeom[1]}, {$_usergeom[2]}, {$_usergeom[3]}, %STYLE%, %LPARAM%);\n";
        } else {
            $width = (int)($matches[4] * WB_KX_SCREEN + $_addx);
            $height = (int)($matches[5] * WB_KY_SCREEN + $_addy);

            $code = "%WINVAR% = wb_create_window(%PARENT%, {$_winclass}, {$matches[6]}, WBC_CENTER, WBC_CENTER, {$width}, {$height}, %STYLE%, %LPARAM%);\n";
        }
    }

    return $code;
}

/**
 * Internal function to scale control dimensions and generate control creation code.
 *
 * @param array $matches The regex match results from the CONTROL pattern.
 * @return string        The generated code for creating the control.
 */
function _scale_controls(array $matches): string {
    global $_tabN, $path_res;

    $winclass = trim($matches[3], '"');
    $winstyle = hexdec($matches[4]);
    $winexstyle = hexdec($matches[9]);

    $style = _bit_test($winstyle, WS_VISIBLE) ? 'WBC_VISIBLE' : 'WBC_INVISIBLE';
    $style .= _bit_test($winstyle, WS_DISABLED) ? ' | WBC_DISABLED' : ' | WBC_ENABLED';

    if (_bit_test($winexstyle, WS_EX_STATICEDGE)) {
        $style .= ' | WBC_BORDER';
    }

    // Initialize control class
    $class = 'UnknownControl';

    // Set attributes according to control class
    switch (strtolower($winclass)) {
        case 'button':
            switch ($winstyle & 0x0F) {
                case BS_AUTORADIOBUTTON:
                case BS_RADIOBUTTON:
                    $class = 'RadioButton';
                    if (_bit_test($winstyle, WS_GROUP)) {
                        $style .= ' | WBC_GROUP';
                    }
                    break;
                case BS_AUTOCHECKBOX:
                case BS_CHECKBOX:
                    $class = 'CheckBox';
                    break;
                case BS_GROUPBOX:
                    $class = 'Frame';
                    break;
                case BS_PUSHBUTTON:
                default:
                    $class = 'PushButton';
            }
            break;
        case 'static':
            switch ($winstyle & 0x1F) {
                case SS_ICON:
                case SS_BITMAP:
                    $style .= ' | WBC_IMAGE | WBC_CENTER';
                    $class = 'Frame';
                    break;
                case SS_CENTER:
                    if (_bit_test($winstyle, SS_WORDELLIPSIS)) {
                        $style .= ' | WBC_ELLIPSIS';
                    }
                    $style .= ' | WBC_CENTER';
                    $class = 'Label';
                    break;
                case SS_RIGHT:
                    if (_bit_test($winstyle, SS_WORDELLIPSIS)) {
                        $style .= ' | WBC_ELLIPSIS';
                    }
                    $style .= ' | WBC_RIGHT';
                    $class = 'Label';
                    break;
                case SS_LEFT:
                default:
                    if (!_bit_test($winstyle, SS_LEFTNOWORDWRAP)) {
                        $style .= ' | WBC_MULTILINE';
                    }
                    if (_bit_test($winstyle, SS_WORDELLIPSIS)) {
                        $style .= ' | WBC_ELLIPSIS';
                    }
                    $class = 'Label';
            }
            break;
        // Add cases for other control types (edit, combobox, listbox, etc.)
        default:
            $class = 'CustomControl';
    }

    // Convert positions and sizes
    $left = (int)($matches[5] * WB_KX_SCREEN);
    $top = (int)($matches[6] * WB_KY_SCREEN);
    $width = (int)($matches[7] * WB_KX_SCREEN);
    $height = (int)($matches[8] * WB_KY_SCREEN);

    // Generate control creation code
    $controlCode = "wb_create_control(%WINVAR%, {$class}, {$matches[1]}, {$left}, {$top}, {$width}, {$height}, {$matches[2]}, {$style}, 0";

    // Add tab index if needed
    if ($_tabN > 0) {
        $controlCode .= ', ' . ($_tabN - 1);
    }
    $controlCode .= ");\n";

    // Handle specific control attributes
    switch ($class) {
        case 'Frame':
            if (strpos($style, 'WBC_IMAGE') !== false) {
                if (($winstyle & (SS_BITMAP | SS_ICON)) && ($matches[1] !== '""')) {
                    $image = $path_res . _trim_quotes($matches[1]);
                    if (preg_match('/\.(bmp|ico)$/i', $image)) {
                        $controlCode = "\$_tmp_ctrl_ = {$controlCode}wb_set_image(\$_tmp_ctrl_, '{$image}', GREEN); unset(\$_tmp_ctrl_);\n";
                    }
                }
            }
            break;
        case 'PushButton':
            if (($winstyle & (BS_BITMAP | BS_ICON)) && ($matches[1] !== '""')) {
                $image = $path_res . _trim_quotes($matches[1]);
                if (preg_match('/\.(bmp|ico)$/i', $image)) {
                    $controlCode = "\$_tmp_ctrl_ = {$controlCode}wb_set_image(\$_tmp_ctrl_, '{$image}', GREEN); unset(\$_tmp_ctrl_);\n";
                }
            }
            break;
        // Handle other control-specific attributes if necessary
    }

    return $controlCode;
}

/**
 * Trims surrounding quotes from a string.
 *
 * @param string $str The input string.
 * @return string     The string without surrounding quotes.
 */
function _trim_quotes(string $str): string {
    return trim($str, '"\'');
}

/**
 * Checks if a specific bit is set in a value.
 *
 * @param int $value The value to test.
 * @param int $test  The bit mask to test.
 * @return bool      True if the bit is set, false otherwise.
 */
function _bit_test(int $value, int $test): bool {
    return ($value & $test) === $test;
}
