<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      0.1
 *
 * @package    GlotPress
 * @subpackage GlotPress/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1
 * @package    GlotPress
 * @subpackage GlotPress/includes
 * @author     Your Name <email@example.com>
 */
class GlotPress_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.1
	 */
	public static function activate() {
		self::installTables();
	}

	/**
	 * Install database tables
	 */
	private static function installTables() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( self::getSchema() );
	}

	/**
	 * Get database schema
	 */
	private static function getSchema() {
		global $wpdb;

		$charset_collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$charset_collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$charset_collate .= " COLLATE $wpdb->collate";
			}
		}

		return "
CREATE TABLE {$wpdb->prefix}gp_translations (
	id INT(10) NOT NULL auto_increment,
	original_id INT(10) DEFAULT NULL,
	translation_set_id INT(10) DEFAULT NULL,
	translation_0 TEXT NOT NULL,
	translation_1 TEXT DEFAULT NULL,
	translation_2 TEXT DEFAULT NULL,
	translation_3 TEXT DEFAULT NULL,
	translation_4 TEXT DEFAULT NULL,
	translation_5 TEXT DEFAULT NULL,
	user_id INT(10) DEFAULT NULL,
	status VARCHAR(20) NOT NULL default 'waiting',
	date_added DATETIME DEFAULT NULL,
	date_modified DATETIME DEFAULT NULL,
	warnings TEXT DEFAULT NULL,
	PRIMARY KEY  (id),
	KEY original_id (original_id),
	KEY user_id (user_id),
	KEY translation_set_id (translation_set_id),
	KEY translation_set_id_status (translation_set_id,status),
	KEY date_added (date_added),
	KEY warnings (warnings (1))
) $charset_collate;
CREATE TABLE {$wpdb->prefix}gp_translation_sets (
	id INT(10) NOT NULL auto_increment,
	name VARCHAR(255) NOT NULL,
	slug VARCHAR(255) NOT NULL,
	project_id INT(10) DEFAULT NULL,
	locale VARCHAR(10) DEFAULT NULL,
	PRIMARY KEY  (id),
	UNIQUE KEY project_id_slug_locale (project_id, slug(171), locale),
	KEY locale_slug (locale, slug(181))
) $charset_collate;
CREATE TABLE {$wpdb->prefix}gp_originals (
	id INT(10) NOT NULL auto_increment,
	project_id INT(10) DEFAULT NULL,
	context VARCHAR(255) DEFAULT NULL,
	singular TEXT NOT NULL,
	plural TEXT DEFAULT NULL,
	`references` TEXT DEFAULT NULL,
	comment TEXT DEFAULT NULL,
	status VARCHAR(255) NOT NULL DEFAULT '+active',
	priority TINYINT NOT NULL DEFAULT 0,
	date_added DATETIME DEFAULT NULL,
	PRIMARY KEY  (id),
	KEY project_id_status (project_id, status),
	KEY singular_plural_context (singular(63), plural(63), context(63))
) $charset_collate;
CREATE TABLE {$wpdb->prefix}gp_glossary_entries (
	id INT(10) unsigned NOT NULL auto_increment,
	glossary_id INT(10) unsigned NOT NULL,
	term VARCHAR(255) NOT NULL,
	part_of_speech VARCHAR(255) DEFAULT NULL,
	comment TEXT DEFAULT NULL,
	translation VARCHAR(255) DEFAULT NULL,
	date_modified DATETIME NOT NULL,
	last_edited_by BIGINT(20) NOT NULL,
	PRIMARY KEY  (id)
) $charset_collate;
CREATE TABLE {$wpdb->prefix}gp_glossaries (
	id INT(10) unsigned NOT NULL auto_increment,
	translation_set_id INT(10)  NOT NULL,
	description TEXT DEFAULT NULL,
	PRIMARY KEY  (id)
) $charset_collate;
CREATE TABLE {$wpdb->prefix}gp_projects (
	id INT(10) NOT NULL auto_increment,
	name VARCHAR(255) NOT NULL,
	slug VARCHAR(255) NOT NULL,
	path VARCHAR(255) NOT NULL,
	description TEXT NOT NULL,
	parent_project_id INT(10) DEFAULT NULL,
	source_url_template VARCHAR(255) DEFAULT '',
	active TINYINT DEFAULT 0,
	PRIMARY KEY  (id),
	KEY path (path),
	KEY parent_project_id (parent_project_id)
) $charset_collate;
CREATE TABLE {$wpdb->prefix}gp_api_keys (
	id INT(10) NOT NULL AUTO_INCREMENT,
	user_id INT(10) NOT NULL,
	api_key VARCHAR(16) NOT NULL,
	PRIMARY KEY  (id),
	UNIQUE KEY user_id (user_id),
	UNIQUE KEY api_key (api_key)
) $charset_collate;
		";
	}

}