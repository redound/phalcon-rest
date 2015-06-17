<?php

namespace PhalconRest\Audit;

class Event
{
    const STATUS_ACCEPT = 'accept';
    const STATUS_REJECT = 'reject';
    const STATUS_FIX = 'fix';
    const STATUS_IGNORE = 'ignore';

    protected $status;
    protected $resolve;
    protected $then;

    public function __construct($status = null)
    {
        $this->status = $status;
        $this->resolve = [];
        $this->args = [];
        $this->then = false;
        $this->callbacks = [];
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function setResolve($resolve)
    {
        $this->resolve = $resolve;

        return $this;
    }

    public function on($status, $callback, $method = null)
    {
        if ($status !== $this->status && $callback) {
            return $this;
        }

        return $this->then($callback, $method);
    }

    protected function resolve($status, $args)
    {
        return $this
            ->setStatus($status)
            ->setResolve($args);
    }

    public function accept()
    {
        return $this->resolve(self::STATUS_ACCEPT, func_get_args());
    }

    public function reject()
    {
        return $this->resolve(self::STATUS_REJECT, func_get_args());
    }

    public function fix()
    {
        return $this->resolve(self::STATUS_FIX, func_get_args());
    }

    public function ignore()
    {
        return $this->resolve(self::STATUS_IGNORE, func_get_args());
    }

    public function then($callback = null, $method = null)
    {
        $resolve = $this->resolve;
        array_unshift($resolve, $this->status);

        if ($callback && $method) {
            call_user_method_array($method, $callback, $resolve);
        } else if ($callback) {
            call_user_func_array($callback, $resolve);
        }

        return $this;
    }
}
