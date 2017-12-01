<?php

// Video Tag
// by Andy Braren
// An all-in-one kirbytag that works for YouTube, Vimeo, and internal files

/* CHANGELOG
2015-12-08 - Simplified code for captions, added support for YouTube timestamps, switched to https for images
2015-12-15 - Forgot the closing video tag for mp4 files, and added caption support
2016-01-07 - Added autoplay support for GIF-like HTML5 videos, and poster images
2016-01-17 - Added an isset check to the minutes array for YouTube videos
2016-01-22 - Wrapped HTML5 videos within a video-container wrapper for propoer sizing. Should eventually write a better CSS solution.
2016-03-19 - More reliable Vimeo id parsing, changed Vimeo html output and eliminated Vimeo-specific JS
2016-06-01 - Vimeo embeds now show fullscreen button
2016-06-15 - Switched to max resolution YouTube thumbnails, eventually need to create a fallback if it doesn't exist
              - http://stackoverflow.com/questions/18029550/fetch-youtube-highest-thumbnail-resolution
              - http://stackoverflow.com/questions/14917664/javascript-detect-youtube-default-thumbnail
2016-07-07 - Added webkit-playsinline to autoplaying videos for iOS 10 compatibility
2016-07-25 - Removed webkit prefix for playsinline, and added data-origin for ContentTools compatibility
              - https://webkit.org/blog/6784/new-video-policies-for-ios/
2016-08-29 - Added data-file to MP4 for ContentTools editing
           - Partially implemented fix for missing thumbnails on sub-720p YouTube videos. Current method delays page load time.
              - List of thumbnail types: http://stackoverflow.com/questions/2068344/how-do-i-get-a-youtube-video-thumbnail-from-the-youtube-api/20542029#20542029
              - youtube_image function: http://stackoverflow.com/questions/2068344/how-do-i-get-a-youtube-video-thumbnail-from-the-youtube-api/20655623#20655623
2016-09-13 - Fixed issue where external MP4 files wouldn't have a filename for the previous change, which caused an error.
2016-09-19 - Implemented fix for missing YouTube thumbnails, without page slowdown. Thumbnails are now downloaded locally once and used thereafter.
2016-09-26 - Moved YouTube thumbnail downloader to network-methods, replaced with more universal downloadedImageURL() function
2016-12-09 - Added (size:) support, same as images
2016-12-19 - Playsinline support for YouTube embeds
2017-02-21 - Figcaption only included if not empty
2017-10-14 - Improved the downloadedImageURL function to include Vimeo and only download video thumbs to the primary page's directory
*/

kirbytext::$tags['video'] = array(
  'attr' => array(
    'caption',
    'autoplay',
    'size'
  ),
  'html' => function($tag) {
    
    $caption = $tag->attr('caption');
    $htmlcaption = ($caption) ? '<figcaption>' . $caption . '</figcaption>' : '';
    $url = $tag->attr('video');
    $autoplay = $tag->attr('autoplay');
    $file = $tag->file($url);
    $size = $tag->attr('size');
    
    if ($size != null) {
      $size = ' class="' . $size . '"';
    }
    
    $posterimage = "";
    $filename = "";
    
    if ($video = $tag->page()->file($url)):
      $url = $video->url();
      if ($poster = $tag->page()->file($video->name() . ".png") OR $poster = $tag->page()->file($video->name() . ".jpg")):
        $posterimage = 'poster="' . thumb($poster, array('width' => 700))->url() . '"';
      endif;
      $filename = $video->filename();
    else:
      $url = $file ? $file->url() : url($url);
    endif;
    
    if (page()->id() != "feed") {
      
      // YouTube Videos
      if (str::contains($url, 'youtu')) {

        if (preg_match("/^((https?:\/\/)?(w{0,3}\.)?youtu(\.be|(be|be-nocookie)\.\w{2,3}\/))((watch\?v=|v|embed)?[\/]?(?P<id>[a-zA-Z0-9-_]{11}))/si", $url, $matches)) {
          $youtubeid = $matches['id'];
          $timestamp = "";
          
          // accounts for https://youtu.be/EKQSijn9FBs?t=53m35s
          if (preg_match("/\?t=([0-9]*?m?[0-9]*?s)/", $url, $matches)):
            preg_match("/([0-9]*)m/", $matches[0], $minutes);
            preg_match("/([0-9]*)s/", $matches[0], $seconds);
            //echo $minutes[1];
            //echo $seconds[1];
            if (isset($minutes[1])):
              $timestamp = "&start=" . ($minutes[1] * 60 + $seconds[1]);
            else:
              $timestamp = "&start=" . ($seconds[1]);
            endif;
            //echo $timestamp;
          endif;
          
          // accounts for https://www.youtube.com/watch?v=EKQSijn9FBs&feature=youtu.be&t=53m35s
          // EVENTUALLY FIGURE OUT HOW TO COMBINE THESE
          if (preg_match("/\&t=([0-9]*?m?[0-9]*?s)/", $url, $matches)):
            preg_match("/([0-9]*)m/", $matches[0], $minutes);
            preg_match("/([0-9]*)s/", $matches[0], $seconds);
            //echo $minutes[1];
            //echo $seconds[1];
            if (isset($minutes[1])):
              $timestamp = "&start=" . ($minutes[1] * 60 + $seconds[1]);
            else:
              $timestamp = "&start=" . ($seconds[1]);
            endif;
            //echo $timestamp;
          endif;
          
          
          $thepage = site()->page(page()->uri());
          if ($tag->page() == $thepage) {
            $imageurl = downloadedImageURL('video-' . $youtubeid, $thepage->uri(), 'youtube');
          }
          
          $blahurl = "/maker/assets/images/blank.gif"; // Added for b-lazy
          $blahurl = "/site/assets/images/blank.gif";
          
          return '<figure><div class="video-container"><div class="youtube"><img class="b-lazy" src="' . $blahurl . '" data-src="' . $imageurl . '"><div class="play"></div></div><iframe data-src="https://www.youtube.com/embed/' . $youtubeid . '?autoplay=1&playsinline=1&wmode=transparent&modestbranding=1&autohide=1&showinfo=0&rel=0' . $timestamp . '" data-orig="' . $url . '" frameborder="0" allowfullscreen></iframe></div>' . $htmlcaption . '</figure>';
        } 
      }
      
      // Vimeo Videos
      if (str::contains($url, "vimeo.com")) {
        
        $vimeoid = substr(parse_url($url, PHP_URL_PATH), 1);;
        //$vimeothumburl = "https://vimeo.com/api/v2/video/" . $vimeoid . ".php";
        //$hash = unserialize(@file_get_contents($vimeothumburl));
        //$vimeothumb = $hash[0]['thumbnail_large'];
        
        $thepage = site()->page(page()->uri());
        if ($tag->page() == $thepage) {
          $imageurl = downloadedImageURL('video-' . $vimeoid, $thepage->uri(), 'vimeo');
        }
        
        $htmlimage = '<img class="b-lazy" src="/site/assets/images/blank.gif" data-src="' . $imageurl . '">';

        //return '<figure><div class="video-container"><div class="vimeo">' . $htmlimage . '<div class="play"></div></div><iframe data-src="//player.vimeo.com/video/' . $vimeoid . '?autoplay=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=808080" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>' . $htmlcaption . '</figure>';
        return '<figure><div class="video-container"><div class="vimeo">' . $htmlimage . '<div class="play"></div></div><iframe data-src="//player.vimeo.com/video/' . $vimeoid . '?autoplay=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=808080" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>' . $htmlcaption . '</figure>';
      }
      
      // HTML5 "GIFs"
      elseif ($autoplay == "on") {
        return '<figure' . $size . '><video controls preload="metadata" autoplay loop muted playsinline class="b-lazy" ' . $posterimage . ' data-src="' . $url . '" data-file="' . $filename . '"></video>
        <noscript><span style="color:#AB2A2A">It looks like you have JavaScript disabled. <a href="' . $url . '">Click here</a> to view the video above.</span></noscript>' . $htmlcaption . '</figure>';
        
        // Attempted HTML5 video with noscript fallback, but there doesn't seem to be an easy way unfortunately
        //return '<figure><video controls preload="metadata" autoplay loop muted' . $posterimage . '><source  class="b-lazy" src="' . $url . '"></source><noscript><source src="' . $url . '"></source></noscript></video>' . $htmlcaption . '</figure>';
      }
      
      // HTML5 embeds
      else {
        return '<figure' . $size . '><video controls preload="metadata" class="b-lazy" ' . $posterimage . ' data-src="' . $url . '" data-file="' . $filename . '"></video>
        <noscript><span style="color:#AB2A2A">It looks like you have JavaScript disabled. <a href="' . $url . '">Click here</a> to view the video above.</span></noscript>' . $htmlcaption . '</figure>';
      }
    }
    
    elseif (page()->id() == "feed") {
      if (str::contains($url, "vimeo.com")) {
        $vimeoid = substr($url, -8 );
        return '<iframe src="http://player.vimeo.com/video/'. urlencode($vimeoid) . '"></iframe>';
      } elseif (str::contains($url, "youtube.com") || str::contains($url, "youtu.be")) {
        $youtubeid = substr($url, -11 );
        return '<iframe src="https://www.youtube.com/embed/' . $youtubeid . '"></iframe>';
      }
    }
  }
);









?>