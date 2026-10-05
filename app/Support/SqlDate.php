<?php

namespace App\Support;

use Illuminate\Database\Connection;

class SqlDate
{
    public static function hour(Connection $connection, string $column): string
    {
        if ($connection->getDriverName() === 'sqlite') {
            return "cast(strftime('%H', {$column}) as integer)";
        }

        return "hour({$column})";
    }

    public static function month(Connection $connection, string $column): string
    {
        if ($connection->getDriverName() === 'sqlite') {
            return "cast(strftime('%m', {$column}) as integer)";
        }

        return "month({$column})";
    }

    public static function yearMonth(Connection $connection, string $column): string
    {
        if ($connection->getDriverName() === 'sqlite') {
            return "strftime('%Y-%m', {$column})";
        }

        return "date_format({$column}, '%Y-%m')";
    }
}
