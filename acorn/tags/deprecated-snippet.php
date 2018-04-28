<?php // This tag allows any snippet to be added anywhere within the text ?>
<?php // http://forum.getkirby.com/t/embedding-snippets/900/4 ?>
<?php
kirbytext::$tags['snippet'] = array(
  'attr' => array(
  ),
  'html' => function($tag) {
  	$file =  $tag->attr('snippet');
  	return snippet($file, array(), true);
  }
);