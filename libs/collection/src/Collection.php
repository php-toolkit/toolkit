<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2016/3/14
 * Time: 19:44
 */

namespace Toolkit\Collection;

use Toolkit\ArrUtil\Arr;
use Toolkit\File\File;
use Toolkit\File\Parse\IniParser;
use Toolkit\File\Parse\JsonParser;
use Toolkit\File\Parse\YmlParser;
use Toolkit\ObjUtil\Obj;

/**
 * Class DataCollector - 数据收集器 (数据存储器 - DataStorage) complex deep
 * @package Toolkit\Collection
 * 支持 链式的子节点 设置 和 值获取
 * e.g:
 * ```
 * $data = [
 *      'foo' => [
 *          'bar' => [
 *              'yoo' => 'value'
 *          ]
 *       ]
 * ];
 * $config = new DataCollector();
 * $config->get('foo.bar.yoo')` equals to $data['foo']['bar']['yoo'];
 * ```
 * 简单的数据对象可使用  @see SimpleCollection
 * ```
 * $config = new SimpleCollection($data)
 * $config->get('foo');
 * ```
 */
class Collection extends SimpleCollection
{
    /**
     * @var array
     */
    // protected $files = [];

    /**
     * Property separator.
     * @var  string
     */
    protected $separator = '.';

    /**
     * name
     * @var string
     */
    protected $name;

    /**
     * formats
     * @var array
     */
    protected static $formats = ['json', 'php', 'ini', 'yml'];

    public const FORMAT_JSON = 'json';
    public const FORMAT_PHP  = 'php';
    public const FORMAT_INI  = 'ini';
    public const FORMAT_YML  = 'yml';

    /**
     * __construct
     * @param mixed  $data
     * @param string $format
     * @param string $name
     * @throws \RangeException
     */
    public function __construct($data = null, $format = 'php', $name = 'box1')
    {
        // Optionally load supplied data.
        $this->load($data, $format);

        parent::__construct();

        $this->name = $name;
    }

    /**
     * @param mixed  $data
     * @param string $format
     * @param string $name
     * @return static
     * @throws \RangeException
     */
    public static function make($data = null, $format = 'php', $name = 'box1')
    {
        return new static($data, $format, $name);
    }

    /**
     * set config value by path
     * @param string $path
     * @param mixed  $value
     * @return mixed
     */
    public function set($path, $value)
    {
        Arr::setByPath($this->data, $path, $value, $this->separator);

        return $this;
    }

    /**
     * get value by path
     * @param string $path
     * @param string $default
     * @return mixed
     */
    public function get(string $path, $default = null)
    {
        return Arr::getByPath($this->data, $path, $default, $this->separator);
    }

    public function exists($path): bool
    {
        return $this->get($path) !== null;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function has(string $path): bool
    {
        return $this->exists($path);
    }

    public function reset()
    {
        $this->data = [];

        return $this;
    }

    /**
     * Clear all data.
     * @return  static
     */
    public function clear()
    {
        return $this->reset();
    }

    /**
     * @param string $class
     * @return mixed
     */
    public function toObject(string $class = \stdClass::class)
    {
        return Arr::toObject($this->data, $class);
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     */
    public function setSeparator($separator): void
    {
        $this->separator = $separator;
    }

    /**
     * @return array
     */
    public static function getFormats(): array
    {
        return static::$formats;
    }

    /**
     * setName
     * @param string $value
     * @return $this
     */
    public function setName($value): self
    {
        $this->name = $value;

        return $this;
    }

    /**
     * getName
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * load
     * @param string|array|mixed $data
     * @param string             $format = 'php'
     * @return static
     * @throws \InvalidArgumentException
     * @throws \RangeException
     */
    public function load($data, $format = 'php')
    {
        if (!$data) {
            return $this;
        }

        if (\is_string($data) && \in_array($format, static::$formats, true)) {
            switch ($format) {
                case static::FORMAT_YML:
                    $this->loadYaml($data);
                    break;

                case static::FORMAT_JSON:
                    $this->loadJson($data);
                    break;

                case static::FORMAT_INI:
                    $this->loadIni($data);
                    break;

                case static::FORMAT_PHP:
                default:
                    $this->loadArray($data);
                    break;
            }

        } elseif (\is_array($data) || \is_object($data)) {
            $this->bindData($this->data, $data);
        }

        return $this;
    }

    /**
     * @param        $file
     * @param string $format
     * @return array|mixed
     * @throws \Toolkit\File\Exception\FileNotFoundException
     */
    public static function read($file, $format = self::FORMAT_PHP)
    {
        return File::load($file, $format);
    }

    /**
     * load data form yml file
     * @param $data
     * @return static
     */
    public function loadYaml($data)
    {
        return $this->bindData($this->data, static::parseYaml($data));
    }

    /**
     * load data form php file or array
     * @param array|string $data
     * @return static
     * @throws \InvalidArgumentException
     */
    public function loadArray($data)
    {
        if (\is_string($data) && is_file($data)) {
            $data = require $data;
        }

        if (!\is_array($data)) {
            throw new \InvalidArgumentException('param type error! must is array.');
        }

        return $this->bindData($this->data, $data);
    }

    /**
     * load data form php file or array
     * @param mixed $data
     * @return static
     * @throws \InvalidArgumentException
     */
    public function loadObject($data)
    {
        if (!\is_object($data)) {
            throw new \InvalidArgumentException('param type error! must is object.');
        }

        return $this->bindData($this->data, $data);
    }

    /**
     * load data form ini file
     * @param string $string
     * @return static
     */
    public function loadIni($string)
    {
        return $this->bindData($this->data, self::parseIni($string));
    }

    /**
     * load data form json file
     * @param $data
     * @return Collection
     */
    public function loadJson($data): Collection
    {
        return $this->bindData($this->data, static::parseJson($data));
    }

    /**
     * @param            $parent
     * @param            $data
     * @param bool|false $raw
     * @return $this
     */
    protected function bindData(&$parent, $data, $raw = false): self
    {
        // Ensure the input data is an array.
        if (!$raw) {
            $data = Obj::toArray($data);
        }

        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (\is_array($value)) {
                if (!isset($parent[$key])) {
                    $parent[$key] = [];
                }

                $this->bindData($parent[$key], $value);
            } else {
                $parent[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getKeys(): array
    {
        return array_keys($this->data);
    }

    /**
     * @return \RecursiveArrayIterator
     */
    public function getIterator(): \Traversable
    {
        return new \RecursiveArrayIterator($this->data);
    }

    /**
     * Unset an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @return  void
     */
    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }

    public function __clone()
    {
        $this->data = \unserialize(\serialize($this->data), ['allowed_classes' => self::class]);
    }

    //////
    ///////////////////////////// helper /////////////////////////
    //////

    /**
     * @param               $string
     * @param bool          $enhancement
     * @param callable|null $pathHandler
     * @param string        $fileDir
     * @return array
     */
    public static function parseIni($string, $enhancement = false, callable $pathHandler = null, $fileDir = ''): array
    {
        return IniParser::parse($string, $enhancement, $pathHandler, $fileDir);
    }

    /**
     * @param               $data
     * @param bool          $enhancement
     * @param callable|null $pathHandler
     * @param string        $fileDir
     * @return array
     */
    public static function parseJson($data, $enhancement = false, callable $pathHandler = null, $fileDir = ''): array
    {
        return JsonParser::parse($data, $enhancement, $pathHandler, $fileDir);
    }

    /**
     * parse YAML
     * @param string|bool $data Waiting for the parse data
     * @param bool        $enhancement Simple support import other config by tag 'import'. must is bool.
     * @param callable    $pathHandler When the second param is true, this param is valid.
     * @param string      $fileDir When the second param is true, this param is valid.
     * @return array
     */
    public static function parseYaml($data, $enhancement = false, callable $pathHandler = null, $fileDir = ''): array
    {
        return YmlParser::parse($data, $enhancement, $pathHandler, $fileDir);
    }
}
