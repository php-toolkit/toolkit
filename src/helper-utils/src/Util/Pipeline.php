<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/16
 * Time: 下午11:59
 * @link https://github.com/ztsu/pipe/blob/master/src/Pipeline.php
 */

namespace Toolkit\Util;

/**
 * Class Pipeline
 * @package Toolkit\Util
 */
class Pipeline implements PipelineInterface
{
    /** @var \SplObjectStorage */
    private $stages;

    public function __construct()
    {
        $this->stages = new \SplObjectStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function add(callable $stage)
    {
        if ($stage instanceof $this) {
            $stage->add(function ($payload) {
                return $this->invokeStage($payload);
            });
        }

        $this->stages->attach($stage);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run($payload)
    {
        $this->stages->rewind();

        return $this->invokeStage($payload);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($payload)
    {
        return $this->run($payload);
    }

    private function invokeStage($payload)
    {
        $stage = $this->stages->current();
        $this->stages->next();

        if (\is_callable($stage)) {
            return $stage($payload, function ($payload) {
                return $this->invokeStage($payload);
            });
        }

        return $payload;
    }
}
