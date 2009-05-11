<?php
class GP_Route_Project {
	function index( $project_path ) {
		global $gpdb;
		$project = &GP_Project::by_path( $project_path );
		if ( !$project ) gp_tmpl_404();
		$sub_projects = $project->sub_projects();
		$translation_sets = GP_Translation_Set::by_project_id( $project->id );
		$title = sprintf( __('%s project '), gp_h( $project->name ) );
		gp_tmpl_load( 'project', get_defined_vars() );
	}

	function originals_get( $project_path ) {
		global $gpdb;
		$project = &GP_Project::by_path( $project_path );
		$title = sprintf( __('Import originals for %s' ), $project->name );
		gp_tmpl_load( 'project-import-originals', get_defined_vars() );
	}

	function originals_post( $project_path ) {
		global $gpdb;
		$project = &GP_Project::by_path( $project_path );
		if ( !$project ) gp_tmpl_404();
		$block = array( 'GP_Route_Project', '_merge_originals');
		GP_Route_Project::_import($project, 'mo-file', 'MO', $block) or GP_Route_Project::_import($project, 'pot-file', 'PO', $block);
		wp_redirect( gp_url_join( gp_url_project( $project ), 'import-originals' ) );
	}

	function translations_get( $project_path, $locale_slug, $translation_set_slug ) {
		global $gpdb;
		$per_page = 1000;
		$project = GP_Project::by_path( $project_path );
		$locale = GP_Locales::by_slug( $locale_slug );
		$translation_set = &GP_Translation_Set::by_project_id_slug_and_locale( $project->id, $translation_set_slug, $locale_slug );
		$limit = gp_limit_for_page( gp_get('page', 1), $per_page );
		$translations = $gpdb->get_results( $gpdb->prepare( "
		    SELECT t.*, o.*, t.id as id, o.id as original_id
		    FROM $gpdb->originals as o
		    LEFT JOIN $gpdb->translations AS t ON o.id = t.original_id AND t.status = 'current' AND t.translation_set_id = %d
		    WHERE o.project_id = %d AND o.status LIKE '+%%' ORDER BY t.id ASC $limit", $translation_set->id, $project->id ) );
		// TODO: expose paging
		gp_tmpl_load( 'translations', get_defined_vars() );
	}

	function translations_post ($project_path, $locale_slug, $translation_set_slug ) {
		global $gpdb;
		$project = GP_Project::by_path( $project_path );
		$locale = GP_Locales::by_slug( $locale_slug );
		$translation_set = &GP_Translation_Set::by_project_id_slug_and_locale( $project->id, $translation_set_slug, $locale_slug );
		//TODO: multiple insert
		foreach($_POST['translation'] as $original_id => $translations) {
		    $data = compact('original_id');
		    $data['translation_set_id'] = $translation_set->id;
		    foreach(range(0, 3) as $i) {
		        if (isset($translations[$i])) $data["translation_$i"] = $translations[$i];
		    }
		    /*
		    Since we still don't have status updates, just insert with status current
		    and set all the previous translations of the same original to sth else
		    */
		    $data['status'] = 'current';
		    $gpdb->update($gpdb->translations, array('status' => 'approved'), array('original_id' => $original_id, 'translation_set_id' => $translation_set->id));
	    
	        $gpdb->insert($gpdb->translations, $data);
		}
	}

	function _import($project, $file_key, $class, $block) {
		global $gpdb;
		if ( is_uploaded_file( $_FILES[$file_key]['tmp_name'] ) ) {
			$translations = new $class();
			$result = $translations->import_from_file( $_FILES[$file_key]['tmp_name'] );
			if ( !$result ) {
				gp_notice_set( __("Couldn&#8217;t load translations from file!"), 'error' );
			} else {
				call_user_func($block, $project, $translations);
			}
			return true;
		}
		return false;
	}
	
	function _merge_originals( $project, $translations ) {
		global $gpdb;
		// TODO: do not insert duplicates. This is tricky, because we can't add unique index on the TEXT fields
		$gpdb->update( $gpdb->originals, array('status' => '+obsolete'), array('project_id' => $project->id));
		foreach( $translations->entries as $entry ) {
			$data = array('project_id' => $project->id, 'context' => $entry->context, 'singular' => $entry->singular,
				'plural' => $entry->plural, 'comment' => $entry->extracted_comments,
				'references' => implode( ' ', $entry->references ), 'status' => '+active' );
			if ( is_null( $entry->context ) ) unset($data['context']);
			if ( is_null( $entry->plural ) ) unset($data['plural']);
			// check if
			$where = array();
			// TODO: fix db::prepare to understand %1$s and so on
			$where[] = is_null( $entry->context )? '(context IS NULL OR %s IS NULL)' : 'context = %s';
			$where[] = 'singular = %s';
			$where[] = is_null( $entry->plural )? '(plural IS NULL OR %s IS NULL)' : 'plural = %s';
			$where[] = 'project_id = %d';
			$where = implode( ' AND ', $where );
			$sql = $gpdb->prepare( "SELECT * FROM $gpdb->originals WHERE $where", $entry->context, $entry->singular, $entry->plural, $project->id );
			$existing = $gpdb->get_row( $sql );
			if ( $existing )
				$gpdb->update( $gpdb->originals, $data, array('id' => $existing->id ) );
			else
				$gpdb->insert( $gpdb->originals, $data );
		}
		$gpdb->update( $gpdb->originals, array('status' => '-obsolete'), array('project_id' => $project->id, 'status' => '+obsolete'));
		// TODO: were they really added?
		gp_notice_set( sprintf(__("%s strings were processed"), count($translations->entries) ) );
	}
}