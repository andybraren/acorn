<?php echo (c::get('cache') == true) ? '<!-- Cached ' . date('Y-m-d H:i:s e') . ' ' . site()->url() . $_SERVER['REQUEST_URI'] . ' -->' : '' ?>

<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <?php if($page->isHomePage()): ?>
  <title><?php echo $site->title()->html() ?></title>
  <?php else: ?>
  <title><?php echo $page->title()->html() ?> | <?php echo $site->title()->html() ?></title>
  <?php endif ?>

  <meta name="description" content="<?php echo $site->description()->html() ?>">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  
	<link rel="shortcut icon" type="image/png" href="<?php echo url('assets/images/logo-favicon.png') ?>">
	<link rel="apple-touch-icon" href="<?php echo url('assets/images/apple-touch-icon.png') ?>">
	
  <?php echo css('assets/css/main.css') ?>

  <?php // Load page-specific css ?>
  <?php foreach($page->files()->filterBy('extension', 'css') as $css): ?>
  <?php echo css($css->url()) ?>
  <?php endforeach ?>

  <?php // Load page-specific javascript ?>
  <?php foreach($page->files()->filterBy('extension', 'js') as $js): ?>
  <?php echo js($js->url(), true) ?>
  <?php endforeach ?>
  
  <!--
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  -->
  
  <?php // Necessary for sticky TOC until better supported ?>
  <?php echo js('assets/js/stickyfill/stickyfill.min.js', true) ?>
  <?php echo js('assets/js/main.js', true) ?>
  
  <?php // Hotjar Tracking Code ?>
  <?php if ($hotjarid = array_search($_SERVER['SERVER_NAME'], array('209846' => 'drewbaren.com', '286199' => 'tuftsmake.com', '232998' => 'maker.tufts.edu'))): ?>
    <script>
      (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:<?php echo $hotjarid ?>,hjsv:5};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
      })(window,document,'//static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
  <?php endif ?>
  
  <?php // Google Analytics Tracking Code ?>
  <?php /*
  <?php if($_SERVER['SERVER_NAME'] != 'makernetwork.org'): ?>
    <?php if ($analyticsID = array_search($_SERVER['SERVER_NAME'], array('209846' => 'drewbaren.com', 'UA-43696470-1' => 'tuftsmake.com', 'UA-55995707-1' => 'maker.tufts.edu'))): ?>
      <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', '<?php echo $site->googleanalytics() ?>', 'auto');
        ga('send', 'pageview');
      </script>
    <?php endif ?>
  <?php endif ?>
  */?>

</head>

<?php
  if ($page->color() != '') {
    $color = $page->color();
  } elseif ($page->parent() == 'users' and $user = $site->user($page->slug()) and $user->color() != '') {
    $color = $user->color();
  } else {
    $color = $site->coloroptions()->split(',')[0];
  }
?>

<?php $subnav = (hasSubMenu()) ? ' subnav' : ''; ?>

<body class="<?php echo $color . $subnav ?>">
  
  <header id="top" class="headroom">
    
      <?php snippet('menu') ?>
    
  </header>

  <?php snippet('modals') ?> 