<?php
/*
Template Name: User - Sign Out
*/
$redirect = get_bloginfo('url');

if ( is_user_logged_in() ) {
  // wp_logout_url() is encoding & as &amp;, which is breaking nonce verification and redirects.
  // This is a recreation which takes the user to the login page, displaying a message that they were logged out.
  $args = array(
    '_wpnonce' => wp_create_nonce('log-out'),
    'action' => 'logout',
    'redirect_to' => add_query_arg( array("loggedout" => "true"), wp_login_url() ),
  );

  $redirect = add_query_arg( $args, site_url('wp-login.php', 'login') );
}else{
  // User is not logged in. We will redirect to the home page
  if ( is_front_page() ) {
    // if we are ont he home page that would be an infinite loop.
    echo 'The front page is a sign out screen? This would give an infinite loop. Please change the front page.';
    exit;
  }
}

wp_redirect( $redirect );
exit;