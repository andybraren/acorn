<?php
  //$items = page()->children()->sortBy('dateCreated','desc');
  //$items = page()->children()->sortBy('dateCreated','desc')->limit(3);
  //$items = page()->children()->slice(0,10)->sortBy('dateCreated','asc');
  //$items = page()->children()->limit(2)->sortBy('dateCreated','asc');
  $items = page()->children()->sortBy('dateCreated','asc')->paginate(10);
?>

<div class="discussion">

<?php foreach ($items as $item): ?>
  
  <?php
    $user = $site->user($item->authors()->first());
    $userurl = $site->url() . "/makers/" . $user->username();
    $firstname = $user->firstname();
    $fullname = $user->firstname() . ' ' . $user->lastname();
                  
    $commentdate = $item->dateCreated();
    $exactdate = date('M j, Y g:ia', $commentdate);
    $humandate = humanDate($commentdate);
    $datemodified = ($item->dateModified()) ? date('M j, Y g:ia', $item->dateModified()) : '';
    
  ?>
  
  
  <div class="item row" id="comment-" data-id="<?php echo $item->slug() ?>">
      
    <div>
      <?php if ($user->usertype() == 'admin'): ?>
        <div class="user-badge">
          <div><?php echo (new Asset('/assets/images/icon-mod.svg'))->content() ?></div>
          <span class="tooltip">Moderator</span>
        </div>
      <?php endif ?>
      <a href="<?php echo $site->url() . "/makers/" . $user->username() ?>" class="user-avatar">
        <img src="<?php echo userAvatar($user->username(), 40) ?>" width="40" height="40" class="<?php echo userColor($user->username()) ?>">
      </a>
    </div>
    
    <div class="column">
      
      <div>
        <a href="<?php echo $item->url() ?>">
          <span class="user-firstname"><?php echo $item->title()->kirbytext() ?></spaZ>
          <div>
            <?php if ($datemodified != ''): ?>
              <span data-role="editbutton" title="<?php echo $datemodified ?>">Edited /</span>
            <?php endif ?>
            <a class="comment-date" title="<?php echo $exactdate ?>" href="<?php echo $page->url() . '#comment-' ?>"><?php echo $humandate ?></a>
          </div>
        </a>
      </div>
      
      <div class="text"><?php echo $item->text()->kirbytext()->excerpt(150) ?></div>
      
    </div>
    
  </div>
    
<?php endforeach ?>

<?php if($items->pagination()->hasPages()): ?>
<nav class="pagination">

  <?php if($items->pagination()->hasNextPage()): ?>
  <a class="next" href="<?php echo $items->pagination()->nextPageURL() ?>">&lsaquo; older posts</a>
  <?php endif ?>

  <?php if($items->pagination()->hasPrevPage()): ?>
  <a class="prev" href="<?php echo $items->pagination()->prevPageURL() ?>">newer posts &rsaquo;</a>
  <?php endif ?>

</nav>
<?php endif ?>

</div>