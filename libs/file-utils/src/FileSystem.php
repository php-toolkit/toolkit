<?php
/**
 * Created by sublime 3.
 * Auth: Inhere
 * Date: 15-1-14
 * Time: 10:35
 * AbstractFileSystem.php.php
 * Uesd: 主要功能是 hi
 */

namespace Toolkit\File;

use Toolkit\ArrUtil\Arr;
use Toolkit\File\Exception\FileNotFoundException;
use Toolkit\File\Exception\IOException;

/**
 * Class FileSystem
 * @package Toolkit\File
 */
abstract class FileSystem
{
    /**
     * @param $path
     * @return bool
     */
    public static function isAbsPath(string $path): bool
    {
        if (!$path || !\is_string($path)) {
            return false;
        }

        if (
            \strpos($path, '/') === 0 ||  // linux/mac
            1 === \preg_match('#^[a-z]:[\/|\\\]{1}.+#i', $path) // windows
        ) {
            return true;
        }

        return false;
    }

    /**
     * Returns whether the file path is an absolute path.
     * @from Symfony-filesystem
     * @param string $file A file path
     * @return bool
     */
    public static function isAbsolutePath(string $file): bool
    {
        return strspn($file, '/\\', 0, 1)
            || (\strlen($file) > 3 && ctype_alpha($file[0])
                && $file[1] === ':'
                && strspn($file, '/\\', 2, 1)
            )
            || null !== parse_url($file, PHP_URL_SCHEME);
    }

    /**
     * 转换为标准的路径结构
     * @param  string $dirName
     * @return string
     */
    public static function pathFormat(string $dirName): string
    {
        $dirName = (string)\str_ireplace('\\', '/', trim($dirName));

        return \substr($dirName, -1) === '/' ? $dirName : $dirName . '/';
    }

    /**
     * @param string $path e.g phar://E:/workenv/xxx/yyy/app.phar/web
     * @return string
     */
    public function clearPharPath(string $path): string
    {
        if (strpos($path, 'phar://') === 0) {
            $path = (string)substr($path, 7);

            if (strpos($path, '.phar')) {
                return preg_replace('//[\w-]+\.phar/', '', $path);
            }
        }

        return $path;
    }

    /**
     * 检查文件/夹/链接是否存在
     * @param string      $file 要检查的目标
     * @param null|string $type
     * @return array|string
     */
    public static function exists(string $file, $type = null)
    {
        if (!$type) {
            return file_exists($file);
        }

        $ret = false;

        if ($type === 'file') {
            $ret = is_file($file);
        } elseif ($type === 'dir') {
            $ret = is_dir($file);
        } elseif ($type === 'link') {
            $ret = is_link($file);
        }

        return $ret;
    }

    /**
     * @param string            $file
     * @param null|string|array $ext eg: 'jpg|gif'
     * @throws FileNotFoundException
     * @throws \InvalidArgumentException
     */
    public static function check(string $file, $ext = null): void
    {
        if (!$file || !file_exists($file)) {
            throw new FileNotFoundException("File {$file} not exists！");
        }

        if ($ext) {
            if (\is_array($ext)) {
                $ext = implode('|', $ext);
            }

            if (preg_match("/\.($ext)$/i", $file)) {
                throw new \InvalidArgumentException("{$file} extension is not match: {$ext}");
            }
        }
    }

    /**
     * Renames a file or a directory.
     * @from Symfony-filesystem
     * @param string $origin The origin filename or directory
     * @param string $target The new filename or directory
     * @param bool   $overwrite Whether to overwrite the target if it already exists
     * @throws IOException When target file or directory already exists
     * @throws IOException When origin cannot be renamed
     */
    public static function rename(string $origin, string $target, bool $overwrite = false): void
    {
        // we check that target does not exist
        if (!$overwrite && static::isReadable($target)) {
            throw new IOException(sprintf('Cannot rename because the target "%s" already exists.', $target));
        }

        if (true !== rename($origin, $target)) {
            throw new IOException(sprintf('Cannot rename "%s" to "%s".', $origin, $target));
        }
    }

    /**
     * Tells whether a file exists and is readable.
     * @from Symfony-filesystem
     * @param string $filename Path to the file
     * @return bool
     * @throws IOException When windows path is longer than 258 characters
     */
    public static function isReadable(string $filename): bool
    {
        if ('\\' === DIRECTORY_SEPARATOR && \strlen($filename) > 258) {
            throw new IOException('Could not check if file is readable because path length exceeds 258 characters.');
        }

        return is_readable($filename);
    }

    /**
     * Creates a directory recursively.
     * @param string|array|\Traversable $dirs The directory path
     * @param int                       $mode The directory mode
     * @throws IOException On any directory creation failure
     */
    public static function mkdir($dirs, $mode = 0777): void
    {
        foreach (Arr::toIterator($dirs) as $dir) {
            if (is_dir($dir)) {
                continue;
            }

            if (!@mkdir($dir, $mode, true) && !is_dir($dir)) {
                $error = error_get_last();

                if (!is_dir($dir)) {
                    // The directory was not created by a concurrent process. Let's throw an exception with a developer friendly error message if we have one
                    if ($error) {
                        throw new IOException(sprintf('Failed to create "%s": %s.', $dir, $error['message']));
                    }

                    throw new IOException(sprintf('Failed to create "%s"', $dir));
                }
            }
        }
    }

    /**
     * Change mode for an array of files or directories.
     * @from Symfony-filesystem
     * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to change mode
     * @param int                       $mode The new mode (octal)
     * @param int                       $umask The mode mask (octal)
     * @param bool                      $recursive Whether change the mod recursively or not
     * @throws IOException When the change fail
     */
    public static function chmod($files, $mode, $umask = 0000, $recursive = false): void
    {
        foreach (Arr::toIterator($files) as $file) {
            if (true !== @chmod($file, $mode & ~$umask)) {
                throw new IOException(sprintf('Failed to chmod file "%s".', $file));
            }

            if ($recursive && is_dir($file) && !is_link($file)) {
                self::chmod(new \FilesystemIterator($file), $mode, $umask, true);
            }
        }
    }

    /**
     * Change the owner of an array of files or directories.
     * @from Symfony-filesystem
     * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to change owner
     * @param string                    $user The new owner user name
     * @param bool                      $recursive Whether change the owner recursively or not
     * @throws IOException When the change fail
     */
    public static function chown($files, string $user, $recursive = false): void
    {
        foreach (Arr::toIterator($files) as $file) {
            if ($recursive && is_dir($file) && !is_link($file)) {
                self::chown(new \FilesystemIterator($file), $user, true);
            }

            if (is_link($file) && \function_exists('lchown')) {
                if (true !== lchown($file, $user)) {
                    throw new IOException(sprintf('Failed to chown file "%s".', $file));
                }
            } elseif (true !== chown($file, $user)) {
                throw new IOException(sprintf('Failed to chown file "%s".', $file));
            }
        }
    }

    /**
     * @param string   $srcDir
     * @param callable $filter
     * @return \RecursiveIteratorIterator
     * @throws \InvalidArgumentException
     */
    public static function getIterator(string $srcDir, callable $filter): \RecursiveIteratorIterator
    {
        if (!$srcDir || !\file_exists($srcDir)) {
            throw new \InvalidArgumentException('Please provide a exists source directory.');
        }

        $directory = new \RecursiveDirectoryIterator($srcDir);
        $filterIterator = new \RecursiveCallbackFilterIterator($directory, $filter);

        return new \RecursiveIteratorIterator($filterIterator);
    }

    /**
     * @param     $path
     * @param int $mode
     * @return bool
     */
    public static function chmodDir(string $path, $mode = 0664): bool
    {
        if (!is_dir($path)) {
            return @chmod($path, $mode);
        }

        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if ($file !== '.' && $file !== '..') {
                $fullPath = $path . '/' . $file;
                if (is_link($fullPath)) {
                    return false;
                }

                if (!is_dir($fullPath) && !@chmod($fullPath, $mode)) {
                    return false;
                }

                if (!self::chmodDir($fullPath, $mode)) {
                    return false;
                }
            }
        }

        closedir($dh);

        return @chmod($path, $mode) ? true : false;
    }

    /**
     * @param string $dir
     * @return string
     */
    public static function availableSpace(string $dir = '.'): string
    {
        $base = 1024;
        $bytes = disk_free_space($dir);
        $suffix = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $class = min((int)log($bytes, $base), \count($suffix) - 1);

        //echo $bytes . '<br />';

        // pow($base, $class)
        return sprintf('%1.2f', $bytes / ($base ** $class)) . ' ' . $suffix[$class];
    }

    /**
     * @param string $dir
     * @return string
     */
    public static function countSpace(string $dir = '.'): string
    {
        $base = 1024;
        $bytes = disk_total_space($dir);
        $suffix = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $class = min((int)log($bytes, $base), \count($suffix) - 1);

        // pow($base, $class)
        return sprintf('%1.2f', $bytes / ($base ** $class)) . ' ' . $suffix[$class];
    }

    /**
     * 文件或目录权限检查函数
     * @from web
     * @access public
     * @param  string $file_path 文件路径
     * @return int  返回值的取值范围为{0 <= x <= 15}，每个值表示的含义可由四位二进制数组合推出。
     *                  返回值在二进制计数法中，四位由高到低分别代表
     *                  可执行rename()函数权限 |可对文件追加内容权限 |可写入文件权限|可读取文件权限。
     */
    public static function pathModeInfo(string $file_path): int
    {
        /* 如果不存在，则不可读、不可写、不可改 */
        if (!file_exists($file_path)) {
            return false;
        }

        $mark = 0;

        if (0 === stripos(PHP_OS, 'WIN')) {
            /* 测试文件 */
            $test_file = $file_path . '/cf_test.txt';

            /* 如果是目录 */
            if (is_dir($file_path)) {
                /* 检查目录是否可读 */
                $dir = @opendir($file_path);

                //如果目录打开失败，直接返回目录不可修改、不可写、不可读
                if ($dir === false) {
                    return $mark;
                }

                //目录可读 001，目录不可读 000
                if (@readdir($dir) !== false) {
                    $mark ^= 1;
                }

                @closedir($dir);

                /* 检查目录是否可写 */
                $fp = @fopen($test_file, 'wb');

                //如果目录中的文件创建失败，返回不可写。
                if ($fp === false) {
                    return $mark;
                }

                //目录可写可读 011，目录可写不可读 010
                if (@fwrite($fp, 'directory access testing.') !== false) {
                    $mark ^= 2;
                }

                @fclose($fp);
                @unlink($test_file);

                /* 检查目录是否可修改 */
                $fp = @fopen($test_file, 'ab+');
                if ($fp === false) {
                    return $mark;
                }

                if (@fwrite($fp, "modify test.\r\n") !== false) {
                    $mark ^= 4;
                }

                @fclose($fp);

                /* 检查目录下是否有执行rename()函数的权限 */
                if (@rename($test_file, $test_file) !== false) {
                    $mark ^= 8;
                }

                @unlink($test_file);

                /* 如果是文件 */
            } elseif (is_file($file_path)) {
                /* 以读方式打开 */
                $fp = @fopen($file_path, 'rb');
                if ($fp) {
                    $mark ^= 1; //可读 001
                }

                @fclose($fp);

                /* 试着修改文件 */
                $fp = @fopen($file_path, 'ab+');
                if ($fp && @fwrite($fp, '') !== false) {
                    $mark ^= 6; //可修改可写可读 111，不可修改可写可读011...
                }

                @fclose($fp);

                /* 检查目录下是否有执行rename()函数的权限 */
                if (@rename($test_file, $test_file) !== false) {
                    $mark ^= 8;
                }
            }
        } else {
            if (@is_readable($file_path)) {
                $mark ^= 1;
            }

            if (@is_writable($file_path)) {
                $mark ^= 14;
            }
        }

        return $mark;
    }
}
