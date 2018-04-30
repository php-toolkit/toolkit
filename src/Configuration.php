<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2016/12/14
 * Time: 19:44
 */

namespace Toolkit\Collection;

use Toolkit\StrUtil\Str;
use RuntimeException;

/**
 * Class Configuration - 配置数据管理
 * @package collections
 */
final class Configuration extends Collection
{
    const MODE_DATA = 'data';
    const MODE_FOLDER = 'folder';

    /**
     * config mode
     * Allow:
     *  folder 文件夹模式，传入一个配置文件夹路径，根据文件夹中的配置名称读取配置数据
     *  data  数据模式，可以一个配置文件的路径，将会自动读取载入；或直接传入数组数据
     * @var string
     */
    protected $mode = self::MODE_DATA;

    /**
     * @var string
     */
    protected $format = self::FORMAT_PHP;

    /**
     * when mode is 'folder', the config folder path
     * @var string
     */
    private $folderPath;

    /**
     * 数据是否只读的
     * @var boolean
     */
    private $readonly = false;

    /**
     * @var bool
     */
   // private $advanced = false;

    /**
     * __construct
     * @param mixed $data If mode is 'folder', $data is config folder path
     * @param string $format
     * @param string $name
     * @throws \RuntimeException
     */
    public function __construct($data = null, string $format = self::FORMAT_PHP, $name = 'config')
    {
        $this->format = $format;

        if (\is_string($data) && is_dir($data)) {
            $this->mode = self::MODE_FOLDER;
            $this->folderPath = $data;
            $data = null;
        }

        if ($this->mode === self::MODE_FOLDER && !is_dir($this->folderPath)) {
            throw new RuntimeException("Config mode is 'folder'. the property 'folderPath' must is a folder path!");
        }

        parent::__construct($data, $this->format, $name);
    }

    /**
     * set config value by path
     * @param string $path
     * @param mixed $value
     * @return mixed
     * @throws \RuntimeException
     */
    public function set($path, $value)
    {
        // if is readonly
        if ($this->readonly && $this->has($path)) {
            throw new RuntimeException("Config data have been setting readonly. don't allow change.");
        }

        return parent::set($path, $value);
    }

    /**
     * get value by path
     * @param string $path
     * @param string $default
     * @return mixed
     * @throws \RuntimeException
     */
    public function get(string $path, $default = null)
    {
        if ($this->mode === self::MODE_FOLDER) {
            $nodes = Str::toArray($path, $this->separator);
            $name = array_shift($nodes);// config file name

            // if config file not load. load it.
            if (!isset($this->data[$name])) {
                $file = $this->folderPath . "/{$name}.{$this->format}";

                if (!is_file($file)) {
                    throw new \RuntimeException("The want get config file not exist, Name: $name, File: $file");
                }

                $this->data[$name] = self::read($file, $this->format);
            }
        }

        return parent::get($path, $default);
    }

    /**
     * get Mode
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode(string $mode)
    {
        $this->mode = $mode;
    }

    /**
     * @param bool $readonly
     */
    public function setReadonly($readonly)
    {
        $this->readonly = (bool)$readonly;
    }

    /**
     * data is Readonly
     * @return boolean
     */
    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getFolderPath(): string
    {
        return $this->folderPath;
    }

    /**
     * @param string $folderPath
     * @throws \InvalidArgumentException
     */
    public function setFolderPath(string $folderPath)
    {
        if (!is_dir($folderPath)) {
            throw new \InvalidArgumentException("The config files folder path is not exists! Path: $folderPath");
        }

        $this->folderPath = $folderPath;
    }
}
