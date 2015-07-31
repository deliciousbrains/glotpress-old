<?php
/*
Plugin Name: GlotPress
Plugin URI:  https://github.com/deliciousbrains/glotpress
Description: GlotPress is a collaborative, web-based software translation tool.
Version:     0.1
Author:      Delicious Brains
Author URI:  https://deliciousbrains.com
Text Domain: glotpress
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'GP_PATH', dirname( __FILE__ ) . '/' );
define( 'GP_INC', 'gp-includes/' );

require_once( GP_PATH . 'gp-settings.php' );

function gp_rewrite_rules() {
    add_rewrite_rule( 'projects/?$', 'index.php?gp_action=projects', 'top' );
}
add_action( 'init', 'gp_rewrite_rules' );