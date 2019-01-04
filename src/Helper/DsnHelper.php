<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2016/3/2 0002
 * Time: 22:33
 */

namespace Toolkit\Helper;

/**
 * Class DsnHelper
 * @see http://php.net/manual/en/pdo.drivers.php
 * @link https://github.com/ventoviro/windwalker-database
 */
class DsnHelper
{
    /**
     * Property options.
     * @var  array
     */
    private static $options = [];

    /**
     * extractDsn
     * @param   string $dsn
     * @return  array
     */
    public static function extractDsn($dsn)
    {
        // Parse DSN to array
        $dsn = str_replace(';', "\n", $dsn);

        return parse_ini_string($dsn);
    }

    /**
     * getDsn
     * @param string $driver
     * @param array $options
     * @throws \LogicException
     * @throws  \RuntimeException
     * @return  string
     */
    public static function getDsn(array $options = [], $driver = null)
    {
        if (!empty($options['dsn'])) {
            return $options['dsn'];
        }

        self::$options = $options;

        if (!$driver) {
            $driver = $options['driver'] ?? null;
        }

        if (!$driver) {
            throw new \LogicException('The driver name is required.');
        }

        if (!\is_callable([static::class, $driver])) {
            throw new \RuntimeException('The ' . $driver . ' driver is not supported.');
        }

        list($dsn, $replace) = static::$driver();

        $dsn = strtr($dsn, $replace);

        self::$options = [];

        return $dsn;
    }

    /**
     * mysql
     * @return  array
     */
    protected static function mysql()
    {
        return [
            'mysql:host={HOST};port={PORT};dbname={DBNAME};charset={CHARSET}',
            [
                '{HOST}' => static::getOption('host', 'localhost'),
                '{PORT}' => static::getOption('port', 3306),
                '{DBNAME}' => static::getOption('database'),
                '{CHARSET}' => static::getOption('charset', 'utf8')
            ]
        ];
    }

    /**
     * cubrid
     * @return  array
     */
    protected static function cubrid()
    {
        return [
            'cubrid:host={HOST};port={PORT};dbname={DBNAME}',
            [
                '{HOST}' => static::getOption('host', 'localhost'),
                '{PORT}' => static::getOption('port', 33000),
                '{DBNAME}' => static::getOption('database')
            ]
        ];
    }

    /**
     * dblib
     * @return  array
     */
    protected static function dblib()
    {
        return [
            'dblib:host={HOST};port={PORT};dbname={DBNAME}',
            [
                '{HOST}' => static::getOption('host', 'localhost'),
                '{PORT}' => static::getOption('port', 1433),
                '{DBNAME}' => static::getOption('database')
            ]
        ];
    }

    /**
     * firebird
     * @return  array
     */
    protected static function firebird()
    {
        return [
            'firebird:dbname={DBNAME}',
            [
                '{DBNAME}' => static::getOption('database')
            ]
        ];
    }

    /**
     * ibm
     * @return  array
     */
    protected static function ibm()
    {
        if ($dsn = static::getOption('dsn')) {
            return [
                'ibm:DSN={DSN}',
                [
                    '{DSN}' => $dsn
                ]
            ];
        }

        return [
            'ibm:hostname={HOST};port={PORT};database={DBNAME}',
            [
                '{HOST}' => static::getOption('host', 'localhost'),
                '{PORT}' => static::getOption('port', 56789),
                '{DBNAME}' => static::getOption('database')
            ]
        ];
    }

    /**
     * dblib
     * @return  array
     */
    protected static function informix()
    {
        if ($dsn = static::getOption('dsn')) {
            return [
                'informix:DSN={DSN}',
                [
                    '{DSN}' => $dsn
                ]
            ];
        }

        return [
            'informix:host={HOST};service={PORT};database={DBNAME};server={SERVER};protocol={PROTOCOL}',
            [
                '{HOST}' => static::getOption('host', 'localhost'),
                '{PORT}' => static::getOption('port', 1526),
                '{DBNAME}' => static::getOption('database'),
                '{SERVER}' => static::getOption('server'),
                '{PROTOCOL}' => static::getOption('protocol')
            ]
        ];
    }

    /**
     * mssql
     * @return  array
     */
    protected static function mssql()
    {
        return [
            'mssql:host={HOST};port={PORT};dbname={DBNAME}',
            [
                '{HOST}' => static::getOption('host', 'localhost'),
                '{PORT}' => static::getOption('port', 1433),
                '{DBNAME}' => static::getOption('database')
            ]
        ];
    }

    /**
     * oci
     * @return  array
     */
    protected static function oci()
    {
        if ($dsn = static::getOption('dsn')) {
            return [
                'oci:dbname={DSN};charset={CHARSET}',
                [
                    '{DSN}' => $dsn,
                    '{CHARSET}' => static::getOption('charset', 'AL32UTF8')
                ]
            ];
        }

        return [
            'oci:dbname=//#HOST#:#PORT#/#DBNAME};charset={CHARSET}',
            [
                '{HOST}' => static::getOption('host', 'localhost'),
                '{PORT}' => static::getOption('port', 56789),
                '{DBNAME}' => static::getOption('database'),
                '{CHARSET}' => static::getOption('charset', 'AL32UTF8')
            ]
        ];
    }

    /**
     * odbc
     * @return  array
     */
    protected static function odbc()
    {
        return [
            'odbc:DSN={DSN};UID:#USER};PWD={PASSWORD}',
            [
                '{HOST}' => static::getOption('host', 'localhost'),
                '{USER}' => static::getOption('user', 1433),
                '{PASSWORD}' => static::getOption('password')
            ]
        ];
    }

    /**
     * pgsql
     * @return  array
     */
    protected static function pgsql()
    {
        return [
            'pgsql:host={HOST};port={PORT};dbname={DBNAME}',
            [
                '{HOST}' => static::getOption('host', 'localhost'),
                '{PORT}' => static::getOption('port', 5432),
                '{DBNAME}' => static::getOption('database', 'postgres')
            ]
        ];
    }

    /**
     * Alias of pgsql
     * @return  array
     */
    protected static function postgresql()
    {
        return static::pgsql();
    }

    /**
     * sqlite
     * @return  array
     */
    protected static function sqlite()
    {
        $version = (int)static::getOption('version');

        $format = $version === 2 ? 'sqlite2:{DBNAME}' : 'sqlite:{DBNAME}';

        return [
            $format,
            [
                '{DBNAME}' => static::getOption('database')
            ]
        ];
    }

    /**
     * sybase
     * @return  array
     */
    protected static function sybase()
    {
        return [
            'pgsql:host={HOST};port={PORT};dbname={DBNAME}',
            [
                '{HOST}' => static::getOption('host', 'localhost'),
                '{PORT}' => static::getOption('port', 1433),
                '{DBNAME}' => static::getOption('database')
            ]
        ];
    }

    /**
     * sybase
     * @return  array
     */
    protected static function fourd()
    {
        return [
            '4D:host={HOST};charset=UTF-8',
            [
                '{HOST}' => static::getOption('host', 'localhost'),
                '{CHARSET}' => static::getOption('charset', 'UTF-8')
            ]
        ];
    }

    /**
     * getOption
     * @param string $name
     * @param string $default
     * @return  mixed
     */
    protected static function getOption($name, $default = null)
    {
        return self::$options[$name] ?? $default;
    }
}

