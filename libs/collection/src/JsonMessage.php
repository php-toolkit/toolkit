<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 16/9/2
 * Time: 上午11:49
 */

namespace Toolkit\Collection;

/**
 * Class JsonMessage
 * @package SlimExt\Helpers
 * $mg = JsonMessage::create(['msg' => 'success', 'code' => 23]);
 * $mg->data = [ 'key' => 'test'];
 * echo json_encode($mg);
 * response to client:
 * {
 *  "code":23,
 *  "msg":"success",
 *  "data": {
 *      "key":"value"
 *  }
 * }
 */
class JsonMessage
{
    /**
     * @var int
     */
    public $code;

    /**
     * @var string
     */
    public $msg;

    /**
     * @var int|float
     */
    public $time;

    /**
     * @var array|string
     */
    public $data;

    public static function make($data = null, $msg = 'success', $code = 0)
    {
        return new static($data, $msg, $code);
    }

    /**
     * JsonMessage constructor.
     * @param null   $data
     * @param string $msg
     * @param int    $code
     */
    public function __construct($data = null, $msg = 'success', $code = 0)
    {
        $this->data = $data;
        $this->msg = $msg;
        $this->code = $code;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return (int)$this->code === 0;
    }

    /**
     * @return bool
     */
    public function isFailure(): bool
    {
        return (int)$this->code !== 0;
    }

    /**
     * @param $code
     * @return $this
     */
    public function code($code): self
    {
        $this->code = (int)$code;

        return $this;
    }

    /**
     * @param $msg
     * @return $this
     */
    public function msg($msg): self
    {
        $this->msg = $msg;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function add($key, $value)
    {
        if (null === $this->data) {
            $this->data = [];
        }

        $this->data[$key] = $value;
    }

    /**
     * @param array|string $data
     * @return $this
     */
    public function data($data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        // add a new alert message
        return [
            'code' => (int)$this->code,
            'msg'  => $this->msg,
            'data' => (array)$this->data
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->all();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->all());
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     * @param        $value
     */
    public function __set(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        throw new \InvalidArgumentException(sprintf('the property is not exists: %s', $name));
    }
}
