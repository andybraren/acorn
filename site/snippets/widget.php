<?php // Master widget snippet for makers, projects, events, tools, etc. ?>

<?php if ($type != null): ?>

<?php // RELATED SPACES AND HANDBOOKS ?>
<?php if ($type == 'spaces' or $type == 'handbooks'): ?>
  
  <?php $divechoed = false; ?>
  
  <?php if ($site->page($type)): ?>
    <?php foreach($site->page($type)->children() as $relatedpage): ?>
      <?php if (in_array($relatedpage->slug(), $page->related())): ?>    
        <?php if (!$divechoed): ?>
          <div class="widget">
            <?php
              switch ($type) {
                case 'spaces':    $title = 'SPACES'; break;
                case 'handbooks': $title = 'HANDBOOKS'; break;
              }
            ?>
            <span class="heading"><?php echo $title ?></span>
            <?php $divechoed = true ?>
        <?php endif ?>
            <a href="<?php echo $relatedpage->url() ?>">
              <?php if ($type == 'spaces' and $page->parent() == 'equipment'): ?>
                <div class="row silver indicator">
                  <!--<img src="<?php echo $relatedpage->images()->first()->crop(40)->url() ?>" width="40" height="40">-->
                  <div class="column">
                    <span><?php echo $relatedpage->title() ?></span>
                    <!--<span class="indicator"># of # Available</span>-->
                    <span class="indicator"># of # Available</span>
                  </div>
                </div>
              <?php else: ?>
                <div class="row">
                  <span><?php echo $relatedpage->title() ?></span>
                </div>
              <?php endif ?>
            </a>
      <?php endif ?>
    <?php endforeach ?>
  <?php endif ?>
    
  <?php if ($divechoed == true): ?>
    </div>
  <?php endif ?>
    
<?php endif ?>








<?php // RELATED EQUIPMENT ?>
<?php if ($type == 'equipment'): ?>
  
  <?php $divechoed = false; ?>
  
  <?php if ($site->page('equipment')): ?>
    <?php foreach($site->page('equipment')->children() as $equipmentpage): ?>
      <?php if ($equipmentpage->related() != null and in_array(page()->slug(), $equipmentpage->related())): ?>
      
        <?php if (!$divechoed): ?>
          <div class="widget">
            <span class="heading">EQUIPMENT</span>
            <?php $divechoed = true ?>
        <?php endif ?>
            <a href="<?php echo $equipmentpage->url() ?>">
              <div class="row silver indicator">
                <img src="<?php echo $equipmentpage->images()->first()->crop(40)->url() ?>" width="40" height="40">
                <div class="column">
                  <span><?php echo $equipmentpage->title() ?></span>
                  <?php if ($page->parent() == 'spaces'): ?>
                    <span class="indicator">Status: unknown</span>
                  <?php else: ?>
                    <span class="indicator"># of # Available</span>
                  <?php endif ?>
                </div>
              </div>
            </a>
      <?php endif ?>
    <?php endforeach ?>
  <?php endif ?>
    
  <?php if ($divechoed == true): ?>
    </div>
  <?php endif ?>
    
<?php endif ?>


<?php // PURCHASE ?>
<?php if ($type == 'purchase' and $page->price() != ''): ?>

  <div class="widget">
    <span class="heading">PURCHASE</span>
    <?php echo $page->price(); ?>
  </div>
  
  <?php
  
    // Set the correct public key, based on whether test_mode is enabled
    $pkey = (c::get('stripe_test_mode')) ? c::get('stripe_test_publishable_key') : c::get('stripe_live_publishable_key');
    
    // Set some variables
    $currency = c::get('stripe_currency');
    $displayAmount = $page->price();
  
    if ($page->price() == null) {
      $amount = c::get('stripe_default_amount');
    } else {
      $amount = str_replace('.', '', $page->price());
      $amount = str_replace(',', '', $amount);
    }
    $checkoutName = $site->title();
    $checkoutDescription = ($page->priceDescription()) ? $page->priceDescription() : c::get('stripe_default_description');
  
    // Check if an icon has been set. 
    $logo = (c::get('stripe_icon')) ? c::get('stripe_icon_location') : null;
  
    // Check if "Remember Me" has been enabled
    $rememberMe = (c::get('stripe_remember_me')) ? 'data-allow-remember-me="false"' : null;
  
    // Process the charge
    if (isset($_POST['stripeToken'])) {
      stripeCheckout();
      return;
    }
  
  ?>
  
  <form action="/stripe" method="POST">
    <script
      src="https://checkout.stripe.com/checkout.js" class="stripe-button"
      data-key="<?php echo $pkey ?>"
      data-amount="<?php echo $amount ?>"
      data-name="<?php echo $checkoutName ?>"
      data-description="<?php echo $checkoutDescription ?>"
      data-image="<?php echo $logo ?>"
      data-locale="auto"
      data-zip-code="true">
    </script>
  </form>
    
<?php endif ?>


<?php // LINKS ?>
<?php if ($type == 'links' and $page->links() != null): ?>
  
  <div class="widget">
    <span class="heading">LINKS</span>
    <ul>
      <?php foreach ($page->links() as $link): ?>
        <?php $part = str::split($link, '==') ?>
        <?php
          switch (true) {
            case str::contains($part[1],'facebook'):
              $linkid = 'link-facebook'; break;
            case str::contains($part[1],'twitter'):
              $linkid = 'link-twitter'; break;
            case str::contains($part[1],'github'):
              $linkid = 'link-github'; break;
            case str::contains($part[1],'drive'):
              $linkid = 'link-drive'; break;
            default:
              $linkid = null;
          }
        ?>
        <li <?php echo ($linkid) ? 'id="' . $linkid . '"' : ''?>><a href="<?php echo $part[1] ?>"><?php echo $part[0] ?></a></li>
      <?php endforeach ?>
    </ul>
  </div>
  
<?php endif ?>

<?php // AUTHORS ?>
<?php if ($type == 'authorss'): ?>
  
  <?php
    $plural = false;
    $authors = $page->authors();
    if (isset($authors[1])) { // if the array has a second element, then there are multiple authors
      $plural = true;
    }
  ?>

  <?php if(!empty($authors) or $page->isEditableByUser()): ?>
    <div class="widget">

      <?php /* Set the widget title */
        switch ($page->parent()) {
          case 'clubs':
            $title = 'OFFICERS'; break;
          case 'spaces':
            $title = 'STAFF'; break;
          case 'handbooks':
          case 'articles':
            $title = ($plural) ? 'AUTHORS' : 'AUTHOR'; break;
          case 'courses':
            $title = ($plural) ? 'INSTRUCTORS' : 'INSTRUCTOR'; break;
          default:
            $title = ($plural) ? 'MAKERS' : 'MAKER'; break;
        }
      ?>
      
      <span class="heading"><?php echo $title ?></span>
      
      <div class="items" id="authors">
        <?php if (isset($authors)): ?>
          <?php foreach(str::split($authors) as $author): ?>
  
            <?php
              $part = str::split($author, '~');
              $username = (isset($part[0])) ? $part[0] : '';
              $role     = (isset($part[1])) ? $part[1] : '';
            ?>
  
            <?php if($site->user($username)): ?>
              <a class="item" href="<?php echo $site->url() . "/makers/" . $username ?>" data-username="<?php echo $site->user($username)->username() ?>">
                
                <?php if ($page->isEditableByUser()): ?>
                  <div class="item-delete"></div>
                <?php endif ?>
                
                <div class="row">
                  <img src="<?php echo userAvatar($username, 40) ?>" width="40" height="40" class="<?php echo userColor($username) ?>">
                  <div class="column">
                    <span><?php echo $site->user($username)->firstname() . ' ' . $site->user($username)->lastname() ?></span>
                    <?php if($role): ?>
                      <span><?php echo $role ?></span>
                    <?php /*
                    <?php elseif(preg_match("/" . $page->slug() . " == (.*?) ~~/", $site->user($username)->roles(), $matches)): ?>
                      <span><?php echo $matches[1]; ?></span>
                    */ ?>
                    <?php elseif($site->user($username)->major() != null): ?>
                      <span><?php echo $site->user($username)->major() ?></span>
                    <?php endif ?>
                  </div>
                </div>
                
              </a>
              <?php /* Eventually display a name with no associated user account. Not needed for now. */ ?>
              <?php /*
              <?php else: ?>
                <a>
                  <div class="author-delete"></div>
                  <div class="row">
                    <img src="<?php echo userAvatar($username, 40) ?>" width="40" height="40" class="blue">
                    <div class="column">
                      <span><?php echo $username ?></span>
                    </div>
                  </div>
                </a>
              */ ?>
            <?php endif ?>
            
          <?php endforeach ?>
        <?php endif ?>
      </div>
      
      <?php if ($page->isEditableByUser()): ?>
        <div class="items" id="requests">
          <?php foreach (str::split($page->requests()) as $username): ?>
            <a class="item" href="<?php echo $site->url() . "/makers/" . $username ?>" data-username="<?php echo $site->user($username)->username() ?>">
              
              <div class="item-delete"></div>
              <div class="item-confirm"></div>
              <div class="row">
                <img src="<?php echo userAvatar($username, 40) ?>" width="40" height="40" class="<?php echo userColor($username) ?>">
                <div class="column">
                  <span><?php echo $site->user($username)->firstname() . ' ' . $site->user($username)->lastname() ?></span>
                  <?php if($role): ?>
                    <span><?php echo $role ?></span>
                  <?php /*
                  <?php elseif(preg_match("/" . $page->slug() . " == (.*?) ~~/", $site->user($username)->roles(), $matches)): ?>
                    <span><?php echo $matches[1]; ?></span>
                  */ ?>
                  <?php elseif($site->user($username)->major() != null): ?>
                    <span><?php echo $site->user($username)->major() ?></span>
                  <?php endif ?>
                </div>
              </div>
              
            </a>
          <?php endforeach ?>
        </div>
      <?php endif ?>
      
      <?php if ($page->isEditableByUser()): ?>
        <?php $image = new Asset('/assets/images/hero-add.png'); ?>
        <form id="form-author-add">
          <div>
            <input type="text" id="author-add" autocomplete="off">
            <label>Add an author</label>
            <ul id="author-results"></ul>
          </div>
  
        </form>
      <?php endif ?>
      
    </div>
  <?php endif ?>

<?php endif ?>






<?php // META ?>
<!--
<?php if ($type == 'meta'): ?>
  <div class="widget">
    <span class="heading">META</span>

    <span>Published:</span>
    <span><?php echo humanDate($page->datePublished()) ?></span>
    
    <br>
    
    <span>Modified:</span>
    <span><?php echo humanDate($page->dateModified()) ?></span>
  </div>
  
<?php endif ?>
-->


<?php // Related Groups, Events, and Authors ?>
<?php if ($type == 'authors' or $type == 'groups' or $type == 'events' or $type == 'projects'): ?>
  
  <?php
    if ($type == 'authors') {
      $items    = $page->authors();
      $singular = 'author';
      $plural   = 'authors';
      $id       = 'users';
    }
    if ($type == 'groups') {
      $items    = $page->relatedGroups();
      $singular = 'group';
      $plural   = 'groups';
      $id       = 'groups';
    }
    if ($type == 'events') {
      $items    = $page->relatedEvents();
      $singular = 'event';
      $plural   = 'events';
      $id       = 'events';
    }
    if ($type == 'projects') {
      $items    = $page->relatedProjects();
      $singular = 'project';
      $plural   = 'projects';
      $id       = 'projects';
    }
    
    $heading = ($items->count() > 1) ? strtoupper($plural) : strtoupper($singular);
  ?>
  
  <?php if (isset($items) and !is_null($items) and $items != '' or $page->isEditableByUser()): ?>
    <div class="widget<?php echo ($items == '' and $page->isEditableByUser()) ? ' hidden' : '' ?>">
      
      <div class="row">
        <span class="heading"><?php echo $heading ?></span>
        <?php if ($page->isEditableByUser()): ?>
          <form data-role="search">
            <div>
              <input id="<?php echo $singular ?>" autocomplete="off" placeholder="+ Add new">
              <ul data-role="results"></ul>
            </div>
    
          </form>
        <?php endif ?>
      </div>
      
      <div class="items" id="<?php echo $id ?>">
        <?php if (isset($items)): ?>
          <?php foreach ($items as $item): ?>
              
              <?php
              if ($type == 'authors') {
                $url = userURL($item);
              } else {
                $url = $item->url();
              }
              ?>
              
              <a class="item" data-slug="<?php echo ($item->slug()) ? $item->slug() : $item->username() ?>" href="<?php echo $url ?>">
                
                <?php if ($page->isEditableByUser()): ?>
                  <div class="item-delete"></div>
                <?php endif ?>
                
                <div class="row">
                  
                  <?php if ($type == 'authors'): ?>
                    <img src="<?php echo userAvatar($item->username(), 40) ?>" width="40" height="40" class="<?php echo userColor($item->username()) ?>">
                  <?php endif ?>
                  
                  <?php if ($type == 'groups'): ?>
                    <img src="<?php echo groupLogo($item->slug(), 40) ?>" width="40" height="40" class="<?php echo groupColor($item->slug()) ?>">
                  <?php endif ?>
                  
                  <?php if (($type == 'events' or $type == 'projects') and $item->heroImage()): ?>
                    <img src="<?php echo $item->heroImage()->crop(40,40)->url() ?>" width="40" height="40" class="<?php echo $item->color() ?>">
                  <?php endif ?>
                  
                  <div class="column">
                    
                    <?php if ($type == 'authors'): ?>
                      <span><?php echo $item->firstname() . ' ' . $item->lastname() ?></span>
                      <?php if ($item->major() != null): ?>
                        <span><?php echo $item->major() ?></span>
                      <?php endif ?>
                    <?php else: ?>
                      <span><?php echo $item->title() ?></span>
                      <?php if ($type == 'events'): ?>
                        <span><?php echo date('F j, Y', $item->datePublished()) ?></span>
                      <?php endif ?>
                    <?php endif ?>
                    
                  </div>
                </div>
                
              </a>
          <?php endforeach ?>
        <?php endif ?>
      </div>
      
    </div>
  <?php endif ?>
<?php endif ?>




























<?php else: ?>
  <?php echo "Error: the web administrator forgot to set the widget type" ?>
<?php endif ?>