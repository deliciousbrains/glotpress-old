<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo wp_title(); ?></title>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<script type="text/javascript">document.body.className = document.body.className.replace('no-js','js');</script>
	<div class="gp-content">
		<div id="gp-js-message"></div>
		<h1>
			<a href="<?php echo gp_url( '/' ); ?>" rel="home" class="logo">
				<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/glotpress-logo.png'; ?>" alt="GlotPress" />
			</a>
			<?php /*echo gp_breadcrumb(); ?>
			<span id="hello">
				<?php
				if (GP::$user->logged_in()):
					$user = GP::$user->current();

					printf( __('Hi, %s.'), '<a href="'.gp_url( '/profile' ).'">'.$user->user_login.'</a>' );
					?>
					<a href="<?php echo gp_url('/logout')?>"><?php _e('Log out'); ?></a>
				<?php elseif( ! GP_INSTALLING ): ?>
					<strong><a href="<?php echo gp_url_login(); ?>"><?php _e('Log in'); ?></a></strong>
				<?php endif; ?>
				<?php do_action( 'after_hello' ); ?>
				</span>
			<div class="clearfix"></div>
		</h1>
		<div class="clear after-h1"></div>
		<?php if (gp_notice('error')): ?>
			<div class="error">
				<?php echo gp_notice( 'error' ); //TODO: run kses on notices ?>
			</div>
		<?php endif; ?>
		<?php if (gp_notice()): ?>
			<div class="notice">
				<?php echo gp_notice(); ?>
			</div>
		<?php endif; ?>
	<?php do_action( 'after_notices' );*/ ?>