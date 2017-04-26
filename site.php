<?php

// site.php

$kirby   = kirby();

//$kirby->roots->avatars = __DIR__ . DS . 'content' . DS . 'users' . DS . 'avatars' . DS . $domain;
//$kirby->urls->avatars  = $kirby->urls()->index() . '/content/users/avatars';

$kirby->roots->accounts = $kirby->roots()->index() . DS . 'users' . DS . 'accounts'; // accounts moved to /accounts/accounts
$kirby->roots->cache    = $kirby->roots()->index() . DS . 'cache' . DS . 'content';     // cache moved to /cache/content

$kirby->roots->avatars  = $kirby->roots()->index() . DS . 'users' . DS . 'avatars';
$kirby->urls->avatars   = $kirby->urls()->index() . '/users/avatars';

$kirby->roots->assets  = $kirby->roots()->index() . DS . 'site' . DS . 'assets';
$kirby->urls->assets   = $kirby->urls()->index() . '/site/assets';

//$kirby->roots->site = __DIR__ . DS . 'acorn';

$kirby->roots->thumbs = $kirby->roots()->index() . DS . 'cache' . DS . 'thumbs';
$kirby->urls->thumbs  = $kirby->urls()->index() . '/cache/thumbs';