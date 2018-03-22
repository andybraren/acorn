<?php
  
  // Specify the price in cents. Accounts for decimal places if they're included.
  $amount = (float)((string)$page->price()) * 100;
    
  if (site()->setting('commerce/stripe/testmode') == true) {
    $stripekey_public = site()->setting('commerce/stripe/pk-test');
  } else {
    $stripekey_public = site()->setting('commerce/stripe/pk-live');
  }
    
?>

<div class="widget">
  <span class="heading">PURCHASE</span>
  <div id="payment-request-button" data-amount="<?= $amount ?>" data-pkey="<?= $stripekey_public ?>"></div>
</div>

<?php
  /*
  initiateStripe();
  
  // Set some variables
  $currency = c::get('stripe_currency');
  $displayAmount = $page->price();
  
  if ($page->price() == null) {
    $amount = c::get('stripe_default_amount');
  } else {
    $amount = str_replace('.', '', $page->price());
    $amount = str_replace(',', '', $amount);
  }
  $checkoutName = $site->title();
  $checkoutDescription = ($page->priceDescription()) ? $page->priceDescription() : c::get('stripe_default_description');
  
  // Check if an icon has been set. 
  $logo = (c::get('stripe_icon')) ? c::get('stripe_icon_location') : null;
  $logo = site()->images()->findBy('name', 'logo-apple-touch')->url();
  
  // Check if "Remember Me" has been enabled
  $rememberMe = (c::get('stripe_remember_me')) ? 'data-allow-remember-me="false"' : null;
  
  // Process the charge
  if (isset($_POST['stripeToken'])) {
    stripeCheckout();
    return;
  }
  */
?>