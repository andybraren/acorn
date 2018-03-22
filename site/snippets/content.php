<?php

// Content snippet
// Outputs the HTML for any collection of pages/items
// This will be used a lot by the API to return HTML on the fly

// Takes in
// $items    - the items/pages that should be collected
// $format   - the arrangement desired (card, list, excerpt, full, etc.)
// $style    - the look, which is ambiguous at the moment
// $type     - links, videos
// $sort     - the order in which items should be sorted
// $tags     - the tags that items need to have
// $number   - the number of items that should be returned, without pagination
// $paginate - the number of items that should be returned, with pagination

/*
// Get items if not already set
if (!isset($items)) {
  echo 'Error: items not defined';
  exit;
} else {
  //print_r($items);
}

// sort the items
if ($items) {
  $items = $items->sortBy('dateCreated','desc');
}

// filter by specified tags
if (isset($tags)) {
  
  // turn comma-separated strings of tags into an array if needed
  if (!is_array($tags)) {
    $tags = str::split($tags,',');
  }
  
  // if any overlap exists between one item's tags and the desired ones, keep it
  $items = $items->filter(function($item) use($tags)  {
    return array_intersect($item->tags(), $tags);
  });
  
}

// filter by specified type
if (isset($type)) {
  
  if ($type == 'links') {
    
    $temp = new Pages();
    foreach ($items as $item) {
      if (!empty($item->links())) {
        if (count($item->links()) === 1 and $item->links()[0]['label'] == '') {
          $temp->add(site()->page($item));
        }
      }
    }
    $items = $temp;
    
  }
  
  if ($type == 'videos') {
    
    $temp = new Pages();
    foreach ($items as $item) {
        $field = $item->text();
        if (strpos($field, 'youtu') or strpos($field, 'vimeo') or strpos($field, 'mp4')) {
          $temp->add(site()->page($item));
        }
    }
    $items = $temp;
    
  }
  
}

// limit to only a certain number
if (isset($number)) {
  $items = $items->limit($number);
}

// paginate items, displaying only a certain amount per page
if (isset($paginate)) {
  $items = $items->paginate($paginate);
  $pagination = true;
}

// card format
if ($format == 'card') {
  
  $html = '';
  
  foreach ($items as $item) {
    
    $color = ($item->color() != "") ? ' ' . $item->color() : "";
    $url = $item->url();
    $herourl = '';
    
    if ($hero = $item->images()->findBy('name', 'icon') AND $page->uid() == 'books') {
      $herourl = $hero->crop(360, 500)->url();
    } elseif ($hero = $item->heroImage()) {
      $herourl = $hero->crop(300, 120)->url();
    } elseif ($item->hasImages()) {
      if ($hero = $item->images()->not('location.jpg')->sortBy('sort', 'asc')->first()) {
        $herourl = $hero->crop(300, 120)->url();
      }
    } else {
      $herourl = null;
    }
    
    if ($herourl) {
      $hero = '<div class="card-hero"><a href="' . $url . '"><img src="' . $herourl . '"></img></a></div>';
    } else {
      $hero = '';
    }
    
    if (preg_match("/[a-z]/i", $item->content()->title())){
      $title = $item->content()->title();
    } else {
      $title = null;
    }
    
    $text = $item->excerpt();
    
    $content = '<div class="card-content"><a href="' . $url . '"><h4>' . $title . '</h4></a><p>' . $text . '</p></div>';
    
    $date = '<span>' . date('M j Y', $item->dateCreated()) . '</span>';
    
    $details = '<div class="card-details">' . $date . '<a href="' . $url . '">Read &rarr;</a></div>';
    
    
    $html .= '<div class="card' . $color . '">' . $hero . $content . $details . '</div>';
    
  }
  
  $blab = '<div class="grid">' . $html . '</div>';
  echo $blab;
}

// full format
if ($format == 'full' OR $format == 'excerpt') {
  
  $html = '';
  
  foreach ($items as $item) {
    
    $color = ($item->color() != "") ? ' ' . $item->color() : "";
    $url = $item->url();
    
    if (preg_match("/[a-z]/i", $item->content()->title())){
      $title = $item->content()->title();
    } else {
      $title = null;
    }
    
    if ($format == 'excerpt') {
      $text = $item->text()->excerpt();
    } else {
      $text = $item->text()->kirbytext();
    }
    
    $meta = '<div class="meta"><a href="' . $url . '">' . date('M j Y', $item->dateCreated()) . '</a></div>';
    
    $hero = $item->hero();
    
    $content = '<h4><a href="' . $url . '">' . $title . '</a></h4>' . $meta . $hero . $text;
        
    
    $html .= '<div class="' . $item->color() . '">' . $content . '</div>';
    
  }
  
  echo '<div class="content-' . $format . '">' . $html . '</div>';        
}

// list format
if ($format == 'list') {
  
  //$start_year = date('Y', $items->first()->datePublished());
  
  //echo '<h2>' . $start_year . '</h2';
  
  $start_year = '';
  $first_year = true;
  
  foreach ($items as $item) {
    
    $item_date = date('M d', $item->dateCreated());
    $item_year = date('Y', $item->dateCreated());
    
    if ($item_year != $start_year) {
      $start_year = $item_year;
      
      if ($first_year == true) {
        echo '<h2>' . $item_year . '</h2><ul>';
        $first_year = false;
      } else {
        echo '</ul><h2>' . $item_year . '</h2><ul>';
      }
      
    }
    
    echo '<li>' . $item_date . ' - <a class="' . $item->color() . '" href="' . $item->url() . '">' . $item->title() . '</a></li>';
    
  }
  
  echo '</ul>';
}




// display pagination if enabled
if (isset($pagination)) {
  
  // Previous page
  if ($items->pagination()->hasPrevPage()) {
    $html = brick('a', 'Previous page');
    $html->attr('href', $items->pagination()->prevPageURL());
    $html->attr('rel', 'nofollow');
    echo $html;
  }
  
  // Next page
  if ($items->pagination()->hasNextPage()) {
    $html = brick('a', 'Next page');
    $html->attr('href', $items->pagination()->nextPageURL());
    $html->attr('rel', 'nofollow');
    echo $html;
  }

}
*/






























