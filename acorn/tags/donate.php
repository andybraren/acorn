<?php
  
// Donate Tag
// created by Andy Braren
// A kirbytag that embeds a Stripe Checkout form that can be used for donations

/* CHANGELOG
2016-11-15 - Initial creation
*/

// link tag
kirbytext::$tags['donate'] = array(
  'attr' => array(
    'title',
    'author',
    'date',
    'excerpt',
  ),
  'html' => function($tag) {
    
    $url       = $tag->attr('link');
    $title     = $tag->attr('title');
    $author    = $tag->attr('author');
    $date      = $tag->attr('date');
    $excerpt   = $tag->attr('excerpt');
    
    $link = url($url);
    
    if(empty($excerpt)) {
      $title = $url;
    } 
    
    if(str::isURL($excerpt)) {
      $excerpt = url::short($excerpt);
    }
    
    $author = '';
    $date = '';
    $excerpt = '';
    $host = '';
    $image = '';
    
    if ($_SERVER['SERVER_NAME'] == parse_url($url, PHP_URL_HOST)) { // if the URL is an internal link
      
      $page = site()->page(trim(parse_url($url, PHP_URL_PATH), '/'));
      
      $title = $page->title();
      $excerpt = preg_replace("!(?=[^\]])\([a-z0-9_-]+:.*?\)!is", "", html::decode(markdown(preg_replace("/(#+)(.*)/", "", $page->text()->short(300)))));
      
      $date = date('M j Y', strtotime($page->datePublished()));
      
      $host = $_SERVER['SERVER_NAME'];
      
      $image = '<div>' . $page->heroImage()->crop(150) . '</div>';
      
      $author = a::first($page->authors());
      if (site()->user($author)) {
        $author = site()->user($author);
        $author = $author->firstname() . ' ' . $author->lastname();
      }
    } else {
      
      //$title = get_title("http://www.washingtontimes.com/");
      
    }
    
    
    $html = '<div><a class="link" href="' . $url . '"><div><strong>' . $title . '</strong><span>' . $excerpt . '</span><span>' . $author . ' - ' . $host . ' - ' . $date . '</span></div>' . $image . '</a></div>';
    
    
    
    return $html;
    
    /*
    return html::a($link, $excerpt, array(
      'rel'    => $tag->attr('rel'),
      'class'  => $tag->attr('class'),
    ));
    */
    
  }
);


?>