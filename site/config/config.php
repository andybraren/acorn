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
ini_set('session.gc_maxlifetime', 2628000); // 1 month
// ini_set('session.gc_maxlifetime', 604800); // 1 week
// ini_set('session.gc_maxlifetime', 86400); // 1 day
// ini_set('session.gc_maxlifetime', 3600); // 1 hour
//ini_set('session.gc_maxlifetime', 10); // 1 hour

// Set session save directory
$session_dir = kirby()->roots()->index() . DS . 'cache' . DS . 'sessions';
if (!is_dir($session_dir)) {
  mkdir($session_dir, 0775, true);
}
ini_set('session.save_path', $session_dir);

// Enable PHP's garbage collection method, even on Ubuntu/Debian, with a prob/divisor % chance of happening on each session_start()
//ini_set('session.gc_probability', 1);
//ini_set('session.gc_divisor', 100);

// Set the kirby_session_auth cookie lifetime to be a month
s::$timeout = 60*24*30; // 1 month
s::$cookie['lifetime'] = 60*24*30; // 1 month


//--------------------------------------------------
// SSL / HTTPS
// - applies to all pages
//--------------------------------------------------

/* The problem with setting this within Kirby is that images and assets can still be navigated to directly without the https */
/* Need to find a way to dynamically set the htaccess file, without impacting its performance */

c::set('ssl', false); // Using htaccess instead

if (c::get('ssl') == true) {
  
  $ContentSecurityPolicy = 'Content-Security-Policy: default-src \'none\'; style-src \'self\' \'unsafe-inline\'; font-src \'self\' themes.googleusercontent.com; img-src *; media-src *; object-src \'none\'; script-src \'self\' www.google-analytics.com ajax.googleapis.com; child-src https://player.vimeo.com https://www.youtube.com https://acorn.blog; connect-src \'self\'';
  
  header($ContentSecurityPolicy);
  
  header('Strict-Transport-Security: max-age=15768000; includeSubDomains');
  header('X-Content-Type-Options: nosniff');
  
  // Block site from being framed with X-Frame-Options and CSP, for IE/Edge, superceded by CSS frame-ancestors
  header('X-Frame-Options: DENY');
  
  // Prevent reflected XSS attacks, for old browsers, superceded by CSP unsafe-inline
  header('X-XSS-Protection: 1; mode=block');
  
}

//--------------------------------------------------
// Kirby Configuration
/*--------------------------------------------------

By default you don't have to configure anything to
make Kirby work. For more fine-grained configuration
of the system, please check out http://getkirby.com/docs/advanced/options

*/

// All folders that Kirby creates should be be group writeable (775)
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
c::set('guggenheim.width', '700');
c::set('guggenheim.height', '200');

/* Turn on Kirby's cachebuster */
c::set('cachebuster', true);


/* Caching for anonymous users */
// https://forum.getkirby.com/t/kirby-cache-routes/2032

/*
if (site()->setting('advanced/cache')) {
  c::set('cache', true);
}
*/

c::set('cache', false);
c::set('cache.ignore', array('sitemap','flush','error','connect','projects/*'));

/*
if (site()->user()) {
  c::set('cache', false);
}
*/


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
if (site()->setting('advanced/debug')) {
  c::set('debug', true);
} else {
  c::set('whoops', false);
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












