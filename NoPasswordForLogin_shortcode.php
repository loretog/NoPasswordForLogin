<?php if( isset( $_SESSION[ 'npfl_message' ] ) && !empty( $_SESSION[ 'npfl_message' ] ) ) echo $_SESSION[ 'npfl_message' ]; ?>
<form method="post">
  <?php wp_nonce_field('save_subscriber'); ?>
  <input type="hidden" name="NoPasswordForLogin" value="1">
  <input type="email" name="email" placeholder="Enter your email">
  <input type="submit" value="Register">
</form>
