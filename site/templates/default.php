<?php // Authentication ?>
<?php if (!$page->isVisibleToUser() or $page->isErrorPage()): ?>
  <?php snippet('header') ?>
  <div class="main">
    <div class="container">
      <div class="sidebar"></div>
      <main class="content">
        <article>
          <h1>
            <?php echo ($site->errorPage()->title() != "") ? $site->errorPage()->title() : "" ?>
          </h1>
          <div class="text">
            <?php echo ($site->errorPage()->text() != "") ? $site->errorPage()->text()->kirbytext() : "" ?>
          </div>
        </article>
      </main>
    </div>
  </div>
  <?php snippet('footer') ?>
<?php else: ?>

<?php snippet('header') ?>

<?php snippet('hero', array('type' => 'fullwidth')) ?>

<div class="main">
  <div class="container">
    
    <div class="sidebar">
      <?php if ($page->sidebarLeft()): ?>
        <?php snippet('sidebar') ?>
      <?php endif ?>
    </div>
    
    <div class="sidebar rightsidebar">
      
      <?php snippet('widget', array('type' => 'discussion')) ?>
      
    <?php if ($page->parent() != '' AND $_SERVER['SERVER_NAME'] != 'dev.andybraren.com' AND $_SERVER['SERVER_NAME'] != 'andybraren.com'): ?>
            
      <?php snippet('widget', array('type' => 'links')) ?>
      <?php if ($page->parent() == "handbooks"): ?>
        <?php snippet('widget', array('type' => 'equipment')) ?>
      <?php endif ?>
      <?php snippet('widget', array('type' => 'handbooks')) ?>
      <?php
        if ($page->visibility() == 'unlisted') {
          echo kirbytag(array('callout' => 'notice', 'text' => 'The author has marked this page as unlisted and hidden from Google. Please think twice before sharing it with others.'));
        }
      ?>
    <?php endif ?>
    </div>
    
    <main class="content">
      <article>
        
        <?php
          if (preg_match("/[a-z]/i", $page->content()->title())){
            $title = $page->content()->title();
          } else {
            $title = '';
          }
        ?>
        
        <?php if ($page->titleVisible() == true): ?>
          <?php
            if ($page->links()) {
              if (count($page->links()) === 1) {
                if ($page->links()[0]['label'] == '') {
                  $linked = true;
                } else {
                  $linked = false;
                }
              } else {
                $linked = false;
              }
            } else {
              $linked = false;
            }
          ?>
          <div class="title" data-editable data-name="title">
            <h1><?php echo ($linked) ? '<a href="' . $page->links()[0]['url'] . '">' : '' ?><?php echo $title ?><?php echo ($linked) ? ' &rarr;</a>' : '' ?></h1>
          </div>
        <?php endif ?>
        
        <?php if($page->text() != ''): ?>
          <div class="text" data-editable data-name="text"><?php echo $page->text()->kirbytext() ?></div>
        <?php elseif($user = $site->user() and $page->slug() == $user or $user = $site->user() and $user->usertype() == 'admin' and $page->parent() == 'users'): ?>
          <div class="text" data-editable data-name="text"><p placeholder=""></p></div>
        <?php elseif($page->isEditableByUser() and $page->uid() != 'projects'): ?>
          <div class="text" data-editable data-name="text"><p placeholder=""></p></div>
        <?php else: ?>
          <div class="text" data-editable data-name="text"><p></p></div>
        <?php endif ?>
        
        <?php if($page->uid() == 'users'): ?>
          <?php snippet('cards', array('type' => 'users')) ?>
        <?php endif ?>
        
      </article>
      
      <?php if($page->uid() == 'settings'): ?>
        <?php snippet('settings') ?>
      <?php endif ?>
      
      <?php // MAKER PROFILES ?>
      <?php if ($page->parent() == 'users'): ?>
        
        <h2>Projects</h2>
        <?php snippet('cards', array('type' => 'projects',  'maker' => $page->slug())) ?>
        <?php /*
        <?php snippet('cards', array('type' => 'articles',  'maker' => $page->slug())) ?>
        <?php snippet('cards', array('type' => 'handbooks', 'maker' => $page->slug())) ?>
        <?php snippet('cards', array('type' => 'groups',    'maker' => $page->slug())) ?>
        */ ?>
        
        <?php if($page->find('gallery') and $page->find('gallery')->hasImages()): ?>
          <h2>Gallery</h2>
          <?php echo guggenheim($page->find('gallery')->images(), array('width' => c::get('guggenheim.width'), 'height' => '150', 'border' => 4)) ?>
        <?php endif ?>
        
      <?php endif ?>
      
      <?php if($page->uid() == 'groups'): ?>
        <?php snippet('cards', array('type' => 'groups')) ?>
      <?php endif ?>
      <?php if($page->parent() == 'groups'): ?>
        <h2>Projects</h2>
        <?php snippet('cards', array('type' => 'projects', 'group' => $page->uid())) ?>
        <h2>Users</h2>
        <?php snippet('cards', array('type' => 'users', 'group' => $page->uid())) ?>
        <h2>Articles</h2>
        <?php snippet('cards', array('type' => 'articles', 'group' => $page->uid())) ?>
      <?php endif ?>
      
      <?php /*
      <?php if($page->uid() == 'articles'): ?>
        <?php snippet('cards', array('type' => 'articles')) ?>
      <?php endif ?>
      */ ?>
      
      <?php if($page->uid() == 'courses'): ?>
        <?php snippet('cards', array('type' => 'courses')) ?>
      <?php endif ?>
      
      <?php if (site()->page('courses')): ?>
      <?php if (site()->page('courses')->isOpen()): ?>
        <?php snippet('cards', array('type' => 'projects', 'group' => $page->uid())) ?>
      <?php endif ?>
      <?php endif ?>
                  
      <?php if($page->uid() == 'handbooks'): ?>
        <?php snippet('cards', array('type' => 'handbooks')) ?>
      <?php endif ?>
            
      <?php if($page->parent() == 'events' && $page->hasChildren()): ?>
        <?php snippet('cards', array('type' => 'projects')) ?>
      <?php endif ?>
      
      <?php if($page->parent() == 'events' && $page->isSubmissibleByUser()): ?>
        <h2>Projects</h2>
        <?php snippet('cards', array('type' => 'projects', 'event' => $page->uid())) ?>
      <?php endif ?>
      
      <?php if($page->uid() == 'forum'): ?>
        <?php snippet('forum') ?>
      <?php endif ?>
      
      <?php if($page->comments()): ?>
        <?php snippet('comments') ?>
      <?php endif ?>
      
      
    </main>
    
  </div>
</div>

<?php snippet('footer') ?>

<?php endif ?>