<?php

namespace Classes;

class BookmarksMemcache
{
    private $mem = null;
    const SALT = 'bookmarks_rest_api_salt';

    public function __construct()
    {
        $this->mem = new Memcached();
        $this->mem->addServer("127.0.0.1", 11211);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getData($key)
    {
        return $this->mem->get(md5(base64_decode($key.self::SALT)));
    }

    /**
     * @param $key
     * @param $value
     */
    public function setData($key, $value)
    {
        $this->mem->set(md5(base64_decode($key.self::SALT)), $value);
    }
}