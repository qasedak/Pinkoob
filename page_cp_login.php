<?php
/*
Template Name: _login
*/

define("DONOTCACHEPAGE", true);

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
	if (wp_verify_nonce($_GET['nonce'], 'logout')) {
		wp_logout();
		wp_safe_redirect(home_url('/login/?action=loggedout'));
		exit();
	}
}

if (is_user_logged_in()) { wp_redirect(home_url()); exit; }

if (of_get_option('captcha_public') != '' && of_get_option('captcha_private') != '')
	require_once(get_template_directory() . '/recaptchalib.php');

get_header();
?>
<div class="container">
	<div class="row">

		<div class="col-sm-2"></div>

		<div class="col-sm-8 usercp-wrapper" style="display:none;">		
			<?php if (isset($_GET['action']) && $_GET['action'] == 'loggedout' && !$_GET['login']) { ?>
				<div class="error-msg-incorrect"><div class="alert alert-success"><strong><?php _e('Logged Out Successfully', 'pinc'); ?></strong></div></div>
			<?php } ?>
			
			<?php 
			if (function_exists('wsl_activate')) {
				do_action('wordpress_social_login');
			}
			?>

			<?php if (isset($_GET['pw']) && $_GET['pw'] == 'reset') {   ?>
				<div class="error-msg-incorrect"><div class="alert alert-success"><strong><?php _e('Your password has been reset.', 'pinc'); ?></strong></div></div>
			<?php } else if (isset($_GET['registration']) && $_GET['registration'] == 'disabled') {   ?>
				<div class="error-msg-incorrect"><div class="alert alert-warning"><strong><?php _e('User registration is currently closed.', 'pinc'); ?></strong></div></div>
			<?php } else if (isset($_GET['registration']) && $_GET['registration'] == 'done' ) {   ?>
				<div class="error-msg-incorrect"><div class="alert alert-success"><strong><?php _e('To activate account, please check your email for verification link.', 'pinc'); ?></strong></div></div>
			<?php } else if (isset($_GET['email']) && $_GET['email'] == 'unverified' ) {   ?>
				<div class="error-msg-incorrect"><div class="alert alert-warning"><strong><?php _e('Account not activated yet. Please check your email for verification link.', 'pinc'); ?></strong></div></div>
			<?php } else if (isset($_GET['email']) && $_GET['email'] == 'verify') {
				$user = get_user_by('login', $_GET['login']);
				$key = get_user_meta($user->ID, '_Verify Email', true);
				if ($key == $_GET['key']) {
					delete_user_meta($user->ID, '_Verify Email', $key);
				?>
				<div class="error-msg-incorrect"><div class="alert alert-success"><strong><?php _e('Verification success. You may login now.', 'pinc'); ?></strong></div></div>
				<?php } else { ?>
				<div class="error-msg-incorrect"><div class="alert alert-warning"><strong><?php _e('Invalid verification key', 'pinc'); ?></strong></div></div>
			<?php }
			} else if (isset($_GET['login']) && $_GET['login'] == 'failed') { ?>
				<div class="error-msg-incorrect"><div class="alert alert-warning"><strong><?php _e('Incorrect Username', 'pinc'); ?>/<?php _e('Password', 'pinc'); if (of_get_option('captcha_public') != '' && of_get_option('captcha_private') != '') { echo '/'; _e('Captcha', 'pinc'); } ?></strong></div></div>
			<?php } ?>

			<div class="error-msg-blank"></div>
			
			<h1<?php if (function_exists('wsl_activate')) echo ' style="border: none"'; ?>><?php _e('Login', 'pinc') ?></h1>
			<?php if (!function_exists('wsl_activate')) echo '<br />'; ?>
			
			<form name="loginform" id="loginform" action="<?php echo site_url('wp-login.php', 'login_post'); ?>" method="post">
				<div class="form-group">
					<label class="form-label" for="log"><?php _e('Username or Email', 'pinc'); ?></label>
					<input class="form-control" type="text" name="log" id="log" value="" tabindex="10" />
				</div>

				<div class="form-group">
					<label class="form-label" for="pwd"><?php _e('Password', 'pinc'); ?> (<a href="<?php echo home_url('/login-lpw/'); ?>"><?php _e('Forgot?', 'pinc'); ?></a>)</label>
					<input class="form-control" type="password" name="pwd" id="pwd" value="" tabindex="20" />
				</div>
				
				<?php
				if (of_get_option('captcha_public') != '' && of_get_option('captcha_private') != '') {
					$publickey = of_get_option('captcha_public');
				?>
					<script src="https://www.google.com/recaptcha/api.js?hl=<?php echo (of_get_option('captcha_lang')); ?>" async defer></script>
					<div class="g-recaptcha" data-sitekey="<?php echo $publickey; ?>"></div>
				<?php }	?>
				
				<br />
				
				<input type="hidden" name="rememberme" id="rememberme" value="forever" />
				<input type="hidden" name="redirect_to" id="redirect_to" value="<?php if ($_GET['redirect_to']) { echo esc_attr($_GET['redirect_to']); } else { echo esc_attr(home_url('/')); } ?>" />
				<input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('login'); ?>" />
				<input type="hidden" name="formname" id="formname" value="pinc_loginform" />
				<input type="submit" class="btn btn-success btn-block btn-pinc-custom" name="wp-submit" id="wp-submit" value="<?php _e('Login', 'pinc'); ?>" tabindex="30" />

				<br />
				<p class="text-center">
				<a class="btn btn-grey" href="<?php echo home_url('/signup/'); ?>"><?php _e('Don\'t have an account? Sign Up Now', 'pinc'); ?></a>
				</p>
			</form>
		</div>

		<div class="col-sm-2 hiddex-xs"></div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	$('.usercp-wrapper').show();
	$('#log').focus();
});
</script>

<?php get_footer(); ?>