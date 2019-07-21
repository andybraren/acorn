<?php

require __DIR__ . '/kirby/bootstrap.php';

// Acorn custom folders
// Tags and users left to figure out
$kirby = new Kirby([
  'roots' => [
    'index'   => __DIR__,
    'cache'    => __DIR__ . '/cache/content',
    'media' => __DIR__ . '/cache/media',
    'assets' => __DIR__ . '/acorn/assets',
    'plugins' => __DIR__ . '/acorn/plugins',
    'snippets' => __DIR__ . '/acorn/snippets',
    'templates' => __DIR__ . '/acorn/templates',
    'site' => __DIR__ . '/acorn/site',
    'config' => __DIR__ . '/acorn/config'
  ],
  'urls' => [
    'media' => site()->url() . '/cache/media',
  ],
]);

echo $kirby->render();