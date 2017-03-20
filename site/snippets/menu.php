<?php $count = 1; ?>
<?php $number = sizeof(navArray()) ?>

<?php

  $activeTop = activeMenuItems()[0];
  $activeSub = activeMenuItems()[1];
  
?>

<?php foreach (navArray() as $item): ?>
  
  <?php if ($count == 1): ?>
    <nav id="navigation" role="navigation" class="newnav">
      <div class="container">
        
        <ul class="menu">
          
          <a class="logo" href="<?php echo url() ?>">
            <div id="logo-1">
              <?php echo (new Asset('/assets/images/logo.svg'))->content() ?>
            </div>
            <div id="logo-2">
              <?php echo (new Asset('/assets/images/logo-icon.svg'))->content() ?>
            </div>
          </a>
  <?php endif ?>
          
          

          <?php
            
            $active = null;
            $missing = null;
            $classarray = array();
            
            // Active
            if ($item['uid'] == $activeTop) {
              $active = true;
              $classarray[] = 'active';
            }
            
            // Missing
            if (!site()->page($item['uid'])) {
              $missing = true;
              $classarray[] = 'missing';
            }
            
            // Create class to echo
            if ($active or $missing) {
              $class = ' class="' . implode(' ', $classarray) . '"';
            } else {
              $class = '';
            }
            
          ?>
          
          <?php $subtitle = (array_key_exists('subtitle', $item)) ? '<div class="subtitle">' . $item['subtitle'] . '</div>' : '' ?>
          
          <li<?php echo $class ?>>
            <a href="<?php echo site()->url() . '/' . $item['uid'] ?>"><?php echo $item['title'] . $subtitle ?></a>
          </li>
  
  <?php if ($count == $number): ?>
        </ul>
        
        <ul class="menu menu-secondary">
          <?php if ($_SERVER['SERVER_NAME'] == 'makernetwork.org'): ?>
            <li><a href="<?php echo $site->page('docs')->url() ?>" <?php echo ($page->uid() == 'docs' OR $page->isChildOf('docs')) ? ' class="active"' : ''; ?>>Docs</a></li>
            <li><a href="https://github.com/andybraren/network">Download</a></li>
          <?php elseif ($_SERVER['SERVER_NAME'] == 'tuftsmake.com'): ?>
            <li><a href="&#109;&#97;ilto&#58;and%79&#98;rare%&#54;&#69;&#64;g&#109;a%&#54;9l&#46;&#99;%6&#70;m">Report an issue</a></li>
          <?php else: ?>
            <li><a href="<?php echo $site->page('about')->url() ?>">About</a></li>
            <li><a href="https://www.facebook.com/groups/535093299989314">Follow</a></li>
          <?php endif ?>
          
            <li><a href="&#109;&#97;ilto&#58;and%79&#98;rare%&#54;&#69;&#64;g&#109;a%&#54;9l&#46;&#99;%6&#70;m">Contact</a></li>
          
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
    
  <?php endif ?>
  
  <?php $count++; ?>
  
<?php endforeach ?>


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
            $url = ($site->page($item['uid'])) ? $site->url() . '/' . $item['uid'] : '';
            $classarray = array();
              if (!site()->page($item['uid'])) $classarray[] = 'missing';
              if ($item['uid'] == $activeSub)  $classarray[] = 'active';
            $class = (!empty($classarray)) ? ' class="' . implode(' ', $classarray) . '"' : '';
          ?>
          <li<?php echo $class ?>>
            <a href="<?php echo $url ?>"><?php echo $item['title'] ?></a>
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







