<footer class="footer">
  <div class="container">
    
    <div class="left">
      <?php echo $site->footerleft()->kirbytext() ?>
    </div>
    
    <div class="right">
      <?php echo $site->footerright()->kirbytext() ?>
    </div>
    
  </div>
  
  <?php // Load all of the editing-related resources if they're a logged-in maker with the right permissions ?>
  <?php if($page->isEditableByUser()): ?>
    <?php echo js('site/assets/js/editing/to-markdown.js') ?>
    <?php echo css('site/assets/js/contenttools/content-tools.min.css') ?>
    <?php echo js('site/assets/js/contenttools/content-tools.js') ?>
    <?php echo js('site/assets/js/editing/editor.js') ?>
  <?php endif ?>
  
</footer>

<?php if ($page->content()->price() != null && $page->content()->price() != ''): ?>
  <script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<?php endif ?>

<?php // if (is_dir('site/assets/js/photoswipe') and kirby()->get('option', 'photoswipe') == 'on' and page()->hasImages()): ?>
<?php if (is_dir('site/assets/js/photoswipe') and strpos($page->content()->text(), 'image:')): ?>
  <?php // Load image scrapers for photoswipe, eventually these should be combined ?>
  <?php echo js('site/assets/js/photoswipe/photoswipe-scraper.js', true) ?>
  <?php echo js('site/assets/js/photoswipe/photoswipe-gallery-scraper.js', true) ?>
  
  <?php echo css('site/assets/js/photoswipe/photoswipe.css') ?>
  <?php echo css('site/assets/js/photoswipe/default-skin/default-skin.css') ?>
  <?php echo js('site/assets/js/photoswipe/photoswipe.js', true) ?>
  <?php echo js('site/assets/js/photoswipe/photoswipe-ui-default.js', true) ?>
<?php endif ?>

<?php // photoswipe DOM element http://photoswipe.com/documentation/getting-started.html#init-add-pswp-to-dom ?>
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"> <div class="pswp__bg"></div><div class="pswp__scroll-wrap"> <div class="pswp__container"> <div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div></div><div class="pswp__ui pswp__ui--hidden"> <div class="pswp__top-bar"> <div class="pswp__counter"></div><button class="pswp__button pswp__button--close" title="Close (Esc)"></button> <button class="pswp__button pswp__button--share" title="Share"></button> <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button> <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button> <div class="pswp__preloader"> <div class="pswp__preloader__icn"> <div class="pswp__preloader__cut"> <div class="pswp__preloader__donut"></div></div></div></div></div><div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"> <div class="pswp__share-tooltip"></div></div><button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button> <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button> <div class="pswp__caption"> <div class="pswp__caption__center"></div></div></div></div></div>

</body>
</html>