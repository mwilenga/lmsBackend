<?php


namespace App\Core\Enum;


class BaseEnum
{
    protected static $enumList;

    public static function get($path = null)
    {
        if ($path) {
            $enumValue = static::$enumList;
            $path = explode('/', $path);

            foreach ($path as $bit) {
                if (isset($enumValue[$bit])) {
                    $enumValue = $enumValue[$bit];
                }
            }

            return $enumValue;
        }

        return null;
    }

    public static function getByValue($value, $list)
    {
        foreach ($list as $item) {
            if ($item['value'] == $value) {
                return $item;
            }
        }

        return null;
    }

    public static function getValueList()
    {
        $list = [];
        foreach (static::$enumList as $item) {
            $list[] = $item['value'];
        }

        return $list;
    }
}
