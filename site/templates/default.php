<?php // Authentication ?>
<?php if (!$page->isVisibleToUser()): ?>
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
    
    <?php if (!empty($page->authors())): ?>
      <?php snippet('sidebar') ?>
    <?php endif ?>
    
    <?php if ($page->links() != '' or $page->equipment() != '' or $page->handbooks() != ''): ?>
      <div class="sidebar rightsidebar">
        <?php snippet('widget', array('type' => 'links')) ?>
        <?php snippet('widget', array('type' => 'equipment')) ?>
        <?php snippet('widget', array('type' => 'handbooks')) ?>
      </div>
    <?php endif ?>
    
    <main class="content">
      <article>
        
        <?php
          if (preg_match("/[a-z]/i", $page->content()->title())){
            $title = $page->content()->title();
          } else {
            $title = '';
          }
          
        ?>
        
        <div class="title" data-editable data-name="title">
          <h1><?php echo $title ?></h1>
          <?php
            //echo $page->authorsRaw();
          ?>
        </div>
        
        <?php if($page->text() != ''): ?>
          <div class="text" data-editable data-name="text"><?php echo $page->text()->kirbytext() ?></div>
        <?php elseif($user = $site->user() and $page->slug() == $user or $user = $site->user() and $user->usertype() == 'admin' and $page->parent() == 'makers'): ?>
          <div class="text" data-editable data-name="text"><p placeholder=""></p></div>
        <?php elseif($page->isEditableByUser() and $page->uid() != 'projects'): ?>
          <div class="text" data-editable data-name="text"><p placeholder=""></p></div>
        <?php else: ?>
          <div class="text" data-editable data-name="text"><p></p></div>
        <?php endif ?>
        
        <?php if($page->uid() == 'makers'): ?>
          <?php snippet('cards', array('type' => 'makers')) ?>
        <?php endif ?>
        
      </article>
      
      <?php // MAKER PROFILES ?>
      <?php if ($page->parent() == 'makers'): ?>
        
        <?php snippet('cards', array('type' => 'projects',  'maker' => $page->slug())) ?>
        <?php snippet('cards', array('type' => 'articles',  'maker' => $page->slug())) ?>
        <?php snippet('cards', array('type' => 'handbooks', 'maker' => $page->slug())) ?>
        <?php snippet('cards', array('type' => 'groups',    'maker' => $page->slug())) ?>
        
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
        <h2>Makers</h2>
        <?php snippet('cards', array('type' => 'makers', 'group' => $page->uid())) ?>
        <h2>Articles</h2>
        <?php snippet('cards', array('type' => 'articles', 'group' => $page->uid())) ?>
      <?php endif ?>
      
      <?php if($page->uid() == 'articles'): ?>
        <?php snippet('cards', array('type' => 'articles')) ?>
      <?php endif ?>
      
      <?php if($page->uid() == 'courses'): ?>
        <?php snippet('cards', array('type' => 'courses')) ?>
      <?php endif ?>
      
      <?php if (site()->page('courses')): ?>
      <?php if (site()->page('courses')->isOpen()): ?>
        <?php snippet('cards', array('type' => 'projects', 'group' => $page->uid())) ?>
      <?php endif ?>
      <?php endif ?>
      
      <?php if($page->uid() == 'learn'): ?>
        <p>Content from the sub-tabs above will be included here in a way that makes sense.</p>      
      <?php endif ?>
      
      <?php if($page->uid() == 'make'): ?>
        <p>Content from the sub-tabs above will be included here in a way that makes sense.</p>
        <p>Ideas should be a sub-set of Projects. Materials might be moved elsewhere. Challenges are maybe more like events.</p>
      <?php endif ?>
      
      <?php if($page->uid() == 'connect'): ?>
        <p>Content from the sub-tabs above will be included here in a way that makes sense.</p>
      <?php endif ?>
      
      <?php if($page->uid() == 'projects'): ?>
        <?php snippet('cards', array('type' => 'projects')) ?>
      <?php endif ?>
      
      <?php if($page->uid() == 'bugs'): ?>
        <?php snippet('cards', array('type' => 'bugs')) ?>
      <?php endif ?>
      
      <?php if($page->uid() == 'spaces'): ?>
        <?php snippet('cards', array('type' => 'spaces')) ?>
      <?php endif ?>
      
      <?php if($page->uid() == 'handbooks'): ?>
        <?php snippet('cards', array('type' => 'handbooks')) ?>
      <?php endif ?>
      
      <?php if($page->uid() == 'equipment'): ?>
        <?php snippet('cards', array('type' => 'equipment')) ?>
      <?php endif ?>
      
      <?php if($page->uid() == 'books'): ?>
        <?php snippet('cards', array('type' => 'books')) ?>
      <?php endif ?>
      
      <?php if (site()->page('books')): ?>
      <?php if (site()->page('books')->isOpen()): ?>
        <hr>
        <h2>Case Studies</h2>
        <?php snippet('cards') ?>
      <?php endif ?>
      <?php endif ?>
      
      <?php if($page->uid() == 'challenges'): ?>
        <?php snippet('cards', array('type' => 'challenges')) ?>
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
      
      <?php /*
      <?php if($page->parent() == 'events'): ?>
        <?php echo guggenheim($page->images(), array('width' => 800, 'height' => 200, 'border' => 10)); ?>
      <?php endif ?>
      */ ?>
      
      <?php if($page->uid() == 'events'): ?>
        <!--
        <h2>Upcoming Events</h2>
        <?php snippet('cards', array('type' => 'events', 'time' => 'upcoming')) ?>
        
        <h2>Past Events</h2>
        -->
        <?php snippet('cards', array('type' => 'events', 'time' => 'past')) ?>
        <?php snippet('events') ?>
      <?php endif ?>
      
    </main>
    
  </div>
</div>

<?php snippet('footer') ?>

<?php endif ?>