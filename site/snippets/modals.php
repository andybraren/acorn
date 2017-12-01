<div id="modals">
  <div class="container">
    
    <?php // Account ?>
    <?php if ($user = $site->user()): ?>
      <div id="modal-account" class="modal top right">
        
        <ul class="list-options">
          <li><a id="datausername" href="<?php echo $site->url() . '/' . $user->username() ?>" data-username="<?php echo $user->username() ?>">View Profile</a></li>
          <li><a href="<?php echo $site->url() . '/settings' ?>">Site Settings</a></li>
          <li><a href="<?php echo $page->url() . '/logout' ?>">Logout</a></li>
        </ul>
          
      </div>
    <?php endif ?>
    
    <?php // Table of Contents ?>
    <?php if(preg_match_all('/(?<!#)#{2,3}([^#].*)\n/', $page->text(), $matches)): // Grabs H2's and H3's ?>
      <div id="modal-toc" class="modal widget top left">
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
      </div>
    <?php endif ?>
    
    <?php // Reading Settings ?>
    <div id="modal-reading" class="modal top right">
      
      <ul class="list-options">
        <li><span class="theme-night">Night Mode: Off</span></li>
        <li><span class="font-family">Dyslexia: Off</span></li>
        <div class="row">
          <span>Text size:</span>
          <span class="font-decrease">-</span>
          <span class="font-reset">16</span>
          <span class="font-increase">+</span>
        </div>
      </ul>
    </div>
    
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
            <input type="text" name="title" class="clicked">
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
            <input type="text" name="username" pattern="^[a-zA-Z0-9]{3,20}$|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-z]{2,3}$" required> <?php // Only letters and numbers, between 3 and 20 ?>
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
        <button type="button" aria-label="close" data-action="close"></button>
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
        
        <?php if (param('forgot') == 'success'): ?>
          <div class="green highlight card-join">
            <span>An email has been sent to your inbox. Check your spam filter.</span><br><br>
            <span>It may take up to 30 seconds to appear.</span><br><br>
            <span>Click the link it contains to reset your password.</span>
          </div>
        <?php elseif (param('forgot') == 'failed'): ?>
            <div class="red highlight card-join">
              <span>Password reset failed. Try again, or contact <a href="&#109;&#97;ilto&#58;and%79&#98;rare%&#54;&#69;&#64;g&#109;a%&#54;9l&#46;&#99;%6&#70;m?subject=Website login issue">Andy</a> for help.</span>
            </div>
        <?php endif ?>
      </div>
        
    </div>
    
    <?php // PASSWORD RESET ?>
    <div id="modal-reset" class="modal<?php if(param('username') or param('reset')) { echo ' visible'; } ?>">
      
      <div class="modal-title">
        <h2>Password Reset</h2>
        <button type="button" aria-label="close" data-action="close"></button>
      </div>
      
      <div class="modal-content">
        <?php if (!param('reset')): ?>
          <p>Please enter a new password for your account.</p>
          
          <form action="reset" method="post">
            <div>
              <input readonly type="text" value="<?php echo param('username') ?>" id="username" name="username" class="clicked">
              <label for="username">Username</label>
            </div>
            <div>
              <input type="password" id="newpassword" name="newpassword" required>
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
    
    
    <?php // SIGN UP ?>
    <div id="modal-signup" class="modal">
      
      <div class="modal-title">
        <h2>Sign up</h2>
        <button type="button" aria-label="close" data-action="close"></button>
      </div>
      
      <div class="modal-content">
                
        <form id="signup" action="<?php echo $page->url() . '/signup' ?>" method="post">
          
            <div role="group">
              <h3>Basic info</h3>
              <div class="size-50">
                <input type="text" name="firstname" pattern="^[^0-9]{2,20}$" required> <?php // No numbers, at least 2 characters ?>
                <label for="firstname">First name</label>
                <div class="icons"><?php echo (new Asset('/site/assets/images/form-user.svg'))->content() ?></div>
              </div>
        
              <div class="size-50">
                <input type="text" name="lastname" pattern="^[^0-9]{2,20}$" required> <?php // No numbers, at least 2 characters ?>
                <label for="lastname">Last name</label>
              </div>
              
              <div class="size-50">
                <input type="email" name="email" pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-z]{2,3}$" required> <?php // (whatever)@xx.co ?>
                <label for="email">Personal email address</label>
                <div class="icons"><?php echo (new Asset('/site/assets/images/form-email.svg'))->content() ?></div>
                <span>Used to reset your password</span>
              </div>
              
              <div class="size-50">
                <select name="color" class="neverclicked" id="signup-color" required>
                  <?php foreach ($site->content()->coloroptions()->split(',') as $option): ?>
                    <?php echo '<option value="' . $option . '">' . ucfirst($option) . '</option>'; ?>
                  <?php endforeach ?>
                </select>
                <label for="color">Favorite color</label>
                <div class="icons"></div>
              </div>
            </div>
            
            <div role="group">
              <h3>Account info</h3>
              
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
            
        </form>
      </div>
      
      <div class="modal-options">
        <button type="button" data-action="close" class="button fullwidth silver">Never mind</button>
        <button type="submit" data-action="signup" class="button fullwidth green" form="signup">Sign up</button>
      </div>
      
    </div>
    
    <?php // CREATE NEW SITE ?>
    <div id="modal-newsite" class="modal">
      
      <div class="modal-title">
        <h2>Create a new Network</h2>
        <button type="button" aria-label="close" data-action="close"></button>
      </div>
      
      <div class="modal-content">
        
        <span>Choose an address for your new site.</span><br><br>
        
        <span>https://tufts.makernetwork.org/<span id="newsitesubdomain">address</span></span>
        
        <form id="newsite">
            
            <div role="group">
              
              
              
              <div class="size-100">
                <input type="text" name="desiredname" pattern="^[a-z]{3,20}$" id="newsitefield" required>
                <label for="username" id="newsitelabel">Address<span id="newsitemessage"></span></label>
              </div>
              
            </div>
            
        </form>
      </div>
      
      <div class="modal-options">
        <button type="button" data-action="close" class="button fullwidth silver">Never mind</button>
        <button type="" data-action="newsite" class="button fullwidth green">Create new site</button>
      </div>
      
    </div>
    
    

  </div>
  
  <div id="modal-backdrop"></div>
  
</div>









