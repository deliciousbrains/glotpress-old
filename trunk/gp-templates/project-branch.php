<?php
gp_title( sprintf( __( 'Branch Project %s &lt; GlotPress' ),  $project->name ) );
gp_breadcrumb_project( $project );
gp_tmpl_header();
?>
<h2><?php echo wptexturize( sprintf( __('Branch project "%s"'), esc_html( $project->name ) ) ); ?></h2>
<p><?php _e('Here you can branch out this project: everything will be duplicated into a new project for you.'); ?></p>
<form action="<?php echo esc_url( gp_url_current() ); ?>" method="post">
	<dt><label for="project[name]"><?php _e('New branch name'); ?></label></dt>
	<dd><input type="text" name="project[name]" value="" placeholder="type tag project name here" id="project[name]"></dd>

	<!-- TODO: make slug edit WordPress style -->
	<dt><label for="project[slug]"><?php _e('New Slug'); ?></label></dt>
	<dd>
		<input type="text" name="project[slug]" value="" id="project[slug]">
		<small><?php _e('If you leave the slug empty, it will be derived from the name.'); ?></small>
	</dd>
	<dt><label for="project[description]"><?php _e('Description'); ?></label> <span class="ternary"><?php _e('can include HTML'); ?></span></dt>
	<dd><textarea name="project[description]" rows="4" cols="40" id="project[description]"><?php echo esc_html( $project->description ); ?></textarea></dd>
	<dt><label for="project[source_url_template]"><?php _e('Source file URL'); ?></label></dt>
	<dd>
		<input type="text" value="<?php echo esc_html( $project->source_url_template ); ?>" name="project[source_url_template]" id="project[source_url_template]" style="width: 30em;" />
		<span class="ternary"><?php _e('Public URL to a source file in the project. You can use <code>%file%</code> and <code>%line%</code>. Ex. <code>http://trac.example.org/browser/%file%#L%line%</code>'); ?></span>
	</dd>
	<div id="preview"></div>
	<input type="hidden" value="<?php echo esc_html( $project->parent_project_id ); ?>" name="project[parent_project_id]" id="project[parent_project_id]" />
	<p><input type="submit" name="submit" value="<?php echo esc_attr( __('Branch project') ); ?>" id="submit" /></p>
</form>

<?php gp_tmpl_footer();
