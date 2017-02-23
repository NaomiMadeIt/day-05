<?php
  error_reporting( E_ALL & ~E_NOTICE );

  //useful function for highlighting field with errors
  //$field is a string that should match the name of the field input
  //$array is an array of error messages
  function error_highlight( $field, $array ){
    if( isset($array) ){
      if( array_key_exists('name', $array) ) {
        echo 'class="error"';
      }
    }
  } // end error_hightlight()

  //parse the form if the user submitted it
  if( $_POST['did_send'] ){
    //extract all the data that was typed in
    //sanitize all data
    $name           = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email          = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone          = filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT);
    $reason         = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);
    $message        = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    $newsletter     = filter_var($_POST['newsletter'], FILTER_SANITIZE_STRING);

    //validate each field
    $valid = true;

    //test for empty name field
    if($name == ''){
      $valid = false;
      $errors['name'] = 'The name field is required.';
    }
    //test if email is blank or invalid format
    if( ! filter_var($email, FILTER_VALIDATE_EMAIL) ){
      $valid = false;
      $errors['email'] = 'Please provide a valid email address.';
    }

    //check to see if the reason given is NOT on the list of allowed reasons
    $allowed_reasons = array( 'help', 'hi', 'bug');
    if( ! in_array($reason, $allowed_reasons) ){
      $valid = false;
      $errors['reason'] = 'Please choose one of the reasons from the list.';
    }

    //make sure the checkbox can only result in true or false
    if( $newsletter != true){
      $newsletter = false;
    }

    if( $valid ){
      //send the mail!
      $to = 'naomi.k.rodriguez@gmail.com, bluehorneddemon@gmail.com';
      $subject = 'A contact form submission form' . $name;

      $body = "Email Address: $email\n";
      $body .= "Phone Number: $phone\n";
      $body .= "Reason for contact me: $reason\n";
      $body .= "Subscripe to newsletter? $newsletter\n\n";
      $body .= "$message";

      $headers = "From: naomi.k.rodriguez@gmail.com\r\n";
      $headers .= "Reply-to: $email\r\n";
      $headers .= "Bcc: e.g.cartas@gmail.com";

      $mail_sent = mail($to, $subject, $body, $headers);
    } //end if is valid

    //give the user feedback
    if($mail_sent){
      $class = 'success';
      $feedback = 'Thank you for contacting me, I will get back to you shortly.';
    }else{
      $class = 'error';
      $feedback = 'Something went wrong, your message could not be sent.';
    }

  } //end parser
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Contact Me</title>
    <link href="https://fonts.googleapis.com/css?family=Chewy" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <h1>Contact Me</h1>

    <?php
      if( isset($feedback) ){
        echo '<div class="feedback ' . $class . '">';
        echo $feedback;

        //preview what the body of the email will look like
        // echo '<pre>';
        // echo $body;
        // echo '</pre>';

        //if there are errors, show them
        if( ! empty($errors) ){
          // print_r($errors);
          echo '<ul>';
          foreach( $errors as $error ){
            echo '<li>';
            echo $error;
            echo '</li>';
          }
          echo '</ul>';
        }

        echo '</div>';
      }
    ?>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" novalidate>
      <label for="the_name">Your Name</label>
      <input type="text" name="name" id="the_name" value="<?php echo $name; ?>" <?php error_highlight( 'name', $errors ); ?> />

      <label for="the_email">Email Address</label>
      <input type="email" name="email" id="the_email" value="<?php echo $email; ?>" <?php error_highlight( 'name', $errors ); ?> />

      <label for="the_phone">Phone Number</label>
      <input type="tel" name="phone" id="the_phone" value="<?php echo $phone; ?>" />

      <label for="the_reason">How can I help You?</label>
      <select name="reason" id="the_reason" <?php error_highlight( 'name', $errors ); ?> >
        <option selected>Choose One</option>
        <option value="help" <?php if( $reason == 'help'){ echo 'selected'; } ?>>I need help</option>
        <option value="hi" <?php if( $reason == 'hi'){ echo 'selected'; } ?>>I just wanted to say "Hi!"</option>
        <option value="bug" <?php if( $reason == 'bug'){ echo 'selected'; } ?>>I found a bug in your code</option>
      </select>

      <label for="the_message">Message</label>
      <textarea name="message" id="the_message"><?php echo $message; ?></textarea>

      <!-- The label below doesn't need a for tag because what it's inbedded inside of it automatically links it -->
      <label>
        <input type="checkbox" name="newsletter" value="true" <?php if( $newsletter ) { echo 'checked'; } ?> />
        Yes! Sign me up for the awesome newsletter! I don't get enough emails, honestly.
      </label>

      <input type="submit" value="Send Message" />
      <input type="hidden" name="did_send" value="true" />

    </form>


  </body>
</html>
