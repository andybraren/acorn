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


page::$methods['categories'] = function($page) {
  
  if ($page->content()->categories() != '') {
    $categories = $page->content()->categories();
  }
  
  return $categories;
  
};

page::$methods['updated'] = function($page) {
  
  if ($page->content()->updated() != '') {
    $updated = $page->content()->updated();
  }
  
  return $updated;
  
};

//==============================================================================
// PAGE DATA METHODS
//==============================================================================

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
    } elseif (isset(str::split($page->content()->datedata(),',')[1])) {
      return strtotime(str::split($page->content()->datedata(),',')[1]);
    } else {
      return null;
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
    if (strpos($author, '~')) {
      $author = str::split($author, '~')[0];
    }
    
    if (site()->user($author)) {
      $collection->append($author, site()->user($author));
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

// Tags
// returns an array of tags
page::$methods['tags'] = function($page) {
  if ($page->content()->reldata() != null and $page->content()->reldata() != '') {
    if (isset(explode('///',$page->content()->reldata())[2])) {
      return str::split(explode('///',$page->content()->reldata())[2],',');
    }
  }
};

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













