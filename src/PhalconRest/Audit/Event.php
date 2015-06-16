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

    public function __construct($status = null, $resolve = null)
    {
        $this->status = $status;
        $this->resolve = $resolve;
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

    public function on($status, $callback)
    {
        if ($status === $this->status && $callback) {
            call_user_func_array($callback, [$this->resolve]);
        }

        return $this;
    }

    protected function resolve($status, $data)
    {
        return $this
            ->setStatus($status)
            ->setResolve($data);
    }

    public function accept($resolve = null)
    {
        return $this->resolve(self::STATUS_ACCEPT, $resolve);
    }

    public function reject($resolve = null)
    {
        return $this->resolve(self::STATUS_REJECT, $resolve);
    }

    public function fix($resolve = null)
    {
        return $this->resolve(self::STATUS_FIX, $resolve);
    }

    public function ignore($resolve = null)
    {
        return $this->resolve(self::STATUS_IGNORE, $resolve);
    }

    public function then($callback = null)
    {
        if ($callback) {
            call_user_func_array($callback, [$this->status, $this->resolve]);
        }

        return $this;
    }
}
