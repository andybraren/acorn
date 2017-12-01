<?php
  
// Link Tag
// created by Andy Braren
// A kirbytag that displays nicely-formatted links to internal (and eventually external) pages

/* CHANGELOG
2016-11-15 - Initial creation. Focused on internal links, grabbing hero image, date, author, excerpt, title
*/

// link tag
kirbytext::$tags['link'] = array(
  'attr' => array(
    'title',
    'author',
    'date',
    'excerpt',
    'quote',
    'image',
  ),
  'html' => function($tag) {
    
    $url       = $tag->attr('link');
    $title     = $tag->attr('title');
    $author    = $tag->attr('author');
    $date      = $tag->attr('date');
    $excerpt   = $tag->attr('excerpt');
    $quote     = $tag->attr('quote');
    $image     = $tag->attr('image');
    
    $link = url($url);
    
    /*
    if(empty($excerpt)) {
      $title = $url;
    }
    */
    
    if(str::isURL($excerpt)) {
      $excerpt = url::short($excerpt);
    }
    
    $excerpt = '';
    $host = '';
    
    if ($quote) {
      $quote = '<blockquote>' . kirbytext($quote) . '</blockquote>';
    }
    
    if ($_SERVER['SERVER_NAME'] == parse_url($url, PHP_URL_HOST)) { // if the URL is an internal link
      
      $page = site()->page(trim(parse_url($url, PHP_URL_PATH), '/'));
      
      if ($page) {
        $title = $page->title();
        $excerpt = preg_replace("!(?=[^\]])\([a-z0-9_-]+:.*?\)!is", "", html::decode(markdown(preg_replace("/(#+)(.*)/", "", $page->text()->short(300)))));
        
        $date = date('M j Y', $page->datePublished());
        
        $host = $_SERVER['SERVER_NAME'] . ' - ';
        
        if ($page->heroImage()) {
          $image = $page->heroImage()->crop(170, 110);
        } else {
          $image = '';
        }
        
        
        //$author = a::first($page->authors()); // Get the first (primary) author of a page
        $author = $page->authors()->first();
        //$author = str::split($author,'~')[0]; // Strip out the author's role (if needed)
        if (site()->user($author)) {
          $author = site()->user($author);
          $author = $author->firstname() . ' ' . $author->lastname() . ' - ';
        }
      }
      
    } else {
      
      //$title = get_title("http://www.washingtontimes.com/");
      //$title = get_title($url);
      
      
      if ($author) {
        $author = $author . ' - ';
      }
      
      if ($date) {
        $date = ' - ' . date('M j Y', strtotime($date));
      }
      
      $host = parse_url($url, PHP_URL_HOST);
      if ($image and page() == site()->page(trim(parse_url($url, PHP_URL_PATH), '/'))) {
        $image = '<img src="' . page()->image($image)->crop(170, 110)->url() . '">';
      } else {
        $image = '';
      }
      
    }
    
    if ($quote) {
      $classes = 'link quote';
    } else {
      $classes = 'link';
    }
    
    $title = '<strong>' . $title . '</strong>';
    $info = '<span>' . $author . $host . $date . '</span>';
    
    if ($quote) {
      $html = '<div class="' . $classes . '"><a href="' . $url . '">' . $image . '<div>' . $title . '<span>' . $excerpt . '</span>' . $info . '</div></a>' . $quote . '</div>';
    } else {
      $html = '<div class="' . $classes . '"><a href="' . $url . '">' . $image . '<div>' . $title . '<span>' . $excerpt . '</span>' . $info . '</div></a></div>';
    }
    
    
    return $html;
    
    /*
    return html::a($link, $excerpt, array(
      'rel'    => $tag->attr('rel'),
      'class'  => $tag->attr('class'),
    ));
    */
    
  }
);

function domain($url) {
  $result = parse_url($url);
  return $result['scheme']."://".$result['host'];
}

function get_http_response_code($url) {
  $headers = get_headers($url);
  if ($headers !== false) {
    return substr($headers[0], 9, 3);
  }
  
}

function get_title($url){
  
  try {
    $str = file_get_contents($url);
    
    if ($str === false) {
      return 'failure';
    }
    
    if (get_http_response_code($url) != "200"){
      return "error";
    } else{
      return 'his';
    }
  } catch(Exception $ex) {
    return $ex;
  }
  


}

?>























