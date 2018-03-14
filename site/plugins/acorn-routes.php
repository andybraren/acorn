<?php

// https://forum.getkirby.com/t/which-router-syntax-should-i-use/4494
// https://forum.getkirby.com/t/is-it-possible-to-define-panel-routes-inside-a-plugin-file/1684/9



//==================================================
// PLUGIN UPDATE MECHANISM
// Downloads, extracts, and safely replaces a plugin
// or potentially the entirey of Acorn in one shot
//
// Still have to figure out how to replace itself though, haven't tested
//==================================================

$kirby->set('route', array(
  'pattern' => array('upgradetest', '(.+upgradetest)'),
  'method' => 'GET',
  'action'  => function() {
    
    // Connect to Acorn Link and check for the latest versions
    $url = 'https://link.acorn.blog/updates/acorn-commerce/acorn-commerce-0.1.3.zip';
    $basename = pathinfo($url, PATHINFO_BASENAME);
    $filename = pathinfo($url, PATHINFO_FILENAME);
    
    // Get the remote zip's version number from its filename
    $latestversion = (preg_match('/-(?!.*-)(.*)/', $filename, $matches)) ? $matches[1] : 0; // Uses a negative lookahead to read from the last dash
    $currentversion = site()->info('acorn/version');
    $upgrade = ($latestversion > $currentversion) ? true : false;
    
    echo 'Latest: ' . $latestversion . "\n";
    echo 'Current: ' . $currentversion . "\n";
    echo ($upgrade) ? 'Begin the upgrade process' . "\n\n" : 'Stopping upgrade' . "\n\n";
    
    // CHECK to see if the destination folder is writable before downloading the zip
    
    // If we're cleared to upgrade, proceed
    if ($upgrade) {
      
      // Set up a temp folder for the remote zip file
      $ziplocation = kirby()->roots()->site() . DS . 'upgrade-test' . DS . 'temp';
      if (!is_dir($ziplocation)) {
        mkdir($ziplocation);
      }
      
      // Download the remote zip to the temp folder
      $zipfile = $ziplocation . DS . $basename;
      if (!file_exists($zipfile)) {
        
        // get the zip file
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_FOLLOWLOCATION => TRUE,  // the magic sauce
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYHOST => FALSE, // suppress certain SSL errors
            CURLOPT_SSL_VERIFYPEER => FALSE, 
        ));
        $data = curl_exec($ch);
        curl_close($ch);
        
        // save the zip file
        $file = fopen($zipfile, "w+");
        fputs($file, $data);
        fclose($file);
        
        echo "Zip file downloaded." .  "\n";
        
      } else {
        echo "Zip file already exists, so we'll use that instead." . "\n";
      }
      
      // Load PclZip
      require_once(kirby()->roots()->plugins() . DS . 'acorn-upgrade' . DS . 'vendor' . DS . 'pclzip' . DS . 'pclzip.lib.php');
      
      // Unzip the new version within the same temp directory
      $tempnew = $ziplocation . DS . $filename . "-temp";
      $archive = new PclZip($zipfile);
      if ($archive->extract(PCLZIP_OPT_PATH, $tempnew) != 0) {
        echo "Unzipped successfully" . "\n\n";
      } else {
        die("Unzip failed. Error : " . $archive->errorInfo(true) . "\n\n");
      }
      
      // Create a zip file of the existing version
      $oldfolder = kirby()->roots()->site() . DS . 'upgrade-test' . DS . 'acorn-commerce';
      if (file_exists($oldfolder)) {
        $newzip = kirby()->roots()->site() . DS . 'upgrade-test' . DS . 'archive' . DS . 'acorn-commerce-' . date('Y-m-d-His') . '.zip';
        $archive = new PclZip($newzip);
        if ($archive->create($oldfolder, PCLZIP_OPT_REMOVE_PATH, kirby()->roots()->site() . DS . 'upgrade-test') != 0) {
          $archiveCreated = true;
          echo "The old plugin was saved in the archive. \n";
        } else {
          $archiveCreated = false;
          die("Error : " . $archive->errorInfo(true));
        }
      }
      
      // delete all files and sub-folders from a folder
      function deleteFolder($dir) {
        foreach (glob($dir . '/*') as $file) {
          if (is_dir($file)) {
            deleteFolder($file);
          } else {
            unlink($file);
          } 
        }
        rmdir($dir);
      }
      
      // Move and rename the new version to the expected folder
      if ($archiveCreated) {
        deleteFolder($oldfolder);
        echo (!is_dir($oldfolder)) ? "The old plugin folder was successfully wiped" . "\n\n" : "There was a problem wiping the old plugin folder" . "\n\n";
        rename($tempnew, $oldfolder);
        echo "The new plugin was successfully installed.";
      }
      
    } else {
      echo "Not eligible for upgrade";
    }
  
  }
));








$kirby->set('route', array(
  'pattern' => array('newsite', '(.+newsite)'),
  'method' => 'POST',
  'action'  => function() {
    
    try {
  		
  		$name = get('desiredname');
  		
  		dir::copy('subdomains/link/versions/v0.6/', 'demo/' . $name . '/');
  		f::copy('subdomains/link/versions/v0.6/.htaccess', 'demo/' . $name . '/.htaccess');
  		  		
      $response = array('redirecturl' => 'https://tufts.makernetwork.org/demo/' . $name);
      echo json_encode($response);
      
    } catch(Exception $e) {
      echo $e->getMessage();
    }
    
    // exec('sh script.sh', $out, $return);
    //echo "<pre>" . print_r($out) . "</pre>";
    
  }
));




$kirby->set('route', array(
  'pattern' => '.well-known/apple-developer-merchantid-domain-association',
  'method' => 'GET',
  'action'  => function() {
    
    if (site()->setting('commerce/stripe/apple-merchant-id')) {
      echo site()->setting('commerce/stripe/apple-merchantid');
    }
    
  }
));














$kirby->set('route', array(
  'pattern' => '(:num)',
  'method'  => 'GET',
  'action'  => function() {
    $path = kirby()->request()->path();
    $page = page($path);
           
    if (!$page) $page = page('posts/' . $path);
    
    return ($page) ? site()->visit($page) : site()->visit($uid);
  }
));

// User profile URLs
// Possibly not needed with the (:all) one below?
// Redirect all requests to /users/ directory to example.com/username
// https://getkirby.com/docs/developer-guide/advanced/routing#omitting-the-blog-folder-in-urls
$kirby->set('route', array(
  'pattern' => 'users/(:any)',
  'method'  => 'GET',
  'action'  => function($uid) {

    go(site()->url() . '/' . $uid);
    
  }
));

// Virtual robots.txt file
$kirby->set('route', array(
  'pattern' => 'robots.txt',
  'method'  => 'GET',
  'action'  => function() {
    
    if (site()->setting('general/indexable')) {
      echo 'User-agent: *<br>Disallow: /thumbs/<br>Disallow: /users<br>Disallow: /drafts/<br>Sitemap: ' . site()->url() . '/sitemap';
      foreach (site()->index()->filterBy('visibility','unlisted') as $hiddenpage) {
        echo '<br>Disallow: /' . $hiddenpage->uri();
      }
    } else {
      echo 'User-agent: *' . "\r\n" . 'Disallow: /';
    }
    
  }
));

// Virtual humans.txt file
$kirby->set('route', array(
  'pattern' => 'humans.txt',
  'method'  => 'GET',
  'action'  => function() {
    $blah = kirby()->roots()->plugins() . '/' . 'humans.txt';
    echo file_get_contents($blah);
  }
));



// Log in
$kirby->set('route', array(
  'pattern' => array('login', '(.+login)'),
  'method'  => 'POST',
  'action'  => function() {
    $currentpath = str_replace(array('/maker/','/ne/','/login','login','/forgot','forgot'),'',$_SERVER['REQUEST_URI']);
    if($user = site()->user(get('username')) and $user->login(get('password'))) {
      
      // Include a username cookie
      cookie::set('username', site()->user()->username(), $expires = 60*24*30, $path = '/', $domain = null, $secure = true);
      return go($currentpath);
      
      //cookie::set('kirby_session_auth', $value, $lifetime = 42000, '/blah', $domain = null);
    } elseif ($user = site()->users()->findBy('email', get('username')) and $user->login(get('password'))){
      return go($currentpath);
    } else {        
      return go($currentpath . '/login:failed');
    }
  }
));

// Log out
$kirby->set('route', array(
  'pattern' => array('logout', '(.+logout)'),
  'action'  => function() {
    if($user = site()->user()) {
      $user->logout();
    }
    $currentpath = str_replace(array('/maker/','/ne/','/logout','logout'),'',$_SERVER['REQUEST_URI']);
    return go($currentpath);
  }
));

// Sign up
$kirby->set('route', array(
  'pattern' => array('signup', '(.+signup)'),
  'method'  => 'POST',
  'action'  => function() {
    
    $site = site();
    
    // redirect logged in users to the homepage
    if($site->user()) go('/');
    
    // handle the signup form submission
    if(r::is('post')) {
      
      // check if the username already exists, and if not, run the signup method
      if(!$site->user(get('username'))) {
        
        $usertype = (page('users')->hasChildren()) ? 'user' : 'admin';
        
        // create the new user.php file
      	try {
  			  $user = $site->users()->create(array(
  			    'username'    => get('username'),
  				  'firstname'   => get('firstname'),
  			    'lastname'    => get('lastname'),
  			    'email'       => strtolower(get('email')),
  			    'password'    => get('password'),
  			    'datedata'    => date('Y-m-d H:i:s'),
  			    'language'    => 'en',
  			    'usertype'    => $usertype,
  			    'color'       => strtolower(get('color')),
  			  ));
  			} catch(Exception $e) {
  			  $e->getMessage();
  			}
  			
  			// create the new maker profile page
  			try {
  				$firstandlast = get('firstname') . " " . get('lastname');
  			  $newPage = page('users')->children()->create(get('username'), 'user', array(
  			    'title' => $firstandlast,
  			    'datedata' => date('Y-m-d H:i:s') . ', ' . date('Y-m-d H:i:s'),
            'userdata' => get('username'),
            'reldata' => '',
            'settings' => 'public, ' . strtolower(get('color')),
            'hero' => '',
  			    'text'  => '',
  			  ));
  			} catch(Exception $e) {
  			  echo $e->getMessage();
  			}
  			
  			// log the user in and redirect them to their new profile page
        try {
          $user->login(get('password'));
          go('/users/' . get('username'));
        } catch(Exception $e) {
          $error = true;
        }
  
  		} else {
        $error = true;
        echo "Username is taken";
      }
    } else {
      $error = false;
    }
    
    return array('error' => $error);
    
  }
));










// Password reset email
$kirby->set('route', array(
  'pattern' => 'forgot',
  'method'  => 'POST',
  'action'  => function() {
    if($user = site()->users()->findBy('email',strtolower(get('email')))) {
      try {

        // set the reset key
        $resetkey = substr(md5(rand()), 0, 50);
        $resetdate = date('Y-m-d H:i:s');
        site()->user($user->username())->update(array(
          'resetkey' => $resetkey,
          'resetdate' => $resetdate
        ));
        // echo 'Great, updated the resetkey, check your email';
        
        // send them an email
        $email = email(array(
          'service' => 'sparkpost',
          'to'      => $user->email(),
          'from'    => 'happyrobot@maker.tufts.edu',
          'subject' => site()->title() . ' Password Reset',
          'body'    => '
            <html>
              <head>
                <title>' . site()->title() . ' Password Reset</title>
              </head>
              <body>
                <p>Hi ' . $user->firstname() . ',</p>
                <p>You can reset your password by opening this link. It will expire within one hour and can only be used once.</p>
                <p>' . site()->url() . '/username:' . $user->username() . '/resetkey:' . $resetkey . '/</p>
                <p>- ' . site()->title() . ' Robot</p>
              </body>
            </html>
          '
        ));
        
        $currentpath = str_replace(array('/maker/','/ne/','/login','login','/forgot','forgot'),'',$_SERVER['REQUEST_URI']);
        
        if($email->send()) {
          //return go($currentpath . '/forgot:success');
          return go('/forgot:success');
        } else {
          //echo $email->error();
          //return go($currentpath . '/forgot:failed');
          return go('/forgot:failed');
        }
      	
      } catch(Exception $e) {
        echo $e->getMessage();
      }

    } else {
      $error = true;
      //echo "Nope, that user does not exist";
      return go($currentpath . '/forgot:failed');
    }
  }
));

// Password reset
$kirby->set('route', array(
  'pattern' => 'reset',
  'method'  => 'POST',
  'action'  => function() {
    // compare to the key to the one stored in the login txt file
    // if they match and the purge date is not reached, reset the user's password
    // delete the key in the login txt file
    
    $username  = get('username');
    $key = get('resetkey');
    $newpassword = get('newpassword');
    
    if($user = site()->users()->findBy('username', $username)) {
    	if($user->resetkey() == $key) {                                      // If the keys match...
    		if(strtotime($user->resetdate()) > (time() - 86400)) {               // And if the time period is right
	        site()->user($user->username())->update(array(                   // Then reset the password and wipe the key
	        	'resetkey' => null,
	        	'resetdate' => null,
	          'password' => $newpassword,
	        ));
          if($user = site()->user($username) and $user->login(get('newpassword'))) { // And log them in for convenience
            return go(site()->url().'/reset:success');
          } else {
            return go(site()->url().'/reset:failed');
          }
    		}
    		else {
		      $error = true;
		      echo "Sorry, this link seems to have expired. Submit a new password reset request. Error 1";
    		}
    	}
    	else {
	      $error = true;
	      echo "Sorry, this link seems to have expired. Submit a new password reset request. Error 2";
    	}
    }
    else {
      $error = true;
      echo "Sorry, something weird seems to be going on. Email the web admin, andybraren. Error 3";
    }
  }
));

// New page creation
$kirby->set('route', array(
  'pattern' => array('/new', '(.+new)'), // matches any url ending in new
  'action'  => function() {
    
    if (isset($_SERVER['REQUEST_URI'])) {
      $string = site()->homePage()->url();
      $blah = parse_url($string, PHP_URL_PATH);
      $tweakedpostpage = str_replace($blah,'', strtolower($_SERVER['REQUEST_URI']));
    }
    
    $parenturi = ltrim(strtok(str_replace('/new','',$tweakedpostpage), '?'), '/');
    //$newpage = $parenturi . '/' . date('His');
    $newpage = $parenturi . '/' . date('YmdHis') . milliseconds();
    
    $user = site()->user();
    
    pageWizard($newpage, $user, $_POST);
    
  }
));

function pageWizard($targetpageuri, $user, $data) {
  
  $_POST = $data;
  $user = $user;
  
  if (!site()->find($targetpageuri)) {
    $targetpage = site()->page('site/contentfile');
    $exists = false;
  } else {
    $targetpage = site()->page($targetpageuri);
    $exists = true;
  }
  
  // TITLE FIELD
  $originaltitle = $targetpage->content()->title();
  //$newTitle = esc($_POST['title']) ? $targetpage->content()->title() : '';
  $newTitle = (isset($_POST['title'])) ? ($_POST['title']) : yaml($targetpage->title());
  /*
  if (isset($_POST['title'])) {
    $originaltitle = $targetpage->content()->title();
    $newTitle = esc($_POST['title']) ? $targetpage->content()->title() : '';
  } else {
    $newTitle = '';
  }
  */
  
  // META FIELD
  $newMeta = array();
  $newMeta = yaml($targetpage->meta());
  
  function getAuthors($string) { // 'abraren, pbraren'
    $authors = array();
    foreach (array_unique(str::split(esc($_POST['authors']),',')) as $author) {
      if (site()->user($author)) {
        $new = array();
        $user = site()->user($author);
        $new['username'] = $user->username();
        $new['description'] = "";
        array_push($authors, $new);
      }
    }
    return $authors;
  }
  $newMeta['authors'] = (isset($_POST['authors'])) ? getAuthors($_POST['authors']) : $newMeta['authors'];
  
  $newMeta['date']['created']     = (isset($_POST['created']))   ? (esc($_POST['created'])) : $newMeta['date']['created'];
  $newMeta['date']['modified']    = date('Y-m-d H:i:s', time());
  $newMeta['date']['modifiedby']  = $user->username();
  $newMeta['date']['published']   = (isset($_POST['published'])) ? (esc($_POST['published'])) : $newMeta['date']['published'];
  $newMeta['date']['updated']     = (isset($_POST['updated']))   ? (esc($_POST['updated'])) : $newMeta['date']['updated'];
  $newMeta['date']['start']       = (isset($_POST['start']))     ? (esc($_POST['start'])) : $newMeta['date']['start'];
  $newMeta['date']['end']         = (isset($_POST['end']))       ? (esc($_POST['end'])) : $newMeta['date']['end'];
  
  $newMeta['related']['tags']     = (isset($_POST['tags']))      ? (esc($_POST['tags'])) : $newMeta['related']['tags'];
  $newMeta['related']['internal'] = (isset($_POST['internal']))  ? (esc($_POST['internal'])) : $newMeta['related']['internal'];
  $newMeta['related']['external'] = (isset($_POST['external']))  ? (esc($_POST['external'])) : $newMeta['related']['external'];
  
  $newMeta['info']['subtitle']    = (isset($_POST['subtitle']))    ? (esc($_POST['subtitle'])) : $newMeta['info']['subtitle'];
  $newMeta['info']['description'] = (isset($_POST['description'])) ? (esc($_POST['description'])) : $newMeta['info']['description'];
  $newMeta['info']['excerpt']     = (isset($_POST['text'])) ? (stringToExcerpt(esc($_POST['text']))) : $newMeta['info']['excerpt'];
  
  $newMeta['data']['likes']       = (isset($_POST['likes'])) ? (esc($_POST['likes'])) : $newMeta['data']['likes'];
  $newMeta['data']['dislikes']    = (isset($_POST['dislikes'])) ? (esc($_POST['dislikes'])) : $newMeta['data']['dislikes'];
  $newMeta['data']['requests']    = (isset($_POST['requests'])) ? (esc($_POST['requests'])) : $newMeta['data']['requests'];
  $newMeta['data']['subscribers'] = (isset($_POST['subscribers'])) ? (esc($_POST['subscribers'])) : $newMeta['data']['subscribers'];
  $newMeta['data']['registrants'] = (isset($_POST['registrants'])) ? (esc($_POST['registrants'])) : $newMeta['data']['registrants'];
  $newMeta['data']['attendees']   = (isset($_POST['attendees'])) ? (esc($_POST['attendees'])) : $newMeta['data']['attendees'];
  $newMeta['data']['address']     = (isset($_POST['address'])) ? (esc($_POST['address'])) : $newMeta['data']['address'];
  $newMeta['data']['addressinfo'] = (isset($_POST['addressinfo'])) ? (esc($_POST['addressinfo'])) : $newMeta['data']['addressinfo'];
  $newMeta['data']['hours']       = (isset($_POST['hours'])) ? (esc($_POST['hours'])) : $newMeta['data']['hours'];
  $newMeta['data']['hoursinfo']   = (isset($_POST['hoursinfo'])) ? (esc($_POST['hoursinfo'])) : $newMeta['data']['hoursinfo'];
  $newMeta['data']['rating']      = (isset($_POST['rating'])) ? (esc($_POST['rating'])) : $newMeta['data']['rating'];
  $newMeta['data']['hero']        = (isset($_POST['hero'])) ? (esc($_POST['hero'])) : $newMeta['data']['hero'];
  $newMeta['data']['icon']        = (isset($_POST['icon'])) ? (esc($_POST['icon'])) : $newMeta['data']['icon'];
  $newMeta['data']['price']       = (isset($_POST['price'])) ? (esc($_POST['price'])) : $newMeta['data']['price'];
  $newMeta['data']['audio']       = (isset($_POST['audio'])) ? (esc($_POST['audio'])) : $newMeta['data']['audio'];
  
  // SETTINGS FIELD
  $newSettings = array();
  $newSettings = yaml($targetpage->settings());
  
  $newSettings['visibility']  = (isset($_POST['visibility'])) ? (esc($_POST['visibility'])) : $newSettings['visibility'];
  $newSettings['color']       = (isset($_POST['color'])) ? (esc($_POST['color'])) : $newSettings['color'];
  $newSettings['hero-color']  = (isset($_POST['hero-color'])) ? (esc($_POST['hero-color'])) : $newSettings['hero-color'];
  $newSettings['hero-style']  = (isset($_POST['hero-style'])) ? (esc($_POST['hero-style'])) : $newSettings['hero-style'];
  $newSettings['toc']         = (isset($_POST['toc'])) ? (esc($_POST['toc'])) : $newSettings['toc'];
  $newSettings['discussion']  = (isset($_POST['discussion'])) ? (esc($_POST['discussion'])) : $newSettings['discussion'];
  $newSettings['submissions'] = (isset($_POST['submissions'])) ? (esc($_POST['submissions'])) : $newSettings['submissions'];
  
  // TEXT FIELD
  $newText = (isset($_POST['text'])) ? (strip_tags($_POST['text'])) : $targetpage->content()->text();
  
  // Set the new fields
  $newTitle = $newTitle;
  $newMeta = yaml::encode($newMeta);
  $newSettings = yaml::encode($newSettings);
  $newText = $newText;
  
  if (!$exists) { // CREATE THE NEW PAGE
    
    try {
      /*
      site()->page($targetpage)->create(array(
        'Title'    => $newTitle,
        'Meta'     => $newMeta,
        'Settings' => $newSettings,
        'Text'     => $newText,
      ));
      */
      
      site()->page()->create($targetpageuri, 'page', array(
        'Title'    => $newTitle,
        'Meta'     => $newMeta,
        'Settings' => $newSettings,
        'Text'     => $newText,
      ));
      
      return go($targetpageuri);
      //echo "yo";
    } catch(Exception $e) {
      //return page('error');
      echo $e->getMessage();
      //echo "blah";
    }
    
  } else { // UPDATE THE EXISTING PAGE
    
    try {
      
      site()->page($targetpage)->update(array(
        'Title'    => $newTitle,
        'Meta'     => $newMeta,
        'Settings' => $newSettings,
        'Text'     => $newText,
      ));
      
      if ($newTitle != $originaltitle and $targetpage->parent() != 'forum' and $targetpage->parent() != 'users') {
        $currentlocation = kirby()->roots()->content() . '/' . $targetpage->diruri();
        $newslug = acornSlugify($newTitle);
        $newlocation = kirby()->roots()->content() . '/' . $targetpage->parent()->diruri() . '/' . $newslug;
        rename($currentlocation, $newlocation);
        
        $changeurl = site()->url() . '/' . $targetpage->parent()->diruri() . '/' . $newslug;
        $changeurl = str_replace('/posts', '', $changeurl);
        
        $response = array('changeurl' => $changeurl); // redirect to new url
        echo json_encode($response);
      }
      
      //echo "successfully updated";
      
    } catch(Exception $e) {
      echo $e->getMessage();
    }
    
  }
  
}



// Save comments
$kirby->set('route', array(
  'pattern' => array('saveblah', '(.+saveblah)'),
  'method' => 'POST',
  'action'  => function() {
    
    if (isset($_POST['page'])) {
      $string = site()->homePage()->url();
      $blah = parse_url($string, PHP_URL_PATH);
      $tweakedpostpage = str_replace($blah,'', strtolower($_POST['page']));
    }
    
		$targetpage = site()->page($tweakedpostpage);
          
    $slug = date('YmdHis') . milliseconds();
    
    $newpage = $targetpage->uri() . '/comments/' . $slug;
    
    $text  = (isset($_POST['text'])) ? $_POST['text'] : $targetpage->text();
    $text = strip_tags($text);
    
    try {
      page()->create($newpage, 'comment', array(
        'DateData'  => date('Y-m-d H:i:s'),
        'UserData' => site()->user()->username(),
        'RelData' => '',
        'Settings' => '',
        'Text' => $text,
      ));
      
      // Return the comment ID # and the data ID # for editing purposes
      $id = 'comment-' . $targetpage->find('comments')->children()->count();
      $response = array('id' => $id, 'dataid' => $slug);
      echo json_encode($response);
      
    } catch(Exception $e) {
      return page('error');
    }
      
  }
));






// Delete pages
$kirby->set('route', array(
  'pattern' => array('delete', '(.+delete)'),
  'method' => 'POST',
  'action'  => function() {
    
		
		//$redirecturl = site()->url() . '/' . $targetpage->parent()->diruri();
		
    if (isset($_POST['page'])) {
      $string = site()->homePage()->url();
      $blah = parse_url($string, PHP_URL_PATH);
      $tweakedpostpage = str_replace($blah,'', strtolower($_POST['page']));
    }
		
		if (site()->page($tweakedpostpage)) {
  		$targetpage = site()->page($tweakedpostpage);
		} elseif (site()->page('posts' . $tweakedpostpage)) {
  		$targetpage = site()->page('posts' . $tweakedpostpage);
		}
		
    try {
      
      $targetpage->delete(true); // Force page to be deleted, even if it has subpages
      
      //$response = array('redirecturl' => $redirecturl);
      $response = array('command' => 'goback');
      echo json_encode($response);
      
    } catch(Exception $e) {
      echo $e->getMessage();
    }
    
  }
));

// Purge Cache
$kirby->set('route', array(
  'pattern' => array('purgeCache', '(.+purgeCache)'),
  'method' => 'POST',
  'action'  => function() {
		kirby()->cache()->flush();
		return true;
  }
));

// Regenerate page cache
// Visit example.com/whatever/flush to surgically excise and regenerate that page's cache file within /site/cache
// https://forum.getkirby.com/t/controlling-the-cache/464
// https://github.com/ChainsawBaby/buildCache
$kirby->set('route', array(
  'pattern' => array('flush', '(.+flush)'),
  'method' => 'GET',
  'action'  => function() {
    $uri = ltrim(str_replace('/flush','',$_SERVER['REQUEST_URI']),'/');
    $url = site()->url() . '/' . $uri;
    $cache_file = kirby()->roots()->cache() . '/' . md5(site()->url() . '/' . $uri);
          
    if ($uri == '') { // nuke every cache file
      kirby()->cache()->flush();
      foreach (site()->index() as $page) {
        if(!in_array($page, c::get('cache.ignore'))) {
          ping($page->url());
          echo $page->uri() . '<br>';
        }
      }
      echo 'The entire site\'s cache was successfully regenerated.';
    } else {
    	if (file_exists($cache_file)) {
      	if (unlink($cache_file)) { // delete the singular cache file
          if (ping($url)) {
            echo 'The page\'s cache file was successfully regenerated.';
          } else {
            echo 'The page\'s cache file was deleted, but a connection error to the page is preventing a new one from being generated.';
          }
      	} else {
        	echo 'The page\'s cache file exists, but could not be deleted.';
      	}
    	} else {
        if(!in_array($uri, c::get('cache.ignore'))) {
          if (ping($url)) {
            echo 'The page\'s cache file did not already exist, so a new one was successfully generated.';
          } else {
            echo 'The page\'s cache file did not already exist, and a connection error is preventing a new one from being generated.';
          }
        } else {
          echo 'This page is being ignored, so a cache file was not generated.';
        }
    	}
    }
  }
));

// Replace Amazon Links
$kirby->set('route', array(
  'pattern' => array('replaceAmazon', '(.+replaceAmazon)'),
  'method' => 'GET',
  'action'  => function() {
    
    // Unshorten URL function
    // http://jonathonhill.net/2012-05-18/unshorten-urls-with-php-and-curl/
    function unshorten_url($matches) {
      //print_r($matches);
      foreach ($matches as $url) {
        
        //echo $url;
        $originalurl = $url;
                  
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_FOLLOWLOCATION => TRUE,  // the magic sauce
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYHOST => FALSE, // suppress certain SSL errors
            CURLOPT_SSL_VERIFYPEER => FALSE, 
        ));
        curl_exec($ch);
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        
        //echo $url . '<br>' . '<br>';
        
        // Get the Amazon ASIN
        // http://stackoverflow.com/questions/21700573/get-asin-from-pasted-amazon-url
        //preg_match('/(?:dp|o|gp|-)\/(B[0-9]{2}[0-9A-Z]{7}|[0-9]{9}(?:X|[0-9]))/', $url, $matches);
        //preg_match('/dp\/([a-zA-Z0-9\/]*)\//', $url, $matches);
        
        /* Exactly Dad's */
        preg_match('/product\/([a-zA-Z0-9\/]*)\//', $url, $matches);
        
        //print_r($matches);
        
        if (isset($matches[1])) {
          $asin = $matches[1];
        }
        
        if (isset($asin)) {
          return 'https://www.amazon.com/dp/' . $asin;
        } else {
          return $originalurl;
        }
        
      }
    }
    
    //$targetpage = site()->page('projects/atest');
    
    // this works
    //$targetpages = site()->page('projects/atest')->children();
    
    // doesn't work
    //$targetpages = site()->page('replacetest')->children();
    
    
    //$rx = '/https?:\/\/(www\.)?amzn\.to\/[a-zA-Z0-9\/]*/';
    //$newtext = preg_replace_callback($rx, 'unshorten_url', $targetpage->text());
    //echo $newtext;
    
    $targetpages = site()->page('replacetest')->children();
    
    //$targetpage = site()->page('replacetest/sixth-gen-intel-skulltrail-nuc'); // success
    //$targetpage = site()->page('replacetest/liberate-western-digital-8tb-from-external-drive-enclosure-to-save-big'); // success
    $targetpage = site()->page('replacetest/first-look-ring-video-doorbell-pro'); // failure
    
    foreach ($targetpages as $targetpage) {
      $rx = '/https?:\/\/(www\.)?amzn\.to\/[a-zA-Z0-9\/]*/';
      $newtext = preg_replace_callback($rx, 'unshorten_url', $targetpage->text());
      //echo $newtext;
      
      try {
        site()->page($targetpage)->update(array(
          'Text'  => $newtext,
        ));
        echo 'success: ' . $targetpage->slug() . '<br>';
      } catch(Exception $e) {
        echo 'error: ' . $targetpage->slug() . '<br>';
      }
    }
  }
));


// Sitemap
// Based on https://getkirby.com/docs/cookbook/xmlsitemap
// Currently lists every single folder, regardless of whether a page.txt is present
$kirby->set('route', array(
  'pattern' => array('sitemap', '(.+sitemap)'),
  'method' => 'GET',
  'action'  => function() {
    $ignore = array('sitemap', 'error', 'drafts', '_gsdata_');
    
    // send the right header
    header('Content-type: text/xml; charset="utf-8"');
    
    // echo the doctype
    echo '<?xml version="1.0" encoding="utf-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    if (site()->setting('general/indexable')) {
      foreach (site()->pages()->index() as $p) {
        if (!in_array($p->uid(), $ignore)) {
          echo '<url>';
            echo '<loc>' . html(site()->url() . '/' . $p->uri()) . '</loc>';
            echo '<lastmod>' . $p->modified('c') . '</lastmod>';
            echo '<priority>' . (($p->isHomePage()) ? 1 : number_format(0.5/$p->depth(), 1)) . '</priority>';
          echo '</url>';
        }
      }
    }
    echo '</urlset>';
  }
));



// Upload
$kirby->set('route', array(
  'pattern' => array('upload', '(.+upload)'),
  'method' => 'POST',
  'action'  => function() {
    
		//var_dump($_FILES);
		
    if (isset($_POST['type'])) {
  		$type = $_POST['type']; // e.g. 'avatar'
		} else {
  		$type = '';
		}
		
    if (isset($_POST['page'])) {
      $string = site()->homePage()->url();
      $blah = parse_url($string, PHP_URL_PATH);
      $tweakedpostpage = str_replace($blah,'', strtolower($_POST['page']));
    }
		
		$targetpage = page($tweakedpostpage); // Account for custom routes
		if (!$targetpage) $targetpage = page('posts' . $tweakedpostpage);
		if (!$targetpage) $targetpage = page('users' . $tweakedpostpage);
		
    
    
		if ($type == 'avatar') {
      $target_dir = kirby()->roots()->avatars() . '/';
    } else {
      $target_dir = kirby()->roots()->content() . '/' . $targetpage->uri() . '/';
    }
    
		$name = key($_FILES); // The name attribute of the input that was uploaded

		if ($type == 'avatar') {
      $file_name = str_replace('/','', strtolower($tweakedpostpage));
		} else {
  		$file_name = pathinfo($_FILES[$name]['name'], PATHINFO_FILENAME);
		}
		
		$file_extension = str_replace('jpeg','jpg',strtolower(pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION)));
		$file = $file_name . '.' . $file_extension;
    
		$file_type = $_FILES[$name]['type'];
		$file_size = $_FILES[$name]['size'];
		
		if ($type == 'avatar') {
      $file_url = str_replace(' ', '%20', kirby()->roots()->avatars() . '/' . $file);
		} else {
  		$file_url = str_replace(' ', '%20', site()->contentURL() . '/' . $targetpage->uri() . '/' . $file);
		}
		
		
		//$file_extension = end(explode('.', $file));
		//$file_extension = substr(strrchr($file,'.'),1);
		
		// Lowercase extension
		$target_file = $target_dir . $file;
		
    
    if ($file_type == 'image/jpg' or $file_type == 'image/jpeg' or $file_type == 'image/png') {
      $file_type = 'image';
    }
    
    $uploadOk = 1;
    
    // Make sure images are actually images
    if ($file_type == 'image') {
      if (getimagesize($_FILES[$name]['tmp_name']) == false) {
        $uploadOk = 0;
        $error = "hey";
      }
    }
    
    // Replace old files with the same name, of any extension
  	$oldfile = $target_dir . $file;
  	if (file_exists($oldfile)) {
    	unlink($oldfile);
  	}
  	
  	if ($type == 'avatar') {
    	$oldfile = $target_dir . $file_name;
    	$todelete = glob($target_dir . $file_name . '*');
    	foreach ($todelete as $delete) {
      	unlink($delete);
    	}
  	}
  	
  	$type = (isset($_POST['type'])) ? $_POST['type'] : null;
  	
    // Save the file in the right place
    if ($uploadOk = 1) {
      try {
        
        if (move_uploaded_file($_FILES[$name]['tmp_name'], $target_file)) {
          
          if ($file_type == 'image') {
          
            // Use GD Lib to detect image orientation and rotate if necessary
            // - https://forum.getkirby.com/t/wrong-orientation-of-images-portrait-landscape-after-upload/692/19
            // - https://github.com/getkirby/starterkit/blob/0b4a6e8cf929237621d77adb996e98b08d234004/kirby/toolkit/lib/thumb.php
            try {
              $img = new abeautifulsite\SimpleImage($target_file);
              $img->auto_orient();
              @$img->save($target_file);
            } catch(Exception $e) {
              echo "Error rotating image";
            }
            
        		if ($type == 'avatar') {
              $file_url = (string)site()->user($file_name)->avatar()->crop(300, 300)->url();
        		} else {
          		$file_url = (string)kirbytag(array('image' => $file, 'targetpage' => $targetpage, 'output' => 'url'));
        		}
            
          }
          
          if ($type == 'hero') {
            $file_url = (string)kirbytag(array('image' => $file, 'targetpage' => $targetpage, 'type' => 'hero', 'output' => 'url'));
            site()->page($targetpage)->update(array(
              'Hero' => $file,
            ));
          }
          
        }
        
        $response = array('filename' => $file, 'fileurl' => $file_url, 'url' => $file_url, 'extension' => $file_extension, 'size' => $file_size);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        
      } catch(Exception $e) {
        echo $e->getMessage();
      }
    } else {
      return go($originurl . '/error:upload1' . $error);
    }
  }
));


// Insert the saved image for ContentTools
$kirby->set('route', array(
  'pattern' => array('insert-image', '(.+insert-image)'),
  'method' => 'POST',
  'action'  => function() {
    try {
      $items = list($width, $height) = getimagesize($_POST['url']);
      
      if ($items[0] > $_POST['width']) {
        $newwidth = $_POST['width'];
        $newheight = ($_POST['width'] / $items[0]) * $items[1];
      } else {
        $newwidth = $items[0];
        $newheight = $items[1];
      }
  
      $arr = array('url' => $_POST['url'], 'width' => $_POST['width'], 'crop' => $_POST['crop'],
       'alt'=> "Image", 'size' => array($newwidth, $newheight)); // size piece tweaked based on GitHub comments
  
      echo json_encode($arr);
    } catch(Exception $e) {
      $error = true;
      echo "Dang, image not inserted. Blame Andy.";
    }
  }
));



// iCal file for calendar subscriptions
$kirby->set('route', array(
  'pattern' => 'events/webcal',
  'method' => 'GET',
  'action'  => function() {
    $blah = "BEGIN:VCALENDAR
      VERSION:2.0
      PRODID:-//Maker Network//EN 
      CALSCALE:GREGORIAN
      METHOD:PUBLISH
      X-ORIGINAL-URL:http://maker.tufts.edu/events
      X-WR-CALNAME:Maker Network Events
      BEGIN:VTIMEZONE
      TZID:America/New_York
      X-LIC-LOCATION:America/New_York
      BEGIN:DAYLIGHT
      TZOFFSETFROM:-0500
      TZOFFSETTO:-0400
      TZNAME:EDT
      DTSTART:19700308T020000
      RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
      END:DAYLIGHT
      BEGIN:STANDARD
      TZOFFSETFROM:-0400
      TZOFFSETTO:-0500
      TZNAME:EST
      DTSTART:19701101T020000
      RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
      END:STANDARD
      END:VTIMEZONE

    ";
    
    $blah = join("\n", array_map("trim", explode("\n", $blah)));
    echo $blah;

    foreach(site()->page('events')->children()->filterBy('StartDate','<',date('c'))->sortBy('StartDate','asc')->limit(100) as $event) {
      echo "BEGIN:VEVENT" . "\r\n";
      echo "DTSTAMP:" . $event->date('Ymd','Created') . "T" . $event->date('his','Created') . "Z\r\n";
      echo "DTSTART;TZID=America/New_York:" . $event->date('Ymd','StartDate') . "T" . $event->date('His','StartDate') . "\r\n";
      echo "DTEND;TZID=America/New_York:" . $event->date('Ymd','EndDate') . "T" . $event->date('His','EndDate') . "\r\n";
      echo "STATUS:CONFIRMED" . "\r\n";
      echo "SUMMARY:" . $event->title() . "\r\n";
      echo "DESCRIPTION:" . $event->text()->excerpt(200) . "\r\n";
      echo "CLASS:PUBLIC" . "\r\n";
      echo "CREATED:20150911T133713Z" . "\r\n";
      echo "GEO:42.36;-71.18" . "\r\n";
      echo "LOCATION:" . $event->place() . "\r\n";
      echo "URL:" . $event->url() . "\r\n";
      echo "LAST-MODIFIED:20150915T134400Z" . "\r\n";
      echo "UID:" . $event->uid() . "\r\n";
      echo "END:VEVENT" . "\r\n";
      echo "\r\n";
    }
    
    echo "END:VCALENDAR";
  }
));





// Receive Raspberry Pi IP Address
$kirby->set('route', array(
  'pattern' => 'raspberry',
  'action'  => function() {
    try {
      site()->page('raspberry')->update(array(
        'raspberryip'  => param('raspip')
      ));
      echo param('raspip');
    }

    catch(Exception $e) {
      echo $e->getMessage();
    }
  }
));




// Convert pages to new datedata
$kirby->set('route', array(
  'pattern' => 'changepagetimes',
  'action'  => function() {
    foreach(site()->page('challengess')->children() as $targetpage) {
      try {
        $targetpage->create($targetpage->uri() . '1', 'challenge', array(
          'Title'  => $targetpage->title(),
          'DateData' => $targetpage->created() . ', ' . $targetpage->content()->modified() . ' == ' . $targetpage->modifiedby() . ', ' . '2016-09-10 20:50:00' . ', ' . $targetpage->startdate() . ', ' . $targetpage->enddate(),
          'Makers' => $targetpage->makers(),
          'Visibility' => $targetpage->visibility(),
          'Submissions' => $targetpage->submissions(),
          'Color' => $targetpage->color(),
          'Hero' => $targetpage->hero(),
          'Text' => $targetpage->text(),
        ));
      }
      
      catch(Exception $e) {
        echo $e->getMessage();
        echo "no";
      }
    }
    
    echo "dyo";
  }
));







// Change URLs of post pages
$kirby->set('route', array(
  'pattern' => 'posts/(:any)',
  'method' => 'GET',
  'action'  => function($uid) {
    go(site()->url() . '/' . $uid);
  }
));
$kirby->set('route', array(
  'pattern' => '(:num)',
  'method'  => 'GET',
  'action'  => function($uid) {
    $path = kirby()->request()->path();
    $page = page($path);
           
    if (!$page) $page = page('posts/' . $path);
    
    return ($page) ? site()->visit($page) : site()->visit($uid);
  }
));


// Save Site Settings
$kirby->set('route', array(
  'pattern' => array('savesettings', '(.+savesettings)'),
  'method' => 'POST',
  'action'  => function() {
    $settings = $site->settings()->yaml();
    
    // Style
    if ($_POST['theme']) $settings['style']['theme'] = $_POST['theme'];
    
    // Monetization
    if ($_POST['ads']) $settings['monetization']['ads'] = $_POST['ads'];
    
    // Update settings
    site()->update(['settings' => yaml::encode($settings)]);
    
    //echo $settings['style']['theme'];
  }
));



// Unsure, old?
/*
$kirby->set('route', array(
  'pattern' => array('(:num)', '(:any)/new/(:num)'),
  'action'  => function() {
    $url = $_SERVER['REQUEST_URI'];
    return page('new/' . basename($url));
  }
));
*/







$kirby->set('route', array(
  'pattern' => 'search',
  'method' => 'GET',
  'action'  => function() {
    
    // I should probably just use this: https://getkirby.com/docs/cookbook/search
    
    $pages = site()->index();
    
    // If I ever want to do subdirectory-specific searches, this would be how
    // $subpage = explode('/', $_SERVER['REQUEST_URI'])[1];
    // echo $uid;
    
    // This should probably be turned into a snippet too?
    // Just like I did with the content snippet, this woould pass values in and
    // the snippet would return just the HTML that's needed.
    
    $results = new Pages();
    
    // filter by tags
    if (isset($_GET['tags'])) {
      
      $tags = filter_var($_GET['tags'], FILTER_SANITIZE_STRING);
      $tags = explode(',', $tags);
      //print_r($tags);
      
      $pages->filter(function($page) use($tags, $results)  {
        if (array_intersect($page->tags(), $tags)) {
          $results->add($page);
        }
      });
      
    }
    
    // filter by text
    if (isset($_GET['q'])) {
      
      $query = filter_var($_GET['q'], FILTER_SANITIZE_STRING);
      
      $pages->filter(function($page) use($query, $results)  {
        if (stripos($page->text(), $query)) {
          $results->add($page);
        }
      });
      
    }
    
    // sort by date
    $results = $results->sortBy('dateCreated','desc');
    
    //tpl::load( kirby()->roots()->templates() . DS . 'default.php' );
    
    // return results
    foreach ($results as $result) {
      $item_date = date('M d Y', $result->dateCreated());
      echo '<span style="font-family:monospace;">' . $item_date . ' - </span>' . '<a href="' . $result->url() . '">' . $result->title() . '</a>';
      echo '<br>';
    }
    
  }
));
$kirby->set('route', array(
  'pattern' => array('(:any)', '(:any)/(:any)'),
  'method'  => 'GET',
  'action'  => function($uid) {

    $path = kirby()->request()->path();
    $page = page($path);
    
    if (!$page) $page = page('users/' . $path);
    if (!$page) $page = page('posts/' . $path);
    
    if ($page) {
      return site()->visit($page);
    } else {
      /*
      return site()->errorPage();
      go(site()->errorPage());
      return site()->errorPage()->title();
      */
      return site()->visit($uid);
    }
    
  }
));
