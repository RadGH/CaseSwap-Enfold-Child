<?php

// Customize the header configuration used by Enfold.
// This is primarily to use a different header menu when the user is logged in.
function caseswap_customize_header( $header ) {
  echo '<pre>';
  var_dump( $header );
  echo '</pre>';
  exit;
}
// add_filter( 'avf_header_setting_filter', 'caseswap_customize_header' );