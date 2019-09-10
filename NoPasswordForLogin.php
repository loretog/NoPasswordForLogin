<?php
   /*
   Plugin Name: No Password For Login
   Plugin URI: http://tagapagkodigo.com
   description: Allows user to signup using an email and doesn't require a password. Only selected pages/posts will be accessed by these users.
   Version: 1.0
   Author: Loreto Gabawa Jr.
   Author URI: http://tagapagkodigo.com
   License: GPL2
   */

class NoPasswordForLogin
{
  protected $pluginPath;
  protected $pluginUrl;

  public function __construct() {
    $this->pluginPath = dirname(__FILE__);
    $this->pluginUrl = WP_PLUGIN_URL . '/NoPasswordForLogin';

    add_shortcode('npfl_signup', [ $this, 'signup_form' ] );
    add_action( 'wp_loaded', [ $this, 'form_submit' ] );
  }
  public function signup_form() {    
    ob_start();
    include dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'NoPasswordForLogin_shortcode.php';
    return ob_get_clean();
  }
  public function form_submit() {
    if ( !is_admin() ) {
      $retrieved_nonce = $_REQUEST['_wpnonce'];
      $user_email = $_POST[ 'email' ];
      $red_url = $_POST[ 'npfl_redirect' ];

      if( isset( $_POST[ 'npfl_signup' ] ) && wp_verify_nonce($retrieved_nonce, 'save_subscriber' ) ) {
        $user_name = $user_email;
        $password = md5( $user_email );
        if( is_email( $user_email ) ) {
          if( email_exists($user_email) ) {
            // create user                        
            $user = get_user_by( 'email', $user_email );
            wp_set_password( $password, $user->id );
          } else {
            $user_id = wp_create_user( $user_name, $password, $user_email );
          }
          // login directly
          wp_signon( [ 'user_login' => $user_email, 'user_password' => $password, 'remember' => true ] );
          wp_redirect( $red_url );
          exit;
        } else {
          $_SESSION[ 'npfl_message' ] = "Invalid email.";
        }        
      }        
   }
 }
}

$nopasswordforlogin = new NoPasswordForLogin();
?>
