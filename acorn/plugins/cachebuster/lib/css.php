<?php

namespace Kirby\Cachebuster;

use F;

use MatthiasMullie\Minify;

/**
 * Kirby Cachebuster CSS Component
 * 
 * @author Lukas Bestle <lukas@getkirby.com>
 * @license MIT
 * @link https://getkirby.com
 */
class CSS extends \Kirby\Component\CSS {

  /**
   * Builds the html link tag for the given css file
   * 
   * @param string $src
   * @param null|string $media
   * @return string
   */
  public function tag($src, $media = null) {

    if (is_array($src)) {
      $css = array();
      foreach($src as $s) $css[] = $this->tag($s, $media);
      return implode(PHP_EOL, $css) . PHP_EOL;
    }

    $file = kirby()->roots()->index() . DS . $src;

    if (file_exists($file)) {
      
      $new = file_get_contents($file);
      
      // Add site width
      $search  = '--site-width: /**/;';
      $replace = '--site-width: ' . site()->setting('style/width') . 'px;';
      $new = str_replace($search, $replace, $new);
      
      // Add default color
      if (site()->setting('style/default-color') != null) {
        $default = site()->setting('style/default-color');
        $search  = "\n" . '.' . $default;
        $replace = "\n" . '.' . $default . ', .default';
        $new = str_replace($search, $replace, $new);
      }
      
      // Add bg color
      $search  = '--theme-bgcolor: /**/;';
      if (site()->setting('style/bg-color-primary') != null) {
        $replace = '--theme-bgcolor: ' . site()->setting('style/bg-color-primary') . ';';
      } else {
        $replace = '--theme-bgcolor: 255, 255, 255;';
      }
      $new = str_replace($search, $replace, $new);
      
      // Add boxed bg color
      $search  = '--theme-boxed-bgcolor: /**/;';
      if (site()->setting('style/bg-color-boxed') != null) {
        $replace = '--theme-boxed-bgcolor: ' . site()->setting('style/bg-color-boxed') . ';';
      } else {
        $replace = '--theme-boxed-bgcolor: 255, 255, 255;';
      }
      $new = str_replace($search, $replace, $new);
      
      
      
      
      // Minify
      if (site()->setting('advanced/debug') == false) {
        $minifier = new Minify\CSS;
        $minifier->add($new);
        $new = $minifier->minify();
      }
      
      $newfilename = f::name($src) . '.' . f::modified($file) . '.css';
      
      $newsrc = kirby()->roots()->index() . DS . 'cache/assets/css/' . $newfilename;
      $newsrc = 'cache/assets/css/' . $newfilename;
      
      $newfile = kirby()->roots()->index() . DS . 'cache/assets/css/' . $newfilename;
      if (!is_dir(kirby()->roots()->index() . DS . 'cache/assets/css/')) {
        mkdir(kirby()->roots()->index() . DS . 'cache/assets/css/', 0775, true);
      }
      file_put_contents($newfile, $new);
      
    }

    return parent::tag($newsrc, $media);

  }

}
