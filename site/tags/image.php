<?php
  
// Image Tag
// created by Bastian Allgeier, modified by Andy Braren
// An all-in-one kirbytag that works for images of all types

/* CHANGELOG
2016-03-20 - Changed default alt attribute to be the base filename without thumbnail numbers
2016-04-11 - Added lazy loading with b-lazy syntax and noscript fallback
2016-04-15 - Added padding-bottom to figures for lazy loaded responsive images that don't reflow page when loaded
             - https://www.codecaptain.io/blog/web-development/responsive-images-and-preventing-page-reflow/474
             - http://aspiringwebdev.com/stop-your-web-pages-from-jumping-around-while-images-load/
2016-05-14 - Improved lazy loading for figures, accounting for figcaptions and more
2016-06-03 - Added data-size to thumbnails for... Contenttools sizing when activated, I believe
2016-06-30 - Added padding correction for smaller images, GIFs are no longer resized to preserve animations, and width is added to images smaller than maxImageSize to prevent reflow when lazy loading
2016-06-30 - Added support for hero images, with padding and max-height to prevent reflow
2017-01-03 - Removed b-lazy class from <figure>
2017-02-26 - Caption field of images now processed with kirbytext(), allowing special formatting (but not links)
*/

// image tag
kirbytext::$tags['image'] = array(
  'attr' => array(
    'width',
    'height',
    'alt',
    'text',
    'title',
    'caption',
    'link',
    'size',      // Added by Andy
    'data-size', // Added by Andy for PhotoSwipe
    'data-src',  // Added by Andy for b-lazy
    'type',      // Added by Andy for hero image logic
    'output',    // Added by Andy for hero image logic via AJAX
    'returndata',
    'targetpage',
    'default'    // Added by Andy for hero image logic
  ),
  'html' => function($tag) {

    $image   = $tag->attr('image');
    $file    = $tag->file($image);
    
    $output = ($tag->attr('output')) ? $tag->attr('output') : null;
    $targetpage = ($tag->attr('targetpage')) ? site()->page($tag->attr('targetpage')) : null;
    
    if ($output == 'url') {
      $file = new Asset ('content/' . $targetpage->uri() . '/' . $image);
    }
    
    $width   = $tag->attr('width');
    $height  = $tag->attr('height');

    // use the file url if available and otherwise the given url
    $url = $file ? $file->url() : url($image);
    
    
    

    $alt     = $tag->attr('alt');
    $title   = $tag->attr('title');
    $link    = $tag->attr('link');
    $caption = kirbytext($tag->attr('caption'));
    
    $size    = $tag->attr('size');      // Added by Andy

    $type    = $tag->attr('type');      // Added by Andy
    $datasize = "null";
    $datasrc = "null";
    $minithumb = "null";
    $thumb = "null";
    

    // Tell the site to load PhotoSwipe in the footer
    kirby()->set('option', 'photoswipe', 'on');
    
    
    
    
    
    

    // alt is just an alternative for text
    if($text = $tag->attr('text')) $alt = $text;

    // try to get the title from the image object and use it as alt text
    if($file != null) {
      
      // use thumbnails for large images - Added by Andy
      // http://getkirby.com/forum/general/20141030/thumb-images-posted-via-image
      $maxImageSize = '700';
      if($width and $height) {
        $thumb = thumb($file, array('width' => $width, 'height' => $height, 'crop' => false));
        $url = $thumb->url();
      } else if(!empty($width) or !empty($height)) {
        $thumb = thumb($file, array('width' => $width, 'height' => $height, 'crop' => false));
        $url = $thumb->url();
      } else if($file->width() > $maxImageSize or $file->height() > $maxImageSize and $file->extension() != "gif") {
        $fit_to = $file->isLandscape() ? 'width' : 'height';
        //$thumb = thumb($file, array('width' => $maxImageSize, 'height' => $maxImageSize));
        $thumb = thumb($file, array('width' => $maxImageSize));
        $url = $thumb->url();
      } else {
        $temp = $file ? $file : $url;
        $url = $temp->url();
        $thumb = $temp;
      }
      
      if($type != 'hero') {
        $minithumb = thumb($file, array('width' => $maxImageSize/10, 'height' => $height/10, 'crop' => false))->url();
      }
      
      // create thumbnails when size attribute used - Added by Andy
      if($tag->attr('size')) {
        if($size == 'small') {
          $thumb = thumb($file, array('width' => $maxImageSize/4, 'height' => $height, 'crop' => false));
          $url = $thumb->url();
          $stylecalc = 'padding-top:' . $thumb->height() / $thumb->width() * 100/4 * ($thumb->width() / ($maxImageSize/4)) . '%;width:100%;';
        } else if($size == 'medium') {
          $thumb = thumb($file, array('width' => $maxImageSize/2, 'height' => $height, 'crop' => false));
          $url = $thumb->url();
          $stylecalc = 'padding-top:' . $thumb->height() / $thumb->width() * 100/2 * ($thumb->width() / ($maxImageSize/2)) . '%;width:100%';
        } else if($size == 'large') {
          $thumb = thumb($file, array('width' => $maxImageSize, 'height' => $height, 'crop' => false));
          $url = $thumb->url();
          $stylecalc = 'padding-top:' . $thumb->height() / $thumb->width() * 100 * ($thumb->width() / $maxImageSize) . '%;width:100%';
        } else {
          // This else isn't really necessary I don't think, just needs to continue to else, or I guess be identical
          $thumb = thumb($file, array('width' => $maxImageSize, 'height' => $height, 'crop' => false));
          $url = $thumb->url();
          $stylecalc = 'padding-top:' . $thumb->height() / $thumb->width() * 100 * ($thumb->width() / $maxImageSize) . '%;';
        }
      } else {
        $widthcalc = $thumb->width() < $maxImageSize ? 'width:' . $thumb->width() / $maxImageSize * 100 . '%' : '';
        $stylecalc = 'padding-top:' . $thumb->height() / $thumb->width() * 100 * ($thumb->width() / $maxImageSize) . '%;' . $widthcalc;
      }
      
      if($type == 'hero') {
        if ($file->ratio() >= 3.5) {
          $thumb = $file->resize(1500);
          $minithumb = $file->resize(250)->url();
          $maxheight = 'max-height:' . $thumb->height() . 'px';
          $stylecalc = 'padding-top:' . $thumb->height() / $thumb->width() * 100 . '%;' . $maxheight;
        } else if ($file->ratio() <= 1.8) {
          $thumb = $file->crop(1200, 500);
          $minithumb = $file->crop(300, 125)->url();
          $maxheight = 'max-height:' . $thumb->height() . 'px';
          $stylecalc = 'padding-top:' . $thumb->height() / $thumb->width() * 100 . '%;max-width:1200px;' . $maxheight;
        } else {
          $thumb = $file->resize(1200);
          $minithumb = $file->resize(200)->url();
          $maxheight = 'max-height:' . $thumb->height() . 'px';
          $stylecalc = 'padding-top:' . $thumb->height() / $thumb->width() * 100 . '%;max-width:1200px;' . $maxheight;
        }
        $url = $thumb->url();
      }
      
      if($file->extension() == "gif") {
        $url = $file->url();
        $thumb = $file;
      }
      
      if (isset($output)) {
        if ($output == 'url') {
          return $thumb->url();
        }
      }
      
      // link builder
      $_link = function($image) use($tag, $url, $link, $file, $datasize, $type, $output) {
        
        // build the href for the link
        if($link == 'self') {
          $href = $url;
        } else if($file and $link == $file->filename()) {
          $href = $file->url();
        } else if($file and empty($link)) {
          $href = $file->url();
          $datasize = $file->width() . "x" . $file->height();
        } else {
          $href = $link;
          //$href = $file->url();
        }
        
        //if(empty($link)) return $image;    // tweaked by Andy to force links
        
        //$datasize = $file->width() . "x" . $file->height();
        if ($type == 'hero' and $output == 'url') {
          return $url;
        }
        else if ($type == 'hero') {
          return $image;
        }
        else if ($datasize != "null") {
          return html::a(url($href), $image, array(
            'rel'    => $tag->attr('rel'),
            'title'  => $tag->attr('title'),
            'target' => $tag->target(),
            'data-size'  => $datasize,          // added by Andy for PhotoSwipe
          ));
        }
        else {
          return html::a(url($href), $image, array(
            'rel'    => $tag->attr('rel'),
            'title'  => $tag->attr('title'),
            'target' => $tag->target()
          ));
        }
        
      };
  
      $datasrc = $url;
  
      // image builder
      //$_image = function($class) use($tag, $url, $alt, $title) {
      $_image = function($class) use($tag, $datasrc, $alt, $title, $minithumb, $thumb) {
        //$url = "/maker/assets/images/blank.gif"; // Added for b-lazy
        $url = 'null';
        $url = "/site/assets/images/blank.gif";
        
        return html::img($url, array(
          'src'    => $url,
          'width'  => $tag->attr('width'),
          'height' => $tag->attr('height'),
          'class'  => $class . "b-lazy",
          'title'  => $title,
          'alt'    => $alt,
          'data-src' => $datasrc,  // Added by Andy for b-lazy
          'data-size' => $thumb->width() . 'x' . $thumb->height(),
        ));
        
        /*
        return html::tag('img', null, array(
          'src'    => $url,
          'width'  => $tag->attr('width'),
          'height' => $tag->attr('height'),
          'class'  => $class . "b-lazy",
          'title'  => $title,
          'alt'    => $alt,
          'data-src' => $datasrc,  // Added by Andy for b-lazy
          'data-size' => $thumb->width() . 'x' . $thumb->height(),
        ));
        */
        //return '<img src="" class="b-lazy" data-src="' . $datasrc . '" data-size="' . $thumb->width() . 'x' . $thumb->height() . '">';
        
        /*
          Need to create:
          
          <figure>
            <div style="padding-top:##%;max-width:1200px;max-height:360px;">   // Maybe I could use data attributes instead of inline styles here?
              <span class="placeholder" data-color="blue"></span>
              <picture>
                <source srcset="" data-srcset="">
                <img src="" data-src="">
              </picture>
            </div>
            <figcaption></figcaption>
            <noscript></noscript>
          </figure>
          
        */
        
        //return '<img src="" class="b-lazy" data-src="' . $datasrc . '" data-size="' . $thumb->width() . 'x' . $thumb->height() . '">';
        
        
        
      };
      
      // noscript image build - same as above, just without the changes
      $_noscriptimage = function($class) use($tag, $url, $alt, $title) {
        return html::img($url, array(
          'width'  => $tag->attr('width'),
          'height' => $tag->attr('height'),
          'class'  => $class . "b-lazy",
          'title'  => $title,
          'alt'    => $alt
        ));
      };
      $noscriptimage = '<noscript>' . $_noscriptimage($tag->attr('imgclass')) . '</noscript>';
  
      if(kirby()->option('kirbytext.image.figure') or !empty($caption)) {
        $image  = $_link($_image($tag->attr('imgclass')) . $noscriptimage);
        $figure = new Brick('figure');
        $figure->addClass($tag->attr('class'));
        //$figure->addClass('b-lazy');
        //$paddingcalc = $file->height() / $file->width() * 100 . '%';
        
        
        $maxwidthcalc = $file->width() / 873 * 100 . '%';
        //$figure->attr('style','padding-bottom:' . $paddingcalc); // Added by Andy for responsive images that don't reflow, assumes we want the image to be 100% width and small images rescaled, which isn't true
        $figure->attr('style','max-width:' . $maxwidthcalc);
        $figure->addClass($tag->attr('size'));                                    // Added by Andy
        $figure->append($image);
        if(!empty($caption)) {
          $figure->append('<figcaption>' . html($caption) . '</figcaption>');
          $figure->attr('style',$stylecalc);
        }
        if(empty($caption)) {
          $figure->attr('style',$stylecalc);
        }
        return $figure;
      } else {
        $class = trim($tag->attr('class'));
        return $_link($_image($class));
      }
    }
    
    /*
    else if ($output == 'url') {
      return $url;
    }
    */
    
    else { // If the image doesn't exist, is missing, or something else is wrong
      //return '<img class="error-image" src="' . $url . '" alt="' . $tag->attr('image') . '">';
    }
  }
);
?>