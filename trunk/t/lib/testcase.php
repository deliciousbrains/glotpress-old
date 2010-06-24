<?php
require_once dirname( __FILE__ ) . '/../../gp-includes/backpress/class.bp-sql-schema-parser.php';
require_once dirname( __FILE__ ) . '/../../gp-includes/schema.php';
require_once dirname( __FILE__ ) . '/../../gp-includes/install-upgrade.php';

require_once dirname( __FILE__ ) . '/request.php';
require_once dirname( __FILE__ ) . '/factory.php';

class GP_UnitTestCase extends PHPUnit_Framework_TestCase {

    var $methods_from_request = array();
    var $url = 'http://example.org/';

	function setUp() {
		global $gpdb;
		$gpdb->suppress_errors = false;
		$gpdb->show_errors = false;
		error_reporting( E_ALL );
		ini_set('display_errors', 1);
		if ( !gp_const_get( 'GP_IS_TEST_DB_INSTALLED' ) ) {
			$gpdb->query( 'DROP DATABASE '.GPDB_NAME.";" );
			$gpdb->query( 'CREATE DATABASE '.GPDB_NAME.";" );
			$gpdb->select( GPDB_NAME, $gpdb->dbh );
			add_filter( 'gp_schema_pre_charset', array( &$this, 'force_innodb' ) );
			gp_install();
			define( 'GP_IS_TEST_DB_INSTALLED', true );
		}
		$this->factory = new GP_UnitTest_Factory;
		$this->clean_up_global_scope();
		$this->start_transaction();
		ini_set( 'display_errors', 1 );
		$this->request = new GP_UnitTest_Request( $this );
		$this->methods_from_request = $this->request->exported_methods;
		$this->url_filter = returner( $this->url );
		add_filter( 'gp_get_option_uri', $this->url_filter );
    }

	function tearDown() {
		global $gpdb;
		$gpdb->query( 'ROLLBACK' );
		remove_filter( 'gp_get_option_uri', $this->url_filter );
	}

	function clean_up_global_scope() {
		GP::$user->reintialize_wp_users_object();
		wp_cache_flush();
	}
	
	function start_transaction() {
		global $gpdb;
		$gpdb->query( 'SET autocommit = 0;' );
		$gpdb->query( 'SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;' );
		$gpdb->query( 'START TRANSACTION;' );		
	}

	function force_innodb( $schema ) {
		foreach( $schema as &$sql ) {
			$sql = str_replace( ');', ') TYPE=InnoDB;', $sql );
		}
		return $schema;
	}

	function temp_filename() {
		$tmp_dir = '';
		$dirs = array( 'TMP', 'TMPDIR', 'TEMP' );
		foreach( $dirs as $dir )
			if ( isset( $_ENV[$dir] ) && !empty( $_ENV[$dir] ) ) {
				$tmp_dir = $dir;
				break;
			}
		if (empty($dir)) $dir = '/tmp';
		$dir = realpath( $dir );
		return tempnam( $dir, 'testpomo' );
	}
	
	function __call( $name, $args ) {
	    if ( is_array( $this->methods_from_request ) && in_array( $name, $this->methods_from_request ) ) {
	        return call_user_func_array( array( &$this->request, $name ), $args );
	    }
		trigger_error( sprintf( 'Call to undefined function: %s::%s().', get_class( $this ), $name ), E_USER_ERROR );
	}
	
	function assertWPError( $actual, $message = '' ) {
		$this->assertTrue( is_wp_error( $actual ), $message );
	}
	
	function assertEqualFields( $object, $fields ) {
		foreach( $fields as $field_name => $field_value ) {
			if ( $object->$field_name != $field_value ) {
				$this->fail();
			}			
		}
	}
}