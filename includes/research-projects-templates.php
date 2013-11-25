<?php
/**
 * Research Template
 *
 * @since 1.0.0
 * 
 */

function research_template($template) {

  // Show template if is singular
  if ( is_singular( 'research-project' ) ) {

    // Default template from plugin
    $template = plugin_dir_path( __FILE__ ) . '../templates/single.php';

    // Override template from theme
    if( $template_from_theme = locate_template( 'single-research.php' ) ) {
      $template = $template_from_theme;
    }
    
  }

  // Show template if is archive
  if ( is_tax( 'research_category' ) ) {

    // Default template from plugin
    $template = plugin_dir_path( __FILE__ ) . '../templates/archive.php';

    // Override template from theme
    if( $template_from_theme = locate_template( 'archive-research.php' ) ) {
      $template = $template_from_theme;
    }

  }


  // Return default template
  return $template;

}

add_filter('template_include', 'research_template', 99);
