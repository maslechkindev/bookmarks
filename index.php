<?php

require_once (__DIR__.'/Classes/Routing.php');

use Classes\Routing;

$rout = Routing::getRoute();

//получить список 10 последних добавленных Bookmark
//GET/user/{ip}/bookmark
//получить Bookmark (с комментариями) по Bookmark.url. Если такого ещё нет, не создавать.
//GET/user/{ip}/bookmark?url=base_64(url)
//добавить Bookmark по url и получить Bookmark.uid. Если уже есть Bookmark с таким url, не добавлять ещё один, но получить Bookmark.uid.
//POST/user/{ip}/bookmark?url=base_64(url)
//добавить Comment к Bookmark (по uid) и получить Comment.uid
//POST/user/{ip}/comment/{uid}
//изменить Comment.text по uid (если он добавлен с этого же IP и прошло меньше часа после добавления)
//PUT/user/{ip}/comment/{uid}?date={timestamp(now)}
//удалить Comment по uid (если он добавлен с этого же IP и прошло меньше часа после добавления)
//DELETE/user/{ip}/comment/{uid}?date={timestamp(now)}