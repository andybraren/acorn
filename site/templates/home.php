<?php snippet('header') ?>
<?php snippet('hero', array('type' => 'fullwidth')) ?>

  <main class="main">
    <div class="container">
      
      <div class="section">
        <div class="subsection">
          <div class="text size-67">
            <h1><?php echo $page->title()->html() ?></h1>
            <?php echo $page->text()->kirbytext() ?>
          </div>
          <div class="text size-33 card-join highlight">
            <h2>Join the Network</h2>
            <p>Get email updates, register for events, reserve equipment, start new projects and more with one account that ties everything together.</p>
            <button class="button fullwidth button-signup">Become a Maker</button>
          </div>
        </div>
      </div>
      
      <hr class="divider">
      
      <div class="section">
        <div class="subsection">
          
          <div class="size-quarter green card-join highlight">
            <?php if ($site->page('projects')): ?>
              <a href="<?php echo page('projects')->url() ?>">
                <h2>Browse projects</h2>
                
                <?php if ($image = page('projects')->children()->filterBy('visibility','public')->sortBy('dateCreated','desc')->first()->heroImage()): ?>
                  <img style="height:100px;" src="<?php echo $image->crop(270,100)->url() ?>"></img>
                <?php endif ?>
                
                <p>Browse projects and ideas created by Tufts students, faculty, and community members.</p>
              </a>
              <a class="button fullwidth" href="<?php echo page('projects')->url() ?>">View projects</a>
            <?php endif ?>
          </div>
        
          <div class="size-quarter red card-join highlight">
            <?php if ($site->page('handbooks')): ?>
              <a href="<?php echo page('handbooks')->url() ?>">
                <h2>Read handbooks</h2>
                
                  <?php if ($image = page('handbooks')->children()->filterBy('visibility','public')->sortBy('dateCreated','desc')->first()->heroImage()): ?>
                    <img style="height:100px;" src="<?php echo $image->crop(270,100)->url() ?>"></img>
                  <?php endif ?>
                
                <p>Learn the basics of a variety of tools and techniques.</p>
              </a>
              <a class="button fullwidth" href="<?php echo page('handbooks')->url() ?>">View handbooks</a>
            <?php endif ?>
          </div>
          
          <div class="size-quarter gold card-join highlight">
            <?php if ($site->page('events')): ?>
              <a href="<?php echo page('events')->url() ?>">
                <h2>Attend an event</h2>
                
                <?php if ($image = page('events')->children()->filterBy('visibility','public')->sortBy('dateCreated','desc')->first()->heroImage()): ?>
                  <img style="height:100px;" src="<?php echo $image->crop(270,100)->url() ?>"></img>
                <?php endif ?>
                
                <p>Join a workshop or event to learn new things with others.</p>
              </a>
              <a class="button fullwidth" href="<?php echo page('events')->url() ?>">View events</a>
            <?php endif ?>
          </div>
          
          <div class="size-quarter purple card-join highlight">
            <?php if ($site->page('spaces')): ?>
              <a href="<?php echo page('spaces')->url() ?>">
                <h2>Visit a space</h2>
                
                  <?php if ($image = page('spaces')->children()->filterBy('visibility','public')->sortBy('dateCreated','desc')->first()->heroImage()): ?>                
                    <img style="height:100px;" src="<?php echo $image->crop(270,100)->url() ?>"></img>
                  <?php endif ?>
                
                <p>Stop by one of Tufts' makerspaces to get access to tools and equipment.</p>
              </a>
              <a class="button fullwidth" href="<?php echo page('spaces')->url() ?>">View spaces</a>
            <?php endif ?>
          </div>
          
        </div>
      </div>
      
      <?php /*
      
      <hr class="divider">
      
          <div class="section">
            <div class="subsection">
              <div class="section size-half">
                <h2>Clubs & Groups</h2>
                <p>Tufts has several clubs and organizations that encourage and promote making of all kinds. Whether you're looking to join a semester-long project, participate in national competitions, take your ideas to market or simply want to hang out with other like-minded makers, there's a group you can join.</p>
              </div>
              <div class="subsection size-half">
              <?php foreach(page('groups')->children() as $club): ?>
                <?php if($exists = $club->image('logo.svg')): ?>
                  <a class="logo-club size-third">
                    <img class="" src="<?php echo $club->image('logo.svg')->url() ?>"></img>
                  </a>
                <?php endif ?>
              <?php endforeach ?>
              </div>
            </div>
          </div>      
          
      <hr class="divider">
      
      <?php snippet('spaces') ?>
      
      <hr class="divider">

      <div class="section">
        <div class="subsection">
          
          <div class="section size-half">
            <h2>Tufts Makers</h2>
            <p>The most important part of the Network is, of course, makers like you.</p>
            <p>The things you make, the events you plan, the clubs you join and the lessons you document are what make the Network tick. All of that activity is bundled up and presented on your Maker Profile page, which allows others to see the cool things you've done and connect with you for more making opportunities.</p>
            <p>All of that starts by joining the Network.</p>
          </div>
          
          <div class="cards-makers size-half">
            <?php snippet('cards', array('type' => 'users')) ?>
          </div>
          
        </div>
      </div>
      
      */ ?>

    </div>
  </main>

<?php snippet('footer') ?>