<div id="modals">
  
  <div id="modal-backdrop"></div>
  
  <?php // DELETE ?>
  <div id="modal-delete" class="modal">
    
    <div class="modal-title">
      <h2>Delete this page?</h2>
      <button type="button" aria-label="close" data-action="close"></button>
    </div>
    
    <div class="modal-content">
      <span>This entire page and all of its content will be deleted.</span>
    </div>
    
    <div class="modal-options">
      <button type="button" data-action="close" class="button fullwidth silver">Never mind</button>
      <button type="button" data-action="delete" class="button fullwidth red">Delete</button>
    </div>
    
  </div>
  
  <?php // NAV ITEM EDIT ?>
  <div id="modal-navitem" class="modal">
    
    <div class="modal-title">
      <h2>Edit nav item</h2>
      <button type="button" aria-label="close" data-action="close"></button>
    </div>
    
    <div class="modal-content">
      <form id="navitem">
        <div>
          <input type="text" name="title" class="clicked" autofocus>
          <label for="title">Title</label>
        </div>
        
        <div>
          <input type="text" name="url" class="clicked">
          <label for="url">URL</label>
        </div>
        
        <div>
          <input type="text" name="subtitle" class="clicked">
          <label for="subtitle">Subtitle</label>
        </div>
      </form>
    </div>
    
    <div class="modal-options">
      <button type="button" data-action="close" class="button fullwidth silver">Cancel</button>
      <button type="button" data-action="deletenav" class="button fullwidth red">Delete</button>
      <button type="button" data-action="updatenav" class="button fullwidth green">Apply</button>
    </div>
    
  </div>
  
  <?php // LOG IN ?>
  <div id="modal-login" class="modal">
    
    <div class="modal-title">
      <h2>Log in</h2>
      <button type="button" aria-label="close" data-action="close"></button>
    </div>
    
    <div class="modal-content">
      <form id="login" action="<?php echo $page->url() . '/login' ?>" method="post">
        
        <?php if (param('login') == 'failed'): ?>
          <div class="red highlight card-join">
            <span>Login failed. Try again, or contact <a href="&#109;&#97;ilto&#58;and%79&#98;rare%&#54;&#69;&#64;g&#109;a%&#54;9l&#46;&#99;%6&#70;m?subject=Website login issue">Andy</a> for help.</span>
          </div>
        <?php endif ?>
        
        <div>
          <input type="text" name="username" pattern="^[a-zA-Z0-9]{3,20}$|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-z]{2,3}$" required autofocus> <?php // Only letters and numbers, between 3 and 20 ?>
          <label for="username">Username (or email)</label>
        </div>
        
        <div>
          <input type="password" name="password" pattern=".{4,}" required> <?php // At least 4 characters?>
          <label for="password">Password</label>
        </div>
        
        <span id="button-forgot" data-modal="forgot">I forgot my password</span>
        
      </form>
    </div>
    
    <div class="modal-options">
      <button type="button" data-action="close" class="button fullwidth silver">Never mind</button>
      <button type="submit" data-action="login" class="button fullwidth" form="login">Log in</button>
    </div>
    
  </div>
  
  <?php // FORGOT PASSWORD ?>
  <div id="modal-forgot" class="modal">
      
    <div class="modal-title">
      <h2>Password Reset</h2>
    </div>
    
    <div class="modal-content">
      <?php if (!param('forgot')): ?>
        <span>Enter the non-Tufts email address you signed up with and password reset instructions will be sent to you.</span>
        
        <form action="forgot" method="post">
          <div>
            <input type="text" name="email" required>
            <label for="email">Email address</label>
          </div>
          <div>
            <input type="submit" class="button fullwidth" name="forgot" value="Send reset email">
          </div>
        </form>
      <?php endif ?>
    </div>
    
    <?php if (param('forgot') == 'success'): ?>
      <div class="green highlight card-join">
        <span>An email has been sent to your inbox. Click the link it contains to reset your password.</span>
      </div>
    <?php elseif (param('forgot') == 'failed'): ?>
        <div class="red highlight card-join">
          <span>Password reset failed. Try again, or contact <a href="&#109;&#97;ilto&#58;and%79&#98;rare%&#54;&#69;&#64;g&#109;a%&#54;9l&#46;&#99;%6&#70;m?subject=Website login issue">Andy</a> for help.</span>
        </div>
    <?php endif ?>
      
  </div>
  
  
  
  
  
  
  

</div>






























<div class="modals">
  
  <div id="modal-reading">
    <span class="theme-night">Night Mode: Off</span><hr>
    <span class="font-family">Dyslexia: Off</span><hr>
    <div class="row">
      <span>Font size:</span>
      <span class="font-decrease">-</span>
      <span class="font-reset">16</span>
      <span class="font-increase">+</span>
    </div>
  </div>
  
  <div id="modal-toc" class="widget">
    <?php if(preg_match_all('/(?<!#)#{2,3}([^#].*)\n/', $page->text(), $matches)): // Grabs H2's and H3's ?>
      <div>
        <span class="heading">CONTENTS</span>
        <a class="toc-top">&#8673;</a>
      </div>
      <div class="toc-items">
        <?php
          $count = 0;
          $sublist = 'none';
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
              
              echo '<li><a href="#' . str::slug($text) . '">' . $text . '</a></li>';
              
            }
            
            if ($rawmatch == $lastmatch) {
              echo ($sublist == 'start') ? '</ul>' : '';
            }
            
            $count++;
          }
        ?>
      </div>
    <?php endif ?>
  </div>
  
  <div id="modal-login" class="modal<?php if(param('login')) { echo ' visible'; } ?>">
    <div class="modal-container">
      <div class="modal-content">
          
        <div class="modal-title">
          <h2>Log in</h2>
        </div>

        <form action="<?php echo $page->url() . '/login' ?>" method="post">
          <div style="display:none">
            <input name="utf8" type="hidden" value="✓">
            <input name="authenticity_token" type="hidden" value="ejnK+ZsHkfh/wG7YeI8LTLsUSARt/cC9jDAn5jRibN0=">
          </div>
          
          <div>
            <input type="text" name="username" pattern="^[a-zA-Z0-9]{3,20}$|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-z]{2,3}$" required autofocus> <?php // Only letters and numbers, between 3 and 20 ?>
            <label for="username">Username (or email)</label>
          </div>
          <div>
            <input type="password" name="password" pattern=".{4,}" required> <?php // At least 4 characters?>
            <label for="password">Password</label>
          </div>
          
          <span id="button-forgot" style="cursor: pointer;">I forgot my password</span>
          
          <?php if (param('login') == 'failed'): ?>
            <div class="red highlight card-join">
              <span>Login failed. Try again, or contact <a href="&#109;&#97;ilto&#58;and%79&#98;rare%&#54;&#69;&#64;g&#109;a%&#54;9l&#46;&#99;%6&#70;m?subject=Website login issue">Andy</a> for help.</span>
            </div>
          <?php endif ?>
          
          <div>
            <input type="submit" class="button fullwidth" name="login" value="Log in">
          </div>
        </form>
                  
      </div>
    </div>
  </div>

  <div id="modal-forgot" class="modal<?php if(param('forgot')) { echo ' visible'; } ?>">
    <div class="modal-container">
      <div class="modal-content">
        
        <div class="modal-title">
          <h2>Password Reset</h2>
        </div>
        
        <?php if (!param('forgot')): ?>
          <span>Enter the non-Tufts email address you signed up with and password reset instructions will be sent to you.</span>
          
          <form action="forgot" method="post">
            <div>
              <input type="text" name="email" required>
              <label for="email">Email address</label>
            </div>
            <div>
              <input type="submit" class="button fullwidth" name="forgot" value="Send reset email">
            </div>
          </form>
        <?php endif ?>

        <?php if (param('forgot') == 'success'): ?>
          <div class="green highlight card-join">
            <span>An email has been sent to your inbox. Click the link it contains to reset your password.</span>
          </div>
        <?php elseif (param('forgot') == 'failed'): ?>
            <div class="red highlight card-join">
              <span>Password reset failed. Try again, or contact <a href="&#109;&#97;ilto&#58;and%79&#98;rare%&#54;&#69;&#64;g&#109;a%&#54;9l&#46;&#99;%6&#70;m?subject=Website login issue">Andy</a> for help.</span>
            </div>
        <?php endif ?>
          
      </div>
    </div>
  </div>

  <div id="modal-reset" class="modal<?php if(param('username') or param('reset')) { echo ' visible'; } ?>">
    <div class="modal-container">
      <div class="modal-content">
        <div class="login">
          
          <div class="modal-title">
            <h2>Password reset</h2>
          </div>
          
          <?php if (!param('reset')): ?>
            <p>Please enter a new password for your account.</p>
            
            <form action="reset" method="post">
              <div>
                <input readonly type="text" value="<?php echo param('username') ?>" id="username" name="username" class="clicked">
                <label for="username">Username</label>
              </div>
              <div>
                <input type="password" id="newpassword" name="newpassword" autofocus="autofocus" required>
                <label for="newpassword">New Password</label>
              </div>
              <div>
                <input readonly type="text" value="<?php echo param('resetkey') ?>" id="resetkey" name="resetkey" class="invisible">
              </div>
              <div>
                <input type="submit" class="button fullwidth" name="reset" value="Reset password">
              </div>
            </form>
          <?php endif ?>

          <?php if (param('reset') == 'success'): ?>
            <div class="green highlight card-join">
              <span>Password reset successful. You are now logged in.</span>
            </div>
          <?php elseif (param('reset') == 'failed'): ?>
            <div class="red highlight card-join">
              <span>Password reset failed. Try again, or contact <a href="&#109;&#97;ilto&#58;and%79&#98;rare%&#54;&#69;&#64;g&#109;a%&#54;9l&#46;&#99;%6&#70;m?subject=Website login issue">Andy</a> for help.</span>
            </div>
          <?php endif ?>
            
        </div>
      </div>
    </div>
  </div>
  
  <div id="modal-signup" class="modal">
    <div class="modal-container">
      <div class="modal-content">
        
        <div class="modal-title">
          <h2>Sign up</h2>
        </div>
        
        <span>Join workshops and events, reserve equipment, start new projects, and connect with Tufts' maker community by creating an account.</span>
        
        <form action="<?php echo $page->url() . '/signup' ?>" method="post">
          
          <?php // Pulling information from the database needed for populating the form ?>
          <?php $errbyear = ''; ?>
          <?php if($site->url() == "https://maker.tufts.edu"): ?>
            <?php /*
              // This Link1 won't work until it's on the Tufts server.
              $link1 = pg_Connect("host=130.64.17.0 dbname=JMN user=jadmin password=jadmin_pw7");
              $result1 = pg_query($link1, "SELECT * FROM departments");
              $depts = pg_fetch_all($result1); // Grabs an array from the database
              $result2 = pg_query($link1, "SELECT * FROM relationship");
              $Rships = pg_fetch_all($result2);
              $youngest = date("Y") - 17;
              $oldest = date("Y") - 75;
              $current_class = date("Y") + 5;
              $Class_YEARS = range(date("Y"),$current_class);
              $years = range($youngest, $oldest);
              pg_close($link1);
            */
            ?>
          <?php endif ?>
          
          <div role="group">
            
            <h3>Basic info</h3>
            <div class="size-50">
              <input type="text" name="firstname" pattern="^[^0-9]{2,20}$" required> <?php // No numbers, at least 2 characters ?>
              <label for="firstname">First name</label>
            </div>
      
            <div class="size-50">
              <input type="text" name="lastname" pattern="^[^0-9]{2,20}$" required> <?php // No numbers, at least 2 characters ?>
              <label for="lastname">Last name</label>
            </div>
            
            
            <div class="size-50">
              <select name="color" class="neverclicked" id="signup-color" required>
                <?php foreach ($site->content()->coloroptions()->split(',') as $option): ?>
                  <?php echo '<option value="' . $option . '">' . ucfirst($option) . '</option>'; ?>
                <?php endforeach ?>
              </select>
              <label for="color">Favorite color</label>
            </div>
            
            <div class="size-50">
              <input type="number" name="birthyear" min="<?php echo (date("Y") - 117) ?>" max="<?php echo (date("Y") - 6) ?>" step="1" required>
              <label for="lastname">Birth year</label>
            </div>
            
            <?php /*
            <div class="size-100">
              <label for="color">Favorite color</label>
              <ul id="colorselector">
                <?php foreach ($site->content()->coloroptions()->split(',') as $option): ?>
                  <?php echo '<li class="' . $option . '"><input type="radio" name="color" id="' . $option . '" value="' . $option . '"><label for="' . $option . '">' . ucfirst($option) . '</label><div class="colorradio"></div></li>'; ?>
                <?php endforeach ?>
              </ul>
            </div>
            */ ?>
            
            
            <?php /* Better to use number type
            <div class="size-50">
              <input type="number" name="byear" pattern="^[0-9]{4}$" required>
              <label for="lastname">Birth year</label>
            </div>
            */ ?>
            

            
          </div>
          
          <div role="group">
            
            <h3>Tufts info</h3>
            
            <?php /*
            <div class="size-50">
              <select name="school" required>
                <option>School of Arts and Sciences</option>
                <option>School of Engineering</option>
                <option>Fletcher School of Law and Diplomacy</option>
                <option>Sackler School of Graduate Biomedical Sciences</option>
                <option>School of Dental Medicine</option>
                <option>School of Medicine</option>
                <option>Cummings School of Veterinary Medicine</option>
                <option>Friedman School of Nutrition Science and Policy</option>
              </select>
              <label for="school">Tufts School</label>
            </div>
            */ ?>
            
            <div class="size-25">
              <select name="affiliation" required>
                <?php foreach ($site->content()->affiliationoptions()->split(',') as $option): ?>
                  <?php echo '<option>' . $option . '</option>'; ?>
                <?php endforeach ?>
              </select>
              <label for="affiliation">Affiliation</label>
            </div>
            
            <div class="size-25">
              <select name="dept">
                <?php foreach ($site->content()->departmentoptions()->split('##') as $option): ?>
                  <?php echo '<option>' . $option . '</option>'; ?>
                <?php endforeach ?>
              </select>
              <label for="dept">Department</label>
            </div>
            
            <div class="size-25">
              <select name="major">
                <?php foreach ($site->content()->majoroptions()->split('##') as $option): ?>
                  <?php echo '<option>' . $option . '</option>'; ?>
                <?php endforeach ?>
              </select>
              <label for="major">Major</label>
            </div>
            
            <div class="size-25">
              <input type="number" name="classyear" min="<?php echo (date("Y") - 99) ?>" max="<?php echo (date("Y") + 8) ?>" maxlength="4" step="1" required>
              <label for="classyear">Class year</label>
            </div>
            
            <?php /*
            <div class="size-33">
              <select name="major">
                <option value="Anthropology">List of majors here</option>
                <option value="Anthropology">List of majors here</option>
                <option value="Anthropology">List of majors here</option>
                <option value="Anthropology">List of majors here</option>
              </select>
              <label for="major">Major</label>
            </div>
            */ ?>
            
            <?php /*
            <div class="size-25">
              <select type="C_year" placeholder="C_year" class="form-control form_text" name = "C_year">
                <option value="" disabled selected>Class Year</option>
                <option value="2016">2016</option>
                <option value="2017">2017</option>
                <option value="2018">2018</option>
                <option value="2019">2019</option>
                <option value="2020">2020</option>
                <option value="2021">2021</option>
              </select>
            </div>
            
            <div class="size-25">
              <select type="byear" placeholder="byear" class="form-control form_text" name = "byear">
                <option value="" disabled selected>Birth Year</option>
                <?php
                  $years = range(date('Y') - 75, date('Y') - 6);
                  foreach ($years as $year) {
                    echo '<option value="' . $year . '">' . $year . '</option>';
                  }
                ?>
              </select>
            </div>
            */ ?>
            
          </div>
          
          <div role="group">
            
            <h3>Account info</h3>
            
            <div class="size-50">
              <input type="email" name="email" pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-z]{2,3}$" required> <?php // (whatever)@xx.co ?>
              <label for="email">Personal email address</label>
              <span>Used to reset your password</span>
            </div>
            <div class="size-50">
              <!--<input type="email" name="tuftsemail" pattern="[a-z0-9._%+-]+@tufts.edu$" required> <?php // x@tufts.edu ?>-->
              <input type="email" name="tuftsemail" pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-z]{2,3}$" required> <?php // can't require Tufts, hmm ?>
              <label for="tuftsemail">Tufts email address</label>
            </div>
            
            <div class="size-50">
              <input type="text" name="username" pattern="^[a-zA-Z0-9]{3,20}$" id="usernamefield" required> <?php // Only letters and numbers, between 3 and 20 ?>
              <label for="username" id="usernamelabel">Username<span id="usernamemessage"></span></label>
            </div>
            <div class="size-50">
              <input type="password" name="password" pattern=".{4,}" required> <?php // At least 4 characters?>
              <label for="password">Password</label>
            </div>
            
            <span>Your profile URL will be <?php echo $site->url() ?>/<span id="usernameurl">username</span></span>
            
          </div>
    
          <div class="button-container">
            <input type="submit" class="button fullwidth submit_button primary_button" name="signup" value="Sign Up">
          </div>
        </form>
          
      </div>
    </div>
  </div>



</div>