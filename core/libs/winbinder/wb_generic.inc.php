<?php
declare(strict_types=0);

/**
 * WINBINDER - The native Windows binding for PHP
 *
 * General-purpose supporting functions
 *
 * @author Rubem Pechansky
 * @copyright Hypervisual
 * @license See LICENSE.TXT for details
 * @link http://winbinder.org/contact.php
 */

/**
 * Returns an array with all files of a subdirectory.
 *
 * If `$subdirs` is `TRUE`, includes subdirectories recursively.
 * `$mask` is a PCRE regular expression.
 *
 * @param string $path The directory path.
 * @param bool $subdirs Include subdirectories recursively.
 * @param bool $fullname Return full file names with path.
 * @param string $mask PCRE regular expression to filter files.
 * @param bool $forcelowercase Force filenames to lowercase.
 * @return array Array of filenames.
 */
function get_folder_files($path, $subdirs = false, $fullname = true, $mask = "", $forcelowercase = true)
{
    // Correct path name, if needed
    $path = str_replace('/', '\\', $path);
    if (substr($path, -1) != '\\') {
        $path .= "\\";
    }
    if (!$path || !@is_dir($path)) {
        return [];
    }

    $dir = [];
    if ($handle = opendir($path)) {
        while (($file = readdir($handle)) !== false) {
            if (!is_dir($path . $file)) {    // No directories / subdirectories
                if ($forcelowercase) {
                    $file = strtolower($file);
                }
                if (!$mask) {
                    $dir[] = $fullname ? $path . $file : $file;
                } elseif ($mask && preg_match($mask, $file)) {
                    $dir[] = $fullname ? $path . $file : $file;
                }
            } elseif ($subdirs && $file[0] != ".") {    // Exclude "." and ".."
                $dir = array_merge($dir, get_folder_files($path . $file, $subdirs, $fullname, $mask));
            }
        }
        closedir($handle);
    }
    return $dir;
}

/**
 * Transforms the array `$data` into text that can be saved as an INI file.
 *
 * Escapes double-quotes as (\")
 *
 * @param array $data The data to be converted into INI format.
 * @param string $comments Optional comments to include at the beginning.
 * @return string|null The INI formatted string, or null on failure.
 */
function generate_ini($data, $comments = "")
{
    if (!is_array($data)) {
        trigger_error(__FUNCTION__ . ": Cannot save INI file.", E_USER_WARNING);
        return null;
    }
    $text = $comments;
    foreach ($data as $name => $section) {
        $text .= "\r\n[$name]\r\n";

        foreach ($section as $key => $value) {
            $value = trim($value);
            if ((string)((int)$value) === (string)$value) {
                // Integer: does nothing
            } elseif ((string)((float)$value) === (string)$value) {
                // Floating point: does nothing
            } elseif ($value === "") {
                // Empty string
                $value = '""';
            } elseif (strpos($value, '"') !== false) {
                // Escape double-quotes
                $value = '"' . str_replace('"', '\"', $value) . '"';
            } else {
                $value = '"' . $value . '"';
            }

            $text .= "$key = $value\r\n";
        }
    }
    return $text;
}

/**
 * Parses an INI formatted string, processing it similarly to how Windows does.
 *
 * Replaces escaped double-quotes (\") with double-quotes (").
 *
 * @param string $initext The INI formatted string.
 * @param bool $changecase Change the case of section and entry names.
 * @param bool $convertwords Convert words like "yes", "no", "null" to their respective values.
 * @return array The parsed INI data.
 */
function parse_ini($initext, $changecase = true, $convertwords = true)
{
    $ini = preg_split("/\r\n|\n/", $initext);
    $secpattern = "/^\[(.[^\]]*)\]/i";
    $entrypattern = "/^([a-z_0-9]*)\s*=\s*\"?([^\"]*)?\"?\$/i";
    $strpattern = "/^\"?(.[^\"]*)\"?\$/i";

    $section = [];
    $sec = "";

    // Predefined words
    static $words = ["yes", "on", "true", "no", "off", "false", "null"];
    static $values = [1, 1, 1, 0, 0, 0, null];

    // Lines loop
    for ($i = 0; $i < count($ini); $i++) {
        $line = trim($ini[$i]);

        // Replaces escaped double-quotes (\") with special marker /%quote%/
        if (strpos($line, '\"') !== false) {
            $line = str_replace('\"', '/%quote%/', $line);
        }

        // Skips blank lines and comments
        if ($line === "" || preg_match("/^;/", $line)) {
            continue;
        }

        if (preg_match($secpattern, $line, $matches)) {
            // It's a section
            $sec = $matches[1];

            if ($changecase) {
                $sec = ucfirst(strtolower($sec));
            }

            $section[$sec] = [];
        } elseif (preg_match($entrypattern, $line, $matches)) {
            // It's an entry
            $entry = $matches[1];

            if ($changecase) {
                $entry = strtolower($entry);
            }

            $value = preg_replace($entrypattern, "\\2", $line);

            // Restores double-quotes (")
            $value = str_replace('/%quote%/', '"', $value);

            // Convert special words to their respective values
            if ($convertwords) {
                $index = array_search(strtolower($value), $words);
                if ($index !== false) {
                    $value = $values[$index];
                }
            }

            $section[$sec][$entry] = $value;
        } else {
            // It's a normal string
            $section[$sec][] = preg_replace($strpattern, "\\1", $line);
        }
    }
    return $section;
}
