<?php

namespace Classes;

use Classes\BookmarksMemcache;

class Bookmark
{
    /**
     * @param array $data
     */
    public function getBookmark($data = []){
        if(!empty($data['url'])){
            echo 'bookmark';
        } else {
            $obj = new BookmarksMemcache();
            $obj->getData();
        }
    }

    public function setBookmark($data = []){
        var_dump($data);
    }

    public function updateBookmark($data = []){
        var_dump($data);
    }

    public function deleteBookmark($data = []){
        echo 22;
    }
}