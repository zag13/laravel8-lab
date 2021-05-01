<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/1
 * Time: 8:08 上午
 */


namespace App\Utils\Single;

use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class SingleSpreadsheet
{

    private static $instance = null;

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): Spreadsheet
    {
        if (static::$instance === null) {
            static::$instance = new Spreadsheet();
        }

        return static::$instance;
    }

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize SingleSpreadsheet");
    }
}
