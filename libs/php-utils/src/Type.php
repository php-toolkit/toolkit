<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/5/1 0001
 * Time: 19:53
 */

namespace Toolkit\PhpUtil;

/**
 * Class Type - php data type
 * @package Toolkit\PhpUtil
 */
final class Type
{
    // basic types
    public const INT      = 'int';
    public const INTEGER  = 'integer';
    public const FLOAT    = 'float';
    public const DOUBLE   = 'double';
    public const BOOL     = 'bool';
    public const BOOLEAN  = 'boolean';
    public const STRING   = 'string';

    // complex types
    public const ARRAY    = 'array';
    public const OBJECT   = 'object';
    public const RESOURCE = 'resource';

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
