<?php

// Button Tag
// by Andy Braren
// Updated 08/01/2015
// Creates a button with a link, color and size

kirbytext::$tags['button'] = array(
  'attr' => array(
    'link',
    'size',
    'color'
  ),
  'html' => function($tag) {
    $title = $tag->attr('button');
  	$link =  $tag->attr('link');
  	$size = $tag->attr('size');
  	$color = $tag->attr('color');
  	
  	return '<form action="' . $link . '" method="post"><button class="button-solution">' . $title . '</button></form>';
  }
);

?>