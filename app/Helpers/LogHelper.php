<?php
namespace App\Helpers;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LogHelper
{
    protected static $loggers = [];

    public static function log($logName, $message, $context = [])
    {
        if (empty(@self::$loggers[$logName])) {
            self::$loggers[$logName] = new Logger($logName);
        }

        /** @var Logger $logInstance */
        $logInstance = self::$loggers[$logName];
        $logPath = storage_path('/logs/' . $logName . '.log');

        if (!file_exists($logPath))
            touch($logPath);

        $logInstance->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
        $logInstance->addDebug($message, $context);
    }

    public static function logSql()
    {
        \DB::listen(function ($query) {
            self::log("SQL", $query->sql, $query->bindings);
        });
    }
}