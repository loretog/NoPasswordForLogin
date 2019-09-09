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
   protected $option_name;
   protected $table_name = 'npfl_users';
   public function __construct() {

     $this->pluginPath = dirname(__FILE__);
     $this->pluginUrl = WP_PLUGIN_URL . '/NoPasswordForLogin';
     $this->option_name = 'npfl_allowed_pages' ;

     register_activation_hook( __FILE__, [ $this, 'plugin_activate' ] );

     add_action( 'admin_menu', function() {
       add_options_page( 'NoPasswordForLogin', 'No Password For Login', 'manage_options', 'no-password-for-login', [ $this, 'admin_options_page' ] );
     });

     add_shortcode('NoPasswordForLogin', [ $this, 'shortcode' ] );
     add_action( 'wp_loaded', [ $this, 'form_submit' ] );

     add_action( 'wp_loaded', [ $this, 'check_and_redirect' ] );
   }
   public function plugin_activate() {
    global $wpdb;
    $npfl_db_version = "1.0";

    $table_name = $wpdb->prefix . $this->table_name;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    email varchar(50) NOT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'npfl_db_version', $npfl_db_version );

   }
   function admin_options_page() {
     if ( !current_user_can( 'manage_options' ) )  {
       wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
     }

     if( isset( $_POST[ 'npfl_form_update_pages' ] ) ) {
       $new_value = $_POST[ 'page_id' ];
       if ( get_option( $this->option_name ) !== false ) {
           update_option( $this->option_name, $new_value );
       } else {
           $deprecated = null;
           $autoload = 'no';
           add_option( $this->option_name, $new_value, $deprecated, $autoload );
       }
     }
     $page_id = get_option( $this->option_name );
     include dirname( __FILE__ ) . '\NoPasswordForLogin_options.php';
   }
   public function shortcode( $atts ) {
     ob_start();
     include dirname( __FILE__ ) . '\NoPasswordForLogin_shortcode.php';
     return ob_get_clean();
   }

   public function form_submit() {
     if ( !is_admin() ) {
       $retrieved_nonce = $_REQUEST['_wpnonce'];
       $email = $_POST[ 'email' ];
       if( isset( $_POST[ 'NoPasswordForLogin' ] ) && wp_verify_nonce($retrieved_nonce, 'save_subscriber' ) ) {
          if( is_email( $email ) ) {
            global $wpdb;
            if( ! $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE email = '$email' ") ) {
              $wpdb->insert( $wpdb->prefix . $this->table_name, array(
                'email' => $email
              ));
            }
            setcookie( "npfl_user", $email, time() + (86400 * 30 * 30), "/");
            $this->redirect_to_page();
          } else {
            $_SESSION[ 'npfl_message' ] = "Invalid email";
          }
       }
     }
   }
   public function redirect_to_page() {
     //$page_id = get_option( $this->option_name );
     $redirect_uri = add_query_arg ('npfl_redirect', '1', get_permalink ( get_option( $this->option_name ) ));
     wp_redirect( $redirect_uri ); exit;
   }
   public function check_and_redirect() {
     if ( !is_admin() ) {
       //$current_url = home_url(add_query_arg(array($_GET), $wp->request));
       //$redirect_url = get_permalink( get_option( $this->option_name ) );


       //echo $current_url . " - " . $redirect_url; exit;
       if( isset( $_COOKIE[ 'npfl_user' ] ) && !empty( $_COOKIE[ 'npfl_user' ] ) ) {
         if( !isset( $_GET[ 'npfl_redirect' ] ) ) {
           $this->redirect_to_page();
         }
       }
     }
   }
 }

 $nopasswordforlogin = new NoPasswordForLogin();
?>
