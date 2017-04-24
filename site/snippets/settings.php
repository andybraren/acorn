<?php // SETTINGS ?>

<?php /*

Note to self: this will become a giant mess of code. Should create a new "formfield" snippet or something.

(formfield: text label: Email address)

(formfield: options label: Turn on ads)

*/ ?>


<form id="sitesettings" action="<?php echo $page->url() . '/savesettings' ?>" method="post">
  
  <?php // Style ?>
  <div role="group">
    <h2>Style</h2>
    
    <div>
      <?php $class = ($page->color() != '') ? "hasbeenclicked clicked" : "neverclicked"; ?>
      <select name="color" id="color" class="<?php echo $class ?>">
        <?php foreach ($site->content()->coloroptions()->split(',') as $color): ?>
          <?php $selected = ($color == $page->color()) ? "selected " : ""; ?>
          <?php echo '<option ' . $selected . 'value="' . $color . '">' . ucfirst($color) . '</option>' ?>
        <?php endforeach ?>
      </select>
      <label for="color">Default Site Color</label>
    </div>
    
    <h3>Logo</h3>
    <div>
      <label><input type="file" name="logo-logo">Logo</label>
      <label><input type="file" name="logo-icon">Logo Icon</label>
      <label><input type="file" name="logo-favicon">Favicon</label>
    </div>
    
    <h3>Header style</h3>
    <div>
      <label><input type="radio" name="style-header" value="one">One line</label>
      <label><input type="radio" name="style-header" value="two">Two lines</label>
        <label><input type="radio" name="style-header-2" value="two-centered">Logo centered</label>
        <label><input type="radio" name="style-header-2" value="two-left">Logo left (for ad spot)</label>
    </div>
    
    <h3>Text style</h3>
    <h4>Bullet style</h4>
    <div>
      <label><input type="radio" name="style-bullet" value="dot">Dot</label>
      <label><input type="radio" name="style-bullet" value="dash">Dash</label>
    </div>
    
  </div>
  
  <?php // Podcasting ?>
  <div role="group">
    <h2>Podcast</h2>
    
    <h3>Feed Details</h3>
    <div>
      <div><input type="text" name="podcast-title"><label>Title</label></div>
      <div><input type="text" name="podcast-owner"><label>Owner</label></div>
      <div><input type="text" name="podcast-description"><label>Description</label></div>
      <div><input type="text" name="podcast-link"><label>Link (automatic)</label></div>
      <div><input type="text" name="podcast-link"><label>iTunes Link (automatic)</label></div>
      
      <div>
        <select name="podcast-language">
          <option value="en">English</option>
          <option value="sp">Spanish</option>
        </select>
        <label>Language</label>
      </div>
      
      <label><input type="checkbox" name="podcast-explicit">Explicit</label>
      
      <h4>Categories</h4>
      <label><input type="checkbox" name="podcast-category">Technology</label>
      <label><input type="checkbox" name="podcast-category">Business</label>
      <label><input type="checkbox" name="podcast-category">Comedy</label>
            
    </div>
  </div>
  
  
  <div role="group">
    <h2>Page Defaults</h2>
    <div>
      <label><input type="checkbox" name="defaults-suggest-edit">Enable "suggest an edit"</label>
      <label><input type="checkbox" name="defaults-comments">Enable comments by default</label>
      <label><input type="checkbox" name="defaults-comments-anonymous">Enable anonymous comments by default</label>
    </div>
  </div>
  
  
  <div role="group">
    <h2>Users</h2>
    
    <?php // Select roles ?>
    <?php // Select default role ?>
    
    <div>
      <label><input type="checkbox" name="users-passwordless">Enable passwordless login</label>
      <label><input type="checkbox" name="users-twitter">Enable login with Twitter</label>
      <label><input type="checkbox" name="users-google">Enable login with Google</label>
      <label><input type="checkbox" name="users-facebook">Enable login with Facebook</label>
    </div>
  </div>
  
  <div role="group">
    <h2>Advertising and Monetization</h2>
    
    <div>
      <label><input type="checkbox" name="ads-enable">Turn on ads</label>
    </div>
    
    <h3>Providers</h3>
    <div>
      
      <h4>Ads</h4>
      <label><input type="checkbox" name="ads-self">Enable self-hosted ads</label>
        <div><input type="text" name="ads-self-location"><label>Ad board located at (automatic)</label></div>
      <label><input type="checkbox" name="ads-google">Enable Google AdSense</label>
        <div><input type="text" name="ads-google-key"><label>AdSense Key</label></div>
      <label><input type="checkbox" name="ads-bsa">Enable BuySellAds</label>
        <div><input type="text" name="ads-bsa-key"><label>BuySellAds Key</label></div>
      <label><input type="checkbox" name="ads-amazon">Enable Amazon ads</label>
        <div><input type="text" name="ads-amazon-key"><label>Amazon Ads Key</label></div>
      
      <h4>Affiliate</h4>
      <label><input type="checkbox" name="affiliate-amazon">Enable Amazon Affiliate</label>
        <div><input type="text" name="affiliate-amazon-key"><label>Amazon Affiliate Key</label></div>
      <label><input type="checkbox" name="affiliate-newegg">Enable Newegg Affiliate</label>
        <div><input type="text" name="affiliate-newegg-key"><label>Newegg Affiliate Key</label></div>
      <label><input type="checkbox" name="affiliate-skimlinks">Enable Skimlinks</label>
        <div><input type="text" name="affiliate-skimlinks-key"><label>Skimlinks Key</label></div>
        
    </div>
    
    <h3>Ad slots</h3>
    <div>
      <label><input type="checkbox" name="ads-header">Enable header ad</label>
        <div>
          <select name="ads-header-provider">
            <option value="adsense">AdSense</option>
            <option value="bsa">BuySellAds</option>
          </select>
          <label>Provider</label>
        </div>
      
      <label><input type="checkbox" name="ads-sidebar">Enable sidebar ads</label>
        <div>
          <select name="ads-sidebar-provider">
            <option value="adsense">AdSense</option>
            <option value="bsa">BuySellAds</option>
          </select>
          <label>Provider</label>
        </div>
        
        <?php // Display on certain page types ?>
      
      <label><input type="checkbox" name="ads-footer">Enable footer ad</label>
        <div>
          <select name="ads-footer-provider">
            <option value="adsense">AdSense</option>
            <option value="bsa">BuySellAds</option>
          </select>
          <label>Provider</label>
        </div>
      
      <label><input type="checkbox" name="ads-rss">Enable RSS ad</label>
        <div>
          <select name="ads-rss-provider">
            <option value="adsense">AdSense</option>
            <option value="bsa">BuySellAds</option>
          </select>
          <label>Provider</label>
        </div>
        
        <?php // Display on certain post types ?>
      
    </div>
    
  </div>
  
  
  <div role="group">
    <h2>Analytics</h2>
    
    <div>
      <label><input type="checkbox" name="analytics-enable">Turn on analytics</label>
      <label><input type="checkbox" name="analytics-google">Use Google Analytics tracking</label>
        <div><input type="text" name="analytics-google-key"><label>Google Analytics Key</label></div>
    </div>
    
  </div>
  
  <div role="group">
    <h2>Commerce</h2>
    
    <?php // Note: CSV export is always available, along with web-based history ?>
    
    <div>
      <label><input type="checkbox" name="commerce-enable">Enable commerce</label>
        
        <h3>Payment processors</h3>
        <label><input type="checkbox" name="commerce-stripe">Enable Stripe payments</label>
          <div><input type="text" name="commerce-stripe-key"><label>Stripe Key</label></div>
        <label><input type="checkbox" name="commerce-braintree">Enable Braintree payments</label>
          <div><input type="text" name="commerce-braintree-key"><label>Braintree Key</label></div>
        <label><input type="checkbox" name="commerce-authorize">Enable Authorize.net payments</label>
          <div><input type="text" name="commerce-authorize-key"><label>Authorize.net Key</label></div>
        
      <label><input type="checkbox" name="commerce-coupons">Enable site-wide coupons</label>
      <label><input type="checkbox" name="commerce-referrals">Enable referrals</label>
      
    </div>
    
  </div>
  
  <div role="group">
    <h2>Membership</h2>
    
    <?php // Note: to set members-only content, adjust each page's visibility and submission settings ?>
    
    <div>
      <label><input type="checkbox" name="membership-enable">Enable memberships</label>
        
        <h3>Tier 1</h3>
        <div><input type="text" name="membership-1-nickname"><label>Name of tier</label></div>
        <div><input type="text" name="membership-1-price-yearly"><label>Yearly price</label></div>
          <label><input type="checkbox" name="membership-1-monthly">Allow monthly pricing</label>
          <div><input type="text" name="membership-1-price-monthly"><label>Monthly price</label></div>
        <h4>Tier 1 Perks</h4>
        <label><input type="checkbox" name="membership-1-perk-analytics">Disable all analytics for members</label>
        <label><input type="checkbox" name="membership-1-perk-affiliate">Disable all affiliate links</label>
        
      <?php // Add another tier ?>
      
    </div>
    
  </div>
  
  <div role="group">
    <h2>Import / Export</h2>
        
    <div>
      <label><input type="checkbox" name="connect-twitter">Connect Twitter</label>
        <?php // Import from Twitter ?>
        <label><input type="checkbox" name="twitter-auto-import">Turn on auto import</label>
        <label><input type="checkbox" name="twitter-auto-export">Turn on auto tweet</label>
        
      <label><input type="checkbox" name="connect-medium">Connect Medium</label>
        <?php // Import from Medium ?>
        <label><input type="checkbox" name="medium-auto-export">Turn on auto import</label>
        <label><input type="checkbox" name="medium-auto-export">Turn on auto post</label>
        
      <label><input type="checkbox" name="connect-tumblr">Connect Tumblr</label>
        <?php // Import from Tumblr ?>
        <label><input type="checkbox" name="tumblr-auto-import">Turn on auto import</label>
        <label><input type="checkbox" name="tumblr-auto-export">Turn on auto post</label>
        
      <label><input type="checkbox" name="connect-wordpress">Connect WordPress</label>
        <?php // Import from WordPress ?>
        <label><input type="checkbox" name="wordpress-auto-import">Turn on auto import</label>
        <label><input type="checkbox" name="wordpress-auto-export">Turn on auto post</label>
      
      <h3>Site export</h3>
      <span>Download entire site as HTML</span><br>
      <span>Download entire site as a new Acorn site</span>
        
    </div>
    
  </div>
  
  <div role="group">
    <h2>Advanced</h2>
        
    <div>
      <label><input type="checkbox" name="cdn-enable">Turn on CDN</label>
        <?php // This will require an increase in monthly payments ?>
        <div>
          <select name="cdn-provider">
            <option value="keycdn">KeyCDN</option>
            <option value="cloudflare">CloudFlare</option>
          </select>
          <label>Provider</label>
        </div>
        
    </div>
    
  </div>
  
  
</form>

















