<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/1/20 0020
 * Time: 21:57
 */

namespace Toolkit\File;

/**
 * Class FileFinder
 *
 * ```php
 * $finder = FileFinder::create()
 *      ->files()
 *      ->name('*.php')
 *      ->notName('some.php')
 *      ->in('/path/to/project')
 * ;
 *
 * foreach($finder as $file) {
 *      // something ......
 * }
 * ```
 * @package Toolkit\File
 * @ref \Symfony\Component\Finder\Finder
 */
final class FileFinder implements \IteratorAggregate, \Countable
{
    const ONLY_FILE = 1;
    const ONLY_DIR = 2;
    const IGNORE_VCS_FILES = 1;
    const IGNORE_DOT_FILES = 2;

    /** @var array */
    private static $vcsPatterns = ['.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg'];

    /** @var int */
    private $mode = 0;

    /** @var int */
    private $ignore;

    /** @var bool */
    private $ignoreVcsAdded = false;

    /** @var array */
    private $dirs = [];

    /** @var array */
    private $names = [];

    /** @var array */
    private $notNames = [];

    /** @var array */
    private $paths = [];

    /** @var array */
    private $notPaths = [];

    /** @var array */
    private $excludes = [];

    /** @var array */
    private $filters = [];

    /** @var array */
    private $iterators = [];

    /** @var bool */
    private $followLinks = false;

    /**
     * @return FileFinder
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @param array $config
     * @return FileFinder
     */
    public static function fromArray(array $config): self
    {
        $finder = new self();
        $allowed = [
            'names' => 'addNames',
            'notNames' => 'addNotNames',
            'paths' => 'addNotPaths',
            'notPaths' => 'addNotPaths',
            'exclude' => 'exclude',
            'excludes' => 'exclude',
        ];

        foreach ($config as $prop => $values) {
            if (isset($allowed[$prop])) {
                $method = $allowed[$prop];
                $finder->$method($values);
            }
        }

        return $finder;
    }

    /**
     * FileFinder constructor.
     */
    public function __construct()
    {
        $this->ignore = self::IGNORE_VCS_FILES | self::IGNORE_DOT_FILES;
    }

    /**
     * @return $this
     */
    public function directories(): self
    {
        $this->mode = self::ONLY_DIR;

        return $this;
    }

    /**
     * @return $this
     */
    public function dirs(): self
    {
        $this->mode = self::ONLY_DIR;

        return $this;
    }

    /**
     * @return FileFinder
     */
    public function files(): self
    {
        $this->mode = self::ONLY_FILE;

        return $this;
    }

    /**
     * $finder->name('*.php')
     * $finder->name('test.php')
     *
     * @param string $pattern
     * @return FileFinder
     */
    public function name(string $pattern): self
    {
        $this->names[] = $pattern;

        return $this;
    }

    /**
     * @param string|array $patterns
     * @return FileFinder
     */
    public function addNames($patterns): self
    {
        $this->names = \array_merge($this->names, (array)$patterns);

        return $this;
    }

    /**
     * @param string $pattern
     * @return FileFinder
     */
    public function notName(string $pattern): self
    {
        $this->notNames[] = $pattern;

        return $this;
    }

    /**
     * @param string|array $patterns
     * @return FileFinder
     */
    public function addNotNames($patterns): self
    {
        $this->notNames = array_merge($this->notNames, $patterns);

        return $this;
    }

    /**
     * $finder->path('some/special/dir')
     *
     * @param string $pattern
     * @return FileFinder
     */
    public function path(string $pattern): self
    {
        $this->paths[] = $pattern;

        return $this;
    }

    /**
     * @param string|array $patterns
     * @return FileFinder
     */
    public function addPaths($patterns): self
    {
        $this->paths = array_merge($this->paths, $patterns);

        return $this;
    }

    /**
     * @param string $pattern
     * @return FileFinder
     */
    public function notPath(string $pattern): self
    {
        $this->notPaths[] = $pattern;

        return $this;
    }

    /**
     * @param string|array $patterns
     * @return FileFinder
     */
    public function addNotPaths($patterns): self
    {
        $this->notPaths = array_merge($this->notPaths, (array)$patterns);

        return $this;
    }

    /**
     * @param $dirs
     * @return FileFinder
     */
    public function exclude($dirs): self
    {
        $this->excludes = array_merge($this->excludes, (array)$dirs);

        return $this;
    }

    /**
     * @param bool $ignoreVCS
     * @return self
     */
    public function ignoreVCS($ignoreVCS): self
    {
        if ($ignoreVCS) {
            $this->ignore |= static::IGNORE_VCS_FILES;
        } else {
            $this->ignore &= ~static::IGNORE_VCS_FILES;
        }

        return $this;
    }

    /**
     * @param bool $ignoreDotFiles
     * @return FileFinder
     */
    public function ignoreDotFiles($ignoreDotFiles): self
    {
        if ($ignoreDotFiles) {
            $this->ignore |= static::IGNORE_DOT_FILES;
        } else {
            $this->ignore &= ~static::IGNORE_DOT_FILES;
        }
        return $this;
    }

    /**
     * @param bool $followLinks
     * @return FileFinder
     */
    public function followLinks($followLinks): self
    {
        $this->followLinks = (bool)$followLinks;

        return $this;
    }

    /**
     * @param \Closure $closure
     * @return FileFinder
     */
    public function filter(\Closure $closure): self
    {
        $this->filters[] = $closure;

        return $this;
    }

    /**
     * @param string|array $dirs
     * @return $this
     */
    public function in($dirs): self
    {
        $this->dirs = array_merge($this->dirs, (array)$dirs);

        return $this;
    }

    /**
     * alias of the `in()`
     * @param string|array $dirs
     * @return FileFinder
     */
    public function inDir($dirs): self
    {
        $this->dirs = array_merge($this->dirs, (array)$dirs);

        return $this;
    }

    /**
     * @param mixed $iterator
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function append($iterator): self
    {
        if ($iterator instanceof \IteratorAggregate) {
            $this->iterators[] = $iterator->getIterator();
        } elseif ($iterator instanceof \Iterator) {
            $this->iterators[] = $iterator;
        } elseif ($iterator instanceof \Traversable || \is_array($iterator)) {
            $it = new \ArrayIterator();
            foreach ($iterator as $file) {
                $it->append($file instanceof \SplFileInfo ? $file : new \SplFileInfo($file));
            }
            $this->iterators[] = $it;
        } else {
            throw new \InvalidArgumentException('The argument type is error');
        }

        return $this;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \iterator_count($this->getIterator());
    }

    /**
     * @return bool
     */
    public function isFollowLinks(): bool
    {
        return $this->followLinks;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Iterator|\SplFileInfo[] An iterator
     * @throws \LogicException
     */
    public function getIterator(): \Traversable
    {
        if (0 === \count($this->dirs) && 0 === \count($this->iterators)) {
            throw new \LogicException('You must call one of in() or append() methods before iterating over a Finder.');
        }

        if (!$this->ignoreVcsAdded && self::IGNORE_VCS_FILES === (self::IGNORE_VCS_FILES & $this->ignore)) {
            $this->excludes = array_merge($this->excludes, self::$vcsPatterns);
            $this->ignoreVcsAdded = true;
        }

        if (self::IGNORE_DOT_FILES === (self::IGNORE_DOT_FILES & $this->ignore)) {
            $this->notNames[] = '.*';
        }

        if (1 === \count($this->dirs) && 0 === \count($this->iterators)) {
            return $this->findInDirectory($this->dirs[0]);
        }

        $iterator = new \AppendIterator();
        foreach ($this->dirs as $dir) {
            $iterator->append($this->findInDirectory($dir));
        }

        foreach ($this->iterators as $it) {
            $iterator->append($it);
        }

        return $iterator;
    }

    /**
     * @param string $dir
     * @return \Iterator
     */
    private function findInDirectory(string $dir): \Iterator
    {
        $flags = \RecursiveDirectoryIterator::SKIP_DOTS;

        if ($this->followLinks) {
            $flags |= \RecursiveDirectoryIterator::FOLLOW_SYMLINKS;
        }

        $iterator = new class ($dir, $flags) extends \RecursiveDirectoryIterator
        {
            private $rootPath;
            private $subPath;
            private $rewindable;
            private $directorySeparator = '/';
            private $ignoreUnreadableDirs;

            public function __construct(string $path, int $flags, bool $ignoreUnreadableDirs = false)
            {
                if ($flags & (self::CURRENT_AS_PATHNAME | self::CURRENT_AS_SELF)) {
                    throw new \RuntimeException('This iterator only support returning current as fileInfo.');
                }

                $this->rootPath = $path;
                $this->ignoreUnreadableDirs = $ignoreUnreadableDirs;
                parent::__construct($path, $flags);

                if ('/' !== DIRECTORY_SEPARATOR && !($flags & self::UNIX_PATHS)) {
                    $this->directorySeparator = DIRECTORY_SEPARATOR;
                }
            }

            public function current()
            {
                if (null === $subPathname = $this->subPath) {
                    $subPathname = $this->subPath = (string)$this->getSubPath();
                }

                if ('' !== $subPathname) {
                    $subPathname .= $this->directorySeparator;
                }

                $subPathname .= $this->getFilename();

                // $fileInfo = new \SplFileInfo($this->getPathname());
                $fileInfo = new \SplFileInfo($this->rootPath . $this->directorySeparator . $subPathname);
                $fileInfo->relativePath = $this->subPath;
                $fileInfo->relativePathname = $subPathname;

                return $fileInfo;
            }

            public function getChildren()
            {
                try {
                    $children = parent::getChildren();

                    if ($children instanceof self) {
                        $children->rootPath = $this->rootPath;
                        $children->rewindable = &$this->rewindable;
                        $children->ignoreUnreadableDirs = $this->ignoreUnreadableDirs;
                    }

                    return $children;
                } catch (\UnexpectedValueException $e) {
                    if ($this->ignoreUnreadableDirs) {
                        return new \RecursiveArrayIterator([]);
                    }

                    throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
                }
            }

            public function rewind()
            {
                if (false === $this->isRewindable()) {
                    return;
                }

                parent::rewind();
            }

            public function isRewindable()
            {
                if (null !== $this->rewindable) {
                    return $this->rewindable;
                }

                if (false !== $stream = @opendir($this->getPath())) {
                    $infoS = stream_get_meta_data($stream);
                    closedir($stream);

                    if ($infoS['seekable']) {
                        return $this->rewindable = true;
                    }
                }

                return $this->rewindable = false;
            }
        };

        // exclude directories
        if ($this->excludes) {
            $iterator = new class ($iterator, $this->excludes) extends \FilterIterator implements \RecursiveIterator
            {
                private $excludes;
                private $iterator;

                public function __construct(\RecursiveIterator $iterator, array $excludes)
                {
                    $this->excludes = array_flip($excludes);
                    $this->iterator = $iterator;

                    parent::__construct($iterator);
                }

                public function accept(): bool
                {
                    $name = $this->current()->getFilename();
                    return !($this->current()->isDir() && isset($this->excludes[$name]));
                }

                public function hasChildren(): bool
                {
                    return $this->iterator->hasChildren();
                }

                public function getChildren()
                {
                    $children = new self($this->iterator->getChildren(), []);
                    $children->excludes = $this->excludes;

                    return $children;
                }
            };
        }

        // create recursive iterator
        $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);

        // mode: find files or dirs
        if ($this->mode) {
            $iterator = new class ($iterator, $this->mode) extends \FilterIterator
            {
                private $mode;

                public function __construct(\Iterator $iterator, int $mode)
                {
                    $this->mode = $mode;
                    parent::__construct($iterator);
                }

                public function accept(): bool
                {
                    $info = $this->current();
                    if (FileFinder::ONLY_DIR === $this->mode && $info->isFile()) {
                        return false;
                    }

                    if (FileFinder::ONLY_FILE === $this->mode && $info->isDir()) {
                        return false;
                    }

                    return true;
                }
            };
        }

        if ($this->names || $this->notNames) {
            $iterator = new class ($iterator, $this->names, $this->notNames) extends \FilterIterator
            {
                private $names;
                private $notNames;

                public function __construct(\Iterator $iterator, array $names, array $notNames)
                {
                    parent::__construct($iterator);
                    $this->names = $names;
                    $this->notNames = $notNames;
                }

                public function accept(): bool
                {
                    $pathname = $this->current()->getFilename();

                    foreach ($this->notNames as $not) {
                        if (fnmatch($not, $pathname)) {
                            return false;
                        }
                    }

                    if ($this->names) {
                        foreach ($this->names as $need) {
                            if (fnmatch($need, $pathname)) {
                                return true;
                            }
                        }

                        return false;
                    }

                    return true;
                }
            };
        }

        if ($this->filters) {
            $iterator = new class ($iterator, $this->filters) extends \FilterIterator
            {
                private $filters;

                public function __construct(\Iterator $iterator, array $filters)
                {
                    parent::__construct($iterator);
                    $this->filters = $filters;
                }

                public function accept(): bool
                {
                    $fileInfo = $this->current();
                    foreach ($this->filters as $filter) {
                        if (false === $filter($fileInfo)) {
                            return false;
                        }
                    }

                    return true;
                }
            };
        }

        if ($this->paths || $this->notPaths) {
            $iterator = new class ($iterator, $this->paths, $this->notPaths) extends \FilterIterator
            {
                private $paths;
                private $notPaths;

                public function __construct(\Iterator $iterator, array $paths, array $notPaths)
                {
                    parent::__construct($iterator);
                    $this->paths = $paths;
                    $this->notPaths = $notPaths;
                }

                public function accept(): bool
                {
                    $pathname = $this->current()->relativePathname;

                    if ('\\' === DIRECTORY_SEPARATOR) {
                        $pathname = str_replace('\\', '/', $pathname);
                    }

                    foreach ($this->notPaths as $not) {
                        if (fnmatch($not, $pathname)) {
                            return false;
                        }
                    }

                    if ($this->paths) {
                        foreach ($this->paths as $need) {
                            if (fnmatch($need, $pathname)) {
                                return true;
                            }
                        }

                        return false;
                    }

                    return true;
                }
            };
        }

        return $iterator;
    }
}
