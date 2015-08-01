<?php

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