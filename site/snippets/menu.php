<?php $count = 1; ?>
<?php $number = sizeof(navArrayYAML()) ?>

<?php

  $activeTop = activeMenuItems()[0];
  $activeSub = activeMenuItems()[1];
  
?>

<nav id="navigation" role="navigation" class="newnav">
  <div class="container">
    
    <ul class="menu">
      
      <? ///// LOGO ///// ?>
      <?php
        $logo     = site()->page('site')->images()->findBy('name', 'logo');
        $logoIcon = site()->page('site')->images()->findBy('name', 'logo-icon');
      ?>
      <?php if ($logo or $logoIcon): ?>
        <a class="logo" href="<?php echo url() ?>">
          
          <?php if ($logo): ?>
            <div id="logo-1">
              
              <?php
                if ($logo->extension() == 'svg') {
                  echo $logo->content();
                } elseif (in_array($logo->extension(), array('jpg','png','gif','ico','tiff','bmp'))) {
                  echo '<img src="' . $logo->url() . '">';
                }
              ?>
              
            </div>
          <?php endif ?>
          
          <?php if ($logoIcon): ?>
            <div id="logo-2">
              
              <?php
                if ($logoIcon->extension() == 'svg') {
                  echo $logoIcon->content();
                } elseif (in_array($logoIcon->extension(), array('jpg','png','gif','ico','tiff','bmp'))) {
                  echo '<img src="' . $logoIcon->url() . '">';
                }
              ?>
              
            </div>
          <?php endif ?>
          
        </a>
      <?php else: ?>
        <li><a href="<?php echo url() ?>">Home</a></li>
      <?php endif ?>
      
      <? ///// PRIMARY MENU ITEMS ///// ?>
      <?php foreach (yaml(site()->MenuPrimary()) as $item): ?>
      
        <?php
          $active = null;
          $missing = null;
          $classes = array();
          
          // add active class
          if ($item['uid'] == $activeTop) {
            $active = true;
            $classes[] = 'active';
          }
          
          // add missing class
          if (!site()->page($item['uid'])) {
            $missing = true;
            $classes[] = 'missing';
          }
          
          // combine classes
          $class = ($active or $missing) ? ' class="' . implode(' ', $classes) . '"' : '';
          
          // check subtitle
          $subtitle = (array_key_exists('subtitle', $item)) ? '<div class="subtitle">' . $item['subtitle'] . '</div>' : '';
        ?>
        
        <li<?php echo $class ?>>
          <a href="<?php echo site()->url() . '/' . $item['uid'] ?>"><?php echo $item['title'] . $subtitle ?></a>
        </li>
      <?php endforeach ?>
      
    </ul>
    
    <ul class="menu menu-secondary">
    
      <?php foreach (yaml(site()->MenuSecondary()) as $item): ?>
        <?php
          $title = $item['title'];
          $subtitle = (array_key_exists('subtitle', $item)) ? '<div class="subtitle">' . $item['subtitle'] . '</div>' : '';
          if (array_key_exists('url', $item)) {
            $url = $item['url'];
          } elseif (array_key_exists('uid', $item)) {
            $url = site()->url() . '/' . $item['uid'];
          } else {
            $url = null;
          }
          $url = ($url) ? ' href="' . $url . '"' : '';
        ?>
        <li>
          <a<?php echo $url ?>><?php echo $title . $subtitle ?></a>
        </li>
      <?php endforeach ?>
                
      <?php if ($site->user()): ?>
        <li><a href="<?php echo $page->url() . '/logout' ?>">Logout</a></li>
      <?php endif ?>
      
      <li class="login">
        <?php if($user = $site->user()): ?>
          <a id="datausername" href="<?php echo $site->url() . "/makers/" . $user->username() ?>" data-username="<?php echo $user->username() ?>">
            <?php echo esc($user->firstName()) ?>
          </a>
        <?php else: ?>
          <a id="button-login" class="login" data-modal="login">Log in</a>
        <?php endif ?>
      </li>
      
    </ul>
    
  </div>
</nav>


<?php if (hasSubMenu()): ?>
  
  <nav id="subnavigation" role="navigation">
    <div class="container">
      
      <ul class="menu">
        
        <?php if(preg_match_all('/(?<!#)#{2,3}([^#].*)\n/', $page->text(), $matches)): // Grabs H2's and H3's ?>
          <li id="toggle-toc">
            <a><?php echo (new Asset('/assets/images/menu-toc.svg'))->content() ?></a>
          </li>
        <?php endif ?>
        
        <?php foreach (submenuItems() as $item): ?>
          <?php
            $url = ($site->page($item['uid'])) ? 'href="' . $site->url() . '/' . $item['uid'] . '"' : '';
            $classarray = array();
              if (!site()->page($item['uid'])) $classarray[] = 'missing';
              if ($item['uid'] == $activeSub)  $classarray[] = 'active';
            $class = (!empty($classarray)) ? ' class="' . implode(' ', $classarray) . '"' : '';
          ?>
          <li<?php echo $class ?>>
            <a <?php echo $url ?>><?php echo $item['title'] ?></a>
          </li>
        <?php endforeach ?>
        
      </ul>
      
      <ul class="menu menu-secondary">
        <li class="search">
          <form class="search-container" action="<?php echo $site->url() . '/search'?>">
            <a><?php echo (new Asset('/assets/images/menu-search.svg'))->content() ?></a>
            <input id="search-box" type="text" class="search-box" name="s">
            <input type="submit" id="search-submit">
          </form>
        </li>
        
        <li>
          <a id="settings-reading"><?php echo (new Asset('/assets/images/menu-font.svg'))->content() ?></a>
        </li>
      </ul>
      
    </div>
  </nav>
<?php endif ?>







