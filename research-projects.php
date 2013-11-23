<?php
/**
 * @package   research_projects
 * @author    Marco Godínez <markotom@gmail.com>
 * @license   GPL-2.0+
 * @link      http://github.com/filosunam/research-projects
 * @copyright Facultad de Filosofía y Letras, UNAM
 *
 * @wordpress-plugin
 * Plugin Name: Research Projects
 * Plugin URI:  http://github.com/filosunam/research-projects
 * Description: Research projects
 * Version:     0.0.1
 * Author:      Marco Godínez <markotom@gmail.com>
 * Author URI:  http://github.com/markotom
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-research-projects.php' );

// Register hooks that are fired when the plugin is activated or deactivated.
// When the plugin is deleted, the uninstall.php file is loaded.
register_activation_hook( __FILE__, array( 'Research_Projects', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Research_Projects', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Research_Projects', 'get_instance' ) );

require_once( plugin_dir_path( __FILE__ ) . 'includes/research-projects-templates.php' );
