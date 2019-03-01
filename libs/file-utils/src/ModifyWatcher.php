<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/12/21 0021
 * Time: 20:56
 */

namespace Toolkit\File;

use Toolkit\Sys\Sys;

/**
 * Class FilesWatcher - Check Dir's files modified by md5_file()
 * @package Inhere\Server\Components
 */
final class ModifyWatcher
{
    /** @var string */
    private $idFile;

    /** @var string[] */
    private $watchDirs = [];

    /** @var string */
    private $dirMd5;

    /** @var string */
    private $md5String;

    /** @var int */
    private $fileCounter = 0;

    /**
     * @var array 包含的文件名
     */
    private $names = ['.php'];

    /**
     * @var array 排除的文件名
     */
    private $notNames = [
        '.gitignore',
        'LICENSE[.txt]', // 'LICENSE' 'LICENSE.txt'
    ];

    /**
     * @var array 排除的目录名
     */
    private $excludes = [];

    /** @var bool */
    private $ignoreDotDirs = true;

    /** @var bool */
    private $ignoreDotFiles = true;

    /**
     * ModifyWatcher constructor.
     * @param string|null $idFile
     */
    public function __construct(string $idFile = null)
    {
        if ($idFile) {
            $this->idFile = $idFile;
        }
    }

    /**
     * @param string $idFile
     * @return $this
     */
    public function setIdFile(string $idFile): self
    {
        $this->idFile = $idFile;

        return $this;
    }

    /**
     * @param string|array $notNames
     * @return ModifyWatcher
     */
    public function name($notNames): self
    {
        $this->notNames = array_merge($this->notNames, (array)$notNames);

        return $this;
    }

    /**
     * @param string|array $notNames
     * @return ModifyWatcher
     */
    public function notName($notNames): self
    {
        $this->notNames = array_merge($this->notNames, (array)$notNames);

        return $this;
    }

    /**
     * @param string|array $excludeDirs
     * @return ModifyWatcher
     */
    public function exclude($excludeDirs): self
    {
        $this->excludes = array_merge($this->excludes, (array)$excludeDirs);

        return $this;
    }

    /**
     * @param bool $ignoreDotDirs
     * @return ModifyWatcher
     */
    public function ignoreDotDirs($ignoreDotDirs): ModifyWatcher
    {
        $this->ignoreDotDirs = (bool)$ignoreDotDirs;

        return $this;
    }

    /**
     * @param bool $ignoreDotFiles
     * @return ModifyWatcher
     */
    public function ignoreDotFiles($ignoreDotFiles): ModifyWatcher
    {
        $this->ignoreDotFiles = (bool)$ignoreDotFiles;
        return $this;
    }

    /**
     * @param string|array $dirs
     * @return $this
     */
    public function watch($dirs): self
    {
        $this->watchDirs = array_merge($this->watchDirs, (array)$dirs);

        return $this;
    }

    /**
     * alias of the watch()
     * @param string|array $dirs
     * @return $this
     */
    public function watchDir($dirs): self
    {
        $this->watchDirs = array_merge($this->watchDirs, (array)$dirs);

        return $this;
    }

    /**
     * @return bool
     */
    public function isModified(): bool
    {
        return $this->isChanged();
    }

    /**
     * @return bool
     */
    public function isChanged(): bool
    {
        if (!$this->idFile) {
            $this->idFile = Sys::getTempDir() . '/' . md5(\json_encode($this->watchDirs)) . '.id';
        }

        // get old hash id
        if (!($old = $this->dirMd5) && (!$old = $this->getMd5ByIdFile())) {
            $this->calcMd5Hash();

            return false;
        }

        $this->calcMd5Hash();

        return $this->dirMd5 !== $old;
    }

    /**
     * @return bool|string
     */
    public function getMd5ByIdFile()
    {
        if (!$file = $this->idFile) {
            return false;
        }

        if (!is_file($file)) {
            return false;
        }

        return trim(file_get_contents($file));
    }

    /**
     * @return string
     */
    public function calcMd5Hash(): string
    {
        if (!$this->watchDirs) {
            throw new \RuntimeException('Please setting want to watched directories before run.');
        }

        foreach ($this->watchDirs as $dir) {
            $this->collectDirMd5($dir);
        }

        $this->dirMd5 = md5($this->md5String);
        $this->md5String = null;

        if ($this->idFile) {
            file_put_contents($this->idFile, $this->dirMd5);
        }

        return $this->dirMd5;
    }

    /**
     * @param string $watchDir
     */
    private function collectDirMd5(string $watchDir): void
    {
        $files = scandir($watchDir, 0);

        foreach ($files as $f) {
            if ($f === '.' || $f === '..') {
                continue;
            }

            $path = $watchDir . '/' . $f;

            // 递归目录
            if (is_dir($path)) {
                if ($this->ignoreDotDirs && $f[0] === '.') {
                    continue;
                }

                if (\in_array($f, $this->excludes, true)) {
                    continue;
                }

                $this->collectDirMd5($path);

                continue;
            }

            // 检测文件
            foreach ($this->notNames as $name) {
                if (preg_match('#' . $name . '#', $name)) {
                    continue;
                }
            }

            if ($this->names) {
                foreach ($this->names as $name) {
                    if (preg_match('#' . $name . '#', $name)) {
                        $this->md5String .= md5_file($path);
                        $this->fileCounter++;
                    }
                }
            } else {
                $this->md5String .= md5_file($path);
                $this->fileCounter++;
            }
        }
    }

    /**
     * @return string
     */
    public function getIdFile(): string
    {
        return $this->idFile;
    }

    /**
     * @return string[]
     */
    public function getWatchDir(): array
    {
        return $this->watchDirs;
    }

    /**
     * @return string
     */
    public function getDirMd5(): string
    {
        return $this->dirMd5;
    }

    /**
     * @return int
     */
    public function getFileCounter(): int
    {
        return $this->fileCounter;
    }
}
