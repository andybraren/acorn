<?php

// Callout Tag
// by Andy Braren
// Creates a highlighted note for highlights, notes, warnings, notices, etc.

/* CHANGELOG
2015-12-08 - Initial creation

*/

/*
(callout: warning text: **Important note:** Be careful when you disassemble the computer.)
*/

/* ISSUES
  - Markdown parsing doesn't really work well in a single line. May have to move to this: https://forum.getkirby.com/t/no-markdown-parsing-with-kirbytags/2070/2
*/

kirbytext::$tags['callout'] = array(
  'attr' => array(
    'icon',
    'title',
    'text',
  ),
  'html' => function($tag) {
    
    $color = $tag->attr('callout');
    $icon = 'callout-icon-' . $tag->attr('icon');
    $title = '<strong>' . $tag->attr('title') . '</strong>';
  	$text = '<p>' . $tag->attr('text') . '</p>';
  	
  	$color = $color ? ' ' . $color : ' gold'; // The color of the callout should never be a globally-changeable "default"
  	$icon = $icon ? ' ' . $icon : '';
  	
  	return '<div class="callout' . $color . $icon . '">' . kirbytext($title . ' ' . $text) . '</div>';
  }
);

?>