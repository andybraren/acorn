<?php // Master card snippet for makers, projects, events, tools, etc. ?>

<?php // MAKER CARDS ?>

<?php
if (!isset($type)) {
  $type = "none";
}
?>

<?php if ($type == "users"): ?>

  <?php if ($page->isHomePage()): ?>
    <?php $counter = 0 ?>
    <?php foreach($site->users()->sortBy('registrationdate', 'desc') as $user): ?>
      <?php if($avatar = $user->avatar() and $counter < 20): ?>
        <a href="<?php echo $site->url() . "/users/" . $user ?>">
            <img src="<?php echo $avatar->crop(108,108)->url() ?>"></img>
        </a>
        <?php $counter++ ?>
      <?php endif ?>
    <?php endforeach ?>
  
  <?php elseif ($type == 'users' and isset($group)): ?>
    <div class="cards-makers">
      <h2>Members</h2>
      <?php foreach ($site->users()->sortBy('registrationdate', 'desc') as $user): ?>
        <?php if ($user->groups() and in_array($group, str::split($user->groups(),','))): ?>
          <a href="<?php echo $site->url() . "/users/" . $user ?>">
            <img src="<?php echo userAvatar($user, 108) ?>" class="<?php echo userColor($user) ?>">
            <div><span><?php echo $user->firstname() . ' ' . $user->lastname() ?></span></div>
          </a>
        <?php endif ?>
      <?php endforeach ?>
    </div>
  
  <?php else: ?>
    <div class="cards-makers">
      
      <style>
      <?php $options = array_merge($site->content()->affiliationoptions()->split(','), $site->content()->departmentoptions()->split('##'), $site->content()->majoroptions()->split('##'));
      ?>
      <?php foreach ($options as $option): ?>
        <?php echo 'main[data-filters="' . str::slug($option,'-') . '"] article .cards-makers a:not([data-filters~="' . str::slug($option,'-') . '"]) { display: none; }' ?>
      <?php endforeach ?>
      </style>
      
      <?php foreach ($site->users()->sortBy('registrationdate', 'desc') as $user): ?>
      
      <?php $filters = str::slug($user->affiliation(),'-') . ' ' . str::slug($user->department(),'-') . ' ' . str::slug($user->major(),'-') . ' ' . str::slug($user->classyear(),'-');
        $filters = trim(preg_replace('/\s+/',' ',$filters));
      ?>
      
        <a class="<?php echo userColor($user) ?>" data-filters="<?php echo $filters ?>" href="<?php echo $site->url() . "/users/" . $user ?>">
          <img src="<?php echo userAvatar($user, 108) ?>" class="<?php echo userColor($user) ?>">
          <?php /* <div><span><?php echo $user->firstname() . ' ' . $user->lastname() ?></span></div> */ ?>
          <span><?php echo $user->firstname() . ' ' . $user->lastname() ?></span>
        </a>
      <?php endforeach ?>
    </div>
  <?php endif ?>
  
<?php endif ?>





<?php // GROUP, CLUB, HANDBOOK, SPACE, PROJECT CARDS ?>


  <?php // GRAB CARDS
    
    $items = '';
    $items = new Collection;
    
    if (isset($type)) {
      if ($type != 'users') {
        if (isset($type)) { // Collect all items of the given type
          //$items = $site->page($type)->children()->sortBy('created','desc');
          //$items = $site->page($type)->children()->sortBy('datedata','desc');
          //$items = $site->page($type)->children()->sortBy('dateCreated','desc');
          //$items = $site->page($type)->children()->dateCreated();
          
          if ($type == 'none') {
            $items = page()->children()->sortBy('datePublished','desc');
          } else {
            if ($site->page($type)) {
              if ($site->page($type)->children()) {
                $items = $site->page($type)->children()->sortBy('datePublished','desc');
              }
            }
          }
        }
        if (isset($maker)) {
          $filtered = new Pages();
          foreach ($items as $item) {
            if ($item->authors() != null) {
              if (in_array($maker,$item->authors()->toArray())) {
                $filtered->add($item);
              }
            }
          }
          $items = $filtered;
        }
        if (isset($group)) {
          $filtered = new Pages();
          foreach ($items as $item) {
            if ($item->relatedGroups() != null) {
              foreach ($item->relatedGroups() as $blah) {
                if ($blah->slug() == $group) {
                  $filtered->add($item);
                }
              }
            }
          }
          $items = $filtered;
        }  
        if (isset($event)) {
          $filtered = new Pages();
          foreach ($items as $item) {
            if ($item->relatedEvents() != null) {
              foreach ($item->relatedEvents() as $blah) {
                if ($blah->slug() == $event) {
                  $filtered->add($item);
                }
              }
            }
          }
          $items = $filtered;
        }
        if (isset($time)) {
          if ($time == 'upcoming') {
            $items = $items->filterBy('StartDate','>=',date('c'))->sortBy('StartDate','desc');
          }
          elseif ($time == 'past') {
            $items = $items->filterBy('StartDate','<',date('c'))->sortBy('datePublished','desc');
          }
        }
        if (isset($relatedto)) {
          
          $uid = $relatedto;
          
          $filtered = new Pages();
          foreach ($items as $item) {
            if (in_array($uid, $item->related())) {
              $filtered->add($item);
            }
          }
          $items = $filtered;
        }
        $items = $items->filterBy('datePublished','!=','');
        $items = $items->visibleToUser();
      }
    }
    
  ?>
  
  <?php /*
  <?php if ($page->uid() != $type and ($site->user() or $items != '')): ?>
    <h2><?php echo ucfirst(strval($type)) ?></h2>
  <?php endif ?>
  */ ?>
  
  <?php if ($site->user() and $type != 'users' or $items != '' and $type != 'users' or $page->isSubmissibleByUser()): ?>
  <div class="cards">
    
    <?php $newhero = new Asset('/assets/images/hero-new.png'); ?>
    <?php $defaulthero = new Asset('/assets/images/hero-1.jpg'); ?>

    
    <?php // New project card ?>
    <?php /*
    <?php if ($type == "projects" and $user = $site->user()): ?>
      <?php if($user = $site->user() and str::contains($page->users(), $user) or $user = $site->user() and str::contains($page->slug(), $user) or $user = $site->user() and $user->usertype() == 'admin'): ?>

        <a href="<?php echo $site->page('projects')->url() ?>/new" class="card">
          <div class="card-hero">
            <img src="<?php echo $newhero->crop(259,101)->url() ?>">
          </div>
                  
          <div class="card-content">
            <h4>Start a new project</h4>
          </div>
        </a>
        
      <?php endif ?>
    <?php endif ?>
    */ ?>
    
    <?php if (($page->isEditableByUser() or $page->isSubmissibleByUser()) and $site->page($type)): ?>
        <a href="<?php echo $site->page($type)->url() ?>/new<?php echo ($page->parent() == '' or $page->parent() == 'users') ? '' : '?related=' . $page->slug() ?>" class="card">
          <div class="card-hero">
            <img src="<?php echo $newhero->crop(259,101)->url() ?>">
          </div>
                  
          <div class="card-content">
            <h4>Add new</h4>
          </div>
        </a>
    <?php endif ?>
    
    <?php // New bug card ?>
    <?php if ($type == "bugs" and $user = $site->user()): ?>
        <a href="<?php echo $site->page('bugs')->url() ?>/new" class="card">
          <div class="card-hero">
            <img src="<?php echo $newhero->crop(259,101)->url() ?>">
          </div>
                  
          <div class="card-content">
            <h4>File a new bug report</h4>
          </div>
        </a>
    <?php endif ?>
    
    <?php foreach($items as $item): ?>
      <div class="card<?php echo ($item->color() != "") ? ' ' . $item->color() : ""; ?><?php echo ($page->uid() == 'books') ? ' size-sixth' : ''; ?>">
        
        <div class="card-hero">
          <a href="<?php echo $item->url() ?>">
            <?php if ($hero = $item->images()->findBy('name', 'icon') AND $page->uid() == 'books'): ?>
              <img src="<?php echo $hero->crop(360, 500)->url() ?>"></img>
            <?php elseif ($hero = $item->heroImage()): ?>
              <img src="<?php echo $hero->crop(300, 120)->url() ?>" height="120px"></img>
            <?php elseif($item->hasImages()): ?>
              <?php if ($hero = $item->images()->not('location.jpg')->sortBy('sort', 'asc')->first()): ?>
                <img src="<?php echo $hero->crop(300, 120)->url() ?>" height="120px"></img>
              <?php endif ?>
            <?php endif ?>
  
            <?php if ($icon = $item->images()->findBy('name', 'logo')): ?>
              <img class="card-iconnew" src="<?php echo $icon->url() ?>"></img>
            <?php endif ?>
          </a>
        </div>
        
        <?php if ($icon = $item->images()->findBy('name', 'logo')): ?>
        <!--
        <div class="card-icon">
            <img src="<?php echo $icon->url() ?>"></img>
        </div>
        -->
        <?php endif ?>
        
        <div class="card-content">
          <a href="<?php echo $item->url() ?>">
            <h4><?php echo $item->title()->html() ?></h4>
          </a>
          <?php if($page->uid() != 'books'): ?>
            <?php // Grabs a 300-character chunk of text, removes Markdown headings, converts the remainder to HTML, strips tags and encoded characters, and removes any (completed) kirbytags ?>
            <p><?php echo preg_replace("!(?=[^\]])\([a-z0-9_-]+:.*?\)!is", "", html::decode(markdown(preg_replace("/(#+)(.*)/", "", $item->text()->short(300))))); ?></p>
          <?php endif ?>
        </div>
        
        <?php if($page->uid() != 'books'): ?>
          <div class="card-details">
            <span><?php echo date('M j Y', $item->datePublished()) ?></span>
            <a href="<?php echo $item->url() ?>">Read &rarr;</a>
          </div>
        <?php endif ?>
        
      </div>
    <?php endforeach ?>
  </div>
  <?php endif ?>

<?php // EVENT CARDS ?>
<?php if ($type == "esssssssvents"): ?>
<div class="events">
  <h2>Upcoming events</h2>
  <div class="cards">
    <?php foreach($site->page('events')->children()->filterBy('StartDate','>',date('c'))->sortBy('StartDate','desc') as $event): ?>
      <a href="<?php echo $event->url() ?>" class="card size-25 <?php echo ($event->color() != "") ? $event->color() : ""; ?>">
  
        <?php // Featured image or first image ?>
        <?php if ($image = $event->image('hero.png') or $image = $event->image('hero.jpg') or $image = $event->images()->sortBy('sort', 'asc')->first()): ?>
          <div>
            <img src="<?php echo thumb($image, array("width" => 360, "height" => 200, "crop" => true))->url() ?>"></img>
            <span class="date"><?php echo $event->date('M j','StartDate') ?></span>
          </div>
        <?php endif ?>
        
        <div class="bookmark"></div>
  
        <div class="card-handbook-content">
          <h4><?php echo $event->title()->html() ?></h4>
          <span class="date"><?php echo $event->date('l g:i','StartDate') . ' - ' . $event->date('g:i','EndDate') ?></span>
        </div>
        
      </a>
    <?php endforeach ?>
      <a style="visibility:hidden;" class="card size-20 "></a>
      <a style="visibility:hidden;" class="card size-20 "></a>
      <a style="visibility:hidden;" class="card size-20 "></a>
      <a style="visibility:hidden;" class="card size-20 "></a>
  </div>

  <h2>Past events</h2>
  <div class="cards">
    <?php foreach($site->page('events')->children()->filterBy('StartDate','<',date('c'))->sortBy('StartDate','desc') as $event): ?>
      <a href="<?php echo $event->url() ?>" class="card size-25 <?php echo ($event->color() != "") ? $event->color() : ""; ?>">
  
        <?php // Featured image or first image ?>
        <?php if ($image = $event->image('hero.png') or $image = $event->image('hero.jpg') or $image = $event->images()->sortBy('sort', 'asc')->first()): ?>
          <div>
            <img src="<?php echo thumb($image, array("width" => 360, "height" => 200, "crop" => true))->url() ?>"></img>
            <span class="date"><?php echo $event->date('M j','StartDate') ?></span>
          </div>
        <?php endif ?>
        
        <div class="bookmark"></div>
  
        <div class="card-handbook-content">
          <h4><?php echo $event->title()->html() ?></h4>
          <span class="date"><?php echo $event->date('l g:i','StartDate') . ' - ' . $event->date('g:i','EndDate') ?></span>
        </div>
        
      </a>
    <?php endforeach ?>
      <a style="visibility:hidden;" class="card size-20 "></a>
      <a style="visibility:hidden;" class="card size-20 "></a>
      <a style="visibility:hidden;" class="card size-20 "></a>
      <a style="visibility:hidden;" class="card size-20 "></a>
  </div>
</div>
<?php endif ?>








