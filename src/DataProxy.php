<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/18
 * Time: 下午9:39
 */

namespace MyLib\Utils;

/**
 * Class DataProxy
 * @package MyLib\Utils
 */
class DataProxy
{
    /**
     * proxy list
     * @var array
     * [
     *     // 'name' => 'handler',
     *     'getArticleList' => [ArticleDao::class, 'getArticleList'],
     * ]
     */
    protected $proxies = [];

    public function __construct(array $proxies = [])
    {
        $this->proxies = $proxies;
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, array $args)
    {
        return $this->call($method, $args);
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     * @throws \RuntimeException
     */
    public function call(string $name, array $args)
    {
        if ($this->hasName($name)) {
            $handler = $this->proxies[$name];
            return $handler(...$args);
        }

        throw new \RuntimeException("Called method $name is not exists.");
    }

    /**
     * @param string $name
     * @param callable $callback
     */
    public function add(string $name, callable $callback)
    {
        if (!isset($this->proxies[$name])) {
            $this->proxies[$name] = $callback;
        }
    }

    /**
     * @param string $name
     * @param callable $callback
     */
    public function addProxy(string $name, callable $callback)
    {
        if (!isset($this->proxies[$name])) {
            $this->proxies[$name] = $callback;
        }
    }

    /**
     * @param string $name
     * @param callable $callback
     */
    public function setProxy(string $name, callable $callback)
    {
        if (!isset($this->proxies[$name])) {
            $this->proxies[$name] = $callback;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasName(string $name)
    {
        return isset($this->proxies[$name]);
    }

    /**
     * @param array $proxies
     */
    public function addProxies(array $proxies)
    {
        foreach ($proxies as $name => $callback) {
            $this->addProxy($name, $callback);
        }
    }

    /**
     * @return array
     */
    public function getProxies(): array
    {
        return $this->proxies;
    }

    /**
     * @param array $proxies
     */
    public function setProxies(array $proxies)
    {
        $this->proxies = $proxies;
    }
}

