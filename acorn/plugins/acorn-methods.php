<?php
  
//==================================================
// SITE METHODS
// Simplifies retrieval of site settings and info
//==================================================

//--------------------------------------------------
// Site Setting
// site()->setting('connections/twitter/key')
//--------------------------------------------------

page::$methods['setting'] = function($page, $setting) {
  
  // If the settings being requested are not from site.txt, then just return the page's settings
  if ($page != site()) {
    return $page->content()->settings();
  } else {
    
    $settings = yaml(site()->content()->settings());
    
    $keys = explode('/', $setting);
    foreach ($keys as $key) {
      if (isset($settings[$key])) {
        $settings = $settings[$key];
      }
    }
    
    $setting = $settings;
    
    if (isset($setting) && !is_array($setting)) {
      return $setting;
    } else {
      return 'Incorrect setting syntax';
    }
  }
  
};

//--------------------------------------------------
// Site info
// site()->info('acorn/version')
//--------------------------------------------------

page::$methods['info'] = function($page, $setting) {
  
  if ($page != site()) {
    return $page->content()->info();
  } else {
    
    $settings = yaml(site()->content()->info());
    
    $keys = explode('/', $setting);
    foreach ($keys as $key) {
      if (isset($settings[$key])) {
        $settings = $settings[$key];
      }
    }
    
    $setting = $settings;
    
    if (isset($setting) && !is_array($setting)) {
      return $setting;
    } else {
      return 'Incorrect setting syntax';
    }
  }
  
};

//--------------------------------------------------
// Site colors
// site()->colors()
// Returns array of available site colors
//--------------------------------------------------

page::$methods['colors'] = function($page) {
  
  if ($page == site()) {
    
    $colors = explode(', ', site()->setting('style/color-options'));
    
    if (isset($colors) and $colors != '') {
      return $colors;
    } else {
      return 'Incorrect colors syntax';
    }
  }
  
};

/* The below also does it I guess, adding a site method, never new this was possible, have to figure out where I found this from
kirby()->set('site::method', 'setting', function($page, $setting) {
  if ($setting = 'ads') return yaml(site()->settings())['style']['default-color'];
});
*/

//==============================================================================
// PAGE META METHODS
//==============================================================================

//--------------------------------------------------
// Author Methods
//--------------------------------------------------

// Authors
// returns a collection of valid page authors
page::$methods['authors'] = function($page) {
  
  $collection = new Collection();
  
  if (!empty(yaml($page->meta())['authors'])) {
    $authors = yaml($page->meta())['authors'];
  } else {
    return $collection;
  }
  
  // Add each valid author to the collection and return them
  foreach ($authors as $author) {
    $username = $author['username'];
    if (site()->user($username)) {                  
      $collection->append($username, site()->user($username));
    }
  }
  
  return $collection;
  
};

// Has Authors
// returns whether or not the page has valid authors
page::$methods['hasAuthors'] = function($page) {
    
  if (!empty(yaml($page->meta())['authors'])) {
    return true;
  } else {
    return false;
  }
    
};

page::$methods['visibility'] = function($page) {
  
  if (!empty(yaml($page->settings())['visibility'])) {
    return yaml($page->settings())['visibility'];
  }
  
};

// Author Description
// $page (object) $username (string)
// returns the page-specific description for a specified author
function authorDescription($page, $username) {
  
  $description = "";
  
  $authors = yaml($page->meta())['authors'];
  
  foreach ($authors as $author) {
    if ($author['username'] == $username) {
      $description = $author['description'];
    }
  }
  
  return $description;
  
}

//--------------------------------------------------
// Date Methods
//--------------------------------------------------

// Date Created
// returns the date a page was first created
page::$methods['dateCreated'] = function($page) {
  if (!empty(yaml($page->meta())['date'])) {
    return strtotime(yaml($page->meta())['date']['created']);
  } else {
    return null;
  }
};

// Date Modified
// returns the most recent date the page was modified
page::$methods['dateModified'] = function($page) {
  if (!empty(yaml($page->meta())['date']['modified'])) {
    return strtotime(yaml($page->meta())['date']['modified']);
  } else {
    return null;
  }
};

// Modified By
// returns the username who last modified the page
page::$methods['modifiedBy'] = function($page) {
  return yaml($page->meta())['date']['modifiedby'];
};

// Date Published
// returns the date the page was first made non-private
page::$methods['datePublished'] = function($page) {
  if (!empty(yaml($page->meta())['date'])) {
    return strtotime(yaml($page->meta())['date']['published']);
  } else {
    return null;
  }
};

// Date Updated
// returns the date the page was significantly updated
page::$methods['dateUpdated'] = function($page) {
  if (!empty(yaml($page->meta())['date']['updated'])) {
    return strtotime(yaml($page->meta())['date']['updated']);
  } else {
    return null;
  }
};

// Start Date
// returns the start day and time of an event
page::$methods['dateStart'] = function($page) {  
  return strtotime(yaml($page->meta())['date']['start']);
};

// End Date
// returns the end day and time of an event
page::$methods['dateEnd'] = function($page) {
  return strtotime(yaml($page->meta())['date']['end']);
};

//--------------------------------------------------
// Related Methods
//--------------------------------------------------

// Tags
// returns an array of tags
page::$methods['tags'] = function($page) {
  if (!empty(yaml($page->meta())['related'])) {
    return str::split(yaml($page->meta())['related']['tags'],',');
  } else {
    return array();
  }
};
page::$methods['tagsraw'] = function($page) {
  return yaml($page->meta())['related']['tags'];
};

// Related "internal" pages
// returns an array of all related "internal" pages
page::$methods['related'] = function($page) {
  return yaml($page->meta())['related']['internal'];
};

// Related "external" links
// returns an array of all related "external" links
page::$methods['links'] = function($page) {
  if (!empty(yaml($page->meta())['related']['external'])) {
    return yaml($page->meta())['related']['external'];
  } else {
    return array();
  }
};

//--------------------------------------------------
// Info Methods
//--------------------------------------------------

page::$methods['subtitle'] = function($page) {
  return yaml($page->meta())['info']['subtitle'];
};

// For some reason using just "description()" to call this breaks something
page::$methods['pagedescription'] = function($page) {
  return yaml($page->meta())['info']['description'];
};

page::$methods['excerpt'] = function($page) {
  if (!empty(yaml($page->meta())['info'])) {
    return yaml($page->meta())['info']['excerpt'];
  } else {
    return '';
  }
};


//--------------------------------------------------
// Data Methods
//--------------------------------------------------

// Likes
// returns an array of usernames who "liked" the page
page::$methods['likes'] = function($page) {
  return yaml($page->meta())['data']['likes'];
};

// Dislikes
// returns an array of usernames who "disliked" the page
page::$methods['dislikes'] = function($page) {
  return yaml($page->meta())['data']['dislikes'];
};

// Requests
// returns an array of usernames who've asked to join an event/group/whatever
page::$methods['requests'] = function($page) {
  return yaml($page->meta())['data']['requests'];
};

// Subscribers
// returns an array of subscribed usernames
page::$methods['subscribers'] = function($page) {
  return yaml($page->meta())['data']['subscribers'];
};

// Registrants
// returns an array of event registrants
page::$methods['registrants'] = function($page) {
  return yaml($page->meta())['data']['registrants'];
};

// Attendees
// returns an array of event attendees
page::$methods['attendees'] = function($page) {
  return yaml($page->meta())['data']['attendees'];
};

// Address
// returns the address
page::$methods['address'] = function($page) {
  return yaml($page->meta())['data']['address'];
};

// Address Info
// returns the address info
page::$methods['addressInfo'] = function($page) {
  return yaml($page->meta())['data']['addressinfo'];
};

// Hours
// returns the hours
page::$methods['hours'] = function($page) {
  if (!empty(yaml($page->meta())['data'])) {
    return yaml($page->meta())['data']['hours'];
  } else {
    return '';
  }
};

// Hours Info
// returns the hours info
page::$methods['hoursInfo'] = function($page) {
  return yaml($page->meta())['data']['hoursinfo'];
};

// Rating
// returns the rating
page::$methods['rating'] = function($page) {
  return yaml($page->meta())['data']['rating'];
};

// Hero
// returns the hero content
page::$methods['hero'] = function($page) {
  
  if (!empty(yaml($page->meta())['data']['hero'])) {
    $hero = yaml($page->meta())['data']['hero'];
  }
  
  if ($page->heroType() == 'image') {
    return kirbytag(array('image' => $hero));
  }
  
  if ($page->heroType() == 'video-native' or $page->heroType() == 'video-embed') {
    return kirbytag(array('video' => $hero));
  }
  
};

// Hero type
// returns the type of the hero
page::$methods['heroType'] = function($page) {
  
  if (!empty(yaml($page->meta())['data']['hero'])) {
    $hero = yaml($page->meta())['data']['hero'];
  } else {
    $hero = '';
  }
  
  if ($file = $page->file($hero)) {
    if ($file->type() == 'image') {
      return 'image';
    }
  }
    
  if (str::contains($hero, 'mp4')) {
    return 'video-native';
  }
  
  if (str::contains($hero, 'youtu')) {
    return 'video-embed';
  }
  
  if (str::contains($hero, 'vimeo.com')) {
    return 'video-embed';
  }
  
};

// Hero image
// returns the first hero image (or first image) of a page
page::$methods['heroImage'] = function($page) {
  
  if (!empty(yaml($page->meta())['data']['hero'])) {
    $hero = yaml($page->meta())['data']['hero'];
  } else {
    $hero = '';
  }
  
  // Use the video thumbnail for hero videos
  if ($id = getVideoID($hero)) {
    $hero = 'video-' . $id;
    if ($blah = $page->images()->findBy('name', $hero)) {
      $hero = $blah->filename();
    }
  }
  
  if ($file = $page->file($hero)) {
    if ($file->type() == 'image') {
      return $file;
    } else {
      return null;
    }
  } elseif ($hero = $page->images()->findBy('name', 'hero')) {
    return $hero;
  } elseif ($page->hasImages()) {
    //return $page->images()->not('location.jpg')->sortBy('sort', 'asc')->first();
  } else {
    return null;
  }
  
};

// Hero images
// returns a collection of the page's hero images
page::$methods['heroImages'] = function($page) {

  if ($hero = $page->images()->findBy('name', 'hero')) {
    return $hero;
  } elseif ($page->hasImages()) {
    return $page->images()->sortBy('sort', 'asc')->first();
  } else {
    return null;
  }
  
};

// Icon
// returns the icon
page::$methods['icon'] = function($page) {
  return yaml($page->meta())['data']['icon'];
};

// Price
// returns the price
page::$methods['price'] = function($page) {
  if (!empty(yaml($page->meta())['data']['price'])) {
    return yaml($page->meta())['data']['price'];
  }
};

// Audio
// returns the audio
page::$methods['audio'] = function($page) {
  return yaml($page->meta())['data']['audio'];
};

//==============================================================================
// PAGE SETTINGS METHODS
//==============================================================================

// Visibility setting
// returns the page's visibility setting
page::$methods['visibility'] = function($page) {
  
  if (!empty(yaml($page->settings())['visibility'])) {
    return yaml($page->settings())['visibility'];
  } else {
    return null;
  }
  
};

// Title setting
// returns the page's title visibility setting
// can be visible, hidden
page::$methods['titleVisible'] = function($page) {
  
  if (!empty(yaml($page->settings())['title'])) {
    
    $setting = yaml($page->settings())['title'];
    
    if ($setting == 'default') {
      return site()->setting('layout/title');
    } elseif ($setting == 'visible') {
      return true;
    } elseif ($setting == 'hidden') {
      return false;
    } else {
      return false;
    }
    
  } else {
    return site()->setting('layout/title');
  }
  
};

// Sidebar Left setting
// returns the page's left sidebar setting
// can be default, enabled, disabled
page::$methods['sidebarLeft'] = function($page) {
    
  if (!empty(yaml($page->settings())['sidebar-left'])) {
    
    $setting = yaml($page->settings())['sidebar-left'];
    
    if ($setting == 'default') {
      return site()->setting('layout/sidebar-left');
    } elseif ($setting == 'enabled') {
      return true;
    } elseif ($setting == 'disabled') {
      return false;
    } else {
      return false;
    }
    
  } else {
    return site()->setting('layout/sidebar-left');
  }
  
};

// Sidebar Right setting
// returns the page's right sidebar setting
// can be default, enabled, disabled
page::$methods['sidebarRight'] = function($page) {
  
  if (!empty(yaml($page->settings())['sidebar-right'])) {
    
    $setting = yaml($page->settings())['sidebar-right'];
    
    if ($setting == 'default') {
      return site()->setting('layout/sidebar-right');
    } elseif ($setting == 'enabled') {
      return true;
    } elseif ($setting == 'disabled') {
      return false;
    } else {
      return false;
    }
    
  } else {
    return site()->setting('layout/sidebar-right');
  }
  
};

// Call To Action setting
// returns the page's call to action if available
// can be default, off, or a cta slug
page::$methods['cta'] = function($page) {
  
  if (!empty(yaml($page->settings())['sidebar-right'])) {
    
    $setting = yaml($page->settings())['sidebar-right'];
    
    if ($setting == 'default') {
      return site()->setting('layout/cta');
    } elseif ($setting == 'on') {
      return true;
    } elseif ($setting == 'off') {
      return false;
    } else {
      return false;
    }
    
  } else {
    return site()->setting('layout/cta');
  }
  
};

// Share setting
// returns the page's social media share icon setting
// can be default, on, off
page::$methods['share'] = function($page) {
  
  if (!empty(yaml($page->settings())['share'])) {
    
    $setting = yaml($page->settings())['share'];
    
    if ($setting == 'default') {
      return site()->setting('layout/share');
    } elseif ($setting == 'on') {
      return true;
    } elseif ($setting == 'off') {
      return false;
    } else {
      return false;
    }
    
  } else {
    return site()->setting('layout/share');
  }
  
};

// Color setting
// returns the page's color
page::$methods['color'] = function($page) {
  
  if (!empty(yaml($page->settings())['color'])) {
    $setting = yaml($page->settings())['color'];
    return $setting;
    /*
    if ($setting == 'default' OR $setting == '') {
      return site()->setting('style/default-color');
    } else {
      return $setting;
    }
    */
  } else {
    return site()->setting('style/default-color');
  }
  
};

// TOC setting
// returns the page's table of contents setting
// default, on, off
page::$methods['toc'] = function($page) {
  
  $setting = yaml($page->settings())['toc'];
  
  if ($setting == 'default') {
    return site()->setting('style/default-toc'); // returns true or false
  } elseif ($setting == 'on') {
    return true;
  } else {
    return false;
  }
};

// Discussion setting
// returns the page's discussion setting
// default on off (date) none
//   Default falls back to the parent page or site-wide setting if it's also default
//   On will always accept new comments unless overriden by the site-wide setting
//   Off will not accept new comments, but any existing comments will be visible
//   Date will stop accepting new comments after that date
//   None will not accept or display any comments whatsoever, hiding any that exist
page::$methods['discussion'] = function($page) {
  
  $setting = yaml($page->settings())['discussion'];
  
  // Check the site-wide override
  // These options need to be figured out and documented
  if (site()->setting('discussion/acorn/enabled') == false or site()->setting('discussion/acorn/enabled') == 'lockdown') {
    $enable = false;
  }
  
  if ($setting == 'default') {
    $enable = site()->setting('discussion/acorn/defaults/enabled');
  }
  
  if ($setting == 'on') {
    $enable = true;
  }
  
  if ($page->parent()->uri() != '') {
    $parentsetting = yaml($page->parent()->settings())['discussion'];
    if ($parentsetting == 'on-children') {
      $enable = true;
    }
  }
  
  return $enable;
  
};

// Submissions setting
// returns the page's current submission status
// on off (date)
page::$methods['submissions'] = function($page) {
  
  $setting = yaml($page->settings())['discussion'];
  
  // DEADLINE LOGIC NEEDED HERE
  
  if ($setting == 'on') {
    return true;
  } else {
    return false;
  }
};

//==================================================
// FILE METHODS
//==================================================

file::$methods['acornURL'] = function($file) {
  
  $url = $file->url();
  
  $url = acornPath($url);
  
  return $url;
  
};

function acornPath($string) {
  
  // Strip /content
  $path = str_replace('/content', '', $string);
  $path = str_replace('content/', '', $string);
  
  // Strip hidden directories (eventually specifiable)
  $path = str_replace('/posts', '', $path);
  $path = str_replace('posts/', '', $path);
  $path = str_replace('/users', '', $path);
  $path = str_replace('users/', '', $path);
  
  return $path;
  
}

//==============================================================================
// PERMISSIONS
//==============================================================================

page::$methods['isEditableByUser'] = function($page) {
  return isEditableByUser($page);
};

page::$methods['isSubmissibleByUser'] = function($page) {
  return isSubmissibleByUser($page);
};

page::$methods['isVisibleToUser'] = function($page) {
  if ($page->visibility() == 'unlisted') {
    return true;
  } else {
    return isVisibleToUser($page);
  }
};

page::$methods['isShowableToUser'] = function($page) {
  
  $showable = false;
  
  if (site()->user()) {
    
    // Show it to admins
    if (site()->user()->usertype() and site()->user()->usertype() == 'admin') {
      $showable = true;
    }
    
    // Show it to authors
    if (in_array(site()->user()->username(), $page->authors()->toArray())) {
      $showable = true;
    }
    
  }
  
  if (in_array($page->visibility(), array('public'))) {
    $showable = true;
  }
  
  return $showable;
};

pages::$methods['visibleToUser'] = function($pages) {  
  $collection = new Pages();
  foreach($pages as $page) {
    if (isVisibleToUser($page)) {
      $collection->add($page);
    }
  }
  return $collection;
};

function isVisibleToUser($page) {
  
  $isvisible = false;
  
  if ($page->visibility() != null) { // the page has a visibility setting
    
    if (in_array($page->visibility(), array('public'))) {
      $isvisible = true;
    }
    
    /*
    if (!site()->user() and in_array($page->visibility(), array('unlisted','groups','private'))) { // hide pages with these settings from the public
      $isvisible = false;
    }
    */
    
    if (site()->user()) { // hide these pages from logged-in users who don't have the right permissions
      if (!in_array(site()->user(), $page->authors()->toArray())) {
        if (!empty($page->relatedGroups())) {
          /*
          if (!array_intersect(str::split(site()->user()->groups()), $page->relatedGroups()->toArray())) {
            $isvisible = false;
          }
          */
        }
      }
    }
  }
  
  if (site()->user() and site()->user()->usertype() and site()->user()->usertype() == 'admin') { // show every page to admins
    $isvisible = true;
  }
  
  if (site()->setting('advanced/lockdown') == true AND !site()->user()) {
    $isvisible = false;
  }
  
  return $isvisible;
}

function isEditableByUser($page) {
  $isEditable = true;
  if (!site()->user()) { // if not logged in
    $isEditable = false;
  }
  if (site()->user()) { // if logged in but not one of the listed authors
    if (!in_array(site()->user(), $page->authors()->toArray())) {
      $isEditable = false;
    }
  }
  if (site()->user() and site()->user()->usertype() and site()->user()->usertype() == 'admin') { // if user is an admin
    $isEditable = true;
  }
  
  if ($page->uid() == 'error') {
    $isEditable = false;
  }
  
  if ($page->uid() == 'search') {
    $isEditable = false;
  }
  
  if (cookie::get('anonymousID')) {
    /*
    if (in_array(cookie::get('anonymousID'), $page->authors()->toArray())) {
      $isEditable = true;
    }
    */
    if (strpos($page->userdata(),cookie::get('anonymousID')) !== false) {
      $isEditable = true;
    }
  }
  
  if ($isEditable == true) {
    kirby()->set('option', 'photoswipe', 'on'); // Load PhotoSwipe in case they add images while editing
  }
  
  return $isEditable;
}

function isSubmissibleByUser($page) {
  
  $isSubmissible = false;
  
  if (isEditableByUser($page)) {
    $isSubmissible = true;
  }
  
  if ($page->submissions() == true and site()->user()) {
    $isSubmissible = true;
  }
  
  return $isSubmissible;
  
}

//==============================================================================
// MISC HELPER FUNCTIONS
//==============================================================================

function acornSanitize($string) {
  $temp = esc($string);
  return $temp;
}

// String to Excerpt
// Used when saving pages to create the excerpt field again
// Gets the first 300 characters, removes Markdown headings, converts the remainder to HTML, strips tags and encoded characters, and removes any (completed) kirbytags
// Used for page saving with just the new text
function stringToExcerpt($string) {
  $temp = $string;
  
  $temp = kirbytext($temp); // Turn into HTML
  $temp = strip_tags($temp); // Remove all HTML tags
  $temp = preg_replace( "/\r|\n/", " ", $temp); // Remove line breaks
  $temp = substr($temp, 0, 300); // Limit to 300 characters
  $temp = preg_match('/(.*)\s/', $temp, $matches); // Remove anything after the last complete word
  
  if (isset($matches[1])) {
    $temp = $matches[1];
  } else {
    $temp = "";
  }
  
  return $temp;
}


// Acorn Slugify
// my own take on Kirby's str::slug with a few opinionated tweaks
function acornSlugify($string) {
  
  $delete = array(
    '&quot;', // "
    '&#039;', // '‘
    '&lt;',   // <
    '&gt;',   // >
  );
  $string = str_replace($delete, '', $string);
  
  $hyphenate = array(
    ' ',
    '&amp;',  // &
    '~',
    '@',
    '*',
    '&',
    '+',
    '=',
    '>',
    '<',
    ' - ',
    '/',
    ' / ',
  );
  $string = str_replace($hyphenate, '-', $string);
  
  // I should probably switch to a whitelist rather than trying to blacklist every possible character
  $delete = array(
    '&quot;', // "
    '&#039;', // '
    '&lt;',   // <
    '&gt;',   // >
    '‘',      // apostrophe 1
    '’',      // apostrophe 2
    '“',      // curly quote 1
    '”',      // curly quote 2
    ':',
    '•',
    '¥',
    '£',
    '€',
    '_',
    '\\',
    '(',
    ')',
    '?',
    '.',
    '!',
    '$',
    ',',
    '%',
    '^',
    ';',
    '[',
    ']',
    '{',
    '}',
    '|',
    '`',
    '#',
    '--',
    '---',
    '"',
    "'",
  );
  $string = str_replace($delete, '', $string);
  
  // Need to delete HTML entities first, like &quot;, and there are probably more that should be added
  
  //$string = htmlspecialchars($string);
  $string = strtolower($string);
  
  // Remove leading and trailing separators
  $string = trim($string, '-');
  
  return $string;
  
}

// Acorn Textify
// get the raw text of a string containing anchor tags or Markdown links
function acornTextify($string) {
  
  if (strpos($string, 'a href="')) {
    $string = preg_replace('/<a.*?>(.*?)<\/a>/', '$1', $string);
  }
  
  if (preg_match('/\[(.*)]\((.*)\)/', $string)) {
    $string = preg_replace('/\[(.*)]\((.*)\)/', '$1', $string);
  }
  
  return trim($string);
  
}

// Get Video ID
// From either YouTube or Vimeo URLs
function getVideoID($url) {
  
  if (str::contains($url, 'youtu')) {
    if (preg_match("/^((https?:\/\/)?(w{0,3}\.)?youtu(\.be|(be|be-nocookie)\.\w{2,3}\/))((watch\?v=|v|embed)?[\/]?(?P<id>[a-zA-Z0-9-_]{11}))/si", $url, $matches)) {
      return $matches['id'];
    }
  }
  
  elseif (str::contains($url, 'vimeo.com')) {
    return substr(parse_url($url, PHP_URL_PATH), 1);
  }
  
  else {
    return null;
  }
  
}

function dir_contains_children($dir) {
  $result = false;
  if($dh = opendir($dir)) {
    while(!$result && ($file = readdir($dh)) !== false) {
      $result = $file !== "." && $file !== "..";
    }
    closedir($dh);
  }
  return $result;
}

// Comments
// returns the page's comments
page::$methods['comments'] = function($page) {
  
  if ($page->discussion()) {
    // If the comments folder doesn't exist, create it
    $target_dir = kirby()->roots()->content() . '/' . $page->uri() . '/comments';
    
    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0775, true);
      $collection = new Pages();
      return $collection;
    }
    
    if (dir_contains_children($target_dir)) {
      return $page->find('comments')->children();
    } else {
      $collection = new Pages();
      return $collection;
    }
  }
  
};

// Text Significant Difference detector
// Used to determine whether a string was "updated" or just simply "modified"
// depending on how different the two are. If the word "update" appears more or
// less in the two, then that's also considered significantly different
function textSigDiff($oldtext, $newtext) {
  
  similar_text($oldtext, $newtext, $percent);
  
  $changed = ($percent < 95) ? true : false;
  
  if (substr_count(strtolower($oldtext), 'update') != substr_count(strtolower($newtext), 'update')) {
    $updateDiff = true;
  } else {
    $updateDiff = false;
  }
  
  if ($changed == true or $updateDiff == true) {
    return true;
  } else {
    return false;
  }
  
}

// Is Feed Request
// Returns whether or not the request is coming from a JSON or RSS Feed, allowing
// tags like (image:) or (video:) to react accordingly
function isFeedRequest() {
  
  $format = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_EXTENSION);
  
  if (in_array($format, array('rss','xml','atom','json'))) {
    return true;
  } else {
    return false;
  }
  
}

//==================================================
// USER PREFERENCES
//==================================================

/* User Avatar image url
  - returns an avatar for the provided username
*/
function userAvatar($username, $size = 256) {
  
  if ($avatar = site()->user($username)->avatar()) {
    return $avatar->crop($size,$size)->url();
  } else {
    $number = 1;
    if     (strlen(site()->user($username)->firstname()) <= 3) { $number = 1; }
    elseif (strlen(site()->user($username)->firstname()) <= 4) { $number = 2; }
    elseif (strlen(site()->user($username)->firstname()) <= 6) { $number = 3; }
    elseif (strlen(site()->user($username)->firstname()) >= 7) { $number = 4; }
    $defaultavatar = new Asset('acorn/assets/images/avatar-' . $number . '.svg');
    return $defaultavatar->url();
  }
  
}
function groupLogo($groupname, $size = 256) {
  
  if (site()->page('groups/' . $groupname)) {
    if ($logo = site()->page('groups/' . $groupname)->images()->findBy('name', 'logo')) {
      return $logo->crop($size,$size)->url();
    } else {
      $number = 1;
      if     (strlen(site()->page('groups/' . $groupname)->title()) <= 3) { $number = 1; }
      elseif (strlen(site()->page('groups/' . $groupname)->title()) <= 4) { $number = 2; }
      elseif (strlen(site()->page('groups/' . $groupname)->title()) <= 6) { $number = 3; }
      elseif (strlen(site()->page('groups/' . $groupname)->title()) >= 7) { $number = 4; }
      
      $defaultlogo = new Asset('acorn/assets/images/avatar-' . $number . '.svg');
      return $defaultlogo->url();
    }
  }
  
  if (site()->page('courses/' . $groupname)) {
    if ($logo = site()->page('courses/' . $groupname)->images()->findBy('name', 'logo')) {
      return $logo->crop($size,$size)->url();
    } else {
      $number = 1;
      if     (strlen(site()->page('courses/' . $groupname)->title()) <= 3) { $number = 1; }
      elseif (strlen(site()->page('courses/' . $groupname)->title()) <= 4) { $number = 2; }
      elseif (strlen(site()->page('courses/' . $groupname)->title()) <= 6) { $number = 3; }
      elseif (strlen(site()->page('courses/' . $groupname)->title()) >= 7) { $number = 4; }
      
      $defaultlogo = new Asset('acorn/assets/images/avatar-' . $number . '.svg');
      return $defaultlogo->url();
    }
  }
  
}

/* User color
  - returns the user's color if set
*/
function userColor($username) {
  if (site()->user($username)->color() != "") {
    return (string)site()->user($username)->color();
  } else {
    return (string)site()->coloroptions()->split(',')[0];
  }
}
function groupColor($groupslug) {
  if (site()->page('groups/' . $groupslug)) {
    if (site()->page('groups/' . $groupslug)->color() != "") {
      return (string)site()->page('groups/' . $groupslug)->color();
    }
  }
  if (site()->page('courses/' . $groupslug)) {
    if (site()->page('courses/' . $groupslug)->color() != "") {
      return (string)site()->page('courses/' . $groupslug)->color();
    }
  }
  else {
    return (string)site()->coloroptions()->split(',')[0];
  }
}

/* User URL
  - returns the user's profile URL, sans /users/ directory
*/
function userURL($username) {
  return site()->url() . '/' . site()->user($username)->username();
}













/* Remote Image Downloader
  - stores a remote image locally and returns the image's URL
  - cannot return the image object because an error occurs, the image is not readable yet
*/
function downloadedImageURL($filename, $pageuri, $remoteURL) {
  
  $page = site()->page($pageuri);
  
  // If the image doesn't already exist, then it must be downloaded
  //if (!$page->image($filename . '.jpg')) {
  if (!$page->images()->findBy('name', $filename)) {
    if ($remoteURL == 'youtube') {
      $youtubeid = substr(strstr($filename, '-'), 1);
      $remoteURL = youtube_image($youtubeid);
    }
    if ($remoteURL == 'vimeo') {
      
      $vimeoid = substr(strstr($filename, '-'), 1);
      $vimeothumburl = "https://vimeo.com/api/v2/video/" . $vimeoid . ".php";
      $hash = unserialize(@file_get_contents($vimeothumburl));
      $vimeothumb = $hash[0]['thumbnail_large'];
      $remoteURL = $vimeothumb;
    }
    
    $extension = pathinfo($remoteURL, PATHINFO_EXTENSION);
    
    // strip any query parameters and lowercase
    $extension = strtolower(strtok($extension, '?'));
    
    // lowercase
    
    $imagepath = kirby()->roots()->content() . '/' . $page->diruri() . '/' . $filename . '.' . $extension;
    
    $response_code = get_http_response_code($remoteURL);
    
    if ($response_code == 200) {
      copy($remoteURL, $imagepath);
    } else {
      $imageURL = 'null';
    }
    
    /*
    if (get_headers($remoteURL)[0] == 'HTTP/1.0 200 OK') {
      copy($remoteURL, $imagepath);
    } elseif (get_headers($remoteURL)[0] == 'HTTP/1.0 200 OK') {
      copy($remoteURL, $imagepath);
    } else {
      $imageURL = 'null';
    }
    */
    
  } else {
    $extension = $page->images()->findBy('name', $filename)->extension();
  }
  
  if (!isset($imageURL)) {
    $imageURL = $page->contentURL() . '/' . $filename . '.' . $extension;
  }
  
  return $imageURL;
};

function get_http_response_code($url) {
  $headers = get_headers($url);
  return substr($headers[0], 9, 3);
}

function youtube_image($id) {
  $resolution = array (
    'maxresdefault',
    'mqdefault',
    'sddefault',
    'hqdefault',
    'default'
  );
  for ($x = 0; $x < sizeof($resolution); $x++) {
    $url = 'https://img.youtube.com/vi/' . $id . '/' . $resolution[$x] . '.jpg';
    if (get_headers($url)[0] == 'HTTP/1.0 200 OK') {
      break;
    }
  }
  return $url;
}



/* Ping
  - checks whether a page is up or not
  - useful way of triggering a page to generate its cache file if it doesn't already exist
*/
function ping($url) {
  $curl = curl_init();
  curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_POST => 0,
      CURLOPT_TIMEOUT => 5,
      CURLOPT_CONNECTTIMEOUT => 5,
      CURLOPT_RETURNTRANSFER => true,
      CURLINFO_HEADER_OUT => true,
      CURLOPT_NOBODY => 1,
  ));
  curl_exec($curl);
  $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);
  return ($httpcode>=200 && $httpcode<300) ? true : false;
}



/* New filterBy operator that's not case-sensitive
  - https://forum.getkirby.com/t/filterby-case-sensitive/3226/8
*/
collection::$filters['c*='] = function($collection, $field, $value, $split = false) {
  foreach($collection->data as $key => $item) {
    if($split) {
      $values = str::split((string)collection::extractValue($item, $field), $split);
      foreach($values as $val) {
        if(stripos($val, $value) === false) {
          unset($collection->$key);
          break;
        }
      }
    } else if(stripos(collection::extractValue($item, $field), $value) === false) {
      unset($collection->$key);
    }
  }
  return $collection;
};

/* Human Date
  - Used by comments and forum to create relative dates
  - http://stackoverflow.com/questions/2915864/php-how-to-find-the-time-elapsed-since-a-date-time
*/
function humanDate($date) {
  
  $time = time() - $date; // to get the time since that moment
  $time = ($time < 1) ? 1 : $time;
  $tokens = array (
    //31536000 => 'year',
    //2592000 => 'month',
    //604800 => 'week',
    86400 => 'day',
    3600 => 'hour',
    60 => 'minute',
    1 => 'second'
  );
  
  if ($time > (2592000*2)) { // over 2 months
    return date('M \'y', $date);
  }
  elseif ($time > 2592000) { // over 1 month
    return date('M j', $date);
  }
  elseif ($time > (604800*2)) { // over 2 weeks
    return date('M j', $date);
  }
  elseif ($time < 60) { // under 60 seconds
    return 'just now';
  }
  else {
    foreach ($tokens as $unit => $text) {
      if ($time < $unit) continue;
      $numberOfUnits = floor($time / $unit);
      return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
    }
  }
}

/* Milliseconds
  - returns the current millisecond time
*/
function milliseconds() {
  $milliseconds = round(microtime() * 1000);
  $milliseconds = sprintf('%03d', $milliseconds); // add a leading 0 if the number is less than 3 digits
  if ($milliseconds == 1000) {
  	$milliseconds = 000;
  };
  return $milliseconds;
}






function navArrayYAML() {
  return yaml(site()->MenuPrimary());
}

function navSecondaryArray() {
  $nav = array(
    array(
      'title' => 'Docs',
      'uid' => 'docs',
      'subtitle' => 'v0.2',
    ),
    array(
      'title' => 'Follow',
      'url' => 'https://www.facebook.com/groups/535093299989314',
    ),
    array(
      'title' => 'Contact',
      'url' => 'mailto:andy@acorn.blog',
    ),
  );
  return $nav;
}


function activeItem() {
  foreach (navArrayYAML() as $item) {
    
    // return top-level item
    if (site()->page($item['uid'])) {
      if (site()->page($item['uid'])->isOpen()) {
        return $item['uid'];
      }
    }
    
    // return sub-level item
    if (array_key_exists('sub', $item)) {
      foreach ($item['sub'] as $subitem) {
        if (site()->page($subitem['uid'])) {
          if (site()->page($subitem['uid'])->isOpen()) {
            return $subitem['uid'];
          }
        }
      }
    }
    
  }
}

function activeMenuItems() {
  
  $top = false;
  $sub = false;
  
  $uid = explode('/', $_SERVER['REQUEST_URI'])[1]; // works even on error pages
  
  foreach (navArrayYAML() as $item) {
    
    if (array_key_exists('uid', $item)) {
    
      // return top-level item
      if (site()->page($item['uid'])) {
        if (site()->page($item['uid'])->isOpen()) {
          //return $item['uid'];
          $top = $item['uid'];
        }
      }
      
      // invalid or missing pages
      elseif ($uid == $item['uid']) {
        $top = $item['uid'];
      }
      
      // return sub-level item
      if (array_key_exists('sub', $item)) {
        $hassub = true;
        foreach ($item['sub'] as $subitem) {
          
          if (array_key_exists('uid', $subitem)) {
            // valid pages
            if (site()->page($subitem['uid'])) {
              if (site()->page($subitem['uid'])->isOpen()) {
                //return $subitem['uid'];
                $sub = $subitem['uid'];
                $top = $item['uid'];
              }
            }
            
            // invalid or missing pages
            elseif ($uid == $subitem['uid']) {
              $sub = $subitem['uid'];
              $top = $item['uid'];
            }
          }
          
        }
      }
    
    }
    
  }
  
  return array($top, $sub);
}

function hasSubMenu() {
  
  $activeTop = activeMenuItems()[0];
  
  if ($activeTop != '') {
    
    // http://stackoverflow.com/questions/7694843/using-array-search-for-multi-dimensional-array
    $key = array_search($activeTop, array_column(navArrayYAML(), 'uid'));
    
    if (array_key_exists('sub', navArrayYAML()[$key])) {
      return true;
    } else {
      return false;
    }
  }
  
}


function submenuItems() {
  
  $activeTop = activeMenuItems()[0];
  $key = array_search($activeTop, array_column(navArrayYAML(), 'uid'));
  if (array_key_exists('sub', navArrayYAML()[$key])) {
    return navArrayYAML()[$key]['sub'];
  } else {
    return false;
  }
  
}
























