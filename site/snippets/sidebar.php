<?php
  $page = page();
  $site = site();
?>


<div class="sidebar">
  
  <?php // PAGE ICON / LOGO ?>
  <?php if ($icon = $page->image('icon.svg') or $icon = $page->image('icon.png') or $icon = $page->image('icon.jpg')): ?>
    <div id="icon"><img id="icon-img" src="<?php echo $icon->url() ?>"></div>
  <?php endif ?>

  <?php // MAKER PROFILE IMAGE ?>
  <?php if ($page->parent() == 'users'): ?>
    <?php $user = $site->user($page->slug()); ?>
    <?php if ($avatar = $user->avatar()): ?>
      <div id="avatar">
        <img id="avatar-img" src="<?php echo $avatar->crop(300, 300)->url() ?>">
        <?php if ($page->isEditableByUser()): ?>
          <div id="avatar-add">Change profile photo</div>
        <?php endif ?>
      </div>
    <?php else: ?>
      <div id="avatar" class="<?php echo userColor($user) ?>">
        <img id="avatar-img" src="<?php echo userAvatar($user, 108) ?>">
        <?php if ($page->isEditableByUser()): ?>
          <div id="avatar-add">Change profile photo</div>
        <?php endif ?>
      </div>
    <?php endif ?>
  <?php endif ?>
  
  <?php // Join group/class button ?>
  <?php if (site()->page('groups')): ?>
    <?php if ($page->isChildOf(site()->page('groups'))): ?>
      <?php if (site()->user() and site()->user()->usertype() != 'admin'): ?>
        <?php if (!$page->isEditableByUser() and !in_array(site()->user()->username(), array_merge($page->requests(),$page->authors()->toArray()))): ?>
          <div id="button-join" class="button">Join group</div>
        <?php elseif (in_array(site()->user()->username(), $page->requests())): ?>
          <div id="button" class="button">Request sent</div>
        <?php endif ?>
      <?php endif ?>
    <?php endif ?>
  <?php endif ?>
  
  <?php // EDIT BUTTON ?>
  <?php snippet('widget', array('type' => 'edit')) ?>
  
  <?php // USERS ?>
  <?php if($page->uid() == 'users'): ?>
    <span class="heading">FILTER BY:</span>
    <form id="filters">
      <div>
        <select name="affiliation">
            <option value="">None</option>
          <?php foreach ($site->content()->affiliationoptions()->split(',') as $option): ?>
            <option value="<?php echo str::slug($option,'-') ?>"><?php echo trim($option) ?></option>
          <?php endforeach ?>
        </select>
        <label for="affiliation">Tufts Affiliation</label>
      </div>
      <div>
        <select name="department">
          <option value="">None</option>
          <?php foreach ($site->content()->departmentoptions()->split('##') as $option): ?>
            <option value="<?php echo str::slug($option,'-') ?>"><?php echo trim($option) ?></option>
          <?php endforeach ?>
        </select>
        <label for="department">Department</label>
      </div>
      <div>
        <select name="majors">
          <option value="">None</option>
          <?php foreach ($site->content()->majoroptions()->split('##') as $option): ?>
            <option value="<?php echo str::slug($option,'-') ?>"><?php echo trim($option) ?></option>
          <?php endforeach ?>
        </select>
        <label for="majors">Major</label>
      </div>
      <div>
        <input type="number" name="classyear" min="<?php echo (date("Y") - 99) ?>" max="<?php echo (date("Y") + 8) ?>" maxlength="4" step="1" required>
        <label for="classyear">Class year</label>
      </div>
    </form>
    
    <?php /*
    <div class="filter column">
      <span class="heading">Clubs</span>
      <form action="">
        <input type="checkbox" checked=""> Robotics<br>
        <input type="checkbox" checked=""> MAKE<br>
        <input type="checkbox" checked=""> Entrepreneurs Society<br>
        <input type="checkbox" checked=""> Crafts House<br>
        <input type="checkbox" checked=""> Computer Science Exchange<br>
        <input type="checkbox" checked=""> Human Factors Society<br>
      </form>
    </div>
    */ ?>
  <?php endif ?>

  <?php // PROJECT FILTER ?>
  <?php if($page->uid() == 'projects'): ?>
    <!--
    <div class="filter">
      <span class="heading">Filter</span>
      <form action="">
        <input type="checkbox" checked="">Personal<br>
        <input type="checkbox" checked="">Research<br>
        <input type="checkbox" checked="">Group<br>
        <input type="checkbox" checked="">Multidisciplinary<br>
      </form>
    </div>
    -->
  <?php endif ?>
  
  <?php // MAKER PROFILES ?>
  <?php if ($page->parent() == 'users'): ?>
    <?php $user = $site->user($page->slug()) ?>
    <div class="info">

      <?php // Class Year ?>
      <?php echo ($user->classyear() != null) ? '<span>Class of ' . $user->classyear() . '</span>' : ""; ?>
      
      <?php // Major ?>
      <?php echo ($user->major() != null) ? '<span>' . $user->major() . '</span>' : ""; ?>
      
      <?php // Social Media ?>
      <?php echo ($user->twitter() != null) ? '<a href="https://twitter.com/' . $user->twitter() . '">Twitter</a>' : ""; ?>
      <?php echo ($user->linkedin() != null) ? '<a href="https://www.linkedin.com/in/' . $user->linkedin() . '">LinkedIn</a>' : ""; ?>
      
      
    </div>
    
    <!--
    <div class="info column">
      <span class="heading">Groups</span>
      <span>CEEO</span>
    </div>
    
    <div class="info column">
      <span class="heading">Equipment</span>
      <span>3D Printing</span>
      <span>Soldering</span>
      <span>Laser cutting</span>
    </div>
    -->
    
  <?php endif ?>
  
  <?php // FORUM ?>
  <?php if ($page->uid() == 'forum' or $page->parent() == 'forum'): ?>
    
    <?php if ($page->uid() == 'forum'): ?>
      <a href="<?php echo $page->url() . '/new' ?>">
        <div id="button-new" class="button green">Start a discussion</div>
      </a>
    <?php endif ?>
    
    <div class="widget categories">
      <span class="heading">CATEGORIES</span>
      <ul>
        <li class="blue"><a href="">General</a></li>
        <li class="red"><a href="">Bugs</a></li>
        <li class="green"><a href="">Ideas</a></li>
        <li class="orange"><a href="">Questions</a></li>
        <li class="purple"><a href="">Flux capacitance</a></li>
        <li class="silver"><a href="">Radial constant</a></li>
        <li class="black"><a href="">Spline reticulation</a></li>
      </ul>
    </div>
  <?php endif ?>


  <?php // EVENTS SORTING ?>
  <?php if($page->uid() == 'events'): ?>
  <!--
    <div class="hours">
      <span class="heading">SUBSCRIBE</span>
      <span><a href="https://www.facebook.com/events/1714521768776526/">Google Calendar</a></span>
      <span><a href="https://www.facebook.com/events/1714521768776526/">RSS</a></span>
    </div>
    
    <div class="hours">
      <span class="heading">Clubs:</span>
      <div class="row"><span><input type="checkbox" name="vehicle" value="all" checked> All</input></span></div>
      <div class="row"><span><input type="checkbox" name="vehicle" value="all"> CSX</input></span></div>
      <div class="row"><span><input type="checkbox" name="vehicle" value="make"> MAKE</input></span></div>
      <div class="row"><span><input type="checkbox" name="vehicle" value="make"> MEGSO</input></span></div>
      <div class="row"><span><input type="checkbox" name="vehicle" value="robotics-"> Robotics</input></span></div>
    </div>

    <div class="hours">
      <span class="heading">Spaces:</span>
      <div class="row"><span><input type="checkbox" name="vehicle" value="all" checked> All</input></span></div>
      <div class="row"><span><input type="checkbox" name="vehicle" value="make"> Maker Studio</input></span></div>
      <div class="row"><span><input type="checkbox" name="vehicle" value="robotics-"> Crafts Center</input></span></div>
      <div class="row"><span><input type="checkbox" name="vehicle" value="all"> Digital Design Studio</input></span></div>
    </div>

    <div class="hours">
      <span class="heading">Type:</span>
      <div class="row"><span><input type="checkbox" name="vehicle" value="all" checked> All</input></span></div>
      <div class="row"><span><input type="checkbox" name="vehicle" value="make"> Training</input></span></div>
      <div class="row"><span><input type="checkbox" name="vehicle" value="robotics-"> Speaker</input></span></div>
      <div class="row"><span><input type="checkbox" name="vehicle" value="all"> Workshop</input></span></div>
    </div>
  -->
  <?php endif ?>

  <?php // LOCATION INFORMATION ?>
  <?php if($page->location() != ""): ?>
    <div class="widget location">
      <span class="heading">LOCATION</span>
      <?php $address = $page->location() ?>
      <?php $address = ($page->location() == "Crafts Center" ? "75 Packard Ave, Medford MA 02155" : $page->location()) ?>
      <?php $address = ($page->location() == "Maker Studio" ? "574 Boston Ave, Medford MA 02155" : $page->location()) ?>
      
      <?php $remoteURL = "https://maps.googleapis.com/maps/api/staticmap?center=" . urlencode($address) . '&zoom=16&scale=1&size=250x150&maptype=roadmap&format=jpg&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7C%7C' . urlencode($address) ?>
      
      <a href='<?php echo "https://maps.google.com?daddr=" . urlencode($address) ?>'>
        <img src='<?php echo downloadedImageURL('location', $remoteURL); ?>'>
      </a>
      <span id="address"><a href='<?php echo "https://maps.google.com?daddr=" . urlencode($address) ?>'><?php echo $address ?></a></span>
    </div>
  <?php endif ?>
    
  <?php // TIME INFORMATION ?>
  <?php if($page->start() != ""): ?>
    <div class="widget hours">
      <span class="heading">TIME / DATE</span>
      <span><?php echo $page->date('l F d','Start') . '<br>' . $page->date('g:i','Start') . ' - ' . $page->date('g:i','End') ?></span>
    </div>
  <?php endif ?>
  
  <?php // TIME INFORMATION NEEDS TO BE MERGED WITH THE ABOVE?>
  <?php if($page->StartDate() != ""): ?>
    <div class="widget hours">
      <span class="heading">EVENT DATE</span>
      <span><?php echo $page->date('l F d','StartDate') . '<br>' . $page->date('g:i A','StartDate') . ' - ' . $page->date('g:i A','EndDate') ?></span>
    </div>
  <?php endif ?>
  
  <?php // SPACE HOURS INFORMATION ?>
  <?php if($page->hours() != ""): ?>
    <div class="widget hours">
      <span class="heading">HOURS</span>
        <?php
          
          //$hours = preg_split('/(\s)/', $page->hours());
          $part = str::split($page->hours(), '~');
          $hours = (isset($part[0])) ? preg_split('/(\s)/', $part[0]) : '';
          $description = (isset($part[1])) ? $part[1] : '';
            
          list($Sun1, $Sun2, $Mon1, $Mon2, $Tue1, $Tue2, $Wed1, $Wed2, $Thu1, $Thu2, $Fri1, $Fri2, $Sat1, $Sat2) = $hours;
          
          date_default_timezone_set('EST');
          
          echo '<div class="row"' . (date('D') == "Sun" ? 'id="currentday"' : '') . '><span class="day">SUN</span><span class="hour">' . ($Sun1 == $Sun2 ? 'Closed</span></div>' : date('g:i A', strtotime($Sun1)) . ' - ' . date('g:i A', strtotime($Sun2)) . '</span></div>');
          echo '<div class="row"' . (date('D') == "Mon" ? 'id="currentday"' : '') . '><span class="day">MON</span><span class="hour">' . ($Mon1 == $Mon2 ? 'Closed</span></div>' : date('g:i A', strtotime($Mon1)) . ' - ' . date('g:i A', strtotime($Mon2)) . '</span></div>');
          echo '<div class="row"' . (date('D') == "Tue" ? 'id="currentday"' : '') . '><span class="day">TUE</span><span class="hour">' . ($Tue1 == $Tue2 ? 'Closed</span></div>' : date('g:i A', strtotime($Tue1)) . ' - ' . date('g:i A', strtotime($Tue2)) . '</span></div>');
          echo '<div class="row"' . (date('D') == "Wed" ? 'id="currentday"' : '') . '><span class="day">WED</span><span class="hour">' . ($Wed1 == $Wed2 ? 'Closed</span></div>' : date('g:i A', strtotime($Wed1)) . ' - ' . date('g:i A', strtotime($Wed2)) . '</span></div>');
          echo '<div class="row"' . (date('D') == "Thu" ? 'id="currentday"' : '') . '><span class="day">THU</span><span class="hour">' . ($Thu1 == $Thu2 ? 'Closed</span></div>' : date('g:i A', strtotime($Thu1)) . ' - ' . date('g:i A', strtotime($Thu2)) . '</span></div>');
          echo '<div class="row"' . (date('D') == "Fri" ? 'id="currentday"' : '') . '><span class="day">FRI</span><span class="hour">' . ($Fri1 == $Fri2 ? 'Closed</span></div>' : date('g:i A', strtotime($Fri1)) . ' - ' . date('g:i A', strtotime($Fri2)) . '</span></div>');
          echo '<div class="row"' . (date('D') == "Sat" ? 'id="currentday"' : '') . '><span class="day">SAT</span><span class="hour">' . ($Sat1 == $Sat2 ? 'Closed</span></div>' : date('g:i A', strtotime($Sat1)) . ' - ' . date('g:i A', strtotime($Sat2)) . '</span></div>');
          echo '<span>' . strip_tags(kirbytext($description), '<a>') . '</span>';
          
        ?>
      
    </div>
  <?php endif ?>
  
  <?php // PURCHASE WIDGET ?>
  <?php if($page->content()->price() != ''): ?>
    <?php snippet('checkout') ?>
  <?php endif ?>

  <?php // AUTHORS WIDGET ?>
  <?php if($page->parent() != 'users' and $page->uid() != 'settings' and $_SERVER['SERVER_NAME'] != 'dev.andybraren.com'): ?>
    <?php snippet('widget', array('type' => 'authors')) ?>
  <?php endif ?>
  
  <?php // META WIDGET ?>
  <?php if (!in_array($page->parent(), array('', 'users', 'spaces', 'settings', 'handbooks'))): ?>
    <?php snippet('widget', array('type' => 'meta')) ?>
  <?php endif ?>
  
  <?php // RELATED PROJECTS WIDGET ?>
  <?php if($page->parent() != 'projects'): ?>
    <?php if ($page->parent() != 'users'): ?>
      <?php snippet('widget', array('type' => 'projects')) ?>
    <?php endif ?>
  <?php endif ?>
  
  <?php // RELATED GROUPS WIDGET ?>
  <?php if($page->parent() != 'groups'): ?>
    <?php if ($page->parent() != 'users'): ?>
      <?php snippet('widget', array('type' => 'groups')) ?>
    <?php endif ?>
  <?php endif ?>
  
  <?php // RELATED EVENTS WIDGET ?>
  <?php if($page->parent() != 'events'): ?>
    <?php if ($page->parent() != 'users'): ?>
      <?php snippet('widget', array('type' => 'events')) ?>
    <?php endif ?>
  <?php endif ?>
  
  <?php // RELEVANT SPACES WIDGET - displays each Handbook related to this piece of Equipment ?>
  <?php if($page->parent() == "equipment"): ?>
    <?php snippet('widget', array('type' => 'spaces')) ?>
  <?php endif ?>
  
  <?php // RELEVANT HANDBOOKS WIDGET - displays each Handbook related to this piece of Equipment ?>
  <?php if($page->parent() == "equipment" or $page->parent() == "groups"): ?>
    <?php snippet('widget', array('type' => 'handbooks')) ?>
  <?php endif ?>
  
  <?php // TABLE OF CONTENTS ?>
  <?php if($page->intendedTemplate() == 'page' or $page->intendedTemplate() == 'handbook' or $page->intendedTemplate() == 'article' or $page->intendedTemplate() == 'project' or $page->uid() == 'docs'): ?>
    <?php if(preg_match_all('/(?<!#)#{2,4}([^#].*)\n/', $page->text(), $matches)): // Grabs H2's and H3's ?>
      <div class="widget toc sticky">
        <div>
          <span class="heading">CONTENTS</span>
          <a class="toc-top">&#8673;</a>
        </div>
        <div class="toc-items">
          <?php
            $count = 0;
            $sublist = 'none';
            $sublist2 = 'none';
            foreach ($matches[0] as $rawmatch) {
              
              $text = $matches[1][$count];
              $lastmatch = end($matches[0]);
              
              if (preg_match('/(?<!#)#{2}([^#].*)\n/', $rawmatch)) { // H2
                
                if ($sublist == 'start') {
                  echo '</ul>';
                  $sublist = 'none';
                }
                
                echo '<li><a href="#' . str::slug($text) . '">' . $text . '</a></li>';
                
              }
              if (preg_match('/(?<!#)#{3}([^#].*)\n/', $rawmatch)) { // H3
                
                if ($sublist == 'none') {
                  $sublist = 'start';
                  echo '<ul>';
                }
                if ($sublist2 == 'start') {
                  echo '</ul>';
                  $sublist2 = 'none';
                }
                
                echo '<li><a href="#' . str::slug($text) . '">' . $text . '</a></li>';
                
              }
              if (preg_match('/(?<!#)#{4}([^#].*)\n/', $rawmatch)) { // H4
                
                if ($sublist2 == 'none') {
                  $sublist2 = 'start';
                  echo '<ul>';
                }
                
                echo '<li><a href="#' . str::slug($text) . '">' . $text . '</a></li>';
                
              }
              
              if ($rawmatch == $lastmatch) {
                echo ($sublist == 'start') ? '</ul>' : '';
                echo ($sublist2 == 'start') ? '</ul>' : '';
              }
              
              $count++;
            }
          ?>
        </div>
      <svg class="toc-marker" width="200" height="200" xmlns="http://www.w3.org/2000/svg">
				<path stroke="#444" stroke-width="3" fill="transparent" stroke-dasharray="0, 0, 0, 1000" stroke-linecap="round" stroke-linejoin="round" transform="translate(-0.5, -0.5)" />
			</svg>
      </div>
    <?php endif ?>
  <?php endif ?>







<?php
function getMakerRole($person) {

  if(preg_match("/" . page()->slug() . " == (.*?) \|\|/", site()->user($person)->roles(), $matches)) {
    return $matches[1];
  }
  
  else {
    return "blah";
  }


}
?>

</div>

