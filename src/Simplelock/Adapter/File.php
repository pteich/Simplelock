<?php

namespace Simplelock\Adapter;

class File implements AdapterInterface {

    protected $path = null;
    protected $filepath = null;
    protected $config = array();


    public function __construct($config)
    {
        $this->config = $config;
        $this->path = realpath(getcwd() . '/' . $config['path']);
    }

    /**
     * @param string $key
     * @param integer $ttl
     */
    public function lock($key, $ttl)
    {
        // Create or refresh lock file
        $fp = fopen($this->getFilename($key),'c');
        if (flock($fp,LOCK_EX)) {
            // write ttl in file
            fwrite($fp, intval($ttl));
        }
        fclose($fp);
    }

    /**
     * @param string $key
     */
    public function unlock($key)
    {
        // delete file to unlock
        if (file_exists($this->getFilename($key))) {
            unlink($this->getFilename($key));
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function locked($key)
    {
        // is unlocked if no file exists
        if (!file_exists($this->getFilename($key))) {
            return false;
        }

        // open file to check ttl
        $fp = fopen($this->getFilename($key),'r');
        $ttl = intval(fread($fp,1024));

        // if time since last modification is less than ttl locked
        if (time()-filemtime($this->getFilename($key))<=$ttl) {
            return true;
        }

        // otherwise delete file
        unlink($this->getFilename($key));
        return false;
    }

    protected function getFilename($key)
    {
        // generate file path and save it in property
        if (!$this->filepath) {
            $this->filepath = $this->path . '/' . md5($key);
        }
        return $this->filepath;
    }


}
