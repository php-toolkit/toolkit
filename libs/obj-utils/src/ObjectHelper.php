<?php
/**
 * Created by sublime 3.
 * Auth: Inhere
 * Date: 14-
 * Time: 10:35
 * Uesd: 主要功能是 hi
 */

namespace Toolkit\ObjUtil;

/**
 * Class ObjectHelper
 * @package Toolkit\ObjUtil
 */
class ObjectHelper
{
    /**
     * 给对象设置属性值
     * - 会先尝试用 setter 方法设置属性
     * - 再尝试直接设置属性
     * @param mixed $object An object instance
     * @param array $options
     * @return mixed
     */
    public static function init($object, array $options)
    {
        foreach ($options as $property => $value) {
            if (\is_numeric($property)) {
                continue;
            }

            $setter = 'set' . \ucfirst($property);

            // has setter
            if (\method_exists($object, $setter)) {
                $object->$setter($value);
            } elseif (\property_exists($object, $property)) {
                $object->$property = $value;
            }
        }

        return $object;
    }

    /**
     * 给对象设置属性值
     * @param       $object
     * @param array $options
     */
    public static function configure($object, array $options)
    {
        foreach ($options as $property => $value) {
            if (\property_exists($object, $property)) {
                $object->$property = $value;
            }
        }
    }

    /**
     * 给对象设置属性值
     * @param       $object
     * @param array $options
     */
    public static function setAttrs($object, array $options)
    {
        self::configure($object, $options);
    }

    /**
     * 定义一个用来序列化数据的函数
     * @param mixed $obj
     * @return string
     */
    public static function encode($obj): string
    {
        return \base64_encode(\gzcompress(\serialize($obj)));
    }

    /**
     * 反序列化
     * @param string     $txt
     * @param bool|array $allowedClasses
     * @return mixed
     */
    public static function decode(string $txt, $allowedClasses = false)
    {
        return \unserialize(\gzuncompress(\base64_decode($txt)), ['allowed_classes' => $allowedClasses]);
    }

    /**
     * php对象转换成为数组
     * @param iterable|array|\Traversable $data
     * @param bool                        $recursive
     * @return array|bool
     */
    public static function toArray($data, bool $recursive = false)
    {
        $arr = [];

        // Ensure the input data is an array.
        if (\is_object($data)) {
            if ($data instanceof \Traversable) {
                $arr = \iterator_to_array($data);
            } elseif (\method_exists($data, 'toArray')) {
                $arr = $data->toArray();
            }
        } else {
            $arr = (array)$data;
        }

        if ($recursive) {
            foreach ($arr as $key => $value) {
                if (\is_array($value) || \is_object($value)) {
                    $arr[$key] = static::toArray($value, $recursive);
                }
            }
        }

        return $arr;
    }

    /**
     * @param mixed $object
     * @param bool  $unique
     * @return string
     */
    public static function hash($object, $unique = true): string
    {
        if (\is_object($object)) {
            $hash = \spl_object_hash($object);

            if ($unique) {
                $hash = \md5($hash);
            }

            return $hash;
        }

        // a class
        return \is_string($object) ? \md5($object) : '';
    }

    /**
     * @from https://github.com/ventoviro/windwalker
     * Build an array of constructor parameters.
     * @param \ReflectionMethod $method Method for which to build the argument array.
     * @param array             $extraArgs
     * @return array
     * @throws \RuntimeException
     * @throws \ReflectionException
     */
    public static function getMethodArgs(\ReflectionMethod $method, array $extraArgs = []): array
    {
        $methodArgs = [];

        foreach ($method->getParameters() as $idx => $param) {
            // if user have been provide arg
            if (isset($extraArgs[$idx])) {
                $methodArgs[] = $extraArgs[$idx];
                continue;
            }

            $dependencyClass = $param->getClass();

            // If we have a dependency, that means it has been type-hinted.
            if ($dependencyClass && ($depClass = $dependencyClass->getName()) !== \Closure::class) {
                $depClass = $dependencyClass->getName();
                $depObject = self::create($depClass);

                if ($depObject instanceof $depClass) {
                    $methodArgs[] = $depObject;
                    continue;
                }
            }

            // Finally, if there is a default parameter, use it.
            if ($param->isOptional()) {
                $methodArgs[] = $param->getDefaultValue();
                continue;
            }

            // $dependencyVarName = $param->getName();
            // Couldn't resolve dependency, and no default was provided.
            throw new \RuntimeException(sprintf(
                'Could not resolve dependency: %s for the %dth parameter',
                $param->getPosition(),
                $param->getName()
            ));
        }

        return $methodArgs;
    }

    /**
     * 从类名创建服务实例对象，会尽可能自动补完构造函数依赖
     * @from windWalker https://github.com/ventoviro/windwalker
     * @param string $class a className
     * @return mixed
     * @throws \RuntimeException
     */
    public static function create(string $class)
    {
        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            return false;
        }

        $constructor = $reflection->getConstructor();

        // If there are no parameters, just return a new object.
        if (null === $constructor) {
            return new $class;
        }

        $newInstanceArgs = self::getMethodArgs($constructor);

        // Create a callable for the dataStorage
        return $reflection->newInstanceArgs($newInstanceArgs);
    }

    /**
     * @param string|array $config
     * @return mixed
     */
    public static function smartCreate($config)
    {
        if (\is_string($config)) {
            return new $config;
        }

        if (\is_array($config) && !empty($config['class'])) {
            $class = $config['class'];
            $args = $config[0] ?? [];

            $obj = new $class(...$args);

            unset($config['class'], $config[0]);
            return self::init($obj, $config);
        }

        return null;
    }
}
