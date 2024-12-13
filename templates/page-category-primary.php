<?php
/**
 * Template Name: Primary Category Page Template
 */

get_header();

$primary_category = get_query_var('primary_category');
$post_type = get_query_var('type') ? get_query_var('type') : 'category';
$category = get_term_by('name', $primary_category, $post_type);

if ($category) {
    $post_types_args = array(
        'public'   => true,
        '_builtin' => false
     );
      
    $custom_post_types = get_post_types( $post_types_args, 'names', 'and' ); // Same logic for getting all custom post types as in primary-category.php

    $category_id = $category->term_id;
    $args = [
        'meta_key' => '_primary_category',
        'meta_value' => $category_id,
        'post_type' => array_merge(['post'], $custom_post_types)
    ];

    $query = new WP_Query($args);

    echo '<main class="wp-block-group alignfull has-global-padding is-layout-constrained wp-block-group-is-layout-constrained">';
    if ($query->have_posts()) {
        echo '<h1 style="line-height:1; padding-top:var(--wp--preset--spacing--50);" class="alignwide wp-block-query-title">'.__( 'Primary Category: ', 'primary-category-plugin' ).'<span>'.$category->name.'</span></h1>';
        echo '<div class="wp-block-query alignwide is-layout-flow wp-block-query-is-layout-flow">';
        echo '<div class="wp-block-group is-layout-flow wp-block-group-is-layout-flow" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:0;padding-bottom:var(--wp--preset--spacing--50);padding-left:0">';
        echo '<ul class="columns-3 alignfull wp-block-post-template is-layout-grid wp-container-core-post-template-is-layout-1 wp-block-post-template-is-layout-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));gap: var(--wp--preset--spacing--30);">';
        while ($query->have_posts()) {
            $query->the_post();
            if(get_post_meta( $post->ID, "_primary_category", true) == $category->term_id) { // Ensure we only print posts that have this primary category value
                echo '<li class="wp-block-post post status-publish format-standard hentry" style="list-style: none;">';
                echo '<div class="wp-block-group is-vertical is-nowrap is-layout-flex wp-container-core-group-is-layout-8 wp-block-group-is-layout-flex" style="margin-top:var(--wp--preset--spacing--20);padding-top:0">';
                echo the_title('<h2>', '</h2>');
                echo '<p>'.the_excerpt().'</p>';
                echo '</div>';
                echo '</li>';
            }
        }
        echo '</ul>';
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

