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

// Load the Minify files when debugging mode is off
require_once(kirby()->roots()->plugins() . DS . 'cachebuster/vendor/minify/src/Minify.php');
require_once(kirby()->roots()->plugins() . DS . 'cachebuster/vendor/minify/src/CSS.php');
require_once(kirby()->roots()->plugins() . DS . 'cachebuster/vendor/minify/src/JS.php');
require_once(kirby()->roots()->plugins() . DS . 'cachebuster/vendor/minify/src/Exception.php');
require_once(kirby()->roots()->plugins() . DS . 'cachebuster/vendor/minify/src/Exceptions/BasicException.php');
require_once(kirby()->roots()->plugins() . DS . 'cachebuster/vendor/minify/src/Exceptions/FileImportException.php');
require_once(kirby()->roots()->plugins() . DS . 'cachebuster/vendor/minify/src/Exceptions/IOException.php');
require_once(kirby()->roots()->plugins() . DS . 'cachebuster/vendor/path-converter/src/ConverterInterface.php');
require_once(kirby()->roots()->plugins() . DS . 'cachebuster/vendor/path-converter/src/Converter.php');
