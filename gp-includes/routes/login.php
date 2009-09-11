<?php
class GP_Route_Login extends GP_Route_Main {
	function login_get() {
		if ( !GP::$user->logged_in() ) {
			gp_tmpl_load( 'login', array() );
		}  else {
			wp_redirect( gp_url( '/' ) );
		}
	}
	
	function login_post() {
		global $wp_users_object, $wp_auth_object;
				
		$user = GP::$user->by_login( $_POST['user_login'] );
				
		if ( !$user || is_wp_error($user) ) {
			$this->errors[] = __("Invalid username!");
			wp_redirect( gp_url( '/login' ) );
			return;
		}
		
		if ( $user->login( $_POST['user_pass'] ) ) {
			if ( gp_post( 'redirect_to' ) ) {
				wp_redirect( gp_url( gp_post( 'redirect_to' ) ) );
			} else {
				$this->notices[] = sprintf( __("Welcome, %s!"), $_POST['user_login'] );
				wp_redirect( gp_url( '/' ) );
			}
		} else {
			$this->errors[] = __("Invalid password!");
			wp_redirect( gp_url( '/login' ) );
		}
	}
	
	function logout() {
		GP::$user->logout();
		wp_redirect( gp_url( '/' ) );
	}
}