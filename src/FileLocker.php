<?php

namespace Brezgalov\SimpleFileLocker;

class FileLocker
{
    /**
     * @var array
     */
    protected static $loc_files = [];

    /**
     * @param string $file_name
     * @param bool $wait
     * @return bool|resource
     * @throws Exception
     */
    public static function lockFile($file_name, $wait = false) {
        $loc_file = fopen($file_name, 'c');
        if ( !$loc_file ) {
            throw new \Exception('Can\'t create lock file!');
        }
        if ( $wait ) {
            $lock = flock($loc_file, LOCK_EX);
        } else {
            $lock = flock($loc_file, LOCK_EX | LOCK_NB);
        }
        if ( $lock ) {
            self::$loc_files[$file_name] = $loc_file;
            fprintf($loc_file, "%s\n", getmypid());
            return $loc_file;
        } else if ( $wait ) {
            throw new \Exception('Can\'t lock file!');
        } else {
            return false;
        }
    }

    /**
     * @param string $file_name
     */
    public static function unlockFile($file_name) {
        fclose(self::$loc_files[$file_name]);
        @unlink($file_name);
        unset(self::$loc_files[$file_name]);
    }
}