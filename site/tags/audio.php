<?php

// Audio Tag
// by Andy Braren
// Embeds an HTML5 audio element with controls

/* CHANGELOG
2016-12-06 - Initial creation
*/

kirbytext::$tags['audio'] = array(
  'attr' => array(
    'caption',
  ),
  'html' => function($tag) {
    
    $caption = $tag->attr('caption');
    $htmlcaption = ($caption) ? '<figcaption>' . $caption . '</figcaption>' : '';
    
    $caption = "hello";
    
    if ($tag->page()->file($tag->attr('audio')) != null) {
      $url = $tag->page()->file($tag->attr('audio'))->url();
      return '<figure><audio controls src="' . $url . '"></audio>' . $htmlcaption . '</figure>';
    }
    
  }
);

?>