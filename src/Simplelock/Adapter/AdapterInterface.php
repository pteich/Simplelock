<?php

namespace Simplelock\Adapter;

interface AdapterInterface {

    /**
     * @param string $key
     * @param integer $ttl
     */
    public function lock($key,$ttl);

    /**
     * @param string $key
     */
    public function unlock($key);

    /**
     * @param string $key
     * @return bool
     */
    public function locked($key);

}
