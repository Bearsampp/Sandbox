<?php


/**
 * WINBINDER - The native Windows binding for PHP
 *
 * Windows functions and utilities.
 *
 * @copyright Hypervisual
 * @license See LICENSE.TXT for details
 * @author Rubem
 * @link http://winbinder.org/contact.php
 */

// Note: This file may not be necessary in the future.

//-------------------------------------------------------------------- CONSTANTS

// Windows constants
define("BM_SETCHECK",          241);
define("LVM_FIRST",            0x1000);
define("LVM_DELETEALLITEMS",   (LVM_FIRST + 9));
define("LVM_GETITEMCOUNT",     (LVM_FIRST + 4));
define("LVM_GETITEMSTATE",     (LVM_FIRST + 44));
define("LVM_GETSELECTEDCOUNT", (LVM_FIRST + 50));
define("LVIS_SELECTED",        2);
define("TCM_GETCURSEL",        4875);
define("CB_FINDSTRINGEXACT",   344);
define("CB_SETCURSEL",         334);
define("LB_FINDSTRINGEXACT",   418);
define("LB_SETCURSEL",         390);
define("TCM_SETCURSEL",        4876);
define("WM_SETTEXT",           12);

//------------------------------------------------------------- WINDOW FUNCTIONS

/**
 * Creates a window control, menu, toolbar, status bar, or accelerator.
 *
 * @param resource $parent The parent window or control.
 * @param string $class The class of the control.
 * @param string|array $caption The caption or array of captions.
 * @param int $xpos The x-coordinate position.
 * @param int $ypos The y-coordinate position.
 * @param int $width The width of the control.
 * @param int $height The height of the control.
 * @param int|null $id The control's identifier.
 * @param int $style The style of the control.
 * @param mixed $lparam Additional parameters.
 * @param int $ntab The tab index.
 * @return resource|null Returns the created control or null on failure.
 */
function wb_create_control($parent, $class, $caption = "", $xpos = 0, $ypos = 0, $width = 0, $height = 0, $id = null, $style = 0, $lparam = null, $ntab = 0)
{
    switch ($class) {

        case Accel:
            return wbtemp_set_accel_table($parent, $caption);

        case ToolBar:
            return wbtemp_create_toolbar($parent, $caption, $width, $height, $lparam);

        case Menu:
            return wbtemp_create_menu($parent, $caption);

        case HyperLink:
            return wbtemp_create_control(
                $parent,
                $class,
                $caption,
                $xpos,
                $ypos,
                $width,
                $height,
                $id,
                $style,
                $lparam ?? NOCOLOR,
                $ntab
            );

        case ComboBox:
        case ListBox:
        case TreeView:
        case ListView:
            $ctrl = wbtemp_create_control(
                $parent,
                $class,
                $caption,
                $xpos,
                $ypos,
                $width,
                $height,
                $id,
                $style,
                $lparam,
                $ntab
            );
            if (is_array($caption)) {
                wb_set_text($ctrl, $caption[0]);
            }
            return $ctrl;

        case Gauge:
        case Slider:
        case ScrollBar:
            $ctrl = wbtemp_create_control(
                $parent,
                $class,
                $caption,
                $xpos,
                $ypos,
                $width,
                $height,
                $id,
                $style,
                $lparam,
                $ntab
            );
            if ($lparam) {
                wb_set_value($ctrl, $lparam);
            }
            return $ctrl;

        default:
            return wbtemp_create_control(
                $parent,
                $class,
                $caption,
                $xpos,
                $ypos,
                $width,
                $height,
                $id,
                $style,
                $lparam,
                $ntab
            );
    }
}

/**
 * Sets the value of a control or control item.
 *
 * @param resource $ctrl The control.
 * @param mixed $value The value to set.
 * @param mixed|null $item Optional item within the control.
 * @return mixed|null Returns the result of the operation, or null on failure.
 */
function wb_set_value($ctrl, $value, $item = null)
{
    if (!$ctrl) {
        return null;
    }

    $class = wb_get_class($ctrl);
    switch ($class) {

        case ListView:  // Array with items to be checked

            if ($value === null) {
                break;
            } elseif (is_string($value) && str_contains($value, ",")) {
                $values = explode(",", $value);
            } elseif (!is_array($value)) {
                $values = [$value];
            } else {
                $values = $value;
            }
            foreach ($values as $index) {
                wbtemp_set_listview_item_checked($ctrl, $index, 1);
            }
            break;

        case TreeView:  // Array with items to be checked

            if ($item === null) {
                $item = wb_get_selected($ctrl);
            }
            return wbtemp_set_treeview_item_value($ctrl, $item, $value);

        default:

            if ($value !== null) {
                return wbtemp_set_value($ctrl, $value, $item);
            }
    }
}

/**
 * Gets the text from a control, control item, or control sub-item.
 *
 * @param resource $ctrl The control.
 * @param mixed|null $item Optional item within the control.
 * @param int|null $subitem Optional sub-item index.
 * @return mixed|null Returns the text, or null on failure.
 */
function wb_get_text($ctrl, $item = null, $subitem = null)
{
    if (!$ctrl) {
        return null;
    }

    $class = wb_get_class($ctrl);

    if ($class === ListView) {

        if ($item !== null) {  // Valid item

            $line = wbtemp_get_listview_text($ctrl, $item);
            if ($subitem === null) {
                return $line;
            } else {
                return $line[$subitem] ?? null;
            }

        } else {  // NULL item

            $sel = wb_get_selected($ctrl);
            if ($sel === null) {  // Returns the entire table
                $items = [];
                for ($i = 0;; $i++) {
                    $itemText = wbtemp_get_listview_text($ctrl, $i);
                    $all = implode('', $itemText);
                    if ($all === '') {
                        break;
                    }
                    $items[] = $itemText;
                }
                return $items ?: null;
            } else {
                $items = [];
                foreach ($sel as $row) {
                    $items[] = wbtemp_get_listview_text($ctrl, $row);
                }
                return $items ?: null;
            }
        }

    } elseif ($class === TreeView) {

        if ($item) {
            return wbtemp_get_treeview_item_text($ctrl, $item);
        } else {
            $sel = wb_get_selected($ctrl);
            if ($sel === null) {
                return null;
            } else {
                return wbtemp_get_text($ctrl);
            }
        }

    } elseif ($class === ComboBox || $class === ListBox) {

        return wbtemp_get_text($ctrl, $item === null ? -1 : $item);

    } else {

        return wbtemp_get_text($ctrl, $item);

    }
}

/**
 * Sets the text of a control or control item.
 *
 * In a ListView, it creates columns where each element of the array is a column.
 * In a TabControl, it renames the tabs.
 *
 * @param resource $ctrl The control.
 * @param mixed $text The text to set.
 * @param mixed|null $item Optional item within the control.
 * @param int|null $subitem Optional sub-item index.
 * @return mixed|null Returns the result, or null on failure.
 */
function wb_set_text($ctrl, $text, $item = null, $subitem = null)
{
    if (!$ctrl) {
        return null;
    }

    $class = wb_get_class($ctrl);

    switch ($class) {

        case ListView:

            if ($item !== null) {

                if (!is_array($text) && $subitem !== null) {

                    // Set text of a ListView cell according to $item and $subitem
                    wbtemp_set_listview_item_text($ctrl, $item, $subitem, $text);

                } else {

                    // Set text of several ListView cells, ignoring $subitem
                    for ($sub = 0; $sub < count($text); $sub++) {
                        if ($text) {
                            if (isset($text[$sub])) {
                                wbtemp_set_listview_item_text($ctrl, $item, $sub, (string)$text[$sub]);
                            }
                        } else {
                            wbtemp_set_listview_item_text($ctrl, $item, $sub, "");
                        }
                    }
                }

            } else {

                if (!is_array($text)) {
                    $text = explode(",", $text);
                }

                wb_delete_items($ctrl);

                if (!$item) {
                    wbtemp_clear_listview_columns($ctrl);

                    // Create column headers
                    for ($i = 0; $i < count($text); $i++) {
                        if (is_array($text[$i])) {
                            wbtemp_create_listview_column(
                                $ctrl,
                                $i,
                                (string)$text[$i][0],
                                isset($text[$i][1]) ? (int)$text[$i][1] : -1,
                                isset($text[$i][2]) ? (int)$text[$i][2] : WBC_LEFT
                            );
                        } else {
                            wbtemp_create_listview_column(
                                $ctrl,
                                $i,
                                (string)$text[$i],
                                -1,
                                0
                            );
                        }
                    }
                }
            }
            break;

        case ListBox:

            if (!$text) {
                wb_delete_items($ctrl);
            } elseif (is_string($text)) {
                if (str_contains($text, "\r") || str_contains($text, "\n")) {
                    $textArray = preg_split("/[\r\n,]/", $text);
                    wb_delete_items($ctrl);
                    foreach ($textArray as $str) {
                        wbtemp_create_item($ctrl, (string)$str);
                    }
                } else {
                    $index = wb_send_message($ctrl, LB_FINDSTRINGEXACT, -1, wb_get_address($text));
                    wb_send_message($ctrl, LB_SETCURSEL, $index, 0);
                }
            } elseif (is_array($text)) {
                wb_delete_items($ctrl);
                foreach ($text as $str) {
                    wbtemp_create_item($ctrl, (string)$str);
                }
            }
            return;

        case ComboBox:

            if (!$text) {
                wb_delete_items($ctrl);
            } elseif (is_string($text)) {
                if (str_contains($text, "\r") || str_contains($text, "\n")) {
                    $textArray = preg_split("/[\r\n,]/", $text);
                    wb_delete_items($ctrl);
                    foreach ($textArray as $str) {
                        wbtemp_create_item($ctrl, (string)$str);
                    }
                } else {
                    $index = wb_send_message($ctrl, CB_FINDSTRINGEXACT, -1, wb_get_address($text));
                    wb_send_message($ctrl, CB_SETCURSEL, $index, 0);
                    if ($index === -1) {
                        wb_send_message($ctrl, WM_SETTEXT, 0, wb_get_address($text));
                    }
                }
            } elseif (is_array($text)) {
                wb_delete_items($ctrl);
                foreach ($text as $str) {
                    wbtemp_create_item($ctrl, (string)$str);
                }
            }
            return;

        case TreeView:

            if ($item) {
                return wbtemp_set_treeview_item_text($ctrl, $item, $text);
            } else {
                return wb_create_items($ctrl, $text, true);
            }

        default:
            if (is_array($text)) {
                return wbtemp_set_text($ctrl, $text, $item);
            } else {
                return wbtemp_set_text($ctrl, (string)$text, $item);
            }
    }
}

/**
 * Opens the standard Open dialog box.
 *
 * @param resource|null $parent The parent window.
 * @param string|null $title The dialog title.
 * @param string|array|null $filter The file filter.
 * @param string|null $path The initial path.
 * @param string|null $filename The default filename.
 * @param int|null $flags Additional flags.
 * @return string|null Returns the selected file path or null on cancellation.
 */
function wb_sys_dlg_open($parent = null, $title = null, $filter = null, $path = null, $filename = null, $flags = null)
{
    $filter = _make_file_filter($filter ?? $filename);
    return wbtemp_sys_dlg_open($parent, $title, $filter, $path, $flags);
}

/**
 * Opens the standard Save As dialog box.
 *
 * @param resource|null $parent The parent window.
 * @param string|null $title The dialog title.
 * @param string|array|null $filter The file filter.
 * @param string|null $path The initial path.
 * @param string|null $filename The default filename.
 * @param string|null $defext The default extension.
 * @return string|null Returns the selected file path or null on cancellation.
 */
function wb_sys_dlg_save($parent = null, $title = null, $filter = null, $path = null, $filename = null, $defext = null)
{
    $filter = _make_file_filter($filter ?? $filename);
    return wbtemp_sys_dlg_save($parent, $title, $filter, $path, $filename, $defext);
}

//----------------------------------------- AUXILIARY FUNCTIONS FOR INTERNAL USE

/**
 * Creates a file filter for Open/Save dialog boxes based on an array or string.
 *
 * @param string|array|null $filter The filter patterns.
 * @return string The formatted file filter string.
 */
function _make_file_filter($filter)
{
    if (!$filter) {
        return "All Files (*.*)\0*.*\0\0";
    }

    if (is_array($filter)) {
        $result = "";
        foreach ($filter as $line) {
            $result .= "{$line[0]} ({$line[1]})\0{$line[1]}\0";
        }
        $result .= "\0";
        return $result;
    } else {
        return $filter;
    }
}

//-------------------------------------------------------------------------- END
