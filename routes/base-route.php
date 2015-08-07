<?php

/**
 * Holds common functionality for routes.
 *
 * @since      0.1
 * @package    GlotPress
 * @subpackage GlotPress/includes
 * @author     Your Name <email@example.com>
 */
class GP_Base_Route {

	/**
	 * The glotpress template
	 *
	 * @since    0.1
	 * @access   protected
	 * @var      string $gp_template The glotpress template
	 */
	protected $gp_template = '';

	/*
	 * Setup the template_include filter
	 *
	 * @since     0.1
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'glotpress_template' ) );

		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies
	 *
	 * @since    0.1
	 * @access   private
	 */
	protected function load_dependencies() {
		/**
		 * Template functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/functions-template.php';
	}

	/*
	 * Make WordPress use the GlotPress template if it exists
	 *
	 * @since     0.1
	 * @param     string $template The default template.
	 * @return    string Path to the template.
	 */
	public function glotpress_template( $template ) {
		if ( $this->gp_template ) {
			$template_path = locate_template( array( 'glotpress-' . $this->gp_template . '.php' ) );
			if ( ! $template_path ) {
				$template_path = plugin_dir_path( dirname( __FILE__ ) ) . 'public/templates/' . $this->gp_template . '.php';
			}

			if ( file_exists( $template_path ) ) {
				return $template_path;
			}
		}

		return $template;
	}

	/*
	 * Set the GlotPress template for this route
	 *
	 * @since     0.1
	 * @param     string $template The template name.
	 */
	protected function template( $template ) {
		$this->gp_template = $template;
	}

}