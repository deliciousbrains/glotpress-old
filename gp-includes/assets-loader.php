<?php
/**
 * Defines default styles and scripts
 */

function gp_styles_default( &$styles ) {
	$url = gp_plugin_url( 'css' );

	$styles->add( 'base', $url . '/style.css', array(), '20141019' );
	$styles->add( 'install', $url . '/install.css', array( 'base' ), '20140902' );
}

add_action( 'wp_default_styles', 'gp_styles_default' );

function gp_scripts_default( &$scripts ) {
	$url = gp_plugin_url( 'js' );

	$bump = '20150430';

	$scripts->add( 'tablesorter', $url . '/jquery.tablesorter.min.js', array( 'jquery' ), '1.10.4' );

	$scripts->add( 'gp-common', $url . '/common.js', array( 'jquery' ), $bump );
	$scripts->add( 'gp-editor', $url . '/editor.js', array( 'gp-common', 'jquery-ui-tooltip' ), $bump );
	$scripts->add( 'gp-glossary', $url . '/glossary.js', array( 'gp-common' ), $bump );
	$scripts->add( 'translations-page', $url . '/translations-page.js', array( 'gp-common' ), $bump );
	$scripts->add( 'mass-create-sets-page', $url . '/mass-create-sets-page.js', array( 'gp-common' ), $bump );
}

add_action( 'wp_default_scripts', 'gp_scripts_default' );

/**
 * Here we abstract WordPress core's enqueuing functions because...
 * 1. We don't want to print scripts and styles that are meant for the WordPress theme
 * 2. GlotPress enqueues scripts and styles from its template files and if we do that
 *    with wp_enqueue_script() and wp_enqueue_style() WordPress complains that
 *    those functions should only be called inside of wp_enqueue_scripts()
 */

function gp_enqueue_scripts() {
	global $gp_enqueued_styles, $gp_enqueued_scripts;

	if ( ! empty( $gp_enqueued_scripts ) ) {
		foreach ( $gp_enqueued_scripts as $handle ) {
			wp_enqueue_script( $handle );
		}
	}

	if ( ! empty( $gp_enqueued_styles ) ) {
		foreach ( $gp_enqueued_styles as $handle ) {
			wp_enqueue_style( $handle );
		}
	}
}

add_action( 'wp_enqueue_scripts', 'gp_enqueue_scripts' );

function gp_enqueue_style( $handle ) {
	global $gp_enqueued_styles;

	$gp_enqueued_styles[] = $handle;
}

function gp_enqueue_script( $handle ) {
	global $gp_enqueued_scripts;

	$gp_enqueued_scripts[] = $handle;
}

function gp_print_styles() {
	global $gp_enqueued_styles;
	wp_print_styles( $gp_enqueued_styles );
}

function gp_print_scripts() {
	global $gp_enqueued_scripts;
	wp_print_scripts( $gp_enqueued_scripts );
}
