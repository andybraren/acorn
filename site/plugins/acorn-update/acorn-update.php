<?php

/* ACORN CONTENT UPGRADE FILE
  Whenever a new version of Acorn is installed, every page in the site is
  checked by the upgrade process.
*/

/* DEPRECATED METHODS
  The field methods in this file override all new ones, allowing the script
  below to more easily grab field data and store them in the new format.
  Eventually these old methods will be removed from Acorn.
*/


$updatePage = false;

if (kirby()->request()->path() == '') {
  $page = site()->page('home');
} else {
  $page = site()->page(kirby()->request()->path());
}





// IF PAGE WAS LAST TOUCHED BEFORE THE UPDATE TIME, THEN UPDATE THE FILE

// Determine whether or not the page should be updated
if ($page) {
  
  if ($meta = yaml($page->meta())) {
    
    // If the page hasn't been modified since the upgrade, mark the page to be updated    
    $siteUpgradeTime = strtotime(site()->setting('info/acorn/upgraded'));
    if ($meta['date']['modified'] < $siteUpgradeTime) {
      $updatePage = true;
    }
    
  } else {
    $updatePage = true; // If the meta field doesn't even exist, then the page needs to be updated
  }
  
  // Safety while testing
  if ($page->uid() != 'update-test') {
    //$updatePage = false;
  }
  
}




if ($updatePage) {
  
  // Bring in the deprecated methods one last time
  require(kirby()->roots()->plugins() . '/acorn-update/methods-deprecated.php');
    
  /* TITLE FIELD */
  
  $newTitle = $page->content()->title();
  
  /* META FIELD */
  
  $newMeta = array();
    
  $authors = array();
  foreach ($page->authors() as $author) {
    $new = array();
    $new['username'] = $author->username();
    $new['description'] = getAuthorDescription($page, $author->username());
    array_push($authors, $new);
  }
  $newMeta['authors'] = $authors;
  
  $date = array();
  $date['created'] = date('Y-m-d H:i:s', getNewDateCreated($page));
  //$date['modified'] = date('Y-m-d H:i:s', $page->dateModified());
  $date['modified'] = date('Y-m-d H:i:s', time());
  $date['modifiedby'] = 'acorn';
  $date['published'] = getDatePublished($page);
  $date['updated'] = getNewDateUpdated($page);
  $date['start'] = getDateStart($page);
  $date['end'] = getDateEnd($page);
  $newMeta['date'] = $date;
  
  $related = array();
  $related['tags'] = getTags($page);
  $related['internal'] = getInternalLinks($page);
  $related['external'] = getExternalLinks($page);
  $newMeta['related'] = $related;
  
  $info = array();
  $info['subtitle'] = '';
  $info['description'] = '';
  $info['excerpt'] = getExcerpt($page);
  $newMeta['info'] = $info;
  
  $data = array();
  $data['likes'] = '';
  $data['dislikes'] = '';
  $data['requests'] = '';
  $data['subscribers'] = '';
  $data['registrants'] = getRegistrants($page);
  $data['attendees'] = getAttendees($page);
  $data['address'] = getAddress($page);
  $data['addressinfo'] = '';
  $data['hours'] = pageHours($page);
  $data['hoursinfo'] = pageHoursInfo($page);
  $newMeta['data'] = $data;
  
  /* SETTINGS FIELD */
  
  $setting = array();
  $setting['visibility'] = $page->visibility();
  $setting['theme'] = getTheme($page);
  $setting['toc'] = 'default';
  $setting['discussion'] = 'default';
  $setting['submissions'] = 'off';
  $setting['price'] = '';
  $setting['audio'] = '';
  $setting['hero'] = getPageHero($page);
  $newSettings = $setting;
  
  /* TEXT FIELD */
  
  $newText = $page->content()->text();
  
  $allClear = true;
  
  if ($allClear) {
    
    /* Delete all existing fields */
    $keys = array();
    foreach ($page->content()->toArray() as $key => $item ) {
      $keys[$key] = null;
    }
    $page->update($keys);
    
    //$newstuff = yaml::encode($newMeta);
    $newMeta = yaml::encode(str::parse($newMeta));
    $newSettings = yaml::encode(str::parse($newSettings));
    
    $page->update(array(
      'title' => $newTitle,
      'meta'  => $newMeta,
      'settings' => $newSettings,
      'text' => $newText
    ));
    
  }
  
  //go(site()->url() . '/flush');
  
}












function getDatePublished($page) {
  if ($page->datePublished()) {
    return date('Y-m-d H:i:s', $page->datePublished());
  } else {
    return '';
  }
}

function getDateStart($page) {
  if ($page->dateStart()) {
    return date('Y-m-d H:i:s', strtotime($page->dateStart()));
  } else {
    return '';
  }
}

function getDateEnd($page) {
  if ($page->dateEnd()) {
    return date('Y-m-d H:i:s', strtotime($page->dateEnd()));
  } else {
    return '';
  }
}



function pageHours($page) {
  if ($page->content()->hours()) {
    $part = str::split($page->hours(), '~');
    return (isset($part[0])) ? (string)$part[0] : '';
  }
}

function pageHoursInfo($page) {
  if ($page->content()->hours()) {
    $part = str::split($page->hours(), '~');
    return (isset($part[1])) ? (string)$part[1] : '';
  }
}

function getTags($page) {
  
  if ($page->tags() == '') {
    return '';
  }
  
  if ($page->tags() != '') {
    return implode(', ', $page->tags());
  }
  
  if ($page->content()->categories() != '') {
    return $page->content()->categories();
  }
}

function getInternalLinks($page) {
  
  $uids = array();
  
  if ($page->related() != null) {
    foreach ($page->related() as $item) {
      if ($uid = site()->index()->findByURI($item)) {
        array_push($uids, $uid);
      }
    }
    return implode(', ', $uids);
  } else {
    return '';
  }
  
}

function getExternalLinks($page) {
  
  $links = array();
  
  if ($page->links() != null) {
    foreach ($page->links() as $link) {
      $part = str::split($link, '==');
      $item = array();
      $item['label'] =$part[0];
      $item['url'] = $part[1];
      $item['icon'] = '';
      array_push($links, $item); 
    }
    return $links;
  } else {
    return '';
  }
  
}

function getExcerpt($page) {
  // Gets the first 300 characters, removes Markdown headings, converts the remainder to HTML, strips tags and encoded characters, and removes any (completed) kirbytags
  $temp = preg_replace("!(?=[^\]])\([a-z0-9_-]+:.*?\)!is", "", html::decode(markdown(preg_replace("/(#+)(.*)/", "", $page->text()->short(303)))));
  $temp = preg_replace( "/\r|\n/", " ", $temp); // Remove line breaks
  $temp = substr($temp, 0, -3); // Remove ... at the end
  return $temp;
}

function getRegistrants($page) {
  
  // Old method
  if ($page->content()->registered() != null) {
    return $page->content()->registered();
  } elseif ($page->registrants() != null) { // UserData method (never really used)
    return implode(', ', $page->registrants());
  } else {
    return '';
  }
  
}

function getAttendees($page) {
  
  // Old method
  if ($page->content()->attended() != null) {
    return $page->content()->attended();
  } elseif ($page->attendees() != null) { // UserData method (never really used)
    return implode(', ', $page->attendees());
  } else {
    return '';
  }
  
}

function getAddress($page) {
  if ($page->location() != null) {
    return $page->location();
  } else {
    return "";
  }
}

function getTheme($page) {
  if ($page->color() != null) {
    return $page->color();
  } else {
    return 'default';
  }
}

function getPageHero($page) {
  if ($page->content()->hero() != null and $page->content()->hero() != '') {
    return $page->content()->hero();
  } else {
    return '';
  }
}

// Author Description
// A temporary way to get a provided author's description for upgrade purposes
function getAuthorDescription($page, $username) {
  
  if (preg_match('/'. $username . ' ~ (.*),/', $page->content()->userdata(), $match)) {
    return $match[1];
  } else {
    return "";
  }
  
}

function getNewDateCreated($page) {
  
  $date = '';
  
  // TinkerTry
  if ($page->content()->date() != '') {
    $date = strtotime($page->content()->date());
  }
  
  // Maker
  if ($page->dateCreated()) {
    $date = $page->dateCreated();
  }
  
  return $date;
  
}

// Used only by TinkerTry to mark major updates
function getNewDateUpdated($page) {
  
  $date = '';
  if ($page->content()->updated() != '') {
    $date = strtotime($page->content()->updated());
    return date('Y-m-d H:i:s', $date);
  } else {
    return "";
  }
  
  
}





