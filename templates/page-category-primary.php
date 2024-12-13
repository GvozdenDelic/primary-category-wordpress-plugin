<?php
/**
 * Template Name: Primary Category Page Template
 */

get_header();


// If post_type query var doesn't exit, default value will be used. I have used 'sanitize_text_field' method for security.
$primary_category = get_query_var('primary_category') ? sanitize_text_field(get_query_var('primary_category')) : '';
$post_type = get_query_var('type') ? sanitize_text_field(get_query_var('type')) : 'category';
$category = get_term_by('name', $primary_category, $post_type);

if ($category && $primary_category) {
    $post_types_args = array(
        'public'   => true,
        '_builtin' => false
     );
      
    $custom_post_types = get_post_types( $post_types_args, 'names', 'and' ); // Same logic for getting all custom post types as in main file: primary-category.php

    $category_id = $category->term_id;
    $args = [
        'meta_key' => '_primary_category',
        'meta_value' => $category_id,
        'post_type' => array_merge(['post'], $custom_post_types)
    ];

    $query = new WP_Query($args);

    echo '<main class="wp-block-group alignfull has-global-padding is-layout-constrained wp-block-group-is-layout-constrained primary-category-gd">';
    if ($query->have_posts()) {
        echo '<h1 class="alignwide wp-block-query-title primary-category-gd__title">'.__( 'Primary Category: ', 'primary-category-plugin' ).$category->name.'</h1>';
        
        /* I have copied this HTML with CSS classes from 'twentytwentyfour' category page. * 
        * I have added my aditional styles with classes starting with 'primary-category-gd' */
        echo '<div class="wp-block-query alignwide is-layout-flow wp-block-query-is-layout-flow">';
            echo '<div class="wp-block-group is-layout-flow wp-block-group-is-layout-flow primary-category-gd__block-group">';
                echo '<div class="columns-3 alignfull wp-block-post-template is-layout-grid wp-container-core-post-template-is-layout-1 wp-block-post-template-is-layout-grid primary-category-gd__grid">';
                    while ($query->have_posts()) {
                        $query->the_post();
                        // Ensure we only print posts that have this category set as their primary
                        if(get_post_meta( $post->ID, "_primary_category", true) == $category->term_id) {
                            echo '<article class="wp-block-post post status-publish format-standard hentry primary-category-gd__post">';
                                echo '<div class="wp-block-group is-vertical is-nowrap is-layout-flex wp-container-core-group-is-layout-8 wp-block-group-is-layout-flex">';
                                    echo the_title('<h2>', '</h2>');
                                    echo the_excerpt();
                                echo '</div>';
                            echo '</article>';
                        }
                    }
                echo '</div>';
            echo '</div>';
        echo '</div>';

        wp_reset_postdata();
    } else {
        echo '<p>'.__( 'This primary category list has no posts :(', 'primary-category-plugin' ).'</p>';
    }
} else {
    echo '<p>'.__( 'This category doesn\'t exist.', 'primary-category-plugin' ).'</p>';
}
echo '</main>';

get_footer();
?>

