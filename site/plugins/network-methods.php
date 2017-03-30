<?php

//==================================================
// PAGE DATA METHODS
// https://makernetwork.org/docs#page-data
//==================================================

//--------------------------------------------------
// DateData Methods
//--------------------------------------------------

// Date Created
// returns the date a page was first created
page::$methods['dateCreated'] = function($page) {
  if ($page->content()->created() != '') {
    return strtotime($page->content()->created());
  }
  elseif ($page->content()->datedata() != '') {
    if (isset(str::split($page->content()->datedata(),',')[0])) {
      return strtotime(str::split($page->content()->datedata(),',')[0]);
    }
  }
};

// Date Modified
// returns the most recent date the page was modified
page::$methods['dateModified'] = function($page) {
  
  if ($page->content()->datedata() != '') {
    
    $array = $page->content()->datedata()->split(',');
    
    if (isset($array[1])) { // if date modified exists
      $parts = explode('==', $array[1]);
      return (isset($parts[0])) ? strtotime($parts[0]) : ''; // return just the date
    }
    
  }
  elseif ($page->content()->modified() != '') { // legacy content format
    return strtotime($page->content()->modified());
  }
  
};

// Modified By
// returns the username who last modified the page
page::$methods['modifiedBy'] = function($page) {
  if ($page->content()->datedata() != '') {
    if (isset(str::split(str::split($page->content()->datedata(),',')[1],'==')[1])) {
      return str::split(str::split($page->content()->datedata(),',')[1],'==')[1];
    }
  }
};

// Date Published
// returns the date the page was first made non-private
page::$methods['datePublished'] = function($page) {
  if ($page->content()->datedata() != '') {
    if (isset(str::split($page->content()->datedata(),',')[2])) {
      return strtotime(str::split($page->content()->datedata(),',')[2]);
    } elseif (isset(str::split($page->content()->datedata(),',')[0])) {
      return strtotime(str::split($page->content()->datedata(),',')[0]);
    }
  }
};

// Start Date
// returns the start day and time of an event
page::$methods['dateStart'] = function($page) {
  if ($page->content()->startdate() != '') {
    return $page->content()->startdate();
  }
  elseif ($page->content()->datedata() != '') {
    if (isset(str::split($page->content()->datedata(),',')[3])) {
      return str::split($page->content()->datedata(),',')[3];
    }
  }
};

// End Date
// returns the end day and time of an event
page::$methods['dateEnd'] = function($page) {
  if ($page->content()->enddate() != '') {
    return $page->content()->enddate();
  }
  elseif ($page->content()->datedata() != '') {
    if (isset(str::split($page->content()->datedata(),',')[4])) {
      return str::split($page->content()->datedata(),',')[4];
    }
  }
};

//--------------------------------------------------
// UserData Methods
//--------------------------------------------------

// Authors
// returns an array of active author usernames (with roles separated by ~)
page::$methods['authorsold'] = function($page) {
  if ($page->content()->makers() != '') {
    return $page->content()->makers()->split(',');
  }
  elseif ($page->content()->userdata() != '') {
    if (isset(explode('///',$page->content()->userdata())[0])) {
      return str::split(explode('///',$page->content()->userdata())[0],',');
    }
  }
  else {
    return array();
  }
};



// Still new to OOP, but this creates a new collection with the same functionality as $collection

// Authors New
// 
page::$methods['authors'] = function($page) {
  
  // Create an array of authors
  if ($page->content()->makers() != '') {
    $authors = $page->content()->makers()->split(',');
  }
  elseif ($page->content()->userdata() != '') {
    if (isset(explode('///',$page->content()->userdata())[0])) {
      $authors = str::split(explode('///',$page->content()->userdata())[0],',');
    }
  } else {
    $authors = array();
  }
  
  $collection = new Collection();
  
  // Add each valid author to the collection and return them
  foreach ($authors as $author) {
    if (site()->user($author)) {
      $collection->append($author, site()->user($author));
    }
    if (strpos($author,'anonymous')) {
      $collection->append($author, $author);
    }
  }
  
  return $collection;
  
};

page::$methods['authorsRaw'] = function($page) {
  
  // Create an array of authors
  $authors = explode('///',$page->content()->userdata())[0];
  
  return $authors;
  
};







// Old Authors
// returns an array of old/retired author usernames (with roles separated by ~)
page::$methods['oldauthors'] = function($page) {
  if ($page->content()->userdata() != '') {
    if (isset(explode('///',$page->content()->userdata())[1])) {
      return str::split(explode('///',$page->content()->userdata())[1],',');
    }
  }
};

// Subscribers
// returns an array of subscribed usernames
page::$methods['subscribers'] = function($page) {
  if ($page->content()->userdata() != '') {
    if (isset(explode('///',$page->content()->userdata())[2])) {
      return str::split(explode('///',$page->content()->userdata())[2],',');
    }
  }
};

// Subscriber Emails
// returns an array of subscribed (non-user) email addresses
page::$methods['subscriberEmails'] = function($page) {
  if ($page->content()->userdata() != '') {
    if (isset(explode('///',$page->content()->userdata())[3])) {
      return str::split(explode('///',$page->content()->userdata())[3],',');
    }
  }
};

// Event Registrants
// returns an array of event registrants
page::$methods['registrants'] = function($page) {
  if ($page->content()->userdata() != '') {
    if (isset(explode('///',$page->content()->userdata())[4])) {
      return str::split(explode('///',$page->content()->userdata())[4],',');
    }
  }
};

// Event Attendees
// returns an array of event attendees
page::$methods['attendees'] = function($page) {
  if ($page->content()->userdata() != '') {
    if (isset(explode('///',$page->content()->userdata())[5])) {
      return str::split(explode('///',$page->content()->userdata())[5],',');
    }
  }
};

// Membership Requests
// returns an array of usernames who've asked to join an event/group
page::$methods['requests'] = function($page) {
  if ($page->content()->userdata() != '') {
    if (isset(explode('///',$page->content()->userdata())[6])) {
      return str::split(explode('///',$page->content()->userdata())[6],',');
    } else {
      return array();
    }
  } else {
    return array();
  }
};

//--------------------------------------------------
// RelData Methods
//--------------------------------------------------

// Related "internal" pages
// returns an array of all related "internal" pages
page::$methods['related'] = function($page) {
  if ($page->content()->reldata() != '') {
    if (isset(explode('///',$page->content()->reldata())[0])) {
      return str::split(explode('///',$page->content()->reldata())[0],',');
    } else {
      return array();
    }
  } else {
    return array();
  }
};

// Related "internal" pages
// returns a collection of all related "internal" pages
page::$methods['relatedPosts'] = function($page) {
  
  $collection = new Pages();
  
  if ($page->related()) {
    foreach ($page->related() as $item) {
      if ($result = site()->page('posts/' . $item)) {
        $collection->add($result);
      }
    }
  }
  
  return $collection;
  
};

// Related Projects
// returns a collection of related "project" pages only
page::$methods['relatedProjects'] = function($page) {
  
  $collection = new Pages();
  
  if ($page->related()) {
    foreach ($page->related() as $item) {
      if ($result = site()->page('projects/' . $item)) {
        $collection->add($result);
      }
    }
  }
  
  return $collection;
  
};

// Related Groups
// returns an array of related "group" pages only
page::$methods['relatedGroups'] = function($page) {
  
  $collection = new Pages();
  
  if ($items = $page->related()) {
    foreach ($items as $item) {
      if ($result = site()->page('groups/' . $item)) {
        $collection->add($result);
      }
      if ($result = site()->page('courses/' . $item)) {
        $collection->add($result);
      }
    }
  }
  
  return $collection;

};

// Related Events
// returns a collection of related "event" pages only
page::$methods['relatedEvents'] = function($page) {
  
  $collection = new Pages();
  
  if ($page->related()) {
    foreach ($page->related() as $item) {
      if ($result = site()->page('events/' . $item)) {
        $collection->add($result);
      }
    }
  }
  
  return $collection;
  
};









// "External" links
// returns an array of titled external links
page::$methods['links'] = function($page) {
  if ($page->content()->links() != null and $page->content()->links() != '') {
    return $page->content()->links()->split(',');
  }
  elseif ($page->content()->reldata() != null and $page->content()->reldata() != '') {
    if (isset(explode('///',$page->content()->reldata())[1])) {
      return str::split(explode('///',$page->content()->reldata())[1],',');
    }
  }
};

// Tags
// returns an array of tags
page::$methods['tags'] = function($page) {
  if ($page->content()->reldata() != null and $page->content()->reldata() != '') {
    if (isset(explode('///',$page->content()->reldata())[2])) {
      return str::split(explode('///',$page->content()->reldata())[2],',');
    }
  }
};

// Likes
// returns an array of usernames who "liked" the page
page::$methods['likes'] = function($page) {
  if ($page->content()->reldata() != null and $page->content()->reldata() != '') {
    if (isset(explode('///',$page->content()->reldata())[3])) {
      return str::split(explode('///',$page->content()->reldata())[3],',');
    }
  }
};

// Votes
// returns an array of usernames who voted for the page
page::$methods['votes'] = function($page) {
  if ($page->content()->reldata() != null and $page->content()->reldata() != '') {
    if (isset(explode('///',$page->content()->reldata())[4])) {
      return str::split(explode('///',$page->content()->reldata())[4],',');
    }
  }
};

//--------------------------------------------------
// Settings Methods
//--------------------------------------------------

// Visibility
// returns the page's visibility
page::$methods['visibility'] = function($page) {
  if ($page->content()->visibility() != '') {
    return $page->content()->visibility();
  }
  elseif ($page->content()->settings() != '') {
    if (isset(explode(',',$page->content()->settings())[0])) {
      return trim(explode(',',$page->content()->settings())[0]);
    }
  }
};

// Color
// returns the page's color
page::$methods['color'] = function($page) {
  if ($page->content()->color() != '') {
    return $page->content()->color();
  }
  elseif ($page->content()->settings() != '') {
    if (isset(explode(',',$page->content()->settings())[1])) {
      return trim(explode(',',$page->content()->settings())[1]);
    }
  } else {
    return null;
  }
};


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
  
  if ($page->content()->settings() != '') {
    
    $array = $page->content()->settings()->split(',');
    if (isset($array[2])) { // if comment setting exists
      $parts = explode('==', $array[2]);
      if (isset($parts[1])) {
        
        //return ($parts[1] == 'on') ? true : false; // return just the setting on/off
        //return trim($parts[1], ' ');
        $blah = str_replace(' ', '', $parts[1]);
        
        if ($blah == 'on') {
          
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
      }
    }
    
  }
  
};

// Submissions
// returns the page's submissions
page::$methods['submissions'] = function($page) {
  if ($page->content()->settings() != '') {
    if (isset(str::split($page->content()->settings(),',')[3])) {                           // check if submissions setting is present
      if (isset(str::split(str::split($page->content()->settings(),',')[3],'==')[1])) {     // check if submissions setting is set
        if (str::split(str::split($page->content()->settings(),',')[3],'==')[1] != 'off') { // check if submissions are not off
          return str::split(str::split($page->content()->settings(),',')[3],'==')[1];       // return "on" or whatever it is
          // Eventually return an array of the actual submission objects themselves
        }
      }
    }
  }
};

// Price
// returns the event/page's price
page::$methods['price'] = function($page) {
  if ($page->content()->settings() != '') {
    if (isset(str::split($page->content()->settings(),',')[4])) {                           // check if price setting is present
      if (isset(str::split(str::split($page->content()->settings(),',')[4],'=')[1])) {      // check if price setting is set
        if (str::split(str::split($page->content()->settings(),',')[4],'=')[1] != 'off') {  // check if price is not off
          return str::split(str::split($page->content()->settings(),',')[4],'=')[1];        // return the price
        }
      }
    }
  }
};

// Price Description
// returns the event/page's price for item description
page::$methods['priceDescription'] = function($page) {
  if ($page->content()->settings() != '') {
    if (isset(str::split($page->content()->settings(),',')[4])) {                           // check if price setting is present
      if (isset(str::split(str::split($page->content()->settings(),',')[4],'=')[2])) {      // check if price setting is set
        if (str::split(str::split($page->content()->settings(),',')[4],'=')[2] != null) {   // check if price is not off
          return str::split(str::split($page->content()->settings(),',')[4],'=')[2];        // return the price
        }
      }
    }
  }
};

// Equipment Status
// returns the event/page's price
page::$methods['price'] = function($page) {
  if ($page->content()->settings() != '') {
    if (isset(str::split($page->content()->settings(),',')[4])) {                           // check if price setting is present
      if (isset(str::split(str::split($page->content()->settings(),',')[4],'==')[1])) {     // check if price setting is set
        if (str::split(str::split($page->content()->settings(),',')[4],'==')[1] != 'off') { // check if price is not off
          return str::split(str::split($page->content()->settings(),',')[4],'==')[1];       // return the price
        }
      }
    }
  }
};

//--------------------------------------------------
// Hero Methods
//--------------------------------------------------

// Hero image
// returns the first hero image (or first image) of a page
page::$methods['heroImage'] = function($page) {

  if ($file = $page->file($page->content()->hero())) {
    if ($file->type() == 'image') {
      return $file;
    } else {
      return null;
    }    
  } elseif ($hero = $page->images()->findBy('name', 'hero')) {
    return $hero;
  } elseif ($page->hasImages()) {
    return $page->images()->not('location.jpg')->sortBy('sort', 'asc')->first();
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



//==================================================
// USER DATA METHODS
// https://makernetwork.org/docs#user-data
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
    $defaultavatar = new Asset('/assets/images/avatar-' . $number . '.svg');
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
      
      $defaultlogo = new Asset('/assets/images/avatar-' . $number . '.svg');
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
      
      $defaultlogo = new Asset('/assets/images/avatar-' . $number . '.svg');
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

//--------------------------------------------------
// Permissions
//--------------------------------------------------

page::$methods['isVisibleToUser'] = function($page) {
  //if (array_intersect(array('unlisted'),$page->visibility()->split(','))) {
  if ($page->visibility() == 'unlisted') {
    return true;
  } else {
    return isVisibleToUser($page);
  }
};

page::$methods['isEditableByUser'] = function($page) {
  /*
  if (!site()->user()) {
    return false;
  } else {
  */
    return isEditableByUser($page);
  /*
  }
  */
};

page::$methods['isSubmissibleByUser'] = function($page) {
  return isSubmissibleByUser($page);
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


page::$methods['groupss'] = function($user) {
  return 'blah, bhsh';

};

function isVisibleToUser($page) {
  $isvisible = true;
  if ($page->visibility() != null) { // the page has a visibility setting
    
    if (!site()->user() and in_array($page->visibility(), array('unlisted','groups','private'))) { // hide pages with these settings from the public
      $isvisible = false;
    }
    
    if (site()->user()) { // hide these pages from logged-in users who don't have the right permissions
      if (in_array($page->visibility(), array('public'))) {
        $isvisible = true;
      }
      elseif (!in_array(site()->user(), $page->authors()->toArray())) {
        if (!empty($page->relatedGroups())) {
          if (!array_intersect(str::split(site()->user()->groups()), $page->relatedGroups()->toArray())) {
            $isvisible = false;
          }
        }
      }
    }
    
    if (site()->user() and site()->user()->usertype() and site()->user()->usertype() == 'admin') { // show every page to admins
      $isvisible = true;
    }
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
  
  return $isEditable;
}

function isSubmissibleByUser($page) {
  
  $isSubmissible = false;
  
  if (isEditableByUser($page)) {
    $isSubmissible = true;
  }
  
  if ($page->submissions() == 'on' and site()->user()) {
    $isSubmissible = true;
  }
  
  if ($page->submissions() == 'public') {
    $isSubmissible = true;
  }
  
  return $isSubmissible;
  
}











/* Remote Image Downloader
  - stores a remote image locally and returns the image's URL
  - cannot return the image object because an error occurs, the image is not readable yet
*/
function downloadedImageURL($filename, $remoteURL) {
  if (!page()->image($filename . '.jpg')) {
    if ($remoteURL == 'youtube') {
      $youtubeid = substr(strstr($filename, '-'), 1);
      $remoteURL = youtube_image($youtubeid);
      $imagepath = kirby()->roots()->content() . '/' . page()->diruri() . '/' . $filename . '.jpg';
    }
    copy($remoteURL, $imagepath);
  }
  $imageURL = page()->contentURL() . '/' . $filename . '.jpg';
  return $imageURL;
};

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



//==================================================
// CUSTOM FUNCTIONS
// https://makernetwork.org/docs
//==================================================

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










/*
function navArray() {
  $nav = array(
    array(
      'title' => 'Learn',
      'uid' => 'learn',
      'sub' => array(
        array(
          'title' => 'Courses',
          'uid' => 'courses',
        ),
        array(
          'title' => 'Handbooks',
          'uid' => 'handbooks',
        ),
        array(
          'title' => 'Books',
          'uid' => 'books',
        ),
      ),
    ),
    array(
      'title' => 'Make',
      'uid' => 'make',
      'sub' => array(
        array(
          'title' => 'Ideas',
          'uid' => 'ideas',
        ),
        array(
          'title' => 'Projects',
          'uid' => 'projects',
        ),
        array(
          'title' => 'Challenges',
          'uid' => 'challenges',
        ),
        array(
          'title' => 'Materials',
          'uid' => 'materials',
        ),
      ),
    ),
    array(
      'title' => 'Connect',
      'uid' => 'connect',
      'sub' => array(
        array(
          'title' => 'Articles',
          'uid' => 'articles',
        ),
        array(
          'title' => 'Makers',
          'uid' => 'makers',
        ),
        array(
          'title' => 'Groups',
          'uid' => 'groups',
        ),
      ),
    ),
    array(
      'title' => 'Spaces',
      'uid' => 'spaces',
    ),
    array(
      'title' => 'Equipment',
      'uid' => 'equipment',
    ),
    array(
      'title' => 'Events',
      'uid' => 'events',
    ),
    array(
      'title' => 'Forum',
      'uid' => 'forum',
      'subtitle' => 'alpha',
    ),
  );
  return $nav;
}
*/

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
































