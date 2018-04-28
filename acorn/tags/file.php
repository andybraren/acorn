<?php
  
// File Tag
// created by Bastian Allgeier, modified by Andy Braren
// An all-in-one kirbytag that displays downloadable files from internal and external sources

/* CHANGELOG
2016-03-20 - Initial creation, only local files supported, with file extensions displayed until something visual can be created
*/

// file tag
kirbytext::$tags['file'] = array(
  'attr' => array(
    'text',
    'class',
    'title',
    'rel',
    'target',
    'extension',
    'popup'
  ),
  'html' => function($tag) {

    // build a proper link to the file
    $file = $tag->file($tag->attr('file'));
    $text = $tag->attr('text');

    if(!$file) return $text;

    // use filename if the text is empty and make sure to
    // ignore markdown italic underscores in filenames
    if(empty($text)) $text = str_replace('_', '\_', $file->name()) . '.' . $file->extension();
    
    $id = ($file->type() == 'image') ? 'img' : $file->extension();
    
    /*
    return html::a($file->url(), html($text), array(
      'class'  => 'file-' . $id,
      'data-filename' => $file->filename(),
      'title'  => html($tag->attr('title')) . '',
      'rel'    => $tag->attr('rel'),
      'target' => $tag->target(),
    ));
    */
    
    return '<a href="' . $file->acornURL() . '" class="file-' . $id . '" data-filename="' . $file->filename() . '">' . html($text) . '</a>';

  }
);

?>