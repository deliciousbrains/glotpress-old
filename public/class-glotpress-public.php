<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.1
 *
 * @package    GlotPress
 * @subpackage GlotPress/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    GlotPress
 * @subpackage GlotPress/public
 * @author     Your Name <email@example.com>
 */
class GlotPress_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies
	 *
	 * @since    0.1
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * Template functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/functions-template.php';
	}

	/**
	 * Rewrite rules
	 *
	 * @since    0.1
	 */
	public function rewrite_rules() {
		add_rewrite_rule( 'glotpress/?$', 'index.php?gp_action=projects', 'top' );
	}

	/**
	 * Query vars
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	public function query_vars( $vars ) {
		$vars[] = 'gp_action';

		return $vars;
	}

	/**
	 * Template include
	 *
	 * @param string $template
	 *
	 * @return string
	 */
	public function template_include( $template ) {
		$action = get_query_var( 'gp_action' );

		if ( 'projects' === $action ) {
			$template = dirname( __FILE__ ) . '/templates/projects.php';
		}

		return $template;
	}

}
