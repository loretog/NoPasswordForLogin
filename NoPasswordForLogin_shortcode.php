<?php if( isset( $_SESSION[ 'npfl_message' ] ) && !empty( $_SESSION[ 'npfl_message' ] ) ) echo $_SESSION[ 'npfl_message' ]; ?>
<form method="post">
  <?php wp_nonce_field('save_subscriber'); ?>
  <input type="hidden" name="npfl_signup" value="1">
  <input type="hidden" name="npfl_redirect" value="<?php echo get_permalink() ?>?confirm=<?php echo md5( time() ) ?>">
  <input type="email" name="email" placeholder="Enter your email" required>
  <input type="submit" value="Register">
</form>
