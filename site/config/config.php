<?php

if (file_exists(__DIR__ . DS . 'private.php')) {
  require_once __DIR__ . DS . 'private.php'; // load private information
}

//--------------------------------------------------
// Stripe Configuration
/*--------------------------------------------------

There are a few quality of life improvements that can't
be configured from within this file. You'll need to tweak
the htaccess or php.ini file to make these changes.

http://www.php.net/manual/en/ini.list.php

Add the following to the htaccess file:

# Increase max amount of data that can be sent via a POST in a form
php_value post_max_size 100M

*/

// Increase memory limit
ini_set('memory_limit', '512M');

// Increase max file size that a user can upload
ini_set('upload_max_filesize', '100M');

// Increase maximum processing time
ini_set('max_execution_time', 60); // ALL 60 seconds = 1 minute

// Set session lifetime
// User will be logged out if they don't visit again within this timespan. If they do, the timer is essentially reset
// http://natesilva.tumblr.com/post/250569350/php-sessions-timeout-too-soon-no-matter-how-you
// ini_set('session.gc_maxlifetime', 2628000); // 1 month
ini_set('session.gc_maxlifetime', 604800); // 1 week
// ini_set('session.gc_maxlifetime', 86400); // 1 day
// ini_set('session.gc_maxlifetime', 3600); // 1 hour

// Set session save directory
ini_set('session.save_path', dirname(dirname(__FILE__)) . '/sessions');

// Enable PHP's garbage collection method, even on Ubuntu/Debian, with a prob/divisor % chance of happening on each session_start()
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

// Set the kirby_session_auth cookie lifetime to be a month
s::$timeout = 60*24*30; // 1 month
s::$cookie['lifetime'] = 60*24*30; // 1 month


//--------------------------------------------------
// Kirby Configuration
/*--------------------------------------------------

By default you don't have to configure anything to
make Kirby work. For more fine-grained configuration
of the system, please check out http://getkirby.com/docs/advanced/options

*/

// Supposedly changes folders that Kirby creates to be 775 instead of 755
// https://forum.getkirby.com/t/content-folder-permissions-on-create/2846
Dir::$defaults['permissions'] = 0775;


// Add additional classes to the guggenheim gallery element
// c::set('guggenheim.classes', 'gallery zoom margin-center');

// Guggenheim is meant to be used with PhotoSwipe
// But if you for some reason don't want to use it, you can remove it additionals with
c::set('guggenheim.photoswipe', true);

// Guggenheim uses some basic srcset and sizes for basic responsiveness and highres support
// if you want to disable it, and make your own
c::set('guggenheim.srcset', false);
//c::set('guggenheim.width', '700');
c::set('guggenheim.width', '720');
c::set('guggenheim.height', '200');

/* Turn on Kirby's cachebuster */
c::set('cachebuster', true);



/* Caching for anonymous users */
// https://forum.getkirby.com/t/kirby-cache-routes/2032

c::set('cache', false);

if (site()->user()) {
  c::set('cache', false);
}

c::set('cache.ignore', array('sitemap','flush','error','connect','projects/*'));


/* Need to somehow send the ga ID to the user's browser */
c::set('googleAnalyticsID', '209846');
cookie::set('ga', c::get('cache.ignore'), $expires = 60*24*30, $path = '/', $domain = null, $secure = true);



/* Turn on GD Lib's image orientation detection. This fixed iOS image rotation issues.
  - https://getkirby.com/docs/developer-guide/configuration/thumbnails
*/
thumb::$defaults['autoOrient'] = 'true';

// Change the sirvy plugin's path
// http://maker.tufts.edu/api/projects?service=json
c::set('sirvy.path', 'api');

/* Turn on debugging */
if($_SERVER['SERVER_NAME'] == 'makernetwork.org') {
  c::set('debug', true);
}

/* Set the timezone */
c::set('timezone','America/New_York');

/* Increase login session duration
   https://forum.getkirby.com/t/login-session-lifetime-extending-for-the-frontend/2922
*/
//s::$timeout = 60*24*30; // 1 month of session validity
//s::$cookie['lifetime'] = 43000; // expires in 1 month

//cookie::set('kirby_session_auth', $value, $lifetime = 42000, '/blah', $domain = null);
/*
Not including the above makes both kirby_session and kirby_session_auth have an expires of Session
Including them makes it longer - to 2017 or whatever

Eventually the site logs out anyway, but leaves both the kirby_session and kirby_session_auth cookies intact. I'm not sure what the duration is.
If I log out manually, then kirby_session_auth gets destroyed.

*/


//s::$timeout = 120;
//s::set($timeout, $value = 120);
//s::$cookie['lifetime'] = 4200;
//s::set($cookie, $value = 0);
//cookie::set('kirby_session_auth', '', $lifetime = 55, $path = '/', $domain = null);
//cookie::set('kirby_session_auth', $value, $lifetime = 42000, $path = '/', $domain = null, $secure = true, $httpOnly = true);
//c::set('panel.session.timeout', 2160); // 36 hours
//cookie::set('username', site()->user()->username(), $expires = 42000, $path = '/', $domain = null, $secure = true);






c::set('debug', true);






// Override carriage returns between fields when using $page->update()
// https://forum.getkirby.com/t/remove-extra-carriage-returns-between-fields-after-using-page-update/5195
data::$adapters['kd']['encode'] = function($data) {

  $result = array();
  foreach($data AS $key => $value) {
    $key = str::ucfirst(str::slug($key));

    if(empty($key) || is_null($value)) continue;

    // avoid problems with arrays
    if(is_array($value)) {
      $value = '';
    }

    // escape accidental dividers within a field
    $value = preg_replace('!(\n|^)----(.*?\R*)!', "$1\\----$2", $value);

    // multi-line content
    if(preg_match('!\R!', $value, $matches)) {
      $result[$key] = $key . ": \n\n" . trim($value);
    // single-line content
    } else {
      $result[$key] = $key . ': ' . trim($value);        
    }

  }
  return implode("\n----\n", $result);

};



c::set('routes', array(
  

  
	// https://getkirby.com/docs/developer-guide/advanced/routing#omitting-the-blog-folder-in-urls
  // Redirect all requests to /users/ directory to example.com/username
  array(
    'pattern' => 'users/(:any)',
    'method' => 'GET',
    'action'  => function($uid) {
      go(site()->url() . '/' . $uid);
    }
  ),
  
  // ROBOTS.TXT FILE
  array(
    'pattern' => 'robots.txt',
    'action'  => function() {
      echo '<pre style="word-wrap: break-word; white-space: pre-wrap;">';
      if($_SERVER['SERVER_NAME'] != 'drewbaren.com' and $_SERVER['SERVER_NAME'] != 'tuftsmake.com' and $_SERVER['SERVER_NAME'] != 'makernetwork.org') {
        echo 'User-agent: *<br>Disallow: /thumbs/<br>Disallow: /users<br>Disallow: /drafts/<br>Sitemap: ' . site()->url() . '/sitemap';
        foreach (site()->index()->filterBy('visibility','unlisted') as $hiddenpage) {
          echo '<br>Disallow: /' . $hiddenpage->uri();
        }
      } else {
        echo 'User-agent: *<br>Disallow: /';
        foreach (site()->index()->filterBy('visibility','unlisted') as $hiddenpage) {
          echo '<br>Disallow: /' . $hiddenpage->uri();
        }
      }
      echo '</pre>';
    }
  ),
  
  // LOGIN
  array(
		'pattern' => array('login', '(.+login)'), // matches any url ending in login
    'method' => 'POST',
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
  ),
  
  // LOGOUT
  array(
		'pattern' => array('logout', '(.+logout)'), // matches any url ending in logout
    'action'  => function() {
      if($user = site()->user()) {
        $user->logout();
      }
      $currentpath = str_replace(array('/maker/','/ne/','/logout','logout'),'',$_SERVER['REQUEST_URI']);
      return go($currentpath);
    }
  ),

  array(
		'pattern' => array('signup', '(.+signup)'), // matches any url ending in signup
		'method' => 'POST',
    'action'  => function() {
      
      $site = site();

      // redirect logged in users to the homepage
      if($site->user()) go('/');
    
      // handle the signup form submission
      if(r::is('post') and get('signup')) {
        
        // check if the username already exists, and if not, run the signup method
        if(!$site->user(get('username'))) {
          
          // create the new user.php file
        	try {
    			  $user = $site->users()->create(array(
    			    'username'  => get('username'),
    				  'firstname' => get('firstname'),
    			    'lastname'  => get('lastname'),
    			    'email'     => strtolower(get('email')),
    			    'tuftsemail'=> strtolower(get('tuftsemail')),
    			    'password'  => get('password'),
    			    'resetkey'  => '',
    			    'resetdate' => '',
    			    'language'  => 'en',
    			    'usertype'  => 'user',
    			    'registrationdate' => date('Y-m-d H:i:s'),
    			    'usegravatar' => false,
    			    'color'       => strtolower(get('color')),
    			    'school'      => get('school'),
    			    'affiliation' => get('affiliation'),
    			    'department'  => get('dept'),
    			    'major'       => get('major'),
    			    'classyear'   => get('classyear'),
    			    'birthyear'   => get('birthyear'),
    			    'groups'      => 'me-184-robotics',
    			  ));
    			} catch(Exception $e) {
    			  $e->getMessage();
    			}
    			
    			// create the new maker profile page
    			try {
    				$firstandlast = get('firstname') . " " . get('lastname');
    			  $newPage = page('users')->children()->create(get('username'), 'maker', array(
    			    'title' => $firstandlast,
    			    'created' => date('Y-m-d H:i:s'),
    			    'modified' => date('Y-m-d H:i:s'),
    			    'modifiedby' => get('username'),
              'makers' => get('username'),
              'visibility' => 'public',
              'color' => strtolower(get('color')),
              'hero' => '',
    			    'text'  => '',
    			  ));
    			} catch(Exception $e) {
    			  echo $e->getMessage();
    			}
          
          // send user info to the RFID database
          if($site->url() == "https://maker.tufts.edu") {
      			try {
      				$link = pg_Connect(c::get('RFIDcredentials'));
      				$uname = get('username');
      				$fname = get('firstname');
      				$lname = get('lastname');
      				$email = get('email');
      				$Temail = get('tuftsemail');
      				$dept = get('dept');
      				$C_year = get('classyear');
      				$byear = get('birthyear');
      				$Rship = get('affiliation');
      				
              $UserAdd = "INSERT INTO users(uid, uname, fname, lname, email, temail, reg_date, exp_date, dept, class, byear) 
                      VALUES (DEFAULT,'$uname','$fname','$lname','$email','$Temail',DEFAULT,DEFAULT,$dept,$C_year,$byear);";
      				$NewUser = pg_exec($link, $UserAdd);
      				
              // Create admin_log to capture new user creation
              $ip = $_SERVER['REMOTE_ADDR'];
              $log_call = "INSERT INTO Admin_log(uid, action, ip) VALUES ($uid,'Created new user through maker.tufts.edu - $uname', '$ip')";
              $admin_log = pg_exec($link, $log_call);
      				
      			} catch(Exception $e) {
      				echo 'failed to send to Database';
      			  echo $e->getMessage();
      			}
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
  ),
  
  // SEND PASSWORD RESET EMAIL
  array(
    'pattern' => 'forgot',
    'method' => 'POST',
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
            'service' => 'html_email',
            'to'      => $user->email(),
            'from'    => 'happyrobot@maker.tufts.edu',
            'subject' => 'Maker Network Password Reset',
            'body'    => '
              <html>
                <head>
                  <title>Maker Network Password Reset</title>
                </head>
                <body>
                  <p>Hi ' . $user->firstname() . ',</p>
                  <p>You can reset your password by opening this link. It will expire within one hour and can only be used once.</p>
                  <p>' . site()->url() . '/username:' . $user->username() . '/resetkey:' . $resetkey . '/</p>
                  <p>- Maker Network Email Robot</p>
                </body>
              </html>
            '
          ));
          
          if($email->send()) {
            $currentpath = str_replace(array('/maker/','/ne/','/login','login','/forgot','forgot'),'',$_SERVER['REQUEST_URI']);
            return go($currentpath . 'forgot:success');
          } else {
            echo $email->error();
            return go($currentpath . 'forgot:failed');
          }
        	
        } catch(Exception $e) {
          echo $e->getMessage();
        }
  
      } else {
        $error = true;
        //echo "Nope, that user does not exist";
        return go($currentpath . 'forgot:failed');
      }
    }
  ),
  
  // RESET PASSWORDS
  array(
    'pattern' => 'reset',
    'method' => 'POST',
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
  ),
  
  // New page creation at temp location
  array(
		'pattern' => array('/new', '(.+new)'), // matches any url ending in new
    'action'  => function() {
            
      $parenturi = ltrim(strtok(str_replace('/new','',$_SERVER['REQUEST_URI']), '?'), '/');
      //$newpage = $parenturi . '/' . date('His');
      $newpage = $parenturi . '/' . date('YmdHis') . milliseconds();
      
      $pagetype = preg_replace('/s\b/', '', strtolower(site()->page($parenturi)->title()));
      
      if ($pagetype == 'forum') {
        $pagetype = 'forum thread';
      }
      
      $related = (kirby()->request()->has('related')) ? get('related') : '';
      
      if ($related != '') {
        $visibility = 'public';
      } else {
        $visibility = 'private';
      }
      
      /* If this new page is coming from an event page, inherit the event page's visibility setting */
      $originpage = null;
      if ($related != '') {
        $originpage = site()->index()->findBy('uid', $related);
      }
      if ($originpage) {
        if ($originpage->parent() == 'events') {
          $visibility = $originpage->visibility();
        }
      }
      
      $color = (site()->user()) ? site()->user()->color() : site()->coloroptions()->split(',')[0];
      
      $username = (site()->user()) ? site()->user()->username() : 'anonymous-kangaroo-' . date('YmdHis') . milliseconds();
      
      /* If the user is anonymous, give them a cookie with an assigned username */
      if (!site()->user()) {
        cookie::set('anonymousID', $username, $expires = 60*24*30, $path = '/', $domain = null, $secure = true);
      }
      
      try {
        page()->create($newpage, 'page', array(
          'Title' => '',
          'DateData'  => date('Y-m-d H:i:s') . ', ' . date('Y-m-d H:i:s'),
          'UserData' => $username,
          'RelData' => $related,
          'Settings' => $visibility . ', ' . $color . ', comments == off, submissions == off, price == off',
          'Hero' => '',
          'Text' => '',
        ));
        return go($newpage);
      } catch(Exception $e) {
        return page('error');
      }
      
    }
  ),
  
  array(
    //'pattern' => array('new', '(:any)/new/(:any)'),
    'pattern' => array('(:num)', '(:any)/new/(:num)'),
    'action'  => function() {
      $url = $_SERVER['REQUEST_URI'];
      return page('new/' . basename($url));
    }
  ),
	
  // SAVE PAGES
	array(
		'pattern' => array('save', '(.+save)'), // matches any url ending in save
		'method' => 'POST',
		'action'  => function() {
  		
  		// Formatting
  		// ----------
  		// $_POST['page'] = /directory/slug
  		
  		$targetpage = site()->page($_POST['page']);
  		
  		// Account for custom routes
  		if (!$targetpage) $targetpage = page('posts' . $_POST['page']);
  		if (!$targetpage) $targetpage = page('users' . $_POST['page']);
  		
  		//$originaltitle = $targetpage->title();
  		$originaltitle = ($targetpage->title()) ? $targetpage->title() : '';
  		
  		$title = (isset($_POST['title'])) ? $_POST['title'] : $targetpage->title();
  		$text  = (isset($_POST['text'])) ? $_POST['text'] : $targetpage->text();
  		
  		/* DateData */
  		$dateCreated = date('Y-m-d H:i:s', $targetpage->dateCreated());
  		$dateModified  = date('Y-m-d H:i:s');
  		//$modifiedBy = site()->user(get('username'));
  		if (!$targetpage->datePublished() or isset($_POST['visibility'])) {
    		if ($_POST['visibility'] == 'public' or $_POST['visibility'] == 'groups') {
      		$datePublished = date('Y-m-d H:i:s');
    		} else {
      		$datePublished = null;
    		}
  		} else {
    		$datePublished = null;
  		}
  		$datedata = implode(', ', array_filter(array($dateCreated, $dateModified, $datePublished)));
  		
  		/* UserData */
  		$authors = (isset($_POST['users'])) ? $_POST['users'] : implode(', ', $targetpage->authors());
  		
  		if (empty($_POST['users'])) {
    		$authors = $targetpage->authorsRaw();
  		}
  		$oldauthors = '';
  		$subscribers = '';
  		$subscriberEmails = '';
  		$registrants = '';
  		$attendees = '';
  		  $requests = (isset($_POST['requests'])) ? $_POST['requests'] : implode(', ', $targetpage->requests());
  		  $requests = (isset($_POST['join'])) ? implode(', ', array_merge($targetpage->requests(), array($_POST['join']))) : $requests;
  		$userdata = implode(' ///', array_filter(array($authors, $oldauthors, $subscribers, $subscriberEmails, $registrants, $attendees, $requests)));
  		
  		/* RelData */
  		$relatedProjects = (isset($_POST['projects'])) ? $_POST['projects'] : $targetpage->relatedProjects();
  		$relatedGroups   = (isset($_POST['groups'])) ? $_POST['groups'] : $targetpage->relatedGroups();
  		$relatedEvents   = (isset($_POST['events'])) ? $_POST['events'] : $targetpage->relatedEvents();
  		$reldata         = implode(', ', array_filter(array($relatedProjects, $relatedGroups, $relatedEvents)));
  		
  		/* Settings */
  		$visibility  = (isset($_POST['visibility'])) ? $_POST['visibility'] : $targetpage->visibility();
  		$color       = (isset($_POST['color'])) ? $_POST['color'] : $targetpage->color();
      $comments    = 'comments == '    . ((isset($_POST['comments']))    ? $_POST['comments']    : $targetpage->comments());
  		$submissions = 'submissions == ' . ((isset($_POST['submissions'])) ? $_POST['submissions'] : $targetpage->submissions());
  		$price       = 'price == ' . 'off';
  		$settings    = implode(', ', array_filter(array($visibility, $color, $comments, $submissions, $price)));
  		
  		/* Hero */
  		$hero = (isset($_POST['hero'])) ? $_POST['hero'] : $targetpage->hero();
  		
  		/* Menu */
  		$menuSecondary = $_POST['menusecondary'];
  		
  		
  		
      try {
        site()->page($targetpage)->update(array(
          'Title'    => $title,
          'DateData' => $datedata,
          'UserData' => $userdata,
          'RelData' => $reldata,
          'Settings' => $settings,
          'Hero' => $hero,
          'Text'  => $text,
        ));
        //echo var_dump($_POST);
        
        site()->page('site')->update(array(
          'newstuff'  => $menuSecondary,
        ));

        if ($title != $originaltitle and $targetpage->parent() != 'forum') {
          $currentlocation = kirby()->roots()->content() . '/' . $targetpage->diruri();
          $newlocation = kirby()->roots()->content() . '/' . $targetpage->parent()->diruri() . '/' . str::slug($title);
          rename($currentlocation, $newlocation);
          
          $changeurl = site()->url() . '/' . $targetpage->parent()->diruri() . '/' . str::slug($title);
          $changeurl = str_replace('/posts','',$changeurl);
          
          $response = array('changeurl' => $changeurl); // redirect to new url
          echo json_encode($response);
        }
        
      } catch(Exception $e) {
        echo $e->getMessage();
      }
          
		}
	),
	
	
  // SAVE COMMENTS
	array(
		'pattern' => array('saveblah', '(.+saveblah)'), // matches any url ending in save
		'method' => 'POST',
		'action'  => function() {
  		
  		$targetpage = site()->page($_POST['page']);
            
      $slug = date('YmdHis') . milliseconds();
      
      $newpage = $targetpage->uri() . '/comments/' . $slug;
      
      $text  = (isset($_POST['text'])) ? $_POST['text'] : $targetpage->text();
      
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
	),
	
	
	
	
	
	
	
	
	
	
	
  // DELETE PAGES
	array(
		'pattern' => array('delete', '(.+delete)'), // matches any url ending in save
		'method' => 'POST',
		'action'  => function() {
  		
  		$targetpage = site()->page($_POST['page']);
  		$redirecturl = site()->url() . '/' . $targetpage->parent()->diruri();
  		
      try {
        
        $targetpage->delete();
        
        $response = array('redirecturl' => $redirecturl);
        echo json_encode($response);
        
      } catch(Exception $e) {
        echo $e->getMessage();
      }
          
		}
	),
	
  // PURGE CACHE
	array(
		'pattern' => array('purgeCache', '(.+purgeCache)'),
		'method' => 'POST',
		'action'  => function() {
			kirby()->cache()->flush();
			return true;
		}
	),
	
  // REGENERATE PAGE CACHE
  // Visit example.com/whatever/flush to surgically excise and regenerate that page's cache file within /site/cache
  // https://forum.getkirby.com/t/controlling-the-cache/464
  // https://github.com/ChainsawBaby/buildCache
	array(
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
	),
	
  // REPLACE AMAZON LINKS
  // Replaces all shortened Amazon URLs with their regular, clean, full URLs
	array(
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
	),
	
	

  

  

  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
	
	
  // SITEMAP
  // Based on https://getkirby.com/docs/cookbook/xmlsitemap
  // Currently lists every single folder, regardless of whether a page.txt is present
	array(
		'pattern' => array('sitemap', '(.+sitemap)'),
		'method' => 'GET',
		'action'  => function() {
      
      $ignore = array('sitemap', 'error', 'drafts', '_gsdata_');
      
      // send the right header
      header('Content-type: text/xml; charset="utf-8"');
      
      // echo the doctype
      echo '<?xml version="1.0" encoding="utf-8"?>';
      echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
      foreach (site()->pages()->index() as $p) {
        if (!in_array($p->uid(), $ignore)) {
          echo '<url>';
            echo '<loc>' . html(site()->url() . '/' . $p->uri()) . '</loc>';
            echo '<lastmod>' . $p->modified('c') . '</lastmod>';
            echo '<priority>' . (($p->isHomePage()) ? 1 : number_format(0.5/$p->depth(), 1)) . '</priority>';
          echo '</url>';
        }
      }
      echo '</urlset>';
      
		}
	),
	
	
  // UPLOAD NEW
	array(
		'pattern' => array('upload', '(.+upload)'),
		'method' => 'POST',
		'action'  => function() {
  		
  		//var_dump($_FILES);
  		
  		$targetpage = page($_POST['page']); // Account for custom routes
  		if (!$targetpage) $targetpage = page('posts' . $_POST['page']);
  		if (!$targetpage) $targetpage = page('users' . $_POST['page']);
  		
      $target_dir = kirby()->roots()->content() . '/' . $targetpage->uri() . '/';
  		
  		$name = key($_FILES); // The name attribute of the input that was uploaded
  		
  		
  		$file_name = pathinfo($_FILES[$name]['name'], PATHINFO_FILENAME);
  		$file_extension = str_replace('jpeg','jpg',strtolower(pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION)));
  		$file = $file_name . '.' . $file_extension;
      
  		$file_type = $_FILES[$name]['type'];
  		$file_size = $_FILES[$name]['size'];
  		$file_url = str_replace(' ', '%20', site()->contentURL() . '/' . $targetpage->uri() . '/' . $file);
  		
  		//$file_extension = end(explode('.', $file));
  		//$file_extension = substr(strrchr($file,'.'),1);
  		
  		// Lowercase extension
  		$target_file = $target_dir . $file_name . '.' . $file_extension;
  		
      
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
    	
    	$type = (isset($_POST['type'])) ? $_POST['type'] : null;
    	
      // Save the file in the right place
      if ($uploadOk = 1) {
        try {
          
          if (move_uploaded_file($_FILES[$name]['tmp_name'], $target_file)) {
            
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
            
            if ($file_type == 'image') {
              $file_url = (string)kirbytag(array('image' => $file, 'targetpage' => $targetpage, 'output' => 'url'));
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
	),
  // Insert the saved image for ContentTools
	array(
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
	),
	
	

  // SAVE AVATAR OR ICON, or possibly ContentTools uploads and eventually videos and other files as well
  /*
	array(
    'pattern' => array('uploadold', '(.+uploadold)', 'makerbox'),
		'method' => 'POST',
		'action'  => function() {
      try {
        
        //print_r($_REQUEST);
        
        // All this code just to get the target uri should probably be replaced with template logic instead just to pass it along. For now I'm putting it in this statement to allow the makerbox to work, but this should be fixed elsewhere eventually
        if (empty($_POST['username'])) {
          $originurl = preg_replace('/\/[^\/]+:.+\/?$/','',$_SERVER['HTTP_REFERER']); // removes Kirby parameters like /../error:whatever/message:blah
          $originslug = basename($originurl);
          
          // need to get the page's uri path but also account for the homepage potentially not being located at /
          $scheme = parse_url($originurl, PHP_URL_SCHEME);
          $hostname = parse_url($originurl, PHP_URL_HOST);
          $baseurl = $scheme . '://' . $hostname . '/' . basename(site()->url());
          $targeturi = str_replace($baseurl,'',$originurl);
        }
        
        // gets us something like /var/www/drewbaren.com/maker/content/frontend-editing-example/
          $originurl = preg_replace('/\/[^\/]+:.+\/?$/','',$_SERVER['HTTP_REFERER']); // removes Kirby parameters like /../error:whatever/message:blah
          $originslug = basename($originurl);
          
          // need to get the page's uri path but also account for the homepage potentially not being located at /
          $scheme = parse_url($originurl, PHP_URL_SCHEME);
          $hostname = parse_url($originurl, PHP_URL_HOST);
          $baseurl = $scheme . '://' . $hostname . '/' . basename(site()->url());
          $targeturiblah = str_replace($baseurl,'',$originurl);
      
      //echo $targeturiblah;
        
        //$newnew = str_replace('https://makernetwork.org/','',$targeturiblah);
        $newnew = str_replace(site()->url() . '/','',$targeturiblah);
        
        $pathParts = explode( '/', parse_url($targeturiblah, PHP_URL_PATH) );
        $lastParts = array( array_pop($pathParts), array_pop($pathParts) );
        $targeturi = $newnew;
        
        $reload = 0;

        // Images uploaded with username data are from Makerboxes, and should go to maker profiles
        if (!empty($_POST['username'])) {
          $targeturi = '/users/' . $_POST['username'] . '/gallery';
          $target_dir = kirby()->roots()->content() . $targeturi . '/';
          if (!file_exists($target_dir)) {
            mkdir($target_dir, 0775, true);
          }
        }
        
        // ContentTools (and makerbox) image uploads
        if (!empty($_FILES['image']['name'])) {
          $name = 'image'; // use the image's regular filename
          //$target_dir = kirby()->roots()->content() . $targeturi . '/';
          $target_dir = kirby()->roots()->content() . '/' . $targeturi . '/';
          $target_filename = pathinfo($_FILES['image']['name'],PATHINFO_FILENAME);
        }
        
        //if (!empty($_POST['hero'])) {
        if (!empty($_FILES['hero']['name'])) {
          $name = 'hero';
          //$target_dir = kirby()->roots()->content() . $targeturi . '/';
          $target_dir = kirby()->roots()->content() . '/' . $targeturi . '/';
          $target_filename = 'hero';
          site()->page($targeturi)->touch();
          $reload = 1;
        }
        //if (!empty($_POST['avatar'])) {
        if (!empty($_FILES['avatar']['name'])) {
          $name = 'avatar';
          $target_dir = kirby()->roots()->avatars() . '/';
          $target_filename = $originslug;
          $reload = 1;
        }
        
        $extension = strtolower(pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION));
        $extension = ($extension == 'jpeg' ? 'jpg' : $extension); // rename jpeg to jpg because it's nicer
        
        $target_file = $target_dir . $target_filename . '.' . $extension;
        
        $uploadOk = 1;

        
        // Allow certain file formats
        if($extension != 'jpg' && $extension != 'png' && $extension != 'jpeg' && $extension != 'gif' ) {
          $uploadOk = 0;
          $error = "heyyy" . $target_dir . "blah";
        }
        
        // See whether $uploadOk was set to 0 by one of the checks above
        if ($uploadOk == 0) {
          return go($originurl . '/error:upload1' . $error);
        } else {
          
          // Check for any old files with the same name, of any extension, and delete them
        	$oldfiles = glob($target_dir . $target_filename . ".*");
        	foreach ($oldfiles as $oldfile) {
        		unlink($oldfile);
        	}
          
          // Save the file in the right place
          if (move_uploaded_file($_FILES[$name]["tmp_name"], $target_file)) {

            // Use GD Lib to detect image orientation and rotate if necessary
            //  - https://forum.getkirby.com/t/wrong-orientation-of-images-portrait-landscape-after-upload/692/19
            //  - https://github.com/getkirby/starterkit/blob/0b4a6e8cf929237621d77adb996e98b08d234004/kirby/toolkit/lib/thumb.php
            
            try {
              $img = new abeautifulsite\SimpleImage($target_file);
              //echo $target_file;
              $img->auto_orient();
              @$img->save($target_file);
            } catch(Exception $e) {
              echo "Error rotating image";
            }
            
            if (empty($_POST['username'])) {
              $blah = $originurl . '/' . $target_filename . '.' . $extension;
              //echo $blah;
              $blah = str_replace(' ', '%20', $blah);
              $arr = array('size' => $_FILES["image"]["size"], 'url' => $blah, 'filename' => $target_filename . '.' . $extension);
              echo json_encode($arr, JSON_UNESCAPED_SLASHES);
            }
            
            if ($reload == 1) {
              return go($originurl); // Successfully moved
            }
            
            if (!empty($_POST['username'])) {
              // send an email when the image comes from the RFID system
              try {
                $email = email(array(
                  'service' => 'html_email',
                  'from'    => 'happyrobot@maker.tufts.edu',
                  'subject' => 'New photo from the Maker Studio',
                  'body'    => '
                    <html>
                      <head>
                        <title>New photo from the Maker Studio</title>
                      </head>
                      <body>
                        <p>The photo you just snapped from the Maker Studio has been added to your Maker Profile.</p>
                        <p>- Maker Network Email Robot</p>
                      </body>
                    </html>
                  '
                ));
                
                // Multiple recipients
                // https://forum.getkirby.com/t/send-e-mail-to-multiple-receipients/2903/6
                //foreach(array('andybraren@gmail.com','tuftsceeohelp@gmail.com') as $to) {
                foreach(array('andybraren@gmail.com') as $to) {
                  $result = $email->send(array('to' => $to));
                }
                
              } catch(Exception $e) {
                echo $e->getMessage();
              }
            }
            
          }
        }
        
      } catch(Exception $e) {
        //return go($originurl . '/error:upload3');
        echo $e;
      }
    }
	),

  // SAVE IMAGES
  // https://github.com/GetmeUK/ContentTools/issues/38
	array(
    'pattern' => array('upload-image', '(.+upload-image)'),
		'method' => 'POST',
		'action'  => function() {
      try {
        
        //print_r($_REQUEST);
        
        // gets us something like /var/www/drewbaren.com/maker/content/frontend-editing-example/
        $target_dir = kirby()->roots()->content() . '/' . str_replace(array('/maker/','/ne/','/upload-image'),'',$_SERVER['REQUEST_URI']) . '/';
        $target_url = site()->url() . '/' . str_replace(array('/maker/','/ne/','/upload-image'),'',$_SERVER['REQUEST_URI']) . '/';
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Check if image file is an actual image or a fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
          //echo "Sorry, file already exists.";
          //echo "File already exists. Overwriting.";
          //echo $target_file;
          unlink($target_file);
          //$uploadOk = 0;
          $uploadOk = 1;
        }
        // Check file size
        
        if ($_FILES["image"]["size"] > 1500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "JPG" &&
           $imageFileType != "png" && $imageFileType != "PNG" &&
           $imageFileType != "jpeg"&& $imageFileType != "JPEG" &&
           $imageFileType != "gif" && $imageFileType != "GIF") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                
                // Use GD Lib to detect image orientation and rotate if necessary
                //  - https://forum.getkirby.com/t/wrong-orientation-of-images-portrait-landscape-after-upload/692/19
                //  - https://github.com/getkirby/starterkit/blob/0b4a6e8cf929237621d77adb996e98b08d234004/kirby/toolkit/lib/thumb.php
                
                try {
                  $img = new abeautifulsite\SimpleImage($target_file);
                  //echo $target_file;
                  $img->auto_orient();
                  @$img->save($target_file);
                } catch(Exception $e) {
                  echo "Error rotating image";
                }
    
                //$arr = array('size' => $_FILES["image"]["size"], 'url' => 'https://drewbaren.com/maker/content/frontend-editing-example/'.$_FILES["image"]["name"].'');
                $arr = array('size' => $_FILES["image"]["size"], 'url' => $target_url.$_FILES["image"]["name"].'');


                echo json_encode($arr);
    
            } else {
                echo "Sorry, there was an error uploading your file.";
                echo '<br>' . $target_dir;
            }
        }

      } catch(Exception $e) {
        $error = true;
        echo "Dang, image not uploaded. Blame Andy.";
      }
    }
	),
  */



















  // ICAL
  array(
    'pattern' => 'events/webcal',
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
  ),

  // RECEIVE RASPBERRY PI IP ADDRESS
  array(
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
  ),
  


  // Convert pages to new datedata
  array(
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
  ),




  // Convert pages to new datedata
  array(
    'pattern' => 'stripe',
    'method' => 'POST',
    'action'  => function() {
      
      require_once('../site'); // need to find the right place
      
      \Stripe\Stripe::setApiKey("sk_test_RHH3uO4EmYB9qobfbPzvuYGd"); // Set secret key
      $token = $_POST['stripeToken']; // Get the form's Stripe token
      $email = $_POST['stripeEmail']; // Get the form's email
      
      try { // Charge the card
        $charge = \Stripe\Charge::create(array(
          "amount" => 1000, // Amount in cents
          "currency" => "usd",
          "source" => $token,
          "description" => "Example charge"
          ));
      } catch(\Stripe\Error\Card $e) {
        // The card has been declined
      }
      
      
      echo "hi";
      
    }
  ),


  array(
    'pattern' => 'posts/(:any)',
    'method' => 'GET',
    'action'  => function($uid) {
      go(site()->url() . '/' . $uid);
    }
  ),


));








