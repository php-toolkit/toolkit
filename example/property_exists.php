<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/5/6 0006
 * Time: 12:45
 */

class Some {
    public $prop0;
    private $prop1;
    protected $prop2;

    /**
     * @return mixed
     */
    public function getProp1()
    {
        return $this->prop1;
    }
}


echo "use class:\n";

echo 'public: ' . (property_exists(Some::class, 'prop0') ? 'Y':'N') . PHP_EOL;
echo 'private: ' . (property_exists(Some::class, 'prop1') ? 'Y':'N') . PHP_EOL;
echo 'protected: ' . (property_exists(Some::class, 'prop2') ? 'Y':'N') . PHP_EOL;

echo "use object:\n";

$object = new Some();

echo 'public: ' . (property_exists($object, 'prop0') ? 'Y':'N') . PHP_EOL;
echo 'private: ' . (property_exists($object, 'prop1') ? 'Y':'N') . PHP_EOL;
echo 'protected: ' . (property_exists($object, 'prop2') ? 'Y':'N') . PHP_EOL;
