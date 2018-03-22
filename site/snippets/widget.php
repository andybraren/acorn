<?php // Master widget snippet for users, projects, events, tools, etc. ?>

<?php if ($type != null): ?>




<?php // EDIT BUTTON ?>
<?php if ($type == 'edit' and $page->isEditableByUser()): ?>
  
  <div class="">
    <div class="button-edit dropdown">
      <button type="button" class="button" id="button-edit" title="Edit this page">Edit</button>
      <button type="button" class="button" title="Additional options for this page"></button>
      <ul class="dropdown-menu">
        <li data-modal="delete"><span>Delete page</span></li>
        <li><s>Change URL</s></li>
        <li><s>Schedule post</s></li>
        <li><s>Save revision</s></li>
        <li><s>Revision history</s></li>
      </ul>
    </div>
    
    <div id="settings" class="">
      <span class="heading">SETTINGS</span>
      
      <form method="post" action="savesettings" id="form-settings">
        
        <div class="size-full">
          <?php $class = ($page->color() != '') ? "hasbeenclicked clicked" : "neverclicked"; ?>
          <select name="color" id="color" class="<?php echo $class ?>">
            <?php foreach ($site->content()->coloroptions()->split(',') as $color): ?>
              <?php $selected = ($color == $page->color()) ? "selected " : ""; ?>
              <?php echo '<option ' . $selected . 'value="' . $color . '">' . ucfirst($color) . '</option>' ?>
            <?php endforeach ?>
          </select>
          <label for="color">Color</label>
        </div>
        
        <div class="size-full">
          <?php $class = ($page->visibility() != '') ? "hasbeenclicked clicked" : "neverclicked"; ?>
          <select name="visibility" id="visibility" class="<?php echo $class ?>">
            <?php foreach ($site->content()->visibilityoptions()->split(',') as $visibility): ?>
              <?php $selected = ($visibility == $page->visibility()) ? "selected " : ""; ?>
              <?php echo '<option ' . $selected . 'value="' . str::slug($visibility) . '">' . ucfirst($visibility) . '</option>' ?>
            <?php endforeach ?>
          </select>
          <label for="visibility">Visibility</label>
        </div>
        
        <?php if ($page->parent() != 'users'): ?>
        <div class="size-full">
          <select name="submissions" id="setting-submissions" class="hasbeenclicked clicked">
            <option value="public" <?php echo ($page->submissions() == 'public') ? 'selected ' : '' ?>>On, anonymous too</option>
            <option value="on" <?php echo ($page->submissions() == 'on') ? 'selected ' : '' ?>>On</option>
            <option value="off" <?php echo (!$page->submissions()) ? 'selected ' : '' ?>>Off</option>
          </select>
          <label for="submissions">Submissions</label>
        </div>
        <?php endif ?>
        
        <?php if ($page->parent() != 'users'): ?>
          <div class="size-full">
            <select name="comments" id="setting-comments" class="hasbeenclicked clicked">
              <option value="on" <?php echo ($page->comments()) ? 'selected ' : '' ?>>On</option>
              <option value="off" <?php echo (!$page->comments()) ? 'selected ' : '' ?>>Off</option>
            </select>
            <label for="comments">Comments</label>
          </div>
        <?php endif ?>
        
      </form>
    </div>
    
    <?php // Used for adding new hero images and icons ?>
    <form method="post" action="uploadnew" id="upload-form" enctype="multipart/form-data">
      <input type="file" accept="image/*" name="icon">
      <input type="file" accept="image/*" name="avatar">
      <input type="file" accept="image/*" name="hero">
      <input type="file" accept="image/*" name="images" id="imageToUpload">
      <input type="file" accept="video/*" name="videos" id="videoToUpload">
      <input type="file" accept="" name="files" id="fileToUpload">
    </form>
      
    <?php /*
      <div id="settings" class="settings column">
        <span class="heading">SETTINGS</span>
        <?php if ($page->parent() != 'users'): ?>
        <div class="row"><span>Visible to:</span></div>
          <select>
            <option>Public</option>
            <option>Tufts MAKE</option>
            <option>Tufts Robotics</option>
            <option>Only Me</option>
          </select>
  
        <?php endif ?>
        <div class="row"><span>Color:</span></div>
          <select>
            <option>Blue</option>
            <option>Red</option>
            <option>Green</option>
            <option>Purple</option>
            <option>Gold</option>
            <option>Silver</option>
          </select>
      </div>
    */ ?>
    
  </div>
  
<?php endif ?>









<?php // RELATED SPACES AND HANDBOOKS ?>
<?php /*
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
*/ ?>



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
<?php if ($type == 'purchase'): ?>
  

  
<?php endif ?>


<?php // LINKS ?>
<?php /*
<?php if ($type == 'links' and $page->links() != null): ?>
  
  <div class="widget">
    <span class="heading">LINKS</span>
    <ul>
      <?php foreach ($page->links() as $link): ?>
        <li <?php echo ($link['icon']) ? 'id="' . $link['icon'] . '"' : ''?>><a href="<?php echo $link['url'] ?>"><?php echo $link['label'] ?></a></li>
      <?php endforeach ?>
    </ul>
  </div>
  
<?php endif ?>
*/ ?>

<?php // AUTHORS ?>
<?php if ($type == 'authors'): ?>
  
  <?php
    $plural = false;
    $authors = $page->authors();
    /*
    if (isset($authors[1])) { // if the array has a second element, then there are multiple authors
      $plural = true;
    }
    */
    $plural = true;
  ?>

  <?php if(!empty($authors) or $page->isEditableByUser()): ?>
    <div class="widget"<?php if(!$page->hasAuthors()) { echo ' data-editor="hidden"'; } ?>>
      <?php /* Set the widget title */
        switch ($page->parent()) {
          case 'clubs':
            $title = 'OFFICERS'; break;
          case 'spaces':
            $title = 'STAFF'; break;
          case 'handbooks':
          case 'projects':
            $title = ($plural) ? 'MAKERS' : 'MAKER'; break;
          case 'courses':
            $title = ($plural) ? 'INSTRUCTORS' : 'INSTRUCTOR'; break;
          default:
            $title = ($plural) ? 'AUTHORS' : 'AUTHOR'; break;
        }
      ?>
      
      <span class="heading"><?php echo $title ?></span>
      
      <div class="items" id="authors">
        <?php if (isset($authors)): ?>
          
          <?php foreach ($authors as $author): ?>
            
            <?php $username = $author->username(); ?>
            
            <a class="item" href="<?php echo $site->url() . "/users/" . $username ?>" data-username="<?php echo $username ?>">
              
              <?php if ($page->isEditableByUser()): ?>
                <div class="item-delete"></div>
              <?php endif ?>
              
              <div class="row">
                <img src="<?php echo userAvatar($username, 40) ?>" width="40" height="40" class="<?php echo userColor($username) ?>">
                <div class="column">
                  <span><?php echo $author->firstname() . ' ' . $author->lastname() ?></span>
                  <?php if ($description = authorDescription($page, $username)): ?>
                    <span><?php echo $description ?></span>
                  <?php endif ?>
                </div>
              </div>
              
            </a>
          
          <?php endforeach ?>
          
        <?php endif ?>
      </div>
      
      <?php if ($page->isEditableByUser()): ?>
        <div class="items" id="requests">
          <?php foreach (str::split($page->requests()) as $username): ?>
            <a class="item" href="<?php echo $site->url() . "/users/" . $username ?>" data-username="<?php echo $site->user($username)->username() ?>">
              
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
        <?php $image = new Asset('site/assets/images/hero-add.png'); ?>
        <form id="form-author-add" data-editor="hidden">
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

<?php if ($type == 'meta'): ?>
  <div class="widget">
    
    <?php if ($page->content()->started()->exists()): ?>
      <span class="heading">INFO</span>
      <span>Started:</span>
      <span><?php echo $page->started() ?></span> <br>
      <?php if ($page->content()->ended() != '' and $page->content()->ended() != 'Present'): ?>
        <span>Ended:</span>
        <span><?php echo $page->ended() ?></span> <br>
      <?php endif ?>
    <?php else: ?>
      <span class="heading">META</span>
      <span>Published:</span>
      <span><?php echo date('M j, Y', $page->datePublished()) ?></span>
      <br>
      <span>Modified:</span>
      <span><?php echo humanDate($page->dateModified()) ?></span>
    <?php endif ?>
  </div>
  
<?php endif ?>



<?php // Related Groups, Events, and Authors ?>
<?php /*
<?php if ($type == 'authors' or $type == 'groups' or $type == 'events' or $type == 'projects'): ?>
  
  <?php
    if ($type == 'authors') {
      
      if ($page->authors()) {
        $items = $page->authors();
      } else {
        $items = null;
      }
      $singular = 'author';
      $plural   = 'authors';
      $id       = 'users';
      
      switch ($page->parent()) {
        case 'clubs':
          $singular = 'officer';
          $plural = 'officers'; break;
        case 'spaces':
          $singular = 'staff';
          $plural = 'staff'; break;
        case 'courses':
          $singular = 'instructor';
          $plural = 'instructors'; break;
        default:
          $singular = $singular;
          $plural = $plural;
      }
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
    
    //$heading = ($items->count() > 1) ? strtoupper($plural) : strtoupper($singular);
    $heading = '';
  ?>
  
  <?php if (isset($items) and !is_null($items) and $items != '' or $page->isEditableByUser()): ?>
    <div class="widget">
      
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
                      <?php if ($description = authorDescription($page, $item->username())): ?>
                        <span><?php echo $description ?></span>
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

*/ ?>


























<?php else: ?>
  <?php echo "Error: the web administrator forgot to set the widget type" ?>
<?php endif ?>