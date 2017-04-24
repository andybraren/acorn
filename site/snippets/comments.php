<div class="discussion">
  
  <?php if ($page->parent() != 'forum'): ?>
    <div id="discussion-header" class="row">
      <h2>Discussion</h2>
      
      <div id="discussion-sections" class="row">
        
        <div>
          <span>Members</span>
          <span><?php echo $page->comments()->count() ?> comments</span>
        </div>
        
        <div>
          <span>Disqus</span>
          <span># comments</span>
        </div>
        
        <div>
          <span>Mentions</span>
          <span># mentions</span>
        </div>
  
      </div>
    </div>
  <?php endif ?>
  
  <?php $commentcount = 0; ?>
  <?php foreach($page->comments() as $comment): ?>
    
    <?php
      $user = $site->user($comment->authors()->first());
      $userurl = $site->url() . "/makers/" . $user->username();
      $firstname = $user->firstname();
      $fullname = $user->firstname() . ' ' . $user->lastname();
                    
      $commentdate = $comment->dateCreated();
      $exactdate = date('M j, Y g:ia', $commentdate);
      $humandate = humanDate($commentdate);
      $datemodified = ($comment->dateModified()) ? date('M j, Y g:ia', strtotime($comment->dateModified())) : '';
      
      $commentcount++;
    ?>
    
    <div class="item row <?php echo ' ' . userColor($user) ?>" id="comment-<?php echo $commentcount ?>" data-id="<?php echo $comment->slug() ?>">
        
      <div>
        <?php if ($user->usertype()): ?>
          <?php if ($user->usertype() == 'admin'): ?>
            <div class="user-badge">
              <div><?php echo (new Asset('/assets/images/icon-mod.svg'))->content() ?></div>
              <span class="tooltip">Moderator</span>
            </div>
          <?php endif ?>
        <?php endif ?>
        <a href="<?php echo $site->url() . "/makers/" . $user->username() ?>" class="user-avatar">
          <img src="<?php echo userAvatar($user->username(), 40) ?>" width="40" height="40" class="<?php echo userColor($user->username()) ?>">
        </a>
      </div>
      
      <div class="column">
        <div>
          <a class="user-firstname" href="<?php echo $userurl ?>"><?php echo $firstname ?></a>
          <div>
            <?php if ($datemodified != ''): ?>
              <span data-role="editbutton" title="<?php echo $datemodified ?>">Edited /</span>
            <?php endif ?>
            <a class="comment-date" title="<?php echo $exactdate ?>" href="<?php echo $page->url() . '#comment-' . $commentcount ?>"><?php echo $humandate ?></a>
          </div>
        </div>
        <div class="text"><?php echo $comment->text()->kirbytext() ?></div>
      </div>
      
    </div>
    
  <?php endforeach ?>
  
  <?php if (site()->user()): ?>
    <?php $user = site()->user() ?>
    <div class="item row<?php echo ' ' . userColor($user) ?>" id="add-comment">
      <div>
        <?php if ($user->usertype()): ?>
          <?php if ($user->usertype() == 'admin'): ?>
            <div class="user-badge">
              <div><?php echo (new Asset('/assets/images/icon-mod.svg'))->content() ?></div>
              <span class="tooltip">Moderator</span>
            </div>
          <?php endif ?>
        <?php endif ?>
        <a href="<?php echo $site->url() . "/makers/" . $user->username() ?>" class="user-avatar">
          <img src="<?php echo userAvatar($user->username(), 40) ?>" width="40" height="40" class="<?php echo userColor($user->username()) ?>">
        </a>
      </div>
      
      <div class="column">
        <div>
          <a class="user-firstname" href="<?php echo 'Blah' ?>"><?php echo $site->user()->firstname() ?></a>
          <div>
            <span data-role="editbutton" title=""></span>
            <a class="comment-date" title="" href=""></a>
          </div>
        </div>
        
        <div class="text" contentEditable="true">
          <p placeholder="Add text here"></p>
        </div>
        
        <div class="post">
          <div class="button" id="save-comment">Post</div>
        </div>
      </div>
    </div>
  <?php endif ?>
</div>