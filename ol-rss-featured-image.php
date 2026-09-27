<?php
/**
 * Plugin Name: Outcome Labs RSS Featured Image
 * Description: Adds the Featured Image to the RSS feed (inline content + enclosure tag) so readers like Feedly can display it.
 * Author: Outcome Labs
 * Version: 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Prepend the Featured Image to the RSS content/excerpt.
 */
function ol_add_featured_image_to_rss($content) {
    global $post;

    if (!$post || !has_post_thumbnail($post->ID)) {
        return $content;
    }

    $image_html = '<div>' . get_the_post_thumbnail($post->ID, 'full') . '</div>';
    return $image_html . $content;
}
add_filter('the_excerpt_rss', 'ol_add_featured_image_to_rss');
add_filter('the_content_feed', 'ol_add_featured_image_to_rss');

/**
 * Add an <enclosure> tag for the Featured Image so feed readers
 * (Feedly included) can pick it up as the item's cover image.
 */
function ol_add_enclosure_for_featured_image() {
    global $post;

    if (!$post || !has_post_thumbnail($post->ID)) {
        return;
    }

    $image_id   = get_post_thumbnail_id($post->ID);
    $image_url  = wp_get_attachment_url($image_id);
    $image_path = get_attached_file($image_id);
    $filesize   = ($image_path && file_exists($image_path)) ? filesize($image_path) : 0;
    $mime_type  = get_post_mime_type($image_id);

    if ($image_url) {
        printf(
            '<enclosure url="%s" length="%d" type="%s" />' . "\n",
            esc_url($image_url),
            (int) $filesize,
            esc_attr($mime_type)
        );
    }
}
add_action('rss2_item', 'ol_add_enclosure_for_featured_image');
