<?php if($section->toc() == "on"): ?>
    <?php if($user = $site->user() and str::contains($user->projects(), $page->slug()) or $user = $site->user() and $user->hasRole('admin')): ?>
      <?php if($section->toc() == "off"): ?>
        <div class="toc-button invisible">Show TOC</div>
      <?php elseif($section->toc() == "on"): ?>
        <div class="toc-button visible">Hide TOC</div>
      <?php endif ?>
    <?php endif ?>

    <?php if($section->toc() == "on"): ?>
      <div class="toc visible sticky">
    <?php if($section->toc() == "off"): ?>
      <div class="toc sticky">
    <?php endif ?>
      <ul>
        <li><a href="<?php echo $page->parent()->url() ?>"><?php echo "&#10094; Back to " . $page->parent()->title() ?></a></li>
        <li><a href="<?php echo $page->url() . '#top' ?>"><?php echo $page->title() ?></a></li>
        <?php $list = $section->children()->visible(); ?>
        <?php if ($list->count() > 0): ?>
          <?php foreach($list as $item): ?>
            <li><a href="<?php echo $section->url() . '#' . $item->uid() ?>"><?php echo $item->title() ?></a></li>
          <?php endforeach ?>
        <?php endif ?>
      </div>
    <?php endif ?>
<?php endif ?>