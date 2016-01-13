<?php

/* PHP 5.5 requirement: select default timezone */
date_default_timezone_set('UTC');


class Logger
{
    public static $conf;

    /**
     * Print Log entry to file.
     *
     * Example: Core::printLog('Logging an error', 'error-log')
     *
     * @param string  $msg  Message to log
     * @param string  $file File name
     * @param boolean $show Echo log?
     *
     * @return boolean
     **/
    public static function printLog($msg, $file, $show = true)
    {
        //$msg = str_replace('  ', '', $msg)."\n";
        $msg .= "\n";

        if ($show) {
            echo $msg;
        }

        return self::appendToFile('logs', strtolower($file).'.log', self::getTime()."> ".$msg);
    }

    /**
     * Notify DEV team of exceptional situations
     *
     * @param array   $category Category of the method where the error occured
     * @param string  $message  Error message
     * @param boolean $echo     Echo log?
     *
     * @return void
     **/
    public static function errorNotify($category, $message, $echo = true)
    {
        $data              = array();
        $data['date']      = self::getTime('Y-m-d H:i:s');
        $data['category']  = $category;
        $data['message']   = $message;
        $data['backtrace'] = debug_backtrace();

        self::printLog(str_pad('!!! EXCEPTION !!! ', 27).wordwrap($message, 100, "\n".str_repeat(' ', 47)), $category['category'], $echo);

        return self::appendToFile('logs/notify', self::getTime('Ymd-His').substr(microtime(), 2, 2).'.json', json_encode($data));
    }

    /**
     * Create folders and print string to file.
     *
     * Example: Core::appendToFile('log', 'error-log.log' , 'Logging an error')
     *
     * @param string  $folder Folder path
     * @param string  $file   File name
     * @param string  $string String to append
     * @param boolean $echo   Echo logs?
     *
     * @return boolean
     */

    public static function appendToFile($folder, $file, $string, $echo = true)
    {
        $folder = APP_DIR.trim($folder, '/');
        $file   = trim(trim($file), '/');

        // Handle folder: create if doesn't exist
        if (!is_dir($folder)) {

            $folder_check = mkdir($folder, 0755, true);
            // if folder doesn't exist and fails creating, return false
            if (!$folder_check) {
                // @codeCoverageIgnoreStart
                self::printLog("Error creating folder: ".$folder, 'error-core', $echo);
                return false;
                // @codeCoverageIgnoreEnd
            }
        }
        // Create/write to file
        $fp = @fopen($folder.'/'.$file, 'a');
        if (!$fp) {
            // @codeCoverageIgnoreStart
            self::printLog("Error creating file: ".$folder.'/'.$file, 'error-core', $echo);
            return false;
            // @codeCoverageIgnoreEnd
        }
        fwrite($fp, $string);
        fflush($fp);

        return true;
    }

    /**
     * Get time in UTC timezone
     *
     * @param string $format Output format
     * @param string $when   time
     *
     * @return string
     */
    public function getTime($format = 'Ymd His', $when = 'now')
    {
        $timezone = new DateTimeZone('UTC');
        $time = new DateTime($when, $timezone);
        $time->setTimezone($timezone);

        return $time->format($format);
    }


    /**
     * Validate email address
     *
     * @param strin $email Email address
     *
     * @return boolean
     */
    public function IsValidEmailAddress($email)
    {
        // Validate if it's a non-empty string
        if (!is_string($email) || empty($email)) {
            return false;
        }

        // Validate that string only has one "@" symbol
        $at_count = 0;
        for ($i=0; $i < strlen($email); $i++) {
            if ($email[$i] == '@') {
                $at_count++;
            }
        }
        if ($at_count != 1) {
            return false;
        }

        // Validate DNS domain & MX records
        $explode = explode("@", $email);
        $dns     = array_pop($explode);
        if (empty($dns) || !checkdnsrr($dns, "MX")) {
            return false;
        }

        return true;
    }
}
