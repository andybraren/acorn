<?php

/* DEPRECATED METHODS */
/*
  Whenever a field method is changed, the original field method is kept here
  for at least a few versions. The below field methods override the new ones
  even if they share the same name, which allows the acorn-update plugin script
  to more easily grab the old fields and store them in the new format.
*/

// ANDY
// Each of these should call a function that checks if these are being used by
// the upgrade script, and if they're not, adds useful information to a log
// that can be accessed somewhere to show which pages are using old methods.
// Although, this is the newly-deprecated file so these shouldn't be able to run
// anywhere else anyway.

// Theme setting
// returns the page's theme color
page::$methods['theme'] = function($page) {
  
  if (!empty(yaml($page->settings())['theme'])) {
    
    $setting = yaml($page->settings())['theme'];
    
    if ($setting == 'default' OR $setting == '') {
      return site()->setting('style/default-color');
    } else {
      return $setting;
    }
  } else {
    return site()->setting('style/default-color');
  }
  
};