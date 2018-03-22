<?php
  require_once('sck.inc.php');

  /* 
  Setting variables using POST and session data to create the charge and pass to Stripe to process.
  */

  $token                            = $_POST['stripeToken'];
  $email                            = $_POST['stripeEmail'];
  $billingName                      = $_POST['stripeBillingName'];
  $billingZip                       = $_POST['stripeBillingAddressZip'];
  $email                            = $_POST['stripeEmail'];
  $stripeShippingName               = $_POST['stripeShippingName'];
  $stripeShippingAddressLine1       = $_POST['stripeShippingAddressLine1'];
  $stripeShippingAddressZip         = $_POST['stripeShippingAddressZip'];
  $stripeShippingAddressState       = $_POST['stripeShippingAddressState'];
  $stripeShippingAddressCity        = $_POST['stripeShippingAddressCity'];
  $stripeShippingAddressCountry     = $_POST['stripeShippingAddressCountry'];
  $amount                           = s::get('stripeAmount');
  $description                      = s::get('stripeDescription');
  $currency                         = c::get('stripe_currency');

  $charge = \Stripe\Charge::create(array(
      'amount'                      => $amount,
      'source'                      => $token,
      'currency'                    => $currency,
      'receipt_email'               => $email,
      'description'                 => $description,
      'metadata'                    => array('customer_name'      => $billingName,
                                             'customer_email'     => $email,
                                             'shipping_name'      => $stripeShippingName,
                                             'shipping_street'    => $stripeShippingAddressLine1,
                                             'shipping_city'      => $stripeShippingAddressCity,
                                             'shipping_state'     => $stripeShippingAddressState,
                                             'shipping_zip'       => $stripeShippingAddressZip,
                                             'shipping_country'   => $stripeShippingAddressCountry)
  ));

  if (c::get('stripe_redirect_on_success')) {
    header('Location: ' . c::get('stripe_redirect_to_page'));
  } else {
      echo c::get('stripe_confirmation_heading');
      echo c::get('stripe_confirmation_message');
  }
  
?>