<?php
/*
Template Name: _register
*/

define("DONOTCACHEPAGE", true);

if (is_user_logged_in()) { wp_redirect(home_url()); exit; }

if ('POST' == $_SERVER['REQUEST_METHOD'] && !wp_verify_nonce($_POST['nonce'], 'register')) { die(); }

if (!get_option('users_can_register')) {
	wp_redirect(home_url('/login/?registration=disabled'));
	exit;
}

if (of_get_option('captcha_public') != '' && of_get_option('captcha_private') != '')
	require_once(get_template_directory() . '/recaptchalib.php');

$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
$user_login = '';
$user_email = '';

if ($http_post) {
	if (isset($_GET['action']) && $_GET['action'] == 'resend') {
		if (empty($_POST['user_email'])) {
			$resend_status = __('<strong>ERROR</strong>: Enter email address.', 'pinc');
		} else {
			if (of_get_option('captcha_public') != '' && of_get_option('captcha_private') != '') {
				$privatekey = of_get_option('captcha_private');
				$reCaptcha = new ReCaptcha($privatekey);
		
				if ($_POST["g-recaptcha-response"]) {
					$resp = $reCaptcha->verifyResponse(
						$_SERVER["REMOTE_ADDR"],
						$_POST["g-recaptcha-response"]
					);
				}
				
				if (!$resp->success) {
					$resend_status = __('<strong>ERROR</strong>: Incorrect Captcha.', 'pinc');
				} else {
					$user = get_user_by('email', sanitize_email($_POST['user_email']));
		
					if ($user) {
						$verify_email = get_user_meta($user->ID, '_Verify Email', true);
						
						if ($verify_email != '') {
							$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
							
							$message  = sprintf(__('Thank you for registering with %s.', 'pinc'), $blogname) . "\r\n\r\n";
							$message .= sprintf(__('Username: %s', 'pinc'), $user->user_login) . "\r\n\r\n";
							$message .= __('Please click the link to verify your email:', 'pinc') . "\r\n";
							$message .= sprintf('%s?email=verify&login=%s&key=%s', home_url('/login/'), rawurlencode($user->user_login), $verify_email);
		
							wp_mail($user->user_email, sprintf(__('[%s] Account Registration', 'pinc'), $blogname), $message);
		
							$resend_status = 'success';
						} else {
							$resend_status = __('<strong>ERROR</strong>: Account is already activated.', 'pinc');
						}
					} else {
						$resend_status = __('<strong>ERROR</strong>: Email not found.', 'pinc');
					}	
				}
			} else {
				$user = get_user_by('email', sanitize_email($_POST['user_email']));
	
				if ($user) {
					$verify_email = get_user_meta($user->ID, '_Verify Email', true);
					
					if ($verify_email != '') {
						$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
						
						$message  = sprintf(__('Thank you for registering with %s.', 'pinc'), $blogname) . "\r\n\r\n";
						$message .= sprintf(__('Username: %s', 'pinc'), $user->user_login) . "\r\n\r\n";
						$message .= __('Please click the link to verify your email:', 'pinc') . "\r\n";
						$message .= sprintf('%s?email=verify&login=%s&key=%s', home_url('/login/'), rawurlencode($user->user_login), $verify_email);
	
						wp_mail($user->user_email, sprintf(__('[%s] Account Registration', 'pinc'), $blogname), $message);
	
						$resend_status = 'success';
					} else {
						$resend_status = __('<strong>ERROR</strong>: Account is already activated.', 'pinc');
					}
				} else {
					$resend_status = __('<strong>ERROR</strong>: Email not found.', 'pinc');
				}
			}
		}
	} else {
		$user_login = $_POST['user_login'];
		$user_email = $_POST['user_email'];
		$errors = pinc_register_new_user($user_login, $user_email);
		if (!is_wp_error($errors)) {
			$redirect_to = home_url('/login/?registration=done');
			wp_safe_redirect( $redirect_to );
			exit();
		}
	}
}

//function from wp-includes\user.php
function pinc_register_new_user( $user_login, $user_email ) {
	$errors = new WP_Error();

	$sanitized_user_login = sanitize_user( $user_login );
	/**
	 * Filter the email address of a user being registered.
	 *
	 * @since 2.1.0
	 *
	 * @param string $user_email The email address of the new user.
	 */
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.', 'pinc' ) );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', 'pinc' ) );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.', 'pinc' ) );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.', 'pinc' ) );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'pinc' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.', 'pinc' ) );
	}
	
	//edited: added to check the passwords
	if ( $_POST['pass1'] == '' ) {
		$errors = new WP_Error('password_blank', __('Password cannot be blank.', 'pinc', 'pinc'));
	}
	if ( strlen( $_POST['pass1'] ) < 6 ) {
	$errors->add('password_too_short', "<strong>ERROR</strong>: Passwords must be at least 6 characters long", 'pinc');
	}
	if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] ) {
		$errors = new WP_Error('password_reset_mismatch', __('The passwords do not match.', 'pinc'));
	}
	
	//edited: check if captcha is correct
	if (of_get_option('captcha_public') != '' && of_get_option('captcha_private') != '') {
		$privatekey = of_get_option('captcha_private');
		$reCaptcha = new ReCaptcha($privatekey);

		if ($_POST["g-recaptcha-response"]) {
			$resp = $reCaptcha->verifyResponse(
				$_SERVER["REMOTE_ADDR"],
				$_POST["g-recaptcha-response"]
			);
		}
		
		if (!$resp->success) {
			$errors = new WP_Error('incorrect_captcha', __('<strong>ERROR</strong>: Incorrect Captcha.', 'pinc'));
		}
	}
	
	//edited: check if is spam user
	if (trim($_POST['anti-spam']) != date('Y') || empty($_POST['anti-spam']) || !empty( $_POST['anti-spam-e-email-url'])) {
		$errors = new WP_Error('password_reset_mismatch', __('Antispam field is incorrect.', 'pinc'));
	}

	/**
	 * Fires when submitting registration form data, before the user is created.
	 *
	 * @since 2.1.0
	 *
	 * @param string   $sanitized_user_login The submitted username after being sanitized.
	 * @param string   $user_email           The submitted email.
	 * @param WP_Error $errors               Contains any errors with submitted username and email,
	 *                                       e.g., an empty field, an invalid username or email,
	 *                                       or an existing username or email.
	 */
	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	/**
	 * Filter the errors encountered when a new user is being registered.
	 *
	 * The filtered WP_Error object may, for example, contain errors for an invalid
	 * or existing username or email address. A WP_Error object should always returned,
	 * but may or may not contain errors.
	 *
	 * If any errors are present in $errors, this will abort the user's registration.
	 *
	 * @since 2.1.0
	 *
	 * @param WP_Error $errors               A WP_Error object containing any errors encountered
	 *                                       during registration.
	 * @param string   $sanitized_user_login User's username after it has been sanitized.
	 * @param string   $user_email           User's email.
	 */
	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->get_error_code() )
		return $errors;

	//$user_pass = wp_generate_password( 12, false); //edited: dun generate password
	$user_pass = trim($_POST['pass1']); //edited: strip white spaces
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( ! $user_id || is_wp_error( $user_id ) ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you&hellip; please contact the <a href="mailto:%s">webmaster</a> !', 'pinc' ), get_option( 'admin_email' ) ) );
		return $errors;
	}
	
	//update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag. //edited: dun nag

	//wp_new_user_notification( $user_id, $user_pass ); //edited: dun notify
	
	$mask_password = str_pad(substr($user_pass,-3), strlen($user_pass), '*', STR_PAD_LEFT); //edited: mask paswword

	//add user meta to verify email
	$verify_email = wp_generate_password(20, false);
	update_user_meta($user_id, '_Verify Email', $verify_email);
	
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('Thank you for registering with %s.', 'pinc'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s', 'pinc'), $sanitized_user_login) . "\r\n";
	$message .= sprintf(__('Password: %s', 'pinc'), $mask_password) . "\r\n\r\n";
	$message .= __('Please click the link to verify your email:', 'pinc') . "\r\n";
	$message .= sprintf('%s?email=verify&login=%s&key=%s', home_url('/login/'), rawurlencode($sanitized_user_login), $verify_email);

	wp_mail($user_email, sprintf(__('[%s] Account Registration', 'pinc'), $blogname), $message);
	
	//notify admin when new user register
	//$message  = sprintf(__('New user registration on your site %s:', 'pinc'), $blogname) . "\r\n\r\n";
	//$message .= sprintf(__('Username: %s', 'pinc'), $sanitized_user_login) . "\r\n\r\n";
	//$message .= sprintf(__('E-mail: %s', 'pinc'), $user_email) . "\r\n";

	//@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	return $user_id;
}

get_header();
?>

<div class="container">
	<div class="row">

		<div class="col-sm-2"></div>

		<div class="col-sm-8 usercp-wrapper">
			<?php if (isset($_GET['action']) && $_GET['action'] == 'resend') { ?>
				<h1><?php _e('Resend Activation Email', 'pinc') ?></h1>
				
				<?php if ($resend_status && $resend_status == 'success') { ?>
				<div class="error-msg"><div class="alert alert-success"><strong><?php _e('Please check your email for activation.', 'pinc'); ?></strong></div></div>
				<?php } else if ($resend_status && $resend_status != 'success') { ?>
				<div class="error-msg"><div class="alert alert-warning"><strong><?php echo $resend_status; ?></strong></div></div>
				<?php } ?>
				
				<br />
				
				<form id="resendform" action="<?php echo home_url('/signup/?action=resend'); ?>" method="post">
					<div class="form-group">
						<label class="form-label" for="user_email"><?php _e('Email', 'pinc'); ?></label>
						<input class="form-control" type="text" name="user_email" id="user_email" value="" />
					</div>
					
					<?php
					if (of_get_option('captcha_public') != '' && of_get_option('captcha_private') != '') {
						$publickey = of_get_option('captcha_public');
					?>
						<script src="https://www.google.com/recaptcha/api.js?hl=<?php echo (of_get_option('captcha_lang')); ?>" async defer></script>
						<div class="g-recaptcha" data-sitekey="<?php echo $publickey; ?>"></div>
					<?php }	?>
	
					<br />
					<input type="hidden" name="action" value="resend" />
					<input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('register'); ?>" />
					<input type="submit" class="btn btn-success btn-block btn-pinc-custom" name="wp-submit" id="wp-submit" value="<?php _e('Resend', 'pinc'); ?>" />
	
					<br />
					<p class="moreoptions"><?php _e('Check your junk/spam folder if you did not receive the activation email', 'pinc'); ?></p>
				</form>
			<?php } else { ?>			
				<?php 
				if (function_exists('wsl_activate')) {
					do_action('wordpress_social_login');
				}
				?>
	
				<?php if (isset($errors) && is_wp_error($errors)) { ?>
					<div class="error-msg"><div class="alert alert-warning"><strong><?php echo $errors->get_error_message(); ?></strong></div></div>
				<?php } ?>
				
				<h1<?php if (function_exists('wsl_activate')) echo ' style="border: none"'; ?>><?php _e('Sign Up', 'pinc') ?></h1>
				<?php if (!function_exists('wsl_activate')) echo '<br />'; ?>
				
				<form name="registerform" id="registerform" action="<?php echo home_url('/signup/'); ?>" method="post">
					<div class="form-group">
						<label class="form-label" for="user_login"><?php _e('Username', 'pinc'); ?></label>
						<input class="form-control" type="text" name="user_login" id="user_login" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="10" />
					</div>
					
					<div class="form-group">
						<label class="form-label" for="user_email"><?php _e('Email', 'pinc'); ?></label>
						<input class="form-control" type="email" name="user_email" id="user_email" value="<?php echo esc_attr(stripslashes($user_email)); ?>" tabindex="20" />
					</div>
					
					<div class="form-group">
						<label class="form-label" for="pass1"><?php _e('Password', 'pinc') ?></label>
						<input class="form-control" type="password" name="pass1" id="pass1" size="20" value="" autocomplete="off" tabindex="30" />
					</div>
						
					<div class="form-group">
						<label class="form-label" for="pass2"><?php _e('Confirm Password', 'pinc') ?></label>
						<input class="form-control" type="password" name="pass2" id="pass2" size="20" value="" autocomplete="off" tabindex="40" />
					</div>
					
					<input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('register'); ?>" />

					<?php
					if (of_get_option('captcha_public') != '' && of_get_option('captcha_private') != '') {
					$publickey = of_get_option('captcha_public');
					?>
					<script src="https://www.google.com/recaptcha/api.js?hl=<?php echo (of_get_option('captcha_lang')); ?>" async defer></script>
					<div class="g-recaptcha" data-sitekey="<?php echo $publickey; ?>"></div>
					<br />
					<?php }	?>
					
					<p class="comment-form-anti-spam" style="clear:both;">
						<label for="anti-spam">Current ye@r <span class="required">*</span></label>
						<input type="hidden" name="anti-spam-0" id="anti-spam-0" value="<?php echo date('Y'); ?>" />
						<input class="form-control" type="text" name="anti-spam" id="anti-spam" size="30" value="" />
					</p>
					
					<p class="comment-form-anti-spam-2" style="display:none;">
						<label for="anti-spam-e-email-url">Leave this field empty<span class="required">*</span></label>
						<input class="form-control" type="text" name="anti-spam-e-email-url" id="anti-spam-e-email-url" size="30" value=""/>
					</p>
	
					<?php if (of_get_option('register_agree') != '0') { ?>
						<i id="register_agree" class="far fa-square fa-fw fa-lg pull-left"></i>
						<div id="register_agree_msg">
							<?php _e('I Agree To The', 'pinc'); ?>
							<a onClick="window.open('<?php echo get_permalink(of_get_option('register_agree')); ?>','','resizable=1,scrollbars=1,top=0,left=0,width=640,height=480'); return false;" href="<?php echo get_permalink(of_get_option('register_agree')); ?>" target="_blank">
								<strong><?php _e('Terms of Service', 'pinc'); ?></strong>
							</a>
						</div>
					<?php } ?>
					<p></p>
					<input class="btn btn-success btn-block btn-pinc-custom"<?php if (of_get_option('register_agree') != '0') echo ' disabled="disabled"'; ?> type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Sign Up', 'pinc'); ?>" tabindex="50" />

					<br />
					<p class="text-center">
					<a class="btn btn-grey" href="<?php echo home_url('/login/'); ?>"><?php _e('Already have an account? Login now', 'pinc'); ?></a>
					</p>
					
					<p class="text-center">
					<a class="btn btn-grey" href="<?php echo home_url('/signup/?action=resend'); ?>"><?php _e('Resend activation email', 'pinc'); ?></a>
					</p>
				</form>
			<?php } ?>
		</div>

		<div class="col-sm-2"></div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	$('.comment-form-anti-spam, .comment-form-anti-spam-2').hide();
	var answer = $('.comment-form-anti-spam input#anti-spam-0').val();
	$('.comment-form-anti-spam input#anti-spam').val(answer);
	$('#user_login').focus();
	
	$(document).on('touchstart click', '#register_agree', function(event) {
		event.stopPropagation();
        event.preventDefault();
		if ($('#register_agree').hasClass('fa-check-square')) {
			$(this).removeClass('fa-check-square').addClass('fa-square');
			$('#wp-submit').attr('disabled', 'disabled');
		} else {
			$(this).removeClass('fa-square').addClass('fa-check-square');
			$('#wp-submit').removeAttr('disabled');
		}
	});
});
</script>

<?php get_footer(); ?>