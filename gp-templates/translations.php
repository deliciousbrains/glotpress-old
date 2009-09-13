<?php
gp_title( sprintf( __( 'Translations &lt; %s &lt; %s &lt; GlotPress' ), $translation_set->name, $project->name ) );
gp_breadcrumb( array(
	gp_link_project_get( $project, $project->name ),
	$locale->combined_name(),
	$translation_set->name,
) );
wp_enqueue_script( 'editor' );
// localizer adds var in front of the variable name, so we can't use $gp.editor.options
wp_localize_script( 'editor', '$gp_editor_options', compact('can_approve') );
$parity = gp_parity_factory();
gp_tmpl_header();
$i = 0;

/**
 * Similar to esc_html() but allows double-encoding.
 */
function esc_translation( $text ) {
	return wp_specialchars( $text, ENT_NOQUOTES, false, true );
}

function textareas( $entry, $can_edit, $index = 0 ) {
	$disabled = $can_edit? '' : 'disabled="disabled"';
?>
<div class="textareas">
	<textarea name="translation[<?php echo $entry->original_id; ?>][]" rows="8" cols="80" <?php echo $disabled; ?>><?php echo esc_translation($entry->translations[$index]); ?></textarea>
<?php if ( $can_edit ): ?>
	<p>
		<a href="#" class="copy" tabindex="-1">Copy from original</a>
	</p>
<?php endif; ?>
</div>
<?php
}

function references( $project, $entry ) {
	if ( !$project->source_url_template ) return;
?>
	References:
			<ul class="refs">
	<?php
		foreach( $entry->references as $reference ):
			list( $file, $line ) = array_pad( explode( ':', $reference ), 2, 0 );
			// TODO: allow the user to override the project setting
			if ( $source_url = $project->source_url( $file, $line ) ):
	?>
				<li><a target="_blank" tabindex="-1" href="<?php echo $source_url; ?>"><?php echo $file.':'.$line ?></a></li>

	<?php
			endif;
		endforeach;
	?>
			</ul>
<?php
}
?>
<form id="upper-filters-toolbar" class="filters-toolbar" action="" method="get" accept-charset="utf-8">
	<a href="#" class="revealing filter">Filter &darr;</a> &bull;	
	<a href="#" class="revealing sort">Sort &darr;</a> <strong style="font-size: 1.8em; vertical-align: bottom;">&bull;</strong>
	<?php
	$filter_links = array();
	$filter_links[] = gp_link_get( $url, 'Current' );
	$filter_links[] = gp_link_get( add_query_arg( array('filters[translated]' => 'no'), $url ), 'Untranslated' );
	if ( $can_approve ) {
		$filter_links[] = gp_link_get( add_query_arg( array('filters[translated]' => 'yes', 'filters[status]' => '-waiting'), $url ),
				'Waiting' );
	}
	// TODO: with warnings
	// TODO: saved searches
	echo implode( '&nbsp;&bull;&nbsp;', $filter_links );
	?>
	<dl class="filters-expanded filters hidden">		
 		<dt><label for="filters[term]">Term:</label></dt>
		<dd><input type="text" value="<?php echo esc_html( gp_array_get( $filters, 'term' ) ); ?>" name="filters[term]" id="filters[term]" /></dd>		
 		<dt><label for="filters[translated]">Translated:</label></dt>
		<dd>
			<?php echo gp_radio_buttons('filters[translated]',
				array(
					'yes' => 'Yes',
					'no' => 'No',
					'either' => 'Either',
				), gp_array_get( $filters, 'translated', 'either' ) );
			?>			
		</dd>		

 		<dt><label for="filters[status]">Status:</label></dt>
		<dd>
			<?php echo gp_radio_buttons('filters[status]', //TODO: show only these, which user is allowed to see afterwards
				array(
					'+current' => 'Current',
					'-old' => 'Approved, but obsoleted by another string',
					'-waiting' => 'Waiting',
					'-rejected' => 'Rejected',
					'either' => 'Any',
				), gp_array_get( $filters, 'status', '+current' ) );
			?>			
		</dd>		
		
		<dd><input type="submit" value="Filter" /></dd>
	</dl>
	<dl class="filters-expanded sort hidden">		
		<dt>By:</dt>
		<dd>
		<?php echo gp_radio_buttons('sort[by]',
			array(
				'date_added' => 'Date added',
				'original' => 'Original string',
				'translation' => 'Translation',
				'priority' => 'Priority',
				'random' => 'Random',
			), gp_array_get( $sort, 'by', 'priority' ) );
		?>
		</dd>
		<dt>How:</dt>
		<dd>
		<?php echo gp_radio_buttons('sort[how]',
			array(
				'asc' => 'Ascending',
				'desc' => 'Descending',
			), gp_array_get( $sort, 'how', 'desc' ) );
		?>
		</dd>
		<dd><input type="submit" value="Sort" /></dd>
	</dl>	
</form>

<?php echo gp_pagination( $page, $per_page, $total_translations_count ); ?>
<table id="translations" class="translations clear">
	<tr>
		<th>#</th>
		<th class="original"><?php _e('Original string'); ?></th>
		<th class="translation"><?php _e('Translation'); ?></th>
		<th><?php _e('Actions'); ?></th>
	</tr>
<?php foreach( $translations as $t ):
		$class = str_replace( array( '+', '-' ), '', $t->translation_status );
		if ( !$class )  $class = 'untranslated';
?>
	<tr class="preview <?php echo $parity().' status-'.$class ?>" id="preview-<?php echo $t->row_id ?>" row="<?php echo $t->row_id; ?>">
		<td><?php echo $i++; ?></td>
		<td class="original">			
			<?php echo esc_translation( $t->singular ); ?>
			<?php if ( $t->context ): ?>
			<span class="context" title="<?php printf( __('Context: %s'), esc_html($t->context) ); ?>"><?php echo esc_html($t->context); ?></span>
			<?php endif; ?>

		</td>
		<td class="translation">
		<?php
			$missing_text = "<span class='missing'>Missing</span>";
			if ( !count( array_filter( $t->translations ) ) ):
				echo $missing_text;
			elseif ( !$t->plural ):
				echo $t->translations[0];
			else: ?>				
			<ul>
				<?php
					foreach( $t->translations as $translation ):
				?>			
					<li><?php echo $translation? esc_translation( $translation ) : $missing_text; ?></li>
				<?php
					endforeach;
				?>
			</ul>
		<?php
			endif;
		?>
		</td>
		<td class="actions">
			<a href="#" row="<?php echo $t->row_id; ?>" class="action edit"><?php $can_edit? _e('More') : _e('View'); echo $t->row_id; ?></a>
		</td>
	</tr>
	<tr class="editor" id="editor-<?php echo $t->row_id; ?>" row="<?php echo $t->row_id; ?>">
		<td colspan="4">
			<div class="strings">
			<?php if ( !$t->plural ): ?>
			<p class="original"><?php echo esc_translation($t->singular); ?></p>
			
			<?php textareas( $t, $can_edit ); ?>
			<?php else: ?>
				<!--
					TODO: use the correct number of plurals
					TODO: dynamically set the number of rows
				-->				
				<p><?php printf(__('Singular: %s'), '<span class="original">'.esc_translation($t->singular).'</span>'); ?></p>
				<?php textareas( $t, $can_edit, 0 ); ?>
				<p class="clear"><?php printf(__('Plural: %s'), '<span class="original">'.esc_translation($t->plural).'</span>'); ?></p>
				<?php textareas( $t, $can_edit, 1 ); ?>				
			<?php endif; ?>
			</div>
			<div class="meta">
				<h3>Meta</h3>
				<dl>
					<dt>Status:</dt>
					<dd><?php echo $t->translation_status? $t->translation_status : 'untranslated'; ?></dd>
				<?php if ( $can_approve ): ?>
					<?php if ( gp_startswith( $t->translation_status, '-' ) ): ?>
					<dd><a href="#" tabindex="-1">Make current</a></dd>
					<?php endif; ?>
					<?php if ( $t->translation_status ): ?>
					<dd><a href="#" tabindex="-1">Reject</a></dd>
					<?php endif; ?>
				<?php endif; ?>
				</dl>
				<dl>					
					<dt>Priority:</dt>
					<dd><?php echo esc_html($t->priority); ?></dd>
				</dl>	
				
				<?php if ( $t->context ): ?>
				<dl>					
					<dt>Context:</dt>
					<dd><span class="context"><?php echo esc_translation($t->context); ?></span></dd>
				</dl>	
				<?php endif; ?>				
				<?php if ( $t->extracted_comment ): ?>
				<dl>					
					<dt>Comment:</dt>
					<dd><?php echo make_clickable( esc_translation($t->extracted_comment) ); ?></dd>
				</dl>
				<?php endif; ?>
				<?php if ( $t->translation_added ): ?>
				<dl>
					<dt>Date added:</dt>
					<dd><?php echo $t->translation_added; ?></dd>				
				</dl>
				<?php endif; ?>
				<?php references( $project, $t ); ?>
			</div>
			<div class="actions">
			<?php if ( $can_edit ): ?>
				<button class="ok">
					<?php echo $can_approve? 'Add translation &rarr;' : 'Suggest translation &rarr;'; ?>
				</button>
			<?php endif; ?>				
				<a href="#" class="close"><?php _e('Close'); ?></a>
			</div>
		</td>
	</tr>
<?php endforeach; ?>
<?php
	if ( !$translations ):
?>
	<tr><td colspan="4">No translations were found!</td></tr>
<?php
	endif;
?>
</table>
<?php echo gp_pagination( $page, $per_page, $total_translations_count ); ?>
<p class="clear">
	<?php gp_link( gp_url_project( $project, array( $locale->slug, $translation_set->slug, 'import-translations' ) ), __('Import translations') ); ?> &bull;
	<?php gp_link( gp_url_project( $project, array( $locale->slug, $translation_set->slug, 'export-translations' ) ), __('Export translations') ); ?>
</p>
<script type="text/javascript" charset="utf-8">
	$gp.showhide('#upper-filters-toolbar a.sort', 'Sort &darr;', 'Sort &uarr;', '#upper-filters-toolbar dl.sort', '#sort\\[by\\]');
	$gp.showhide('#upper-filters-toolbar a.filter', 'Filter &darr;', 'Filter &uarr;', '#upper-filters-toolbar dl.filters', '#filters\\[term\\]');
</script>
<?php gp_tmpl_footer(); ?>