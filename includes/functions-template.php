<?php

/**
 * Get header
 */
function gp_get_header() {
	load_template( dirname( dirname( __FILE__ ) ) . '/public/templates/header.php' );
}

/**
 * Get footer
 */
function gp_get_footer() {
	load_template( dirname( dirname( __FILE__ ) ) . '/public/templates/footer.php' );
}