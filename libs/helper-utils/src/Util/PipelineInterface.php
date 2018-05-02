<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/5/2
 * Time: 上午9:46
 */

namespace Toolkit\Util;

/**
 * Interface PipelineInterface
 * @package Toolkit\Util
 */
interface PipelineInterface
{
    /**
     * Adds stage to the pipeline
     *
     * @param callable $stage
     * @return $this
     */
    public function add(callable $stage);

    /**
     * Runs pipeline with initial value
     *
     * @param mixed $payload
     * @return mixed
     */
    public function run($payload);

    /**
     * Makes pipeline callable. Does same as {@see run()}
     *
     * @param mixed $payload
     * @return mixed
     */
    public function __invoke($payload);
}
