<?php
/**
 * Research Projects Class.
 *
 * @package Research_Projects
 * @author  Marco Godínez <markotom@gmail.com>
 */
class Research_Projects {

  /**
   * Plugin version, used for cache-busting of style and script file references.
   *
   * @since   1.0.0
   *
   * @var     string
   */
  const VERSION = '0.0.1';

  /**
   * Unique identifier for your plugin.
   *
   * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
   * match the Text Domain file header in the main plugin file.
   *
   * @since    1.0.0
   *
   * @var      string
   */
  protected $plugin_slug = 'research-projects';

  /**
   * Instance of this class.
   *
   * @since    1.0.0
   *
   * @var      object
   */
  protected static $instance = null;

  /**
   * Slug of the plugin screen.
   *
   * @since    1.0.0
   *
   * @var      string
   */
  protected $plugin_screen_hook_suffix = 'research-project';

  /**
   * Slug of research projects custom post type.
   *
   * @since    1.0.0
   *
   * @var      string
   */
  protected $custom_post_type_slug = 'research-project';

  /**
   * Initialize the plugin by setting localization, filters, and administration functions.
   *
   * @since     1.0.0
   */
  private function __construct() {

    // Load plugin text domain
    add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

    // Activate plugin when new blog is added
    add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

    // Add the options page and menu item.
    // add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

    // Add an action link pointing to the options page. TODO: Rename "plugin-name.php" to the name your plugin
    // $plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'plugin-name.php' );
    // add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

    // Load admin style sheet and JavaScript.
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

    // Load public-facing style sheet and JavaScript.
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );


    // Define custom functionality. Read more about actions and filters: http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
    // add_action( 'TODO', array( $this, 'action_method_name' ) );
    // add_filter( 'TODO', array( $this, 'filter_method_name' ) );
    
    add_action( 'init', array( $this, 'register_taxonomies' ) );
    add_action( 'init', array( $this, 'register_post_type' ) );
    add_action( 'save_post', array( $this, 'save_post_meta' ), 1, 2 );

  }

  /**
   * Return an instance of this class.
   *
   * @since     1.0.0
   *
   * @return    object    A single instance of this class.
   */
  public static function get_instance() {

    // If the single instance hasn't been set, set it now.
    if ( null == self::$instance ) {
      self::$instance = new self;
    }

    return self::$instance;
  }

  /**
   * Fired when the plugin is activated.
   *
   * @since    1.0.0
   *
   * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
   */
  public static function activate( $network_wide ) {
    if ( function_exists( 'is_multisite' ) && is_multisite() ) {
      if ( $network_wide  ) {
        // Get all blog ids
        $blog_ids = self::get_blog_ids();

        foreach ( $blog_ids as $blog_id ) {
          switch_to_blog( $blog_id );
          self::single_activate();
        }
        restore_current_blog();
      } else {
        self::single_activate();
      }
    } else {
      self::single_activate();
    }
  }

  /**
   * Fired when the plugin is deactivated.
   *
   * @since    1.0.0
   *
   * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
   */
  public static function deactivate( $network_wide ) {
    if ( function_exists( 'is_multisite' ) && is_multisite() ) {
      if ( $network_wide ) {
        // Get all blog ids
        $blog_ids = self::get_blog_ids();

        foreach ( $blog_ids as $blog_id ) {
          switch_to_blog( $blog_id );
          self::single_deactivate();
        }
        restore_current_blog();
      } else {
        self::single_deactivate();
      }
    } else {
      self::single_deactivate();
    }
  }

  /**
   * Fired when a new site is activated with a WPMU environment.
   *
   * @since    1.0.0
   *
   * @param int $blog_id ID of the new blog.
   */
  public function activate_new_site( $blog_id ) {
    if ( 1 !== did_action( 'wpmu_new_blog' ) )
      return;

    switch_to_blog( $blog_id );
    self::single_activate();
    restore_current_blog();
  }

  /**
   * Get all blog ids of blogs in the current network that are:
   * - not archived
   * - not spam
   * - not deleted
   *
   * @since    1.0.0
   *
   * @return  array|false The blog ids, false if no matches.
   */
  private static function get_blog_ids() {
    global $wpdb;

    // get an array of blog ids
    $sql = "SELECT blog_id FROM $wpdb->blogs
      WHERE archived = '0' AND spam = '0'
      AND deleted = '0'";
    return $wpdb->get_col( $sql );
  }

  /**
   * Fired for each blog when the plugin is activated.
   *
   * @since    1.0.0
   */
  private static function single_activate() {
    // TODO: Define activation functionality here
    flush_rewrite_rules();
  }

  /**
   * Fired for each blog when the plugin is deactivated.
   *
   * @since    1.0.0
   */
  private static function single_deactivate() {
    // TODO: Define deactivation functionality here
    flush_rewrite_rules();
  }

  /**
   * Load the plugin text domain for translation.
   *
   * @since    1.0.0
   */
  public function load_plugin_textdomain() {

    $domain = $this->plugin_slug;
    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

    load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
    load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
  }

  /**
   * Register Research Projects Post Type
   *
   * @since    1.0.0
   */
  public function register_post_type() {
    register_post_type(
      $this->custom_post_type_slug,
      array(
        'labels' => array(
          'name'                => __('Research projects', $this->plugin_slug),
          'singular_name'       => __('Research project', $this->plugin_slug),
          'add_new'             => __('Add new', $this->plugin_slug),
          'add_new_item'        => __('Add new project', $this->plugin_slug),
          'edit_item'           => __('Edit project', $this->plugin_slug),
          'new_item'            => __('New project', $this->plugin_slug),
          'view_item'           => __('View project', $this->plugin_slug),
          'search_items'        => __('Search research projects', $this->plugin_slug),
          'not_found'           => __('Not found research projects', $this->plugin_slug),
          'not_found_in_trash'  => __('Not found research projects in trash', $this->plugin_slug)
        ),
        'description'           => __('Research projects', $this->plugin_slug),
        'public'                => true,
        'rewrite' => array(
          'slug' => _x('projects', 'Slug URL (single)', $this->plugin_slug),
          'with_front' => false
        ),
        'exclude_from_search'   => false,
        'publicy_queryable'     => true,
        'query_var'             => false,
        'show_ui'               => true,
        'show_in_nav_menus'     => false,
        'register_meta_box_cb'  => array( $this, 'register_meta_boxes' ),
        'capabilities' => array(
          'edit_post'           => 'edit_theme_options',
          'edit_posts'          => 'edit_theme_options',
          'edit_others_posts'   => 'edit_theme_options',
          'publish_posts'       => 'edit_theme_options',
          'read_post'           => 'read',
          'read_private_posts'  => 'read',
          'delete_post'         => 'edit_theme_options'
        ),
        'taxonomies'            => array('research_category'),
        'has_archive'           => true,
        'hierarchical'          => false,
        'supports' => array( 'title', 'editor', 'revisions', 'thumbnail' )
      )
    );
  }

  /**
   * Register Taxonomies of Research Projects
   *
   * @since    1.0.0
   */
  public function register_taxonomies () {
    register_taxonomy('research_category', $this->custom_post_type_slug, array(
      'hierarchical' => true,
      'labels' => array(
        'name' => _x( 'Categories', 'taxonomy general name' ),
        'singular_name' => _x( 'Category', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Categories' ),
        'all_items' => __( 'All Categories' ),
        'parent_item' => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category' ),
        'edit_item' => __( 'Edit Category' ),
        'update_item' => __( 'Update Category' ),
        'add_new_item' => __( 'Add New Category' ),
        'new_item_name' => __( 'New Category Name' ),
        'menu_name' => __( 'Categories' ),
      ),
      'show_ui' => true,
      'query_var' => true,
      'public' => true,
      'rewrite' => array(
        'slug' => _x('projects/category', 'Slug URL (archive)', $this->plugin_slug),
        'with_front' => false,
        'hierarchical' => true
      )
    ));
  }

  /**
   * Register Meta Boxes of Research Projects
   *
   * @since    1.0.0
   */
  public function register_meta_boxes() {

    // Add funding programs to custom type
    add_meta_box(
      'rp_funding_programs',
      __('Funding Programs', $this->plugin_slug),
      array($this, 'box_funding_programs'),
      $this->custom_post_type_slug,
      'side',
      'default'
    );
    
    // Add research products to custom type
    add_meta_box(
      'rp_research_products',
      __('Research products', $this->plugin_slug),
      array($this, 'box_research_products'),
      $this->custom_post_type_slug,
      'normal',
      'high'
    );

    // Add RSS link to custom type
    add_meta_box(
      'rp_rss_link',
      __('RSS Link', $this->plugin_slug),
      array($this, 'box_rss_link'),
      $this->custom_post_type_slug,
      'side',
      'default'
    );

  }

  /**
   * RSS Link (metabox)
   *
   * @since    1.0.0
   */
  public function box_rss_link() {
    global $post;
    
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $rp_rss_link = get_post_meta($post->ID, 'rp_rss_link', true);

    include_once( 'views/meta_boxes/rss-link.php' );
  }

  /**
   * Funding Programs (metabox)
   *
   * @since    1.0.0
   */
  public function box_funding_programs() {
    global $post;

    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $rp_funding_programs = get_post_meta($post->ID, 'rp_funding_programs', true);

    include_once( 'views/meta_boxes/funding-programs.php' );
  }

  /**
   * Research Products (metabox)
   *
   * @since    1.0.0
   */
  public function box_research_products() {
    include_once( 'views/meta_boxes/research-products.php' );
  }

  /**
   * Custom save post meta
   *
   * @since    1.0.0
   */
  public function save_post_meta($post_id, $post) {

    // Only users allowed to edit post
    if ( !current_user_can( 'edit_post', $post->ID )) {
      return $post->ID;
    }

    // Save
    if ( $_POST ) {

      // Verify nonce names
      if ( !wp_verify_nonce( $_POST['rsslink_noncename'], plugin_basename(__FILE__) )) {
        return $post->ID;
      }

      if ( !wp_verify_nonce( $_POST['rp_funding_programs_noncename'], plugin_basename(__FILE__) )) {
        return $post->ID;
      }

      // RSS Link
      if(get_post_meta($post->ID, 'rp_rss_link', false)) {
        update_post_meta($post->ID, 'rp_rss_link', $_POST['rp_rss_link']);
      } else {
        add_post_meta($post->ID, 'rp_rss_link', $_POST['rp_rss_link']);
      }

      // Funding Programs

      $funding_programs = array_filter($_POST['rp_funding_programs']);

      if(get_post_meta($post->ID, 'rp_funding_programs', false)) {
        update_post_meta($post->ID, 'rp_funding_programs', $funding_programs);
      } else {
        add_post_meta($post->ID, 'rp_funding_programs', $funding_programs);
      }
      
    }

  }

  /**
   * Register and enqueue admin-specific style sheet.
   *
   * @since     1.0.0
   *
   * @return    null    Return early if no settings page is registered.
   */
  public function enqueue_admin_styles() {

    if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
      return;
    }

    $screen = get_current_screen();
    if ( $screen->id == $this->plugin_screen_hook_suffix ) {
      wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), self::VERSION );
    }

  }

  /**
   * Register and enqueue admin-specific JavaScript.
   *
   * @since     1.0.0
   *
   * @return    null    Return early if no settings page is registered.
   */
  public function enqueue_admin_scripts() {

    if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
      return;
    }

    $screen = get_current_screen();
    if ( $screen->id == $this->plugin_screen_hook_suffix ) {
      wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery', 'backbone' ), self::VERSION );
    }

  }

  /**
   * Register and enqueue public-facing style sheet.
   *
   * @since    1.0.0
   */
  public function enqueue_styles() {
    wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), self::VERSION );
  }

  /**
   * Register and enqueues public-facing JavaScript files.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
  }

  /**
   * Register the administration menu for this plugin into the WordPress Dashboard menu.
   *
   * @since    1.0.0
   */
  public function add_plugin_admin_menu() {

    /*
     * TODO:
     *
     * Change 'Page Title' to the title of your plugin admin page
     * Change 'Menu Text' to the text for menu item for the plugin settings page
     * Change 'manage_options' to the capability you see fit (http://codex.wordpress.org/Roles_and_Capabilities)
     */
    $this->plugin_screen_hook_suffix = add_plugins_page(
      __( 'Page Title', $this->plugin_slug ),
      __( 'Menu Text', $this->plugin_slug ),
      'manage_options',
      $this->plugin_slug,
      array( $this, 'display_plugin_admin_page' )
    );

  }

  /**
   * Render the settings page for this plugin.
   *
   * @since    1.0.0
   */
  public function display_plugin_admin_page() {
    include_once( 'views/admin.php' );
  }

  /**
   * Add settings action link to the plugins page.
   *
   * @since    1.0.0
   */
  public function add_action_links( $links ) {

    return array_merge(
      array(
        'settings' => '<a href="' . admin_url( 'plugins.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
      ),
      $links
    );

  }

  /**
   * NOTE:  Actions are points in the execution of a page or process
   *        lifecycle that WordPress fires.
   *
   *        WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
   *        Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
   *
   * @since    1.0.0
   */
  public function action_method_name() {
    // TODO: Define your action hook callback here
  }

  /**
   * NOTE:  Filters are points of execution in which WordPress modifies data
   *        before saving it or sending it to the browser.
   *
   *        WordPress Filters: http://codex.wordpress.org/Plugin_API#Filters
   *        Filter Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
   *
   * @since    1.0.0
   */
  public function filter_method_name() {
    // TODO: Define your filter hook callback here
  }

}
