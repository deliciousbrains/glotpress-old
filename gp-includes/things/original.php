<?php
class GP_Original extends GP_Thing {
	
	var $table_basename = 'originals';
	var $field_names = array( 'id', 'project_id', 'context', 'singular', 'plural', 'references', 'comment', 'status', 'priority', 'date_added' );
	var $non_updatable_attributes = array( 'id', 'path' );
	
    static $priorities = array( '-2' => 'hidden', '-1' => 'low', '0' => 'normal', '1' => 'high' );

	function restrict_fields( $original ) {
		$original->singular_should_not_be('empty');
		$original->status_should_not_be('empty');
		$original->project_id_should_be('positive_int');
		$original->priority_should_be('int');
		$original->priority_should_be('between', -2, 1);
	}

	function normalize_fields( $args ) {
		$args = (array)$args;
		foreach ( array('plural', 'context', 'references', 'comment') as $field ) {
			if ( isset( $args['parent_project_id'] ) ) {
				$args[$field] = $this->force_false_to_null( $args[$field] );
			}
		}
		return $args;
	}
	
	function by_project_id( $project_id ) {
		return $this->many( "SELECT * FROM $this->table WHERE project_id= %d AND status = '+active'", $project_id );
	}

	function by_project_id_and_entry( $project_id, $entry ) {
		$where = array();
		// now each condition has to contain a %s not to break the sequence
		$where[] = is_null( $entry->context )? '(context IS NULL OR %s IS NULL)' : 'BINARY context = %s';
		$where[] = 'BINARY singular = %s';
		$where[] = is_null( $entry->plural )? '(plural IS NULL OR %s IS NULL)' : 'BINARY plural = %s';
		$where[] = 'project_id = %d';
		$where = implode( ' AND ', $where );
		return $this->one( "SELECT * FROM $this->table WHERE $where", $entry->context, $entry->singular, $entry->plural, $project_id );
	}
	
	function import_for_project( $project, $translations ) {
		global $gpdb;
		$originals_added = $originals_existing = 0;
		$originals = $this->many_no_map( "SELECT * FROM $this->table WHERE project_id= %d AND status = '+active'", $project->id );
		$this->update( array( 'status' => '+obsolete' ), array( 'project_id' => $project->id ) );
		$current_entries = array();
		foreach( $originals as $original ) {
			$entry = new Translation_Entry( array( 'singular' => $original->singular, 'plural' => $original->plural, 'context' => $original->context ) );
			$current_entries[$entry->key()] = $original->id;
		}
		foreach( $translations->entries as $entry ) {
			$gpdb->queries = array();
			$data = array('project_id' => $project->id, 'context' => $entry->context, 'singular' => $entry->singular,
				'plural' => $entry->plural, 'comment' => $entry->extracted_comments,
				'references' => implode( ' ', $entry->references ), 'status' => '+active' );
				
			// TODO: do not obsolete similar translations			
			if ( isset( $current_entries[$entry->key()] ) ) {
				$this->update( $data, array( 'id' => $current_entries[$entry->key()] ) );
				$originals_existing++;
			} else {
				GP::$original->create( $data );
				$originals_added++;
			}
		}
		$this->update( array('status' => '-obsolete'), array('project_id' => $project->id, 'status' => '+obsolete'));
		return array( $originals_added, $originals_existing );
	}
}
GP::$original = new GP_Original();