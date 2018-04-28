<?php
  
// Content tag
// display a collection of pages/items in a variety of formats and styles

// $items    - the items/pages that should be collected
// $format   - the arrangement desired (card, list, thumblist, excerpt, full, etc.)
// $style    - the look, which is ambiguous at the moment
// $type     - links, videos
// $sort     - the order in which items should be sorted
// $tags     - the tags that items need to have
// $number   - the number of items that should be returned, without pagination
// $paginate - the number of items that should be returned, with pagination

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
        
        if (site()->page($item)->hasChildren() != 0) {
          foreach(site()->page($item)->children() as $item) {
            $items->add($item);
          }
        } else {
          $items->add(site()->page($item));
        }
        
      }
    } else {
      $items = new Pages();
      if (site()->page($content)) {
        if (site()->page($content)->hasChildren() != 0) {
          foreach(site()->page($content)->children() as $item) {
            $items->add($item);
          }
        } else {
          $items->add($content);
        }
      }
    }
    
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
    
    // FILTER BY TAGS
    // (tags: tag1, tag2)
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
    
    // FILTER BY TAG URL PARAMETERS
    // ?tags=tag1,tag2,tag3
    if (isset($_GET['tags'])) {
      
      $urltags = filter_var($_GET['tags'], FILTER_SANITIZE_STRING);
      $urltags = explode(',', $urltags);
      
      $items = $items->filter(function($item) use($urltags)  {
        return array_intersect($item->tags(), $urltags);
      });
      
    }
    
    // FILTER BY SEARCH QUERY URL PARAMETER
    if (isset($_GET['q'])) {
      
      $query = filter_var($_GET['q'], FILTER_SANITIZE_STRING);
      
      $items = $items->filter(function($item) use($query)  {
        if (stripos($item->content()->text(), $query)) {
          return $item;
        }
      });
      
    }
    
    if (isset($_GET['types'])) {
      $type = filter_var($_GET['types'], FILTER_SANITIZE_STRING);
    } else {
      $type = null;
    }
    
    // FILTER BY TYPE
    // (type: links)
    if (isset($type)) {
      
      if ($type == 'drafts') {
        $temp = new Pages();
        foreach ($items as $item) {
          if (!$item->datePublished()) {
            $temp->add(site()->page($item));
          }
        }
        $items = $temp;
      }
      
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
    
    // FILTER BY DATE URL PARAMETER
    if (isset($_GET['date'])) {
      
      $parameter = filter_var($_GET['date'], FILTER_SANITIZE_STRING);
      $start = '';
      $end = '';
      
      // filter by date range
      if (strpos($parameter, ',')) {
        if (strpos($parameter, ',') >= 0) {
          $daterange = explode(',', $parameter);
          $start = strtotime($daterange[0]);
          $end = strtotime($daterange[1]);
        }
      }
      
      // filter by date shorthand
      else {
        switch ($parameter) {
          case 'pasthour':
            $start = strtotime('-1 hour');
            break;
          case 'past24hours':
            $start = strtotime('-24 hours');
            break;
          case 'pastweek':
            $start = strtotime('-1 week');
            break;
          case 'pastmonth':
            $start = strtotime('-1 month');
            break;
          case 'past3months':
            $start = strtotime('-3 months');
            break;
          case 'past6months':
            $start = strtotime('-6 months');
            break;
          case 'pastyear':
            $start = strtotime('-1 year');
            break;
          default:
            $start = '';
        }
      }
      
      // between these two dates
      // ?date=2018-01-01,2018-01-15
      if ($start and $end) {
        $items = $items->filter(function($item) use($start, $end, $type) {
          if (!$item->datePublished() && $type == 'drafts') {
            return $item;
          }
          elseif ($start <= $item->datePublished() && $item->datePublished() <= $end) {
            return $item;
          }
        });
      }
      
      // after this date
      // ?date=2018-01-01,
      elseif ($start and !$end) {
        $items = $items->filter(function($item) use($start, $type) {
          if (!$item->datePublished() && $type == 'drafts') {
            return $item;
          }
          elseif ($item->datePublished() >= $start) {
            return $item;
          }
        });
      }
      
      // before this date
      // ?date=,2018-01-15
      elseif (!$start and $end) {
        $items = $items->filter(function($item) use($end, $type) {
          if (!$item->datePublished() && $type == 'drafts') {
            return $item;
          }
          elseif ($item->datePublished() <= $end) {
            return $item;
          }
        });
      }
              
    }
    
    // PAGE VISIBILITY
    // Only include items that should be visible to the user
    $items = $items->filter(function($item) use($type) {
      if ($item->isShowableToUser()) {
        return $item;
      }
    });
    
    // LIMIT
    // (limit: 20)
    if (isset($number)) {
      $items = $items->limit($number);
    }
    
    // PAGINATE
    // (paginate: 25)
    if (isset($paginate)) {
      $items = $items->paginate($paginate);
      $pagination = true;
    } else {
      $pagination = false;
    }
    
    // OUTPUT HTML
    // based on format
    
    $html = '';
    
    // card format
    if ($format == 'card') {
      
      // Show the "Add new" link on non-search pages
      if (page()->uid() != 'search') {
        $addnew = (page()->isEditableByUser()) ? '<a href="' . page()->url() . '/new' . '">Add New</a>' : '';
      } else {
        $addnew = '';
      }
      
      foreach ($items as $item) {
        
        $item_title = $item->content()->title() ?? '';
        $item_text  = $item->excerpt();
        $item_color = ($item->color()) ? ' ' . $item->color() : '';
        $item_url   = $item->url();
        
        if ($hero = $item->heroImage()) {
          $herourl = $item->heroImage()->crop(300, 120)->url();
          $item_hero = '<div class="card-hero"><a href="' . $item_url . '"><img src="' . $herourl . '"></img></a></div>';
        } else {
          $item_hero = '';
        }
        
        $content = '<div class="card-content"><a href="' . $item_url . '"><h4>' . $item_title . '</h4></a><p>' . $item_text . '</p></div>';
        
        $date = '<span>' . date('M j Y', $item->dateCreated()) . '</span>';
        $details = '<div class="card-details">' . $date . '<a href="' . $item_url . '">Read &rarr;</a></div>';
        
        $html .= '<div class="card' . $item_color . '">' . $item_hero . $content . $details . '</div>';
        
      }
      
      echo '<div class="grid">' . $addnew . $html . '</div>';
    }
    
    // full format
    if ($format == 'full' OR $format == 'excerpt') {
      
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
        
        $meta = '<div class="meta"><a href="' . $url . '">' . date('M j Y', $item->datePublished()) . '</a></div>';
        
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
    if ($pagination) {
      
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
    
    
    
    
    
  }
);