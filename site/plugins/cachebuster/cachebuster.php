<?php

// Cachebuster Plugin
// created by Bastian Allgeier & Lukas Bestle
// https://github.com/getkirby-plugins/cachebuster-plugin
// Adds modification timestamps to the filenames of CSS/JS files, forcing CDNs to always download the latest file

if(!c::get('cachebuster')) return;

load([
  'kirby\\cachebuster\\css' => __DIR__ . DS . 'lib' . DS . 'css.php',
  'kirby\\cachebuster\\js'  => __DIR__ . DS . 'lib' . DS . 'js.php'
]);

kirby()->set('component', 'css', 'Kirby\\Cachebuster\\CSS');
kirby()->set('component', 'js',  'Kirby\\Cachebuster\\JS');