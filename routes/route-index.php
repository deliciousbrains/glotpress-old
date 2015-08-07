<?php

/**
 * Index route
 *
 * @since      0.1
 * @package    GlotPress
 * @subpackage GlotPress/includes
 * @author     Your Name <email@example.com>
 */
class GP_Route_Index extends GP_Base_Route {

	public function index() {
		wp_redirect( home_url( 'glotpress/projects' ) );
	}

}