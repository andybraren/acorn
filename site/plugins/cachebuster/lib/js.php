<?php

namespace Kirby\Cachebuster;

use F;

/**
 * Kirby Cachebuster JS Component
 * 
 * @author Lukas Bestle <lukas@getkirby.com>
 * @license MIT
 * @link https://getkirby.com
 */
class JS extends \Kirby\Component\JS {

  /**
   * Builds the html script tag for the given javascript file
   * 
   * @param string $src
   * @param boolean async
   * @return string
   */
  public function tag($src, $async = false) {

    if(is_array($src)) {
      $js = array();
      foreach($src as $s) $js[] = $this->tag($s, $async);
      return implode(PHP_EOL, $js) . PHP_EOL;
    }

    $file = kirby()->roots()->index() . DS . $src;

    if(file_exists($file)) {
      
      // Add Google Analytics ID - Added by Andy
      if (site()->googleanalytics() != '') {
        
        $target = 'var VAR_GoogleAnalytics = "";';
        $script = '(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,"script","//www.google-analytics.com/analytics.js","ga");ga("create", "' . site()->googleanalytics() . '", "auto");ga("send", "pageview");';
        
        $newfile = str_replace('.js', '.mod.js', $file);
        file_put_contents($newfile, str_replace($target, $script, file_get_contents($file)));
        $src = str_replace('.js', '.mod.js', $src);
      }
      
      $mod = f::modified($file);
      $src = dirname($src) . '/' . f::name($src) . '.' . $mod . '.js';
    }

    return parent::tag($src, $async);

  }

}
