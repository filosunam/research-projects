<?php
/**
 * Research Page Template
 *
 * @since 1.0.0
 * 
 */
function research_page_template($template) {

  if ( is_singular( 'research-project' ) ) {
    $template_from_theme  = get_template_directory() . '/single-research.php';
    $template_from_plugin = plugin_dir_path( __FILE__ ) . '../templates/single.php';

    if (file_exists($template_from_theme)) {
      return $template_from_theme;
    }
    
    return $template_from_plugin;
  }
  
  return $template;
}

add_filter('template_include', 'research_page_template', 99);
