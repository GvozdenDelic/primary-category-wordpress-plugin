<?php
/**
* Plugin Name: Primary category by Gvozden
* Description: Primary category plugin developed by Gvozden
* Version: 1.0
* Author: Gvozden Delić
* Author URI: gvozdendelic1@gmail.com
**/

if ( ! defined( 'ABSPATH' ) ) { exit; } 

function primary_category_meta_box() {
    $args = array(
        'public'   => true,
        '_builtin' => false
     );
      
    $custom_post_types = get_post_types( $args, 'names', 'and' );

    add_meta_box(
        'primary_category_meta_box', 
        'Primary Category', 
        'primary_category_meta_box_callback', 
        ['post', $custom_post_types],
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'primary_category_meta_box');

function primary_category_meta_box_callback($post) {
    $primary_category = get_post_meta($post->ID, '_primary_category', true);
    $is_custom_post_type = ($post->post_type != 'posts');

    if($is_custom_post_type) {
        $taxonomies = get_object_taxonomies($post->post_type);
        $categories = get_the_terms( $post->ID, $taxonomies[0]);
    } else {
        $categories = get_the_category($post->ID);
    }
    
    if($categories) {
        // If a post is assigned to only one category, that category is marked as primary
        if (count($categories) == 1) {
            $primary_category = $categories[0]->term_id;
        }
        
        echo '<select name="primary_category" data-post-type="' . $post->post_type .'">';
            foreach ($categories as $category) {
                // Added escaping attributes and HTML for better security
                echo '<option value="' . esc_attr($category->term_id) . '" ' . selected($primary_category, $category->term_id, false) . '>' . esc_html($category->name) . '</option>';
            }
        echo '</select>';
    } else {
        echo '<select name="primary_category" disabled><option>'.__( 'Please, select at least one category', 'primary-category-plugin').'</option></select>';
    }
}

function primary_category_save_meta_box($post_id) {
    if (isset($_POST['primary_category'])) {
        update_post_meta($post_id, '_primary_category', sanitize_text_field($_POST['primary_category']));
    }
}
add_action('save_post', 'primary_category_save_meta_box');

/* I added frontend JS to handle updating primary categories list with available categories *
* I also added a CSS file. Styles are based on category styles from "twentytwentyfour" theme */
function enqueue_primary_category_script() {
    wp_enqueue_script('primary-category-js', plugins_url('js/primary-category.js', __FILE__), false, true);
}
add_action('admin_enqueue_scripts', 'enqueue_primary_category_script');

// I used 'wp_enqueue_scripts' action here, because CSS is needed on frontend, while 'primary-category-js' is only needed in backend
function wpdocs_theme_name_scripts() {
	wp_enqueue_style( 'primary-category-css', plugins_url('css/primary-category.css', __FILE__) );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts' );

function register_primary_category_page_template($templates) {
    $templates['templates/page-category-primary.php'] = 'Primary Category Template';
    return $templates;
}
add_filter('theme_page_templates', 'register_primary_category_page_template');

function load_primary_category_page_template($template) {
    if (get_page_template_slug() == 'templates/page-category-primary.php') {
        $template = plugin_dir_path(__FILE__) . 'templates/page-category-primary.php';
    }
    return $template;
}
add_filter('template_include', 'load_primary_category_page_template');

function add_query_vars($vars) { 
    $vars[] = 'primary_category';
    $vars[] = 'type';
    return $vars; 
} 
add_filter('query_vars', 'add_query_vars');

?>
