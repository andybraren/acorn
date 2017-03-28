  <footer class="footer">
  <div class="container">

    <div class="copyright">
      <?php echo $site->copyright()->kirbytext() ?>
    </div>

    <div class="colophon">
      <a href="/about">Learn by Making</a>
    </div>

	

  </div>


  <?php // Redundant, but needed for PhotoSwipe in Guggenheim galleries ?>
  <!--
  <?= photoswipe() ?>
  <?php echo js('assets/guggenheim/js/guggenheim-photoswipe.min.js', true) ?>
  <?php echo css('assets/guggenheim/css/guggenheim-photoswipe.min.css') ?>
  -->

  <?php // Load all of the editing-related resources if they're a logged-in maker with the right permissions ?>
  <?php if($page->isEditableByUser() or $page->comments() or $page->submissions() == 'public'): ?>
    <?php echo js('assets/js/editing/packery.min.js') ?>
    <?php echo js('assets/js/editing/to-markdown.js') ?>
    <?php echo css('assets/js/contenttools/content-tools.min.css') ?>
    <?php echo js('assets/js/contenttools/content-tools.js') ?>
    <?php echo js('assets/js/editing/editor.js') ?>
  <?php endif ?>

  </footer>

<?php /*
<div id="whatever" class="ct-widget ct-toolbox ct-widget--active" style="left: 323px; top: 322px;"><div class="ct-toolbox__grip ct-grip"><div class="ct-grip__bump"></div><div class="ct-grip__bump"></div><div class="ct-grip__bump"></div></div><div class="ct-tool-groups"><div class="ct-tool-group"><div class="ct-tool ct-tool--bold ct-tool--disabled" data-tooltip="Bold"></div><div class="ct-tool ct-tool--italic ct-tool--disabled" data-tooltip="Italic"></div><div class="ct-tool ct-tool--link ct-tool--disabled" data-tooltip="Link"></div><div class="ct-tool ct-tool--heading ct-tool--disabled" data-tooltip="Heading"></div><div class="ct-tool ct-tool--subheading ct-tool--disabled" data-tooltip="Subheading"></div><div class="ct-tool ct-tool--paragraph ct-tool--disabled" data-tooltip="Paragraph"></div><div class="ct-tool ct-tool--unordered-list ct-tool--disabled" data-tooltip="Bullet list"></div><div class="ct-tool ct-tool--ordered-list ct-tool--disabled" data-tooltip="Numbers list"></div><div class="ct-tool ct-tool--table ct-tool--disabled" data-tooltip="Table"></div></div><div class="ct-tool-group"><div class="ct-tool ct-tool--image ct-tool--disabled" data-tooltip="Image"></div><div class="ct-tool ct-tool--video ct-tool--disabled" data-tooltip="Video"></div><div class="ct-tool ct-tool--preformatted ct-tool--disabled" data-tooltip="Preformatted"></div></div><div class="ct-tool-group"><div class="ct-tool ct-tool--undo ct-tool--disabled" data-tooltip="Undo"></div><div class="ct-tool ct-tool--redo ct-tool--disabled" data-tooltip="Redo"></div><div class="ct-tool ct-tool--remove ct-tool--disabled" data-tooltip="Remove"></div></div></div></div>
*/ ?>

  <?php // Load image scrapers for photoswipe, eventually these should be combined ?>
  <?php echo js('assets/js/photoswipe/photoswipe-scraper.js', true) ?>
  <?php echo js('assets/js/photoswipe/photoswipe-gallery-scraper.js', true) ?>

  <?php echo css('assets/js/photoswipe/photoswipe.css') ?>
  <?php echo css('assets/js/photoswipe/default-skin/default-skin.css') ?>
  <?php echo js('assets/js/photoswipe/photoswipe.min.js', true) ?>
  <?php echo js('assets/js/photoswipe/photoswipe-ui-default.min.js', true) ?>

  <?php // photoswipe DOM element http://photoswipe.com/documentation/getting-started.html#init-add-pswp-to-dom ?>
  <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"> <div class="pswp__bg"></div><div class="pswp__scroll-wrap"> <div class="pswp__container"> <div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div></div><div class="pswp__ui pswp__ui--hidden"> <div class="pswp__top-bar"> <div class="pswp__counter"></div><button class="pswp__button pswp__button--close" title="Close (Esc)"></button> <button class="pswp__button pswp__button--share" title="Share"></button> <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button> <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button> <div class="pswp__preloader"> <div class="pswp__preloader__icn"> <div class="pswp__preloader__cut"> <div class="pswp__preloader__donut"></div></div></div></div></div><div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"> <div class="pswp__share-tooltip"></div></div><button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button> <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button> <div class="pswp__caption"> <div class="pswp__caption__center"></div></div></div></div></div>

<?php if($page->intendedTemplate() == 'project' or $page->intendedTemplate() == 'handbook' or $page->intendedTemplate() == 'article' or $page->intendedTemplate() == 'section' or $page->intendedTemplate() == 'maker' or $page->intendedTemplate() == 'photostream'): ?>

<?php endif ?>


</body>
</html>