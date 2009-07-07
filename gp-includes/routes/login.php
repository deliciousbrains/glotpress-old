<?php
class GP_Route_Login {
	function login_get() {
		if ( !GP_User::logged_in() ) {
			gp_tmpl_load( 'login', array() );
		}  else {
			wp_redirect( gp_url( '/' ) );
		}
	}
	
	function login_post() {
		global $wp_users_object, $wp_auth_object;
				
		$user = GP_User::by_login( $_POST['user_login'] );
				
		if ( !$user || is_wp_error($user) ) {
			gp_notice_set( __("Invalid username!"), 'error' );
			wp_redirect( gp_url( '/login' ) );
		}
		
		if ( $user->login( $_POST['user_pass'] ) ) {
			if ( gp_post( 'redirect_to' ) ) {
				wp_redirect( gp_url( gp_post( 'redirect_to' ) ) );
			} else {
				gp_notice_set( __("Welcome, gonzo &amp; bonzo!") );
				wp_redirect( gp_url( '/' ) );
			}
		} else {
			gp_notice_set( __("Invalid password!"), 'error' );
			wp_redirect( gp_url( '/login' ) );
		}
	}
	
	function logout() {
		GP_User::logout();
		wp_redirect( gp_url( '/' ) );
	}
}