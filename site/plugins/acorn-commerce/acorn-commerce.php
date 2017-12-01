<?php



// Register the checkout form snippet
$kirby->set('snippet', 'checkout', __DIR__ . '/snippets/checkout.php');



function initiateStripe() {
  
  if (site()->setting('commerce/stripe/testmode') == true) {
    $stripekey_secret = site()->setting('commerce/stripe/sk-test');
    $stripekey_public = site()->setting('commerce/stripe/pk-test');
  } else {
    $stripekey_secret = site()->setting('commerce/stripe/sk-live');
    $stripekey_public = site()->setting('commerce/stripe/pk-live');
  }
  
  $stripe = array(
    "secret_key"      => $stripekey_secret,
    "publishable_key" => $stripekey_public,
  );
  
  require_once(kirby()->roots()->plugins() . '/acorn-commerce/vendor/stripe-php/init.php'); // need to find the right place
      
  \Stripe\Stripe::setApiKey($stripe['secret_key']); // Set secret key
}


// Stripe testing
$kirby->set('route', array(
  'pattern' => 'stripecheckoutcharge',
  'method' => 'POST',
  'action'  => function() {
    
    initiateStripe();
    
    $token = $_POST['stripeToken']; // Get the form's Stripe token
    $email = $_POST['stripeEmail']; // Get the form's email
    
    try { // Charge the card
      
      $customer = \Stripe\Customer::create(array(
        'email' => $email,
        'source'  => $token
      ));
      
      $charge = \Stripe\Charge::create(array(
        'customer' => $customer->id,
        "amount" => 1000, // Amount in cents
        "currency" => "usd",
        "description" => "Example charge"
      ));
      
      echo "Success";
      
    } catch(\Stripe\Error\Card $e) {
      // The card has been declined
      
      echo "Failure";
    }
    
  }
));

// Stripe testing
$kirby->set('route', array(
  'pattern' => 'stripecharge',
  'method' => 'POST',
  'action'  => function() {
    
    initiateStripe();
        
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body, true);
    $token = $data['token']; // Get the Stripe token passed from Stripe.js
    
    try { // Charge the card
      
      $charge = \Stripe\Charge::create(array(
        "amount" => 100,
        "currency" => "usd",
        "description" => "Example charge",
        "statement_descriptor" => "Custom descriptor",
        "source" => $token,
      ));
      
      echo "Success";
      
    } catch(\Stripe\Error\Card $e) {
      // The card has been declined
      
      echo "Failure";
    }
    
  }
));