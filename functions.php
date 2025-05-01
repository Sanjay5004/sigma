<?php
/**
 * Sigma- Webnovel Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package sigma-_webnovel_theme
 */

// Custom menu order
add_filter('custom_menu_order', '__return_true');

add_filter('menu_order', 'custom_menu_order');

function custom_menu_order() {
    return array(
        'index.php',
        'edit.php?post_type=novel',
        'edit.php?post_type=chapter',
        'edit.php',
        'edit.php?post_type=page',
        'edit-comments.php'
    );
}

// hide posts menu
function hide_edit_php_for_roles() 
     {
        remove_menu_page('edit.php'); // Hide "Posts" menu
    }

add_action('admin_menu', 'hide_edit_php_for_roles');



 // set parent for novel chapter
 function set_parent_for_chapters_using_taxonomy() {
    $args = array(
        'post_type' => 'chapter',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );
    $chapters = get_posts($args);

    foreach ($chapters as $chapter) {
        // Get the associated novel_selector term
        $terms = wp_get_post_terms($chapter->ID, 'novel_selector');
        if (!empty($terms) && !is_wp_error($terms)) 
        {
            // Find the corresponding novel post based on the term slug
            $novel_posts = get_posts(array(
                'post_type' => 'novel',
                'name' => $terms[0]->slug,
                'fields' => 'ids',
                'posts_per_page' => 1
            ));
            if ($novel_posts) {
                $parent_novel_id = $novel_posts[0];

                // Update the parent ID of the chapter
                wp_update_post(array(
                    'ID' => $chapter->ID,
                    'post_parent' => $parent_novel_id
                ));
            }
        }
    }
}

add_action('init', 'set_parent_for_chapters_using_taxonomy');



// Increment the post view count
function set_novel_post_views($postID) {
    $count_key = 'novel_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if ($count == '') {
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    } else {
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

// Track the post views
function track_novel_post_views($post_id) {
    if (!is_single()) return;
    if (empty($post_id)) {
        global $post;
        $post_id = $post->ID;
    }
    set_novel_post_views($post_id);
}
add_action('wp_head', 'track_novel_post_views');

// Display the post view count
function get_novel_post_views($postID) {
    $count_key = 'novel_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if ($count == '') {
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count . ' Views';
}


// Register Custom Post Type for Novels
function cptui_register_my_cpts_novel() {
    $labels = [
        "name" => esc_html__("Novels"),
        "singular_name" => esc_html__("Novel"),
        "add_new_item" => esc_html__("Add new novel"),
    ];

    $args = [
        "label" => esc_html__("Novels"),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "has_archive" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "hierarchical" => false,
        "rewrite" => [ 
            'slug' => 'novel',
            'with_front' => false,
            'hierarchical' => false,
        ],

        "menu_icon" => "dashicons-book",
        'taxonomies' => ['category', 'post_tag'],
        "supports" => ["title", "editor", "comments","thumbnail"],
    ];

    register_post_type("novel", $args);
}
add_action('init', 'cptui_register_my_cpts_novel');

// widget search & menu

function custom_header_widget_area() {
    register_sidebar( array(
        'name'          => __( 'Header Widget Area', 'your-theme-textdomain' ),
        'id'            => 'header-widget',
        'before_widget' => '<div class="header-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action( 'widgets_init', 'custom_header_widget_area' );




// In archive css
function add_body_classes($classes) {
    if (is_archive()) {
        $classes[] = 'custom-archive-page';
    }
    return $classes;
}
add_filter('body_class', 'add_body_classes');

// for categories and tags to show result on click
function include_custom_post_type_in_archives($query) {
    if (is_category() || is_tag()) {
        if ($query->is_archive() && empty($query->get('post_type')) ) {
            $query->set('post_type', array('post', 'novel'));
        }
    }
}
add_action('pre_get_posts', 'include_custom_post_type_in_archives');

// Register Custom Post Type for Chapters
function cptui_register_my_cpts_chapter() {
    $labels = [
        "name" => esc_html__("Chapters"),
        "singular_name" => esc_html__("Chapter"),
    ];

    $args = [
        "label" => esc_html__("Chapters"),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "has_archive" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "hierarchical" => true,
        "rewrite" => [ 
            'slug' => '%novel_selector%',
            'with_front' => false,
            'hierarchical' => true,
        ],
        "menu_icon" => "dashicons-media-default",
        "supports" => ["title", "editor",  "page-attributes"],
        "taxonomies" => ['novel_selector'], // Link taxonomy
    ];

    register_post_type("chapter", $args);
}
add_action('init', 'cptui_register_my_cpts_chapter');



// Register Custom Taxonomy for Novel Selector
function custom_taxonomy_novel_selector() {
    $labels = [
        'name' => _x('Novel Selectors', 'taxonomy general name'),
        'singular_name' => _x('Novel Selector', 'taxonomy singular name'),
        'search_items' => __('Search Novel Selectors'),
        'all_items' => __('All Novel Selectors'),
        'edit_item' => __('Edit Novel Selector'),
        'update_item' => __('Update Novel Selector'),
        'add_new_item' => __('Add New Novel Selector'),
        'new_item_name' => __('New Novel Selector Name'),
        'menu_name' => __('Novel Selector'),
    ];

    $args = [
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'novel-selector'],
    ];

    register_taxonomy('novel_selector', ['novel', 'chapter'], $args);
}
add_action('init', 'custom_taxonomy_novel_selector');

// filter chapter permalink
function filter_post_type_link($link, $post) {
    if ($post->post_type != 'chapter')
        return $link;

    if ($terms = get_the_terms($post->ID, 'novel_selector')) {
        $link = str_replace('%novel_selector%', array_pop($terms)->slug, $link);
    }
    return $link;
}
add_filter('post_type_link', 'filter_post_type_link', 10, 2);

// auhtor meta box
function add_author_metabox_to_custom_post_types() {
    add_meta_box(
        'authordiv', // Metabox ID
        __('Author'), // Title
        'post_author_meta_box', // Callback function
        'novel', // Your custom post type
        'normal', // Context
        'default' // Priority
    );
}
add_action('add_meta_boxes', 'add_author_metabox_to_custom_post_types');





// Enqueue font_awesome
function enqueue_font_awesome() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'enqueue_font_awesome');

// Enqueue js menu mobile
function sigma_custom_menu_script() {
    wp_enqueue_script('custom-menu-js', get_template_directory_uri() . '/js/custom-menu.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'sigma_custom_menu_script');

// Enqueue Bootstrap's CSS and JS
function enqueue_bootstrap_assets() {
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js', ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_bootstrap_assets');

// Enqueue single-novel.css for the single-novel.php template
function enqueue_custom_styles() {
    if (is_singular('novel')) { // Only enqueue for the 'novel' post type
        wp_enqueue_style('single-novel', get_template_directory_uri() . '/single-novel.css', array(), '1.0.0', 'all');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_styles');

// Enqueue single-chapter.css for single chapter post templates
function style_single_chapter() {
    if (is_singular('chapter')) { // Ensure this only runs on 'chapter' post type
        wp_enqueue_style('single-chapter', get_template_directory_uri() . '/single-chapter.css', array(), '1.0.0', 'all');
    }
}
add_action('wp_enqueue_scripts', 'style_single_chapter');



// Enqueue cards.js for article cards on front page
function wpdocs_theme_name_scripts() {
    if (is_front_page()) {
        wp_enqueue_script('artical', get_template_directory_uri() . '/js/artical.js', true);
    }
}
add_action('wp_enqueue_scripts', 'wpdocs_theme_name_scripts');


if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function sigma_webnovel_theme_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on sigma- webnovel theme, use a find and replace
		* to change 'sigma-webnovel-theme' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'sigma-webnovel-theme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	add_post_type_support('series', 'thumbnail');


	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'sigma-webnovel-theme' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'sigma_webnovel_theme_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'sigma_webnovel_theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function sigma_webnovel_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'sigma_webnovel_theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'sigma_webnovel_theme_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function sigma_webnovel_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'sigma-webnovel-theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'sigma-webnovel-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'sigma_webnovel_theme_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function sigma_webnovel_theme_scripts() {
	wp_enqueue_style( 'sigma-webnovel-theme-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'sigma-webnovel-theme-style', 'rtl', 'replace' );

	wp_enqueue_script( 'sigma-webnovel-theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'sigma_webnovel_theme_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}
