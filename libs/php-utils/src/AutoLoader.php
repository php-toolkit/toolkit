<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/17
 * Time: 上午9:56
 * 参考并提取自 composer, 简单的文件 class 加载器
 */

namespace Toolkit\PhpUtil;

/**
 * Class AutoLoader
 * @package Toolkit\PhpUtil
 * ```php
 * AutoLoader::addFiles([
 *  // file
 * ]);
 * $loader = AutoLoader::getLoader();
 * $loader->addPsr4Map([
 *  'namespace' => 'path'
 * ]);
 * $loader->addClassMap([
 *  'name' => 'file'
 * ]);
 * ```
 */
class AutoLoader
{
    /**
     * @var self
     */
    private static $loader;

    /**
     * @var array
     */
    private static $files = [];

    /**
     * @var array
     * array (
     *  'prefix' => 'dir path'
     * )
     */
    private $psr0Map = [];

    /**
     * @var array
     * array (
     *  'prefix' => 'dir path'
     * )
     */
    private $psr4Map = [];

    /**
     * @var array
     */
    private $classMap = [];

    /**
     * @var array
     */
    private $missingClasses = [];

    /**
     * @return self
     */
    public static function getLoader(): self
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        self::$loader = $loader = new self();

        $loader->register(true);

        foreach (self::$files as $fileIdentifier => $file) {
            globalIncludeFile($fileIdentifier, $file);
        }

        return $loader;
    }

    //////////////////////////////////////////////////////
    /// independent files
    //////////////////////////////////////////////////////

    /**
     * @return array
     */
    public static function getFiles(): array
    {
        return self::$files;
    }

    /**
     * @param array $files
     */
    public static function setFiles(array $files)
    {
        self::$files = $files;
    }

    /**
     * @param array $files
     */
    public static function addFiles(array $files)
    {
        if (self::$files) {
            self::$files = \array_merge(self::$files, $files);
        } else {
            self::$files = $files;
        }
    }

    //////////////////////////////////////////////////////
    /// class loader
    //////////////////////////////////////////////////////

    /**
     * @param string $prefix
     * @param string $path
     */
    public function addPsr0($prefix, $path)
    {
        $this->psr0Map[$prefix] = $path;
    }

    /**
     * @param array $psr0Map Class to filename map
     */
    public function addPsr0Map(array $psr0Map)
    {
        if ($this->psr0Map) {
            $this->psr0Map = \array_merge($this->psr0Map, $psr0Map);
        } else {
            $this->psr0Map = $psr0Map;
        }
    }

    /**
     * @param string $prefix
     * @param string $path
     * @throws \InvalidArgumentException
     */
    public function addPsr4($prefix, $path)
    {
        // Register directories for a new namespace.
        $length = \strlen($prefix);

        if ('\\' !== $prefix[$length - 1]) {
            throw new \InvalidArgumentException('A non-empty PSR-4 prefix must end with a namespace separator.');
        }

        $this->psr4Map[$prefix] = $path;
    }

    /**
     * @param array $psr4Map Class to filename map
     */
    public function addPsr4Map(array $psr4Map)
    {
        if ($this->psr4Map) {
            $this->psr4Map = \array_merge($this->psr4Map, $psr4Map);
        } else {
            $this->psr4Map = $psr4Map;
        }
    }

    /**
     * @return array
     */
    public function getPsr4Map(): array
    {
        return $this->psr4Map;
    }

    /**
     * @param array $psr4Map
     */
    public function setPsr4Map($psr4Map)
    {
        $this->psr4Map = $psr4Map;
    }

    /**
     * @return array
     */
    public function getClassMap(): array
    {
        return $this->classMap;
    }

    /**
     * @param array $classMap
     */
    public function setClassMap(array $classMap)
    {
        $this->classMap = $classMap;
    }

    /**
     * @param array $classMap Class to filename map
     */
    public function addClassMap(array $classMap)
    {
        if ($this->classMap) {
            $this->classMap = \array_merge($this->classMap, $classMap);
        } else {
            $this->classMap = $classMap;
        }
    }

    /**
     * Registers this instance as an autoloader.
     * @param bool $prepend Whether to prepend the autoloader or not
     */
    public function register($prepend = false)
    {
        \spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }

    /**
     * Un-registers this instance as an autoloader.
     */
    public function unRegister()
    {
        \spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     * @param  string $class The name of the class
     * @return bool|null True if loaded, null otherwise
     */
    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) {
            includeClassFile($file);

            return true;
        }

        return null;
    }

    /**
     * Finds the path to the file where the class is defined.
     * @param string $class The name of the class
     * @return string|false The path if found, false otherwise
     */
    public function findFile($class)
    {
        // work around for PHP 5.3.0 - 5.3.2 https://bugs.php.net/50731
        if ('\\' === $class[0]) {
            $class = (string)\substr($class, 1);
        }

        // class map lookup
        if (isset($this->classMap[$class])) {
            return $this->classMap[$class];
        }

        $file = $this->findFileWithExtension($class, '.php');

        if (false === $file) {
            // Remember that this class does not exist.
            $this->missingClasses[$class] = true;
        }

        return $file;
    }

    private function findFileWithExtension($class, $ext)
    {
        // PSR-4 lookup
        $logicalPathPsr4 = \str_replace('\\', DIRECTORY_SEPARATOR, $class) . $ext;

        // PSR-4
        foreach ($this->psr4Map as $prefix => $dir) {
            if (0 === \strpos($class, $prefix)) {
                $length = \strlen($prefix);

                if (\file_exists($file = $dir . DIRECTORY_SEPARATOR . substr($logicalPathPsr4, $length))) {
                    return $file;
                }
            }
        }

        // PEAR-like class name
        $logicalPathPsr0 = \str_replace('_', DIRECTORY_SEPARATOR, $class) . $ext;

        foreach ($this->psr0Map as $prefix => $dir) {
            if (0 === \strpos($class, $prefix)) {
                if (\file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0)) {
                    return $file;
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getMissingClasses(): array
    {
        return $this->missingClasses;
    }
}

function globalIncludeFile($fileIdentifier, $file)
{
    if (empty($GLOBALS['__global_autoload_files'][$fileIdentifier])) {
        require $file;

        $GLOBALS['__global_autoload_files'][$fileIdentifier] = true;
    }
}

/**
 * Scope isolated include.
 * Prevents access to $this/self from included files.
 * @param $file
 */
function includeClassFile($file)
{
    include $file;
}

