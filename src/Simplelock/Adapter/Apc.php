<?php

namespace Simplelock\Adapter;

class Apc implements AdapterInterface {

    protected $hashedkey = null;
    protected $config = array();


    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param string $key
     * @param integer $ttl
     */
    public function lock($key, $ttl)
    {
        // store ttl and time as value just to get sure
        $payload = array(
            'timestamp' => time(),
            'ttl'   => $ttl
        );
        apc_store($this->getHashedkey($key),$payload,$ttl);
    }

    /**
     * @param string $key
     */
    public function unlock($key)
    {
        // delete key to unlock
        if (apc_exists($this->getHashedkey($key))) {
            apc_delete($this->getHashedkey($key));
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function locked($key)
    {
        // is unlocked if no file exists
        if (!apc_exists($this->getHashedkey($key))) {
            return false;
        }

        // get value from apc to check ttl in case gc has not purged our key
        $payload = apc_fetch($this->getHashedkey($key));

        // if time since last modification is less than ttl locked
        if (time()-$payload['timestamp']<=$payload['ttl']) {
            return true;
        }

        // otherwise delete file
        apc_delete($this->getHashedkey($key));
        return false;
    }

    protected function getHashedkey($key)
    {
        // generate hashed key and save it in property
        if (!$this->hashedkey) {
            $this->hashedkey = md5($key);
        }
        return $this->hashedkey;
    }


}
