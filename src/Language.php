<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2016/2/24
 * Time: 15:04
 */

namespace Toolkit\Collection;

use Toolkit\File\FileSystem;
use Toolkit\StrUtil\Str;

/**
 * Class Language
 * @package Toolkit\Utils
 */
class Language implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * current use language
     * @var string
     */
    private $lang = 'en';

    /**
     * fallback language
     * @var string
     */
    private $fallbackLang = 'en';

    /**
     * current use language
     * @var string[]
     */
    private $langs = [];

    /**
     * @var Collection
     */
    private $data;

    /**
     * @var Collection
     */
    private $fallbackData = [];

    /**
     * The base path language directory.
     * @var string
     */
    private $basePath;

    /**
     * default file name.
     * @var string
     */
    private $defaultFile = 'default';

    /**
     * the language file type. more see Collection::FORMAT_*
     * @var string
     */
    private $format = 'php';

    /**
     * file separator char. when want to get translation form other file.
     * e.g:
     *  $language->tl('app.createPage');
     * @var string
     */
    private $separator = '.';

    /**
     * e.g.
     * [
     *    'user'  => '{basePath}/zh-CN/user.yml'
     *    'admin' => '{basePath}/zh-CN/admin.yml'
     * ]
     * @var array
     */
    private $langFiles = [];

    /**
     * loaded language file list.
     * @var array
     */
    private $loadedFiles = [];

    /**
     * whether ignore not exists lang file when addLangFile()
     * @var bool
     */
    private $ignoreError = false;

    const DEFAULT_FILE_KEY = '__default';

    /**
     * {@inheritDoc}
     * @throws \RangeException
     */
    protected function init()
    {
        $this->data = new Collection();

        if ($this->defaultFile) {
            $file = $this->buildLangFilePath($this->defaultFile . '.' . $this->format);

            if (is_file($file)) {
                $this->data->load($file, $this->format);
            }
        }
    }

    /**
     * {@inheritdoc}
     * @see self::translate()
     * @throws \InvalidArgumentException
     * @throws \Inhere\Exceptions\NotFoundException
     */
    public function t($key, array $args = [], $lang = '')
    {
        return $this->translate($key, $args, $lang);
    }

    /**
     * {@inheritdoc}
     * @see self::translate()
     * @throws \InvalidArgumentException
     * @throws \Inhere\Exceptions\NotFoundException
     */
    public function tl($key, array $args = [], $lang = null)
    {
        return $this->translate($key, $args, $lang);
    }

    /**
     * {@inheritdoc}
     * @see self::translate()
     * @throws \InvalidArgumentException
     * @throws \Inhere\Exceptions\NotFoundException
     */
    public function trans($key, array $args = [], $lang = null)
    {
        return $this->translate($key, $args, $lang);
    }

    /**
     * how to use language translate ? please see '/doc/language.md'
     * @param string|bool $key
     * @param array $args
     * @param string $lang
     * @return string|array
     * @throws \InvalidArgumentException
     */
    public function translate($key, array $args = [], $lang = null)
    {
        if (!\is_string($key)) {
            throw new \InvalidArgumentException('The translate key must be a string.');
        }

        if (!$key = trim($key, ' ' . $this->separator)) {
            throw new \InvalidArgumentException('Cannot translate the empty key');
        }

        list($lang, $key) = $this->parseKey($key, $lang);

        // translate form current language. if not found, translate form fallback language.
        if (($value = $this->findTranslationText($key)) === null) {
            $value = $this->transByFallbackLang($key);

            // no translate text
            if ((string)$value === '') {
                if (!empty($args['__default'])) {
                    return $args['__default'];
                }

                return ucfirst(Str::toSnake(str_replace(['-', '_'], ' ', $key), ' '));
            }
        }

        return $args ? sprintf($value, ...$args) : $value;
    }

    /*********************************************************************************
     * handle current language translate
     *********************************************************************************/

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set(string $key, $value)
    {
        return $this->data->set($key, $value);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function has(string $key)
    {
        return $this->data->has($key);
    }

    /**
     * @param string $key
     * @return string|mixed
     */
    protected function findTranslationText(string $key)
    {
        if ($val = $this->data->get($key)) {
            return $val;
        }

        if (strpos($key, $this->separator)) {
            list($fileKey,) = explode($this->separator, $key);
        } else {
            $fileKey = $key;
        }

        // at first, load language data to collection.
        if ($file = $this->getLangFile($fileKey)) {
            $this->loadedFiles[] = $file;
            $this->data->set($fileKey, Collection::read($file, $this->format));

            return $this->data->get($key);
        }

        return null;
    }

    /*********************************************************************************
     * fallback language translate handle
     *********************************************************************************/

    /**
     * @param string $key
     * @return string|mixed
     * @throws \RangeException
     */
    protected function transByFallbackLang(string $key)
    {
        if ($this->lang === $this->fallbackLang || !$this->fallbackLang) {
            return null;
        }

        // init fallbackData
        if (!$this->fallbackData) {
            $this->fallbackData = new Collection();

            if ($this->defaultFile) {
                $file = $this->buildLangFilePath($this->defaultFile . '.' . $this->format, $this->fallbackLang);

                if (is_file($file)) {
                    $this->fallbackData->load($file, $this->format);
                }
            }
        }

        if ($val = $this->fallbackData->get($key)) {
            return $val;
        }

        if (strpos($key, $this->separator)) {
            list($fileKey,) = explode($this->separator, $key);
        } else {
            $fileKey = $key;
        }

        // the first times fetch, instantiate lang data
        if ($file = $this->getLangFile($fileKey)) {
            $file = str_replace("/{$this->lang}/", "/{$this->fallbackLang}/", $file);

            if (is_file($file)) {
                $this->loadedFiles[] = $file;
                $this->fallbackData->set($fileKey, Collection::read($file, $this->format));

                return $this->fallbackData->get($key);
            }
        }

        return null;
    }


    /*********************************************************************************
     * helper
     *********************************************************************************/

    /**
     * @param string $key
     * @param string $lang
     * @return array
     */
    private function parseKey(string $key, string $lang = null): array
    {
        if ($lang) {
            return [$lang, $key];
        }

        if (strpos($key, $this->separator)) {
            $info = explode($this->separator, $key, 2);

            if ($this->isLang($info[0])) {
                return $info;
            }
        }

        return [$this->lang, $key];
    }

    /**
     * @param string $filename
     * @param string $lang
     * @return string
     */
    protected function buildLangFilePath(string $filename, $lang = ''): string
    {
        $path = ($lang ?: $this->lang) . DIRECTORY_SEPARATOR . trim($filename);

        return $this->basePath . DIRECTORY_SEPARATOR . $path;
    }

    /*********************************************************************************
     * language file handle
     *********************************************************************************/

    /**
     * @param string $fileKey
     * @return string|null
     */
    public function getLangFile(string $fileKey)
    {
        return $this->langFiles[$fileKey] ?? null;
    }

    /**
     * @param string $fileKey
     * @return bool
     */
    public function hasLangFile(string $fileKey): bool
    {
        return isset($this->langFiles[$fileKey]);
    }

    /**
     * @param string $file
     * @param string $fileKey
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function addLangFile(string $file, string $fileKey = null): bool
    {
        if (!FileSystem::isAbsPath($file)) {
            $file = $this->buildLangFilePath($file);
        }

        if (!is_file($file)) {
            if ($this->ignoreError) {
                return false;
            }

            throw new \InvalidArgumentException("The language file don't exists. FILE: $file");
        }

        $fileKey = $fileKey ?: basename($file, '.' . $this->format);

        if (!preg_match('/^[a-z][\w-]+$/i', $fileKey)) {
            throw new \InvalidArgumentException("language file key [$fileKey] naming format error!!");
        }

        if ($this->hasLangFile($fileKey)) {
            if ($this->ignoreError) {
                return false;
            }

            throw new \InvalidArgumentException("language file key [$fileKey] have been exists, don't allow override!!");
        }

        $this->langFiles[$fileKey] = $file;

        return true;
    }

    /**
     * @param $fileKey
     * @return bool
     */
    public function hasLangFileData($fileKey): bool
    {
        return isset($this->data[$fileKey]);
    }

    /*********************************************************************************
     * getter/setter
     *********************************************************************************/

    /**
     * @param string $lang
     * @return bool
     */
    public function hasLang(string $lang): bool
    {
        return $this->isLang($lang);
    }

    /**
     * @param string $lang
     * @return bool
     */
    public function isLang(string $lang): bool
    {
        return $lang && \in_array($lang, $this->langs, true);
    }

    /**
     * Allow quick access default file translate by `$lang->key`,
     * is equals to `$lang->tl('key')`.
     * @param string $name
     * @return mixed|string
     * @throws \InvalidArgumentException
     */
    public function __get($name)
    {
        return $this->translate($name);
    }

    public function __isset($name)
    {
    }

    public function __set($name, $value)
    {
    }

    /**
     * Allow quick access default file translate by `$lang->key()`,
     * is equals to `$lang->tl('key')`.
     * @param string $name
     * @param array $args
     * @return mixed|string
     * @throws \InvalidArgumentException
     */
    public function __call($name, $args)
    {
        return $this->translate($name);
    }

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang(string $lang)
    {
        $this->lang = trim($lang);
    }

    /**
     * @return \string[]
     */
    public function getLangs(): array
    {
        return $this->langs;
    }

    /**
     * @param \string[] $langs
     */
    public function setLangs(array $langs)
    {
        $this->langs = $langs;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @param string|array $path
     * @throws \InvalidArgumentException
     */
    public function setBasePath(string $path)
    {
        if ($path && is_dir($path)) {
            $this->basePath = $path;
        } else {
            throw new \InvalidArgumentException("The language files path: $path is not exists.");
        }
    }

    /**
     * @return Collection
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getLangFiles(): array
    {
        return $this->langFiles;
    }

    /**
     * @param array $langFiles
     * @throws \InvalidArgumentException
     */
    public function setLangFiles(array $langFiles)
    {
        foreach ($langFiles as $fileKey => $file) {
            $this->addLangFile($file, is_numeric($fileKey) ? '' : $fileKey);
        }
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getDefaultFile($full = false): string
    {
        return $full ? $this->getLangFile(self::DEFAULT_FILE_KEY) : $this->defaultFile;
    }

    /**
     * @return string
     */
    public function getFallbackLang(): string
    {
        return $this->fallbackLang;
    }

    /**
     * @param string $fallbackLang
     */
    public function setFallbackLang(string $fallbackLang)
    {
        $this->fallbackLang = $fallbackLang;
    }

    /**
     * @return Collection
     */
    public function getFallbackData(): Collection
    {
        return $this->fallbackData;
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
    public function setFormat($format)
    {
        if (\in_array($format, Collection::getFormats(), true)) {
            $this->format = $format;
        }
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
    public function setSeparator($separator)
    {
        $this->separator = $separator;
    }

    /**
     * @return array
     */
    public function getLoadedFiles(): array
    {
        return $this->loadedFiles;
    }

    /**
     * @return bool
     */
    public function isIgnoreError(): bool
    {
        return $this->ignoreError;
    }

    /**
     * @param bool $ignoreError
     */
    public function setIgnoreError($ignoreError)
    {
        $this->ignoreError = (bool)$ignoreError;
    }

    /*********************************************************************************
     * interface implementing
     *********************************************************************************/

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return $this->data->getIterator();
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     * @throws \InvalidArgumentException
     */
    public function offsetGet($offset)
    {
        return $this->translate($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count(): int
    {
        return \count($this->data);
    }
}
