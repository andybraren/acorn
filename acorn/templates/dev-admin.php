<?php snippet('header') ?>

<main class="main">
  <div class="container handbook">
    
    <?php if($user = $site->user() and $user->hasRole('admin')): ?>
    
        <?php foreach($site->users()->sortBy('registrationdate', 'desc') as $user): ?>

      
              <?php echo $user->tuftsemail() . ', ' ?>
            
          
        <?php endforeach ?>
    
    <?php endif ?>


  </div>
</main>

<?php snippet('footer') ?>