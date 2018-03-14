<?php
  
// Content tag
// display a collection of pages/items in a variety of formats and styles

// "format" = the arrangement, like cards, list, full content, etc.
// "style"  = the look, which is ambiguous at the moment

// Maybe this should be renamed "content" instead?

kirbytext::$tags['content'] = array(
  'attr' => array(
    'format',
    'style',
    'type',
    'sort',
    'tags',
    'number',
    'paginate',
  ),
  'html' => function($tag) {

    $content  = $tag->attr('content');
    $format   = $tag->attr('format');
    $style    = $tag->attr('style');
    $sort     = $tag->attr('sort');
    $tags     = $tag->attr('tags');
    $number   = $tag->attr('number');
    $paginate = $tag->attr('paginate');
    
    // get the items specified by $content
    if ($content == 'children') {
      $items = $tag->page()->children() ?? null;
    } elseif (stripos($content, ',')) {
      $items = new Pages();
      foreach(str::split($content, ',') as $item) {
        $items->add(site()->page($item));
      }
    } else {
      $items = (site()->page($content)) ? site()->page($content)->children() : null;
    }
    
    $all = $tag->attr();
    $all['items'] = $items;
    
    return snippet('content', $all);
    
  }
);