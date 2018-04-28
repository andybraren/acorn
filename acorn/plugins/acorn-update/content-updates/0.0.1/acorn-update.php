<?php

// ACORN v0.0.1 UPDATE

// Bring in the deprecated methods one last time
require('methods-deprecated.php');

// TITLE FIELD
$newTitle = $page->content()->title();

// META FIELD
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
$date['modified'] = date('Y-m-d H:i:s', time());
$date['modifiedby'] = 'acorn';
$date['published'] = getNewDatePublished($page);
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

// SETTINGS FIELD

$setting = array();
$setting['visibility'] = getPageVisibility($page);
$setting['theme'] = getTheme($page);
$setting['toc'] = 'default';
$setting['discussion'] = 'default';
$setting['submissions'] = 'off';
$setting['price'] = '';
$setting['audio'] = '';
$setting['hero'] = getPageHero($page);
$newSettings = $setting;

// TEXT FIELD
$newText = cleanupText($page->content()->text());

// UPDATE THE PAGE

// Delete all existing fields and add the new ones
$keys = array();
foreach ($page->content()->toArray() as $key => $item ) {
  $keys[$key] = null;
}
$page->update($keys);

$newMeta = yaml::encode(str::parse($newMeta));
$newSettings = yaml::encode(str::parse($newSettings));

$page->update(array(
  'title' => $newTitle,
  'meta'  => $newMeta,
  'settings' => $newSettings,
  'text' => $newText
));

// Change the filename if it's anything other than page.txt
if ($page->name() != 'page') {
  rename($page->textfile(), $page->root() . DS . 'page.txt');
}

function cleanupText($string) {
  $text = convert_smart_quotes_and_dashes_and_spaces($string);
  $text = convert_dots($text);
  $text = convert_spans($text);
  $text = remove_bad_headings($text);
  return $text;
}

function convert_smart_quotes_and_dashes_and_spaces($string) {
  // http://shiflett.org/blog/2005/convert-smart-quotes-with-php
  // https://www.danshort.com/ASCIImap/
  // http://www.theasciicode.com.ar/extended-ascii-code/non-breaking-space-no-break-space-ascii-code-255.html
  // https://www.ascii.cl/htmlcodes.htm
  $search = array(chr(145),chr(146),chr(147),chr(148),chr(150),chr(151),'-',chr(8211),chr(255),chr(32),chr(020),chr(160));
  $replace = array("'","'",'"','"','-','-','-','-',' ',' ',' ',' ');
  
  $temp = str_replace($search, $replace, $string);
  
  $temp = str_replace('â€"', '–', $temp); // en dash
  
  return $temp;
}

function convert_dots($string) {
  $search = array(chr(133));
  $replace = array('...');
  return str_replace($search, $replace, $string);
}

function convert_spans($string) {
  $temp = str_replace('<span style="color:red">', '<mark class="red">', $string);
  $temp = str_replace('<span style="color:green">', '<mark class="green">', $temp);
  $temp = str_replace('</span>', '</mark>', $temp);
  return $temp;
}

function remove_bad_headings($string) {
  return preg_replace( "/(#+)\n/", "", $string); // Remove random broken headings
}

function getPageVisibility($page) {
  
  if ($page->visibility() != null) {
    return $page->visibility();
  }
  
  // TinkerTry and Thinkerbit drafts and posts
  elseif (strpos($page->uri(), 'drafts')) {
    return 'private';
  }
  
  else {
    return 'public';
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
  
  $tags = '';
  if ($page->tags() == '') {
    $tags = '';
  }
  
  if ($page->tags() != '') {
    $tags = implode(', ', $page->tags());
  }
  
  if ($page->content()->categories() != '') {
    $tags = $page->content()->categories();
  }
  return $tags;
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
      $item['label'] = $part[0];
      $item['url'] = $part[1];
      $item['icon'] = '';
      array_push($links, $item); 
    }
    return $links;
  } elseif ($page->content()->link() != '') { // Thinkerbit
    $item = array();
    $item['label'] = '';
    $item['url'] = $page->content()->link();
    $item['icon'] = '';
    array_push($links, $item);
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
  } elseif ($hero = $page->images()->findBy('name', 'featured')) {
    return $hero->filename();
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
  
  // TinkerTry and Thinkerbit
  if ($page->content()->date() != '') {
    
    // Strip any "at" left over from IFTTT on Thinkerbit pages
    $tempdate = str_replace(' at ','', $page->content()->date());
    
    $date = strtotime($tempdate);
  }
  
  // Maker
  if ($page->dateCreated()) {
    $date = $page->dateCreated();
  }
  
  return $date;
  
}

function getNewDatePublished($page) {
  
  if ($page->datePublished()) {
    return date('Y-m-d H:i:s', $page->datePublished());
  }
  
  // TinkerTry and Thinkerbit drafts and posts
  if ($page->content()->date() != '' and !strpos($page->uri(), 'drafts')) {
    $tempdate = str_replace(' at ','', $page->content()->date()); // Strip any "at" left over from IFTTT on Thinkerbit pages
    $date = strtotime($tempdate);
    return date('Y-m-d H:i:s', $date);
  }
  
  else {
    return "";
  }
}

function getDateStart($page) {
  if ($page->dateStart()) {
    return date('Y-m-d H:i:s', strtotime($page->dateStart()));
  }
  
  // Andy Braren site
  elseif ($page->content()->started() != '') {
    return $page->content()->started() . '-01-01 00:00:00';
  }
  
  else {
    return "";
  }
}

function getDateEnd($page) {
  if ($page->dateEnd()) {
    return date('Y-m-d H:i:s', strtotime($page->dateEnd()));
  }
  
  // Andy Braren site
  elseif ($page->content()->ended() == 'Present') {
    return "";
  }
  elseif ($page->content()->ended() != '') {
    return $page->content()->ended() . '-01-01 00:00:00';
  }
  
  else {
    return "";
  }
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