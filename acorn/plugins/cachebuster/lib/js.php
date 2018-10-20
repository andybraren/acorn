<?php

namespace Kirby\Cachebuster;

use F;

use MatthiasMullie\Minify;

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

    if (is_array($src)) {
      $js = array();
      foreach($src as $s) $js[] = $this->tag($s, $async);
      return implode(PHP_EOL, $js) . PHP_EOL;
    }

    $file = kirby()->roots()->index() . DS . $src;
    
    if (file_exists($file)) {
      
      $new = file_get_contents($file);
      
      // Add Google Analytics ID
      if (site()->setting('analytics/google/enabled')) {
        $search = '// VAR_GoogleAnalytics';
        $replace = '(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,"script","//www.google-analytics.com/analytics.js","ga");ga("create", "' . site()->setting('analytics/google/id') . '", "auto");ga("send", "pageview");';
        $new = str_replace($search, $replace, $new);
      }
      
      // Enable Disqus
      if (site()->setting('discussion/disqus/enabled')) {
        if (site()->setting('discussion/disqus/shortname') != '') {
          $search = '// VAR_DISQUS';
          $replace = 'const disqus_shortname = "' . site()->setting('discussion/disqus/shortname') . '";';
          $new = str_replace($search, $replace, $new);
        }
      }
      
      // Enable BuySellAds
      if (site()->setting('monetization/ads-bsa/enabled')) {
        $search = '// VAR_BSA';
        $replace = "(function(){var bsa = document.createElement('script');bsa.type = 'text/javascript';bsa.async = true;bsa.src = '//s3.buysellads.com/ac/bsa.js';(document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(bsa);})();";
        $new = str_replace($search, $replace, $new);
      }
      
      // Enable console logging
      if (site()->setting('advanced/debug')) {
        $search = '// console.log';
        $replace = 'console.log';
        $new = str_replace($search, $replace, $new);
      }
      
      // Minify
      if (site()->setting('advanced/debug') == false) {
        $minifier = new Minify\JS;
        $minifier->add($new);
        $new = $minifier->minify();
      }
      
      $newfilename = f::name($src) . '.' . f::modified($file) . '.js';
      
      $newsrc = kirby()->roots()->index() . DS . 'cache/assets/js/' . $newfilename;
      $newsrc = 'cache/assets/js/' . $newfilename;
      
      $newfile = kirby()->roots()->index() . DS . 'cache/assets/js/' . $newfilename;
      if (!is_dir(kirby()->roots()->index() . DS . 'cache/assets/js/')) {
        mkdir(kirby()->roots()->index() . DS . 'cache/assets/js/', 0775, true);
      }
      file_put_contents($newfile, $new);
      
    }

    return parent::tag($newsrc, $async);

  }

}
