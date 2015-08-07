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
		add_action( 'wp_print_styles', array( $this, 'enqueue_glotpress_styles' ), 99 );
		add_action( 'wp_print_scripts', array( $this, 'enqueue_glotpress_scripts' ), 99 );

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

	/**
	 * Enqueue the styles for GlotPress
	 */
	public function enqueue_glotpress_styles() {
		global $wp_scripts;

		// Dequeue any enqueued styles from WordPress
		/*if ( ! empty( $wp_scripts->queue ) ) {
			foreach ( $wp_scripts->queue as $handle ) {
				wp_dequeue_style( $handle );
			}
		}*/

		wp_enqueue_style( 'glotpress-base', plugin_dir_url( dirname( __FILE__ ) ) . 'public/assets/css/style.css', array(), GLOTPRESS_VERSION );
	}

	/**
	 * Enqueue the scripts for GlotPress
	 */
	public function enqueue_glotpress_scripts() {
		global $wp_scripts;

		// Dequeue any enqueued scripts from WordPress
		/*if ( ! empty( $wp_scripts->queue ) ) {
			foreach ( $wp_scripts->queue as $handle ) {
				wp_dequeue_script( $handle );
			}
		}*/

		wp_enqueue_script( 'jquery' );
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