<?php

// Use a custom template for the Membership Premium account management page, and the [accountform] shortcode
define( 'MEMBERSHIP_ACCOUNT_FORM', dirname(__FILE__) . '/membership/accountform.php' );

/*
 * Gets the menu location key based on whether the user is logged in or not, or another condition.
 *
 * Menu locations:
 *   1. "primary" - Main menu at the top of the page. Replaces "avia"
 *   2. "secondary" - Displayed at the top of the page (I assume). Replaces "avia2"
 *   3. "footer" - Displayed in the footer. Replaces "avia3"
 */
function caseswap_menu_location( $location ) {
  if ( is_user_logged_in() ) return $location . '-logged_in';
  else return $location . '-logged_out';
}

/*
 * Add custom, descriptive menus with a logged in and logged out variation.
 *
 * And unregister the Enfold default menus (avia, avia2, avia3), as they have been replaced.
 */
function caseswap_register_menus() {
  register_nav_menus(array(
    'primary-logged_out' => 'Primary (Logged Out)', // replaces "avia"
    'primary-logged_in' => 'Primary (Logged In)',

    'secondary-logged_out' => 'Secondary (Logged Out)', // replaces "avia2"
    'secondary-logged_in' => 'Secondary (Logged In)',

    'footer-logged_out' => 'Footer (Logged Out)', // replaces "avia3"
    'footer-logged_in' => 'Footer (Logged In)',
  ));

  unregister_nav_menu('avia');
  unregister_nav_menu('avia2');
  unregister_nav_menu('avia3');
}
add_action( 'after_setup_theme', 'caseswap_register_menus', 15 );