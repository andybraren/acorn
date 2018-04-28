<?php

// ACORN v0.0.2 UPDATE
  
// Bring in the deprecated methods one last time
require('methods-deprecated.php');
  
// TITLE FIELD
$newTitle = $page->content()->title();

// META FIELD
$newMeta = array();

$newMeta['authors'] = yaml($page->meta())['authors'];

$newMeta['date'] = yaml($page->meta())['date'];
$newMeta['date']['modified'] = date('Y-m-d H:i:s', time());
$newMeta['date']['modifiedby'] = 'acorn';

$newMeta['related'] = yaml($page->meta())['related'];

$newMeta['info'] = yaml($page->meta())['info'];

$data = array();
$data['likes']       = yaml($page->meta())['data']['likes'];
$data['dislikes']    = yaml($page->meta())['data']['dislikes'];
$data['requests']    = yaml($page->meta())['data']['requests'];
$data['subscribers'] = yaml($page->meta())['data']['subscribers'];
$data['registrants'] = yaml($page->meta())['data']['registrants'];
$data['attendees']   = yaml($page->meta())['data']['attendees'];
$data['address']     = yaml($page->meta())['data']['address'];
$data['addressinfo'] = yaml($page->meta())['data']['addressinfo'];
$data['hours']       = yaml($page->meta())['data']['hours'];
$data['hoursinfo']   = yaml($page->meta())['data']['hoursinfo'];
$data['rating']      = "";
$data['hero']        = yaml($page->settings())['hero'] ?? yaml($page->meta())['data']['hero'];
$data['icon']        = "";
$data['price']       = yaml($page->settings())['price'] ?? yaml($page->meta())['data']['price'];
$data['audio']       = "";
$newMeta['data'] = $data;

// SETTINGS FIELD
$setting = array();
$setting['visibility']  = yaml($page->settings())['visibility'];
$setting['color']       = $page->theme() ?? $page->color();
$setting['hero-color']  = "";
$setting['hero-style']  = "";
$setting['toc']         = yaml($page->settings())['toc'];
$setting['discussion']  = yaml($page->settings())['discussion'];
$setting['submissions'] = yaml($page->settings())['submissions'];
$newSettings = $setting;

// TEXT FIELD
$newText = $page->content()->text();

// UPDATE THE PAGE
// Delete all existing fields and add the new ones
$keys = array();
foreach ($page->content()->toArray() as $key => $item ) {
  $keys[$key] = null;
}
$page->update($keys);

// Set the new fields
$newTitle = $newTitle;
$newMeta = yaml::encode(str::parse($newMeta));
$newSettings = yaml::encode(str::parse($newSettings));
$newText = $newText;

// Update the page with the new fields
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