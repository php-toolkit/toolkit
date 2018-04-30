<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/17
 * Time: 上午10:11
 */

namespace Toolkit\Util ;

/**
 * Class PhpDotEnv - local env read
 * @package Inhere\Library\Utils
 *
 * in local config file `.env` (must is 'ini' format):
 * ```ini
 * ENV=dev
 * DEBUG=true
 * ... ...
 * ```
 *
 * IN CODE:
 *
 * ```php
 * PhpDotEnv::load(__DIE__);
 * env('DEBUG', false);
 * env('ENV', 'pdt');
 * ```
 */
final class PhpDotEnv
{
    const FULL_KEY = 'PHP_DOTENV_VARS';

    /**
     * @param string $fileDir
     * @param string $fileName
     * @return static
     */
    public static function load(string $fileDir, string $fileName = '.env')
    {
        return new self($fileDir, $fileName);
    }

    /**
     * constructor.
     * @param string $fileDir
     * @param string $fileName
     */
    public function __construct(string $fileDir, string $fileName = '.env')
    {
        $file = $fileDir . DIRECTORY_SEPARATOR . ($fileName ?: '.env');

        $this->add($file);
    }

    /**
     * @param string $file
     */
    public function add(string $file)
    {
        if (is_file($file) && is_readable($file)) {
            $this->settingEnv(parse_ini_file($file));
        }
    }

    /**
     * setting env data
     * @param array $data
     */
    private function settingEnv(array $data)
    {
        $loadedVars = array_flip(explode(',', getenv(self::FULL_KEY)));
        unset($loadedVars['']);

        foreach ($data as $name => $value) {
            if (\is_int($name) || !\is_string($value)) {
                continue;
            }

            $name = strtoupper($name);
            $notHttpName = 0 !== strpos($name, 'HTTP_');

            // don't check existence with getenv() because of thread safety issues
            if ((isset($_ENV[$name]) || (isset($_SERVER[$name]) && $notHttpName)) && !isset($loadedVars[$name])) {
                continue;
            }

            // is a constant var
            if ($value && \defined($value)) {
                $value = \constant($value);
            }

            // eg: "FOO=BAR"
            putenv("$name=$value");
            $_ENV[$name] = $value;

            if ($notHttpName) {
                $_SERVER[$name] = $value;
            }

            $loadedVars[$name] = true;
        }

        if ($loadedVars) {
            $loadedVars = implode(',', array_keys($loadedVars));
            putenv(self::FULL_KEY . "=$loadedVars");
            $_ENV[self::FULL_KEY] = $loadedVars;
            $_SERVER[self::FULL_KEY] = $loadedVars;
        }
    }
}
