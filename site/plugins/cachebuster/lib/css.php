<?php

namespace Kirby\Cachebuster;

use F;

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
   * @param string $url
   * @param null|string $media
   * @return string
   */
  public function tag($url, $media = null) {

    if(is_array($url)) {
      $css = array();
      foreach($url as $u) $css[] = $this->tag($u, $media);
      return implode(PHP_EOL, $css) . PHP_EOL;
    }

    $file = kirby()->roots()->index() . DS . $url;

    if(file_exists($file)) {
      /*
      $mod = f::modified($file);
      $url = dirname($url) . '/' . f::name($url) . '.' . $mod . '.css';
      */
      
      $newcss = file_get_contents($file);
      
      // Add site width
      $search  = '--site-width: /**/;';
      $replace = '--site-width: ' . site()->setting('style/width') . 'px;';
      $newcss = str_replace($search, $replace, $newcss);
      
      // Add bg color
      $search  = '--theme-bgcolor: /**/;';
      if (site()->setting('style/bg-color-primary') != null) {
        $replace = '--theme-bgcolor: ' . site()->setting('style/bg-color-primary') . ';';
      } else {
        $replace = '--theme-bgcolor: 255, 255, 255;';
      }
      $newcss = str_replace($search, $replace, $newcss);
      
      $newfilename = f::name($url) . '.' . f::modified($file) . '.css';
      
      $newsrc = kirby()->roots()->index() . DS . 'cache/assets/css/' . $newfilename;
      $newsrc = 'cache/assets/css/' . $newfilename;
      
      $newfile = kirby()->roots()->index() . DS . 'cache/assets/css/' . $newfilename;
      if (!is_dir(kirby()->roots()->index() . DS . 'cache/assets/css/')) {
        mkdir(kirby()->roots()->index() . DS . 'cache/assets/css/', 0775, true);
      }
      file_put_contents($newfile, $newcss);
      
    }

    //return parent::tag($url, $media);
    return parent::tag($newsrc, $media);

  }

}
