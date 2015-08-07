<?php

/**
 * Retrieve the URL to GlotPress.
 *
 * @param  string $path Optional. Path relative to the glotpress url. Default empty.
 * @param  string $scheme Optional. Scheme to give the glotpress url context. Accepts 'http', 'https', or 'relative'. Default null.
 *
 * @return string GlotPress url link with optional path appended.
 */
function gp_url( $path = '', $scheme = null ) {
	return home_url( GLOTPRESS_URL_BASE . '/' . ltrim( $path, '/' ), $scheme );
}

/**
 * Get header
 */
function gp_get_header() {
	load_template( dirname( __FILE__ ) . '/templates/header.php' );
}

/**
 * Get footer
 */
function gp_get_footer() {
	load_template( dirname( __FILE__ ) . '/templates/footer.php' );
}