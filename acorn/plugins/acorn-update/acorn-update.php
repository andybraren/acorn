<?php

// ACORN UPDATE

$updatePage = false;

if (kirby()->request()->path() == '') {
  $page = site()->page('home');
} else {
  $page = site()->page(kirby()->request()->path());
}

// Identify the current page version
if ($page) {
  
  // get the site's last upgraded time
  if (site()->info('acorn/versionhistory')) {
    $temp = yaml(site()->content()->info())['acorn']['versionhistory'];
    end($temp);
    $siteUpgradeTime = strtotime($temp[key($temp)]);
  }
  
  // get the page's last modified time
  if ($page->content()->date() != '') {
    $pageModifiedTime = strtotime($page->content()->date());
  } else {
    $pageModifiedTime = $page->dateModified();
  }
  
  // determine whether or not to update the page
  if ($pageModifiedTime <= $siteUpgradeTime) {
    $update = true;
  } else {
    $update = false;
  }
  
  // update the page
  if ($update == true) {
    
    // get the target version number
    foreach (yaml(site()->content()->info())['acorn']['versionhistory'] as $key => $value) {
      if ($pageModifiedTime < strtotime($value)) {
        $targetVersion = $key;
        break;
      } else {
        $targetVersion = null;
      }
    }
    
    // update to the target version number
    if (isset($targetVersion)) {
      
      // if the target Acorn update contains a page-content update as well, then run it
      $newversion = kirby()->roots()->plugins() . DS . 'acorn-update' . DS . 'content-updates' . DS . $targetVersion;
      if (is_dir($newversion)) {
        
        $updates = array_diff(scandir(dirname(__FILE__) . '/content-updates'), array('.', '..'));
        
        // run every new update available
        foreach($updates as $updateVersion) {
          if (version_compare($updateVersion, $targetVersion, '>=')) {
            require('content-updates/' . $updateVersion . '/acorn-update.php');
          }
        }
      }
    }
  }
}