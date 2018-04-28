<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">

<?php
  if ($page->uid() == '') {
    $title = site()->title();
    $url = site()->url();
  } else {
    $title = site()->title() . ' - ' . $page->title();
    $url = $page->url();
  }
?>
<channel>

  <title><?php echo $title ?></title>
  <link><?php echo xml($url) ?></link>
  <?php if(!empty($description)): ?>
  <description><?php echo xml($description) ?></description>
  <?php endif ?>
    
  <?php foreach($items as $item): ?>
    <?php
      if ($item->hero()) {
        $hero = $item->hero($item);
      } else {
        $hero = '';
      }
    ?>
    <item>
      <title><?php echo xml($item->content()->title()) ?></title>
      <link><?php echo xml($item->url()) ?></link>
      <guid><?php echo xml($item->id()) ?></guid>
      <pubDate><?php echo date('r', $item->datePublished()) ?></pubDate>
      <?php foreach ($item->authors() as $author): ?>
        <author>
          <name><?php echo $author->firstname() . ' ' . $author->lastname() ?></name>
        </author>
      <?php endforeach ?>
      <description>
        <![CDATA[<?php echo $hero ?><?php echo $item->text()->kirbytext() ?>]]>
      </description>
    </item>
  <?php endforeach ?>
  
</channel>
</rss>