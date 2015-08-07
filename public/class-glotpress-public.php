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
	 * The router
	 *
	 * @since    0.1
	 * @access   protected
	 * @var      object $router The router
	 */
	protected $router;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct() {
		$this->router = new GlotPress_Router();
	}

	/*
	 * Handle routes
	 */
	public function router() {
		$routes_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'routes/';

		$routes = $this->default_routes();
		foreach ( $routes as $pattern => $route ) {
			$http_method = isset( $route[0] ) ? strtoupper( $route[0] ) : 'GET';
			$file_name   = isset( $route[1] ) ? $route[1] : '';
			$method_name = isset( $route[2] ) ? $route[2] : '';

			if ( ! $file_name ) {
				continue;
			}

			if ( file_exists( $routes_dir . $file_name . '.php' ) ) {
				require_once $routes_dir . $file_name . '.php';
			}

			$class_name = $this->file_name_to_class_name( $file_name );

			if ( method_exists( $class_name, $method_name ) ) {
				if ( $pattern === '/' ) {
					$pattern = '';
				}

				$this->router->match( $http_method, untrailingslashit( 'glotpress/' . $pattern ), array(
					new $class_name,
					$method_name
				) );
			}
		}

		$this->router->run();
	}

	/**
	 * Define the default routes
	 *
	 * @return mixed|void
	 */
	private function default_routes() {
		return apply_filters( 'glotpress_routes', array(
			'/'        => array( 'get', 'route-index', 'index' ),
			'projects' => array( 'get', 'route-project', 'index' ),
		) );
	}

	/*
	 * Convert file name to class name
	 */
	private function file_name_to_class_name( $filename ) {
		$parts = explode( '-', $filename );

		return 'GP_' . ucwords( implode( '_', $parts ), '_' );
	}

}
