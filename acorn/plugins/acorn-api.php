<?php

// The order these are in is important, beware of changing it
// This file appears before the routes loaded in acorn-routes.php as well, which is important too


$kirby->set('route', array(
  'pattern' => 'api',
  'method'  => 'POST',
  'action'  => function() {
    
    $key = '';
    $error = '';
    
    if (get('key')) {
      if (in_array(get('key'), str::split(site()->content()->apikeys(),','))) {
        $key = 'valid';
      } else {
        $key = 'invalid';
      }
    } else {
      $key = 'public';
    }
    
    header('Content-type: application/json; charset=utf-8');
    
    $temp = array();
    
    // Users API
    if (get('users')) {
      
      if (get('users') == 'empty') {
        $users = array();
        $error = "No username.";
        echo 'hi';
      }
      
      if (get('users') == 'all') {
        $users = site()->users();
      } elseif (site()->user(get('users'))) {
        $users = array(site()->user(get('users')));
      } else {
        $users = array();
        $error = "No user exists with the provided username.";
      }
      
      if (get('search')) {
        $users = site()->users()->filterBy('firstname', 'c*=', get('search'));
        if ($users == "") {
          $error = "No users found.";
        }
      }
      
      foreach ($users as $user) {
        $temp['username']   = ($username = $user->username()) ? (string)$username : null;
        $temp['profileURL'] = ($user->username()) ? (string)site()->url() . '/users/' . $user->username() : null;
        //$temp['avatarURL']  = ($avatar = site()->user($user)->avatar()) ? (string)$avatar->crop(40, 40)->url() : null;
        $temp['avatarURL']  = userAvatar($user->username());
        $temp['firstname']  = ($user->firstname()) ? (string)$user->firstname() : null;
        $temp['lastname']   = ($user->lastname()) ? $user->lastname() : null;
        $temp['email']      = ($user->email() and $key == 'valid') ? (string)strtolower($user->email()) : null;
        $temp['tuftsemail'] = ($user->tuftsemail() and $key == 'valid') ? (string)strtolower($user->tuftsemail()) : null;
        $temp['color']      = userColor($user->username());
        $temp['affiliation']   = ($user->affiliation()) ? (string)$user->affiliation() : null;
        $temp['department']   = ($user->department()) ? (string)$user->department() : null;
        $temp['major']   = ($user->major()) ? (string)$user->major() : null;
        $temp['classyear']   = ($user->classyear()) ? (string)$user->classyear() : null;
        $temp['birthyear']   = ($user->birthyear() and $key == 'valid') ? (string)$user->birthyear() : null;
        
        //$results['data'][$user->username()] = array_filter($temp);
        $results['data'][] = array_filter($temp);
      }
    }
    
    // Groups API
    if (get('groups')) {
      if (get('groups') == 'all') {
        $items = site()->page('groups')->children();
      } elseif ($thepage = site()->page('groups/' . get('groups'))) {
        $items = array($thepage);
      } else {
        $error = "No group exists with the provided name.";
      }
      if (get('search')) {
        $items = site()->page('groups')->children()->filterBy('title', 'c*=', get('search'));
        if ($items == "") {
          $error = "No groups found with the provided query.";
        }
      }
      
      foreach ($items as $item) {
        $temp['title']     = ($item->title()) ? (string)$item->title() : null;
        $temp['slug']      = ($item->slug()) ? (string)$item->slug() : null;
        $temp['url']       = ($item->url()) ? (string)$item->url() : null;
        $temp['image']     = groupLogo($item->slug(), 40);
        $temp['color']     = groupColor($item->slug());
        $results['data'][] = array_filter($temp);
      }
    }
    
    // Projects API
    if (get('projects')) {
      if (get('projects') == 'all') {
        $items = site()->page('projects')->children();
      } elseif ($thepage = site()->page('projects/' . get('projects'))) {
        $items = array($thepage);
      } else {
        $error = "No project exists with the provided name.";
      }
      if (get('search')) {
        $items = site()->page('projects')->children()->filterBy('title', 'c*=', get('search'));
        if ($items == "") {
          $error = "No projects found with the provided query.";
        }
      }
      
      foreach ($items as $item) {
        $temp['title']     = ($item->title()) ? (string)$item->title() : null;
        $temp['slug']      = ($item->slug()) ? (string)$item->slug() : null;
        $temp['url']       = ($item->url()) ? (string)$item->url() : null;
        $temp['image']     = ($item->heroImage()) ? $item->heroImage()->crop(40,40)->url() : null;
        $temp['color']     = $item->color();
        $results['data'][] = array_filter($temp);
      }
    }
    
    // Events API
    if (get('events')) {
      if (get('events') == 'all') {
        $items = site()->page('events')->children();
      } elseif ($thepage = site()->page('events/' . get('events'))) {
        $items = array($thepage);
      } else {
        $error = "No event exists with the provided name.";
      }
      if (get('search')) {
        $items = site()->page('events')->children()->filterBy('title', 'c*=', get('search'));
        if ($items == "") {
          $error = "No events found with the provided query.";
        }
      }
      
      foreach ($items as $item) {
        $temp['title']     = ($item->title()) ? (string)$item->title() : null;
        $temp['slug']      = ($item->slug()) ? (string)$item->slug() : null;
        $temp['url']       = ($item->url()) ? (string)$item->url() : null;
        $temp['image']     = ($item->heroImage()) ? $item->heroImage()->crop(40,40)->url() : null;
        //$temp['color']     = ($item->color()) ? $item->color() : null;
        $temp['date']       = ($item->dateStart()) ? date('F j, Y', strtotime($item->dateStart())) : null;
        $results['data'][] = array_filter($temp);
      }
    }
    
    // Display results as JSON
    if ($error == '') {
      $data['status']  = 'success';
      $data['code']    = '200';
      $data['message'] = 'Returning JSON';
      $data['data'] = $results['data'];
      echo json_encode($data);
    } else {
      $data['status']  = 'error';
      $data['code']    = '404';
      $data['message'] = $error;
      echo json_encode($data);
    }

  }
));

$kirby->set('route', array(
  'pattern' => 'apib',
  'method'  => 'POST|GET',
  'action'  => function() {
    $error = '';
        
    $name = (get('name')) ? get('name') : '';
    $thekey  = (get('key'))  ? get('key')  : '';
    //$key = (isset($_POST['key'])) ? $_POST['key'] : '';
    
    $ip = (isset($_POST['ip'])) ? $_POST['ip'] : null;
    
    function deviceArray() {
      $array       = array();
      $string      = site()->devices();
      $cleanstring = str_replace(array(', ','== '), array(',','=='), $string);
      $toparray    = explode(',', $cleanstring);
      foreach($toparray as $x) {
        $x = explode('==', $x);
        $array[] = $x;
      }
      return $array;
    }
    
    //print_r(deviceArray());
    
    $apikeys     = a::extract(deviceArray(), 0);
    $ipaddresses = a::extract(deviceArray(), 1);
    $devicenames = a::extract(deviceArray(), 2);
    
    //print_r($apikeys);
    //print_r($ipaddresses);
    //print_r($devicenames);
    
    //echo "NEW";
    
    $devices = site()->devices()->toStructure();
    
    //print_r($devices);
    
    
    foreach ($devices as $device) {
      //echo $device->name();
    }
    
    //$array = str::split(explode('==',site()->page('site')->content()->devices())[0],',');
    //print_r($array);
    // Get Staff Page
    $devices = site()->devices()->yaml();
    
    foreach($devices as &$device) {
      if ($device['key'] == $thekey) {
        $device['ip'] = ($ip) ? $ip : $device['ip'];
      }
    }
    
    site()->update(['devices' => yaml::encode($devices)]);
    
    
    if (get('key')) {
      if (in_array(get('key'), deviceArray())) {
        $key = 'valid';
      } else {
        $key = 'invalid';
      }
    } else {
      $key = 'public';
    }
    
    //echo get('key');
    
    //echo $key;
    
    //echo file_get_contents('php://input');
  }
));





































