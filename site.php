<?php

// site.php
$kirby = kirby();

// cache
$kirby->roots->cache = $kirby->roots()->index() . DS . 'cache' . DS . 'content';

// thumbs
$kirby->roots->thumbs = $kirby->roots()->index() . DS . 'cache' . DS . 'thumbs';
$kirby->urls->thumbs  = $kirby->urls()->index() . '/cache/thumbs';

// -----

// assets
$kirby->roots->assets = $kirby->roots()->index() . DS . 'acorn' . DS . 'assets';
$kirby->urls->assets  = $kirby->urls()->index() . '/acorn/assets';

// config
$kirby->roots->config = $kirby->roots()->index() . DS . 'acorn' . DS . 'config';

// plugins
$kirby->roots->plugins = $kirby->roots()->index() . DS . 'acorn' . DS . 'plugins';
$kirby->urls->plugins  = $kirby->urls()->index() . '/acorn/plugins';

// snippets
$kirby->roots->snippets = $kirby->roots()->index() . DS . 'acorn' . DS . 'snippets';
$kirby->urls->snippets  = $kirby->urls()->index() . DS . 'acorn' . DS . 'snippets';

// tags
$kirby->roots->tags = $kirby->roots()->index() . DS . 'acorn' . DS . 'tags';
$kirby->urls->tags  = $kirby->urls()->index() . DS . 'acorn' . DS . 'tags';

// templates
$kirby->roots->templates = $kirby->roots()->index() . DS . 'acorn' . DS . 'templates';
$kirby->urls->templates  = $kirby->urls()->index() . DS . 'acorn' . DS . 'templates';

// users and avatars
$kirby->roots->accounts = $kirby->roots()->index() . DS . 'acorn' . DS . 'users' . DS . 'accounts';
$kirby->roots->avatars  = $kirby->roots()->index() . DS . 'acorn' . DS . 'users' . DS . 'avatars';
$kirby->urls->avatars   = $kirby->urls()->index() . DS . 'acorn' . DS . 'users' . DS . 'avatars';