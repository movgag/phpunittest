<?php

namespace Coalition;

class ConfigRepository implements \ArrayAccess
{
    public $configs = array();

    /**
     * ConfigRepository Constructor
     */
    public function __construct($configs = array())
    {
        $this->configs = $configs;
    }

    /**
     * Determine whether the config array contains the given key
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key,$this->configs);
    }

    /**
     * Set a value on the config array
     *
     * @param string $key
     * @param mixed  $value
     * @return \Coalition\ConfigRepository
     */
    public function set($key, $value)
    {
        $configs = $this->configs;
        $configs[$key] = $value;
        $this->configs = $configs;
        return $this;
    }

    /**
     * Get an item from the config array
     *
     * If the key does not exist the default
     * value should be returned
     *
     * @param string     $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $configs = $this->configs;
        return array_key_exists($key, $configs) ? $configs[$key] : $default;
    }

    /**
     * Remove an item from the config array
     *
     * @param string $key
     * @return \Coalition\ConfigRepository
     */
    public function remove($key)
    {
        $configs = $this->configs;
        unset($configs[$key]);
        $this->configs = $configs;
        return $this;
    }

    /**
     * Load config items from a file or an array of files
     *
     * The file name should be the config key and the value
     * should be the return value from the file
     * 
     * @param array|string The full path to the files $files
     * @return void
     */
    public function load($files)
    {
        $configs = array();
        if (!is_array($files)) {
            $files = array($files);
        }
        foreach ($files as $file) {
            // exploding path with delimiter /
            $a1 = explode('/',$file);
            // getting last element
            $f = $a1[count($a1) - 1];
            // exploding path with delimiter .
            $a2 = explode('.',$f);
            // getting first element , the file name
            $key = $a2[0];

            $arr = include($file);
            $arr = is_array($arr) && $arr ? $arr : array();
            $configs[$key] = $arr;
        }
        $this->configs = $configs;
    }


    public function offsetSet($key, $value){
        if (is_null($key)) {
            $this->configs[] = $value;
        } else {
            $this->configs[$key] = $value;
        }
    }
    public function offsetExists($key){
        return isset($this->configs[$key]);
    }
    public function offsetUnset($key){
        unset($this->configs[$key]);
    }
    public function offsetGet($key){
        if (! isset($this->configs[$key])) {
            return null;
        }
        $val = $this->configs[$key];
        if (is_callable($val)) {
            return $val($this);
        }
        return $val;
    }
}