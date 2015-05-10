<?php

$msg = '';
$success = null;
$user_id = get_current_user_id();

if ( isset( $_POST['action'] ) && $_POST['action'] == 'update' ) {
  $success = false;

  if ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'update-user_' . $user_id ) ) {
    $msg = '<div class="alert alert-success">' . __( 'Your details have been updated.', 'membership' ) . '</div>';

    if ( !empty( $_POST['pass1'] ) && $_POST['pass1'] != $_POST['pass2'] ) {
      $msg = '<div class="alert alert-error">' . __( 'Your password settings do not match', 'membership' ) . "</div>";
    } else {
      $errors = edit_user( $user_id );
      if ( isset( $errors ) && is_wp_error( $errors ) ) {
        $msg = '<div class="alert alert-error">' . implode( '<br>', $errors->get_error_messages() ) . '</div>';
      }else{
        $success = true;
      }
    }
  } else {
    $msg = '<div class="alert alert-error">' . __( 'Your details could not be updated.', 'membership' ) . '</div>';
  }

  do_action( 'edit_user_profile_update', $user_id );
}

$profileuser = get_user_to_edit( $user_id );

// Save CaseSwap data if submit successful
$submit_state = isset($_REQUEST['cs_user']['state'])              ? (array) stripslashes_deep($_REQUEST['cs_user']['state'])              : null;
$submit_types = isset($_REQUEST['cs_user']['investigator-types']) ? (array) stripslashes_deep($_REQUEST['cs_user']['investigator-types']) : null;


if ( $success ) {
  if ( $submit_state !== null ) {
    delete_user_meta( $profileuser->ID, 'state' );
    foreach( $submit_state as $val ) {
      add_user_meta( $profileuser->ID, 'state', $val );
    }
  }

  if ( $submit_types !== null ) {
    delete_user_meta( $profileuser->ID, 'investigator-types' );
    foreach( $submit_types as $val ) {
      add_user_meta( $profileuser->ID, 'investigator-types', $val );
    }
  }
}

// Get CaseSwap metadata to display in form
global $CSCore;
$options = $CSCore->Options->get_options();

$all_states = $options['states'];
$all_types = $options['investigator-types'];

$state = get_user_meta( $profileuser->ID, 'state', false ); // Get preferred states
$types = get_user_meta( $profileuser->ID, 'investigator-types', false );

if ( $success === false ) {
  // Failed to save data, keep the settings that the user tried to save so they can try again
  if ( $submit_state !== null ) $state = $submit_state;
  if ( $submit_types !== null ) $types = $submit_types;
}


?><div id="membership-wrapper">
  <?php echo $msg ?>
  <form class="form-membership" action="<?php echo get_permalink(); ?>" method="post">
    <?php wp_nonce_field( 'update-user_' . $user_id ) ?>
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $user_id ); ?>">
    <?php if ( filter_var( get_user_meta( $user_id, 'show_admin_bar_front', true ), FILTER_VALIDATE_BOOLEAN ) ) : ?>
      <input type="hidden" name="admin_bar_front" value="1">
    <?php endif; ?>
    <input type="hidden" name="rich_editing" value="<?php echo esc_attr( get_user_meta( $user_id, 'rich_editing', true ) ) ?>">
    <input type="hidden" name="admin_color" value="<?php echo esc_attr( get_user_meta( $user_id, 'admin_color', true ) ) ?>">
    <input type="hidden" name="comment_shortcuts" value="<?php echo esc_attr( get_user_meta( $user_id, 'comment_shortcuts', true ) ) ?>">

    <fieldset>
      <legend><?php _e( 'Edit your details', 'membership' ) ?></legend>

      <?php
      /*
      <div class="form-element">
        <label class="control-label" for="user_login"><?php _e( 'Username', 'membership' ); ?></label>
        <div class="element plaintext-element">
          <input type="text" id="user_login" nmae="user_login" class="plaintext-input" placeholder="" value="<?php echo esc_attr( $profileuser->user_login ); ?>" disabled="disabled" >
        </div>
      </div>
      */
      ?>

      <div class="form-element">
        <label class="control-label" for="email"><?php _e( 'Email', 'membership' ); ?></label>
        <div class="element">
          <input type="text" class="input-xlarge" name="email" id="email" value="<?php echo esc_attr( $profileuser->user_email ) ?>" />
        </div>
      </div>

      <div class="form-element">
        <label class="control-label" for="first_name"><?php _e( 'First Name', 'membership' ); ?></label>
        <div class="element">
          <input type="text" class="input-xlarge" id="first_name" name="first_name" placeholder="" value="<?php echo esc_attr( $profileuser->first_name ); ?>" >
        </div>
      </div>

      <div class="form-element">
        <label class="control-label" for="last_name"><?php _e( 'Last Name', 'membership' ); ?></label>
        <div class="element">
          <input type="text" class="input-xlarge" id="last_name" name="last_name" placeholder="" value="<?php echo esc_attr( $profileuser->last_name ) ?>" >
        </div>
      </div>

      <?php
      /*
			<div class="form-element">
				<label class="control-label" for="nickname"><?php _e( 'Nickname', 'membership' ); ?></label>
				<div class="element">
					<input type="text" class="input-xlarge" id="nickname" name="nickname" placeholder="" value="<?php echo esc_attr( $profileuser->nickname ) ?>" >
				</div>
			</div>
      */
      ?>

      <?php
      /*
			<div class="form-element">
				<label class="control-label" for="display_name"><?php _e( 'Display name as', 'membership' ); ?></label>
				<div class="element">
					<select name="display_name" id="display_name">
						<?php
						$public_display = array();
						$public_display['display_username'] = $profileuser->user_login;
						$public_display['display_nickname'] = $profileuser->nickname;
						if ( !empty( $profileuser->first_name ) )
							$public_display['display_firstname'] = $profileuser->first_name;
						if ( !empty( $profileuser->last_name ) )
							$public_display['display_lastname'] = $profileuser->last_name;
						if ( !empty( $profileuser->first_name ) && !empty( $profileuser->last_name ) ) {
							$public_display['display_firstlast'] = $profileuser->first_name . ' ' . $profileuser->last_name;
							$public_display['display_lastfirst'] = $profileuser->last_name . ' ' . $profileuser->first_name;
						}
						if ( !in_array( $profileuser->display_name, $public_display ) ) // Only add this if it isn't duplicated elsewhere
							$public_display = array( 'display_displayname' => $profileuser->display_name ) + $public_display;
						$public_display = array_map( 'trim', $public_display );
						$public_display = array_unique( $public_display );
						foreach ( $public_display as $id => $item ) {
							?>
							<option id="<?php echo $id; ?>" value="<?php echo esc_attr( $item ); ?>"<?php selected( $profileuser->display_name, $item ); ?>><?php echo $item; ?></option>
							<?php
						}
						?>
					</select>
				</div>
			</div>
      */
      ?>

      <?php
      /*
			<div class="form-element">
				<label class="control-label" for="url"><?php _e( 'Website', 'membership' ); ?></label>
				<div class="element">
					<input type="text" class="input-xlarge" name="url" id="url" value="<?php echo esc_attr( $profileuser->user_url ) ?>" />
				</div>
			</div>
      */
      ?>

      <div class="form-element">
        <p class="help-block"><?php _e( 'To change your password, enter the new password below and then repeat it to confirm, otherwise leave these two fields blank.', 'membership' ); ?></p>
      </div>

      <div class="form-element">
        <label class="control-label" for="pass1"><?php _e( 'New Password', 'membership' ); ?></label>
        <div class="element">
          <input type="password" class="input-xlarge" name="pass1" id="pass1" value="" autocomplete="off">
        </div>
      </div>

      <div class="form-element">
        <label class="control-label" for="pass1"><?php _e( 'Confirm Password', 'membership' ); ?></label>
        <div class="element">
          <input type="password" class="input-xlarge" name="pass2" id="pass2" value="" autocomplete="off">
        </div>
      </div>

      <h3>Investigator Preferences</h3>

      <div class="form-element">
        <label class="control-label" for="cs_user-state"><?php _e( 'State', 'caseswap' ); ?></label>
        <div class="element">
          <select name="cs_user[state][]" id="cs_user-state" multiple>
            <option value="">&ndash; Select &ndash;</option>
            <?php
            foreach( $all_states as $this_state ) {
              echo sprintf(
                '<option value="%s" %s>%s</option>',
                esc_attr( $this_state ),
                selected( in_array($this_state, $state), true, false ),
                esc_html( $this_state )
              );
            }
            ?>
          </select>
        </div>
      </div>

      <div class="form-element">
        <label class="control-label" for=""><?php _e( 'Investigation Types', 'caseswap' ); ?></label>
        <div class="element">
          <div class="cs_checkbox_list">
            <?php
            foreach( $all_types as $this_type ) {
              $html_id = 'cs-investigator-type-' . sanitize_title_with_dashes($this_type);

              echo sprintf(
                '<div class="cs_cb_item"><label for="%s"><input type="checkbox" name="cs_user[investigator-types][]" id="%s" value="%s" %s> %s</label></div>',
                esc_attr($html_id),
                esc_attr($html_id),
                esc_attr( $this_type ),
                checked( in_array( $this_type, $types ), true, false ),
                esc_html( $this_type )
              );
            }
            ?>
          </div>
        </div>
      </div>

      <p><input type="submit" value="<?php _e( 'Update Account', 'membership' ); ?>" class="alignright button button-primary <?php echo apply_filters( 'membership_account_button_color', '' ); ?>" name="submit"></p>
    </fieldset>
  </form>
</div>