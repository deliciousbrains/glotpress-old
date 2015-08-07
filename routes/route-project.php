<?php

/**
 * Project route
 *
 * @since      0.1
 * @package    GlotPress
 * @subpackage GlotPress/includes
 * @author     Your Name <email@example.com>
 */
class GP_Route_Project extends GP_Base_Route {

	public function index() {
		$this->template( 'projects' );
	}

}