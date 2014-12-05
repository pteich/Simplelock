<?php

namespace Simplelock;

use Simplelock\Adapter\AdapterInterface;

class Simplelock {

    protected $adapter = null;

    protected $autounlock = true;

    protected $keys = array();

    /**
     * @param AdapterInterface $adapter
     * @param bool $autounlock
     */
    public function __construct(AdapterInterface $adapter,$autounlock=true)
    {
        $this->adapter = $adapter;
        $this->autounlock = $autounlock;
    }

    /**
     * @param $key
     * @param int $ttl
     */
    public function lock($key,$ttl=3600)
    {
        $this->keys[$key] = true;
        $this->adapter->lock($key,$ttl);
    }

    /**
     * @param $key
     */
    public function unlock($key)
    {
        $this->keys[$key] = false;
        unset($this->keys[$key]);
        $this->adapter->unlock($key);
    }

    /**
     * @param $key
     * @return bool
     */
    public function locked($key)
    {
        return $this->adapter->locked($key);
    }

    public function __destruct()
    {
        if ($this->autounlock) {
            foreach($this->keys as $key=>$value) {
                if ($value) {
                    $this->adapter->unlock($this->key);
                }
            }
        }
    }

}
