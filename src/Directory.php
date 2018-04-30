<?php
/**
 * Created by sublime 3.
 * Auth: Inhere
 * Date: 15-1-14
 * Time: 10:35
 * Uesd: 主要功能是 hi
 */

namespace Toolkit\File;

use DirectoryIterator;
use Toolkit\File\Exception\FileSystemException;
use Toolkit\File\Exception\FileNotFoundException;

/**
 * Class Directory
 * @package Toolkit\File
 */
class Directory extends FileSystem
{
    /**
     * ```php
     * $filter = function ($current, $key, $iterator) {
     *  // \SplFileInfo $current
     *  // Skip hidden files and directories.
     *  if ($current->getFilename()[0] === '.') {
     *      return false;
     *  }
     *  if ($current->isDir()) {
     *      // Only recurse into intended subdirectories.
     *      return $current->getFilename() !== '.git';
     *  }
     *      // Only consume files of interest.
     *      return strpos($current->getFilename(), '.php') !== false;
     * };
     *
     * // $info is instance of \SplFileInfo
     * foreach(Directory::getRecursiveIterator($srcDir, $filter) as $info) {
     *    // $info->getFilename(); ...
     * }
     * ```
     * @param string $srcDir
     * @param callable $filter
     * @return \RecursiveIteratorIterator
     * @throws \LogicException
     */
    public static function getRecursiveIterator($srcDir, callable $filter): \RecursiveIteratorIterator
    {
        return self::getIterator($srcDir, $filter);
    }

    /**
     * 判断文件夹是否为空
     * @param $dir
     * @return bool
     * @throws FileSystemException
     */
    public static function isEmpty($dir): bool
    {
        $handler = opendir($dir);

        if (false === $handler) {
            throw new FileSystemException("Open the dir failure! DIR: $dir");
        }

        while (($file = readdir($handler)) !== false) {

            if ($file !== '.' && $file !== '..') {
                closedir($handler);

                return false;
            }
        }

        closedir($handler);

        return true;
    }

    /**
     * 查看一个目录中的所有文件和子目录
     * @param $path
     * @throws FileNotFoundException
     * @return array
     */
    public static function ls($path): array
    {
        $list = [];

        try {
            /*** class create new DirectoryIterator Object ***/
            foreach (new DirectoryIterator($path) as $item) {
                $list[] = $item;
            }
            /*** if an exception is thrown, catch it here ***/
        } catch (\Exception $e) {
            throw new FileNotFoundException($path . ' 没有任何内容');
        }

        return $list;
    }

    /**
     * 只获得目录结构
     * @param $path
     * @param int $pid
     * @param int $son
     * @param array $list
     * @return array
     * @throws FileNotFoundException
     */
    public static function getList($path, $pid = 0, $son = 0, array $list = []): array
    {
        $path = self::pathFormat($path);

        if (!is_dir($path)) {
            throw new FileNotFoundException("directory not exists! DIR: $path");
        }

        static $id = 0;

        foreach (glob($path . '*') as $v) {
            if (is_dir($v)) {
                $id++;

                $list[$id]['id'] = $id;
                $list[$id]['pid'] = $pid;
                $list[$id]['name'] = basename($v);
                $list[$id]['path'] = realpath($v);

                //是否遍历子目录
                if ($son) {
                    $list = self::getList($v, $id, $son, $list);
                }
            }
        }

        return $list;
    }

    /**
     * @param $path
     * @param bool $loop
     * @param null $parent
     * @param array $list
     * @return array
     * @throws FileNotFoundException
     */
    public static function getDirs($path, $loop = false, $parent = null, array $list = []): array
    {
        $path = self::pathFormat($path);

        if (!is_dir($path)) {
            throw new FileNotFoundException("directory not exists! DIR: $path");
        }

        $len = \strlen($path);

        foreach (glob($path . '*') as $v) {
            if (is_dir($v)) {
                $relatePath = substr($v, $len);
                $list[] = $parent . $relatePath;

                //是否遍历子目录
                if ($loop) {
                    $list = self::getDirs($v, $loop, $relatePath . '/', $list);
                }
            }
        }

        return $list;
    }

    /**
     * 获得目录下的文件，可选择类型、是否遍历子文件夹
     * @param string $dir string 目标目录
     * @param string|array $ext array('css','html','php') css|html|php
     * @param bool $recursive int|bool 是否包含子目录
     * @return array
     * @throws FileNotFoundException
     */
    public static function simpleInfo($dir, $ext = null, $recursive = false): array
    {
        $list = [];
        $dir = self::pathFormat($dir);
        $ext = \is_array($ext) ? implode('|', $ext) : trim($ext);

        if (!is_dir($dir)) {
            throw new FileNotFoundException("directory not exists! DIR: $dir");
        }

        // glob()寻找与模式匹配的文件路径 $file is pull path
        foreach (glob($dir . '*') as $file) {

            // 匹配文件 如果没有传入$ext 则全部遍历，传入了则按传入的类型来查找
            if (is_file($file) && (!$ext || preg_match("/\.($ext)$/i", $file))) {
                //basename — 返回路径中的 文件名部分
                $list[] = basename($file);

                // is directory
            } else {
                $list[] = '/' . basename($file);

                if ($recursive) {
                    $list = array_merge($list, self::simpleInfo($file, $ext, $recursive));
                }
            }
        }

        return $list;
    }

    /**
     * 获得目录下的文件，可选择类型、是否遍历子文件夹
     * @param string $path string 目标目录
     * @param array|string $ext array('css','html','php') css|html|php
     * @param bool $recursive 是否包含子目录
     * @param null|string $parent
     * @param array $list
     * @return array
     * @throws FileNotFoundException
     */
    public static function getFiles($path, $ext = null, $recursive = false, $parent = null, array $list = []): array
    {
        $path = self::pathFormat($path);

        if (!is_dir($path)) {
            throw new FileNotFoundException("directory not exists! DIR: $path");
        }

        $len = \strlen($path);
        $ext = \is_array($ext) ? implode('|', $ext) : trim($ext);

        foreach (glob($path . '*') as $v) {
            $relatePath = substr($v, $len);

            // 匹配文件 如果没有传入$ext 则全部遍历，传入了则按传入的类型来查找
            if (is_file($v) && (!$ext || preg_match("/\.($ext)$/i", $v))) {
                $list[] = $parent . $relatePath;

            } elseif ($recursive) {
                $list = self::getFiles($v, $ext, $recursive, $relatePath . '/', $list);
            }
        }

        return $list;
    }

    /**
     * 获得目录下的文件以及详细信息，可选择类型、是否遍历子文件夹
     * @param $path string 目标目录
     * @param array|string $ext array('css','html','php') css|html|php
     * @param $recursive int|bool 是否包含子目录
     * @param array $list
     * @return array
     * @throws \InvalidArgumentException
     * @throws FileNotFoundException
     */
    public static function getFilesInfo($path, $ext = null, $recursive = 0, &$list): array
    {
        $path = self::pathFormat($path);

        if (!is_dir($path)) {
            throw new FileNotFoundException("directory not exists! DIR: $path");
        }

        $ext = \is_array($ext) ? implode('|', $ext) : trim($ext);

        static $id = 0;

        //glob()寻找与模式匹配的文件路径
        foreach (glob($path . '*') as $file) {
            $id++;

            // 匹配文件 如果没有传入$ext 则全部遍历，传入了则按传入的类型来查找
            if (is_file($file) && (!$ext || preg_match("/\.($ext)$/i", $file))) {
                $list[$id] = File::info($file);

                //是否遍历子目录
            } elseif ($recursive) {
                $list = self::getFilesInfo($file, $ext, $recursive, $list);
            }
        }

        return $list;
    }

    /**
     * 支持层级目录的创建
     * @param $path
     * @param int|string $mode
     * @param bool $recursive
     * @return bool
     */
    public static function create($path, $mode = 0775, $recursive = true): bool
    {
        return (is_dir($path) || !(!@mkdir($path, $mode, $recursive) && !is_dir($path))) && is_writable($path);
    }

    /**
     * 复制目录内容
     * @param $oldDir
     * @param $newDir
     * @return bool
     * @throws FileNotFoundException
     */
    public static function copy($oldDir, $newDir): bool
    {
        $oldDir = self::pathFormat($oldDir);
        $newDir = self::pathFormat($newDir);

        if (!is_dir($oldDir)) {
            throw new FileNotFoundException('复制失败：' . $oldDir . ' 不存在！');
        }

        $newDir = self::create($newDir);

        foreach (glob($oldDir . '*') as $v) {
            $newFile = $newDir . basename($v);//文件

            //文件存在，跳过复制它
            if (file_exists($newFile)) {
                continue;
            }

            if (is_dir($v)) {
                self::copy($v, $newFile);
            } else {
                copy($v, $newFile);//是文件就复制过来
                @chmod($newFile, 0664);// 权限 0777
            }
        }

        return true;
    }

    /**
     * 删除目录及里面的文件
     * @param $path
     * @param  boolean $delSelf 默认最后删掉自己
     * @return bool
     */
    public static function delete($path, $delSelf = true): bool
    {
        $dirPath = self::pathFormat($path);

        if (is_file($dirPath)) {
            return unlink($dirPath);
        }

        foreach (glob($dirPath . '*') as $v) {
            is_dir($v) ? self::delete($v) : unlink($v);
        }

        $delSelf && rmdir($dirPath);//默认最后删掉自己

        return true;
    }

    /**
     * 比较文件路径
     * @param $newPath
     * @param $oldPath
     * @return string
     */
    public static function comparePath($newPath, $oldPath): string
    {
        $oldDirName = basename(rtrim($oldPath, '/'));
        $newPath_arr = explode('/', rtrim($newPath, '/'));
        $oldPath_arr = explode('/', rtrim($oldPath, '/'));

        $reOne = array_diff($newPath_arr, $oldPath_arr);
        $numOne = \count((array)$reOne);//

        /**
         * 跟框架在同一个父目录[phpTest]下
         * projectPath 'F:/www/phpTest/xxx/yyy/[zzz]'--应用目录 zzz,
         * yzonePath 'F:/www/phpTest/[yzonefk]'---框架目录 [yzonefk]
         * 从应用'F:/www/phpTest/xxx/yyy/[zzz]/'目录回滚到共同的父目录[这里是从zzz/web回滚到phpTest]
         * 入口文件 在 zzz/web/index.php
         */
        $dirStr = '__DIR__';

        for ($i = 0; $i <= $numOne; $i++) {
            $dirStr = 'dirname( ' . $dirStr . ' )';
        }

        $dirStr .= '.\'';

        /**
         * 跟框架在不同父目录下,在回滚到共同的父目录后，再加上到框架的路径
         * newPath 'F:/www/otherDir/ddd/eee/xxx/yyy/[zzz]'--应用目录 zzz
         * oldPath 'F:/www/phpTest/[yzonefk]'---框架目录[yzonefk]
         */
        if (\dirname($newPath) !== \dirname($oldPath)) {
            $reTwo = array_diff($oldPath_arr, $newPath_arr);
            $reTwo = array_shift($reTwo);
            // $numTwo = count($reTwo);// 从框架目录向上回滚，找到相同的父节点，得到相隔几层
            $dirStr .= implode('/', (array)$reTwo);
        }

        $dirStr = $dirStr . '/' . $oldDirName . '/Gee.php\'';

        return $dirStr;
    }
}
