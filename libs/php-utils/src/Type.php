<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/5/1 0001
 * Time: 19:53
 */

namespace Toolkit\PhpUtil;

/**
 * Class Type
 * @package Toolkit\PhpUtil
 */
final class Type
{
    // php data type
    const INT      = 'int';
    const INTEGER  = 'integer';
    const FLOAT    = 'float';
    const DOUBLE   = 'double';
    const BOOL     = 'bool';
    const BOOLEAN  = 'boolean';
    const STRING   = 'string';

    const ARRAY    = 'array';
    const OBJECT   = 'object';
    const RESOURCE = 'resource';

    /**
     * @return array
     */
    public static function all(): array
    {
        return [
            self::ARRAY,
            self::BOOL,
            self::BOOLEAN,
            self::DOUBLE,
            self::FLOAT,
            self::INT,
            self::INTEGER,
            self::OBJECT,
            self::STRING,
            self::RESOURCE
        ];
    }

    /**
     * @return array
     */
    public static function scalars(): array
    {
        return [
            self::BOOL,
            self::BOOLEAN,
            self::DOUBLE,
            self::FLOAT,
            self::INT,
            self::INTEGER,
            self::STRING
        ];
    }
}
