<?php $imagesold = $page->images()->filter(function($image){ return str::contains($image->filename(), 'hero'); }); ?>
<?php $images = $page->images()->filterBy('name', '*=', 'hero'); ?>

<?php if (in_array($page->uid(), array('articles','equipment'))): ?>
  <?php $images = $page->children()->filterBy('visibility','public')->sortBy('dateCreated','desc')->images()->filterBy('name', '*=', 'hero')->limit(3) ?>
  
  <?php
    
    // https://forum.getkirby.com/t/get-all-images-from-children/1746/3
    
    $images = new Collection();
    
    $blahs = $page->children()->filterBy('visibility','public')->filterBy('hero', '!=', '')->sortBy('dateCreated','desc')->limit(10);
    
    /*
    $images = new Collection();
    foreach ($pages as $page) {
      if ($page->heroImage()) {
        $images->data[] = $page->heroImage();
      }
    }
    */
    
    $images = new Collection();
    foreach ($blahs as $blah) {
      
      if ($blah->heroImage()) {
        $images->data[] = $blah->heroImage();
      }
    }
    
    foreach ($images as $image) {
      //echo $image->url();
    }
    
  
  ?>
  
<?php endif ?>

<?php if($page->uid() == 'equipment'): ?>
  <?php $images = $page->children()->filterBy('visibility','public')->sortBy('created','desc')->images()->filterBy('name', '*=', 'hero')->limit(10) ?>
<?php endif ?>




<?php /*
<?php if ($page->hero() != ''): ?>
  
  <?php if (is_string($page->hero())): ?>
    
    <?php // YouTube Videos ?>
    <?php if (str::contains($page->hero(), 'youtu')): ?>
      <div id="hero" class="hero fullwidth video">
        <?php echo kirbytag(array('video' => $page->hero())) ?>
      </div>
    <?php endif ?>
    
    <?php // Vimeo videos ?>
    <?php if (str::contains($page->hero(), 'vimeo.com')): ?>
      <div id="hero" class="hero fullwidth video">
        <?php echo kirbytag(array('video' => $page->hero())) ?>
      </div>
    <?php endif ?>
    
  <?php else: ?>
    
    <?php // Video files ?>
    <?php if ($page->hero()->type()): ?>
      <?php if ($page->hero()->type() == 'video'): ?>
        <div id="hero" class="hero fullwidth video">
          <?php echo kirbytag(array('video' => $page->hero()->filename())) ?>
        </div>
      <?php endif ?>
    <?php endif ?>
    
  <?php endif ?>
  
<?php endif ?>
*/ ?>

<?php if ($page->heroType() == 'video-embed' or $page->heroType() == 'video-native'): ?>
  <div id="hero" class="hero fullwidth video">
    <?php echo $page->hero(); ?>
  </div>

<?php // Local Files ?>
<?php elseif ($file = $page->heroImage()): ?>

  <?php // Image File ?>
  <?php if ($file->type() == 'image'): ?>
    <div id="hero" class="<?php echo ($file->ratio() >= 3.5) ? 'fullwidth' : '' ?>">
      <div class="container">
        <?php echo kirbytag(array('image' => $file->filename(), 'type' => 'hero')) ?>
      </div>
    </div>
  <?php endif ?>
  
<?php // HERO CAROUSELS ?>

<?php elseif (($image = $page->image('hero-1.jpg') OR $image = $page->image('hero-1.png')) or $images != ''): ?>

  <?php if ($type == "fullwidth"): ?>
    <div id="hero" class="hero fullwidth carousel">
      <div class="container">
      <?php foreach($images as $image): ?>
        <figure>
          <img class="hero-image" src="<?php echo thumb($image, array('width' => 1200, 'height' => 300, 'crop' => true))->url() ?>">
          <?php if (!$image->caption()->empty() or !$image->title()->empty()): ?>
            <figcaption><span><?php echo $image->title() ?></span><br><span><?php echo $image->subtitle() ?></span></figcaption>
          <?php endif ?>
        </figure>
      <?php endforeach ?>
      </div>
    </div>
    
  <?php else: ?>
    <div id="hero" class="hero carousel">
      <div class="container">
      <?php foreach($images as $image): ?>
        <figure><?php echo $images->url() ?>
          <img class="hero-image" src="<?php echo thumb($image, array('width' => 1200, 'height' => 300, 'crop' => true))->url() ?>">
          <?php if (!$image->caption()->empty() or !$image->title()->empty()): ?>
            <figcaption><h2><?php echo $image->title() ?></h2><h3><?php echo $image->subtitle() ?></h3></figcaption>
          <?php endif ?>
        </figure>
      <?php endforeach ?>
      </div>
    </div>
  
  <?php endif ?>

<?php // HERO SINGULAR IMAGE ?>
<?php /*
<?php elseif ($image = $page->images()->filterBy('name', '==', 'hero')->first() and $page->hero()->isEmpty()): ?>

  <div id="hero" class="<?php echo ($image->ratio() >= 3.5) ? 'fullwidth' : '' ?>">
    <div class="container">
      <?php echo kirbytag(array('image' => $image->filename(), 'type' => 'hero')) ?>
    </div>
  </div>
*/ ?>
  
<?php // NO HERO ?>
<?php else: ?>

  <div id="hero" class="nohero">
    <div class="container">
      <div id="hero-add"><span>Add a featured image</span></div>
    </div>
  </div>

<?php endif ?>

























