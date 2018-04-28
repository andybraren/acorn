<?php echo (c::get('cache') == true) ? '<!-- Cached ' . date('Y-m-d H:i:s e') . ' ' . site()->url() . $_SERVER['REQUEST_URI'] . ' -->' : '' ?>

<?php
  if ($site->setting('style/theme') == 'boxed') {
    $classes = ' class="boxed"';
    $shim = true;
  } else {
    $classes = '';
    $shim = false;
  }
?>

<!DOCTYPE html>
<html lang="en"<?php echo $classes ?>>
<head>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  

  <?php if($page->isHomePage()): ?>
    <title><?php echo $site->title()->html() ?></title>
  <?php else: ?>
  
    <?php $title = (preg_match("/[a-z]/i", $page->content()->title())) ? $page->content()->title() : 'Post'; ?>
    
    <title><?php echo $title . ' | ' . $site->title()->html() ?></title>
  <?php endif ?>
  
  <meta name="description" content="<?php echo $site->description() ?>">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  
  <?php if (site()->setting('advanced/lockdown') == true): ?>
  <meta name="robots" content="noindex, noimageindex, nofollow, noarchive, noimageindex">
  <?php endif ?>
  
  <meta property="og:title" content='<?php echo $page->title() ?>'>
  <meta property="og:description" content="<?php echo $page->description() ?>">
  <meta property="og:site_name" content='<?php echo $site->title() ?>'>
  <meta property="og:type" content="article">
  <meta property="og:url" content="<?php echo $page->url() ?>">
  <?php if ($page->heroImage() != null): ?>
  <meta property="og:image" content="<?php echo $page->heroImage()->acornURL() ?>">
  <?php endif ?>
	
	<?php if ($favicon = site()->images()->findBy('name', 'logo-favicon')): ?>
	<link rel="icon" type="image/png" href="<?php echo $favicon->acornURL() ?>">
	<link rel="shortcut icon" type="image/png" href="<?php echo $favicon->acornURL() ?>">
	<?php endif ?>
	
	<?php $appletouch = site()->images()->findBy('name', 'logo-apple-touch') ?>
	<?php if ($appletouch): ?>
	<link rel="apple-touch-icon" href="<?php echo $appletouch->acornURL() ?>">
	<?php endif ?>
	
	<link rel="alternate" type="application/rss+xml" title="<?php echo $site->title() ?>" href="/feed.rss" />
	
  <?php echo css('acorn/assets/css/main.css') ?>
  
  <?php // Load page-specific css ?>
  <?php foreach($page->files()->filterBy('extension', 'css') as $css): ?>
  <?php echo css($css->url()) ?>
  <?php endforeach ?>

  <?php // Load page-specific javascript ?>
  <?php foreach($page->files()->filterBy('extension', 'js') as $js): ?>
  <?php echo js($js->url(), true) ?>
  <?php endforeach ?>
  
  <?php echo js('acorn/assets/js/main.js', true) ?>
      
  <?php // Hotjar Tracking Code ?>
  <?php if ($hotjarid = array_search($_SERVER['SERVER_NAME'], array('286199' => 'tuftsmake.com', '232998' => 'maker.tufts.edu'))): ?>
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

</head>

<?php
  /*
  if ($page->color() != '') {
    $color = $page->color();
  } elseif ($page->parent() == 'users' and $user = $site->user($page->slug()) and $user->color() != '') {
    $color = $user->color();
  } elseif ($site->setting('style/default-color')) {
    $color = $site->setting('style/default-color');
  }
  */
?>

<?php $subnav = (hasSubMenu()) ? ' subnav' : ''; ?>

<body class="<?php echo $page->color() . $subnav ?>" data-color="<?php echo $page->color() ?>">
  
  
  <?php if ($shim): ?>
    <div id="shim" class="container"></div>
  <?php endif ?>
  
  <header id="top" class="headroom">
    
      <?php snippet('menu') ?>
      
      <div class="placemat container"></div>
    
  </header>

  <?php snippet('modals') ?> 