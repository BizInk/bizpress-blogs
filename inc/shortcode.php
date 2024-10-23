<?php 
// Shortcode
function bizpress_blog_shortcode( $atts ) {

	// Attributes
	$atts = shortcode_atts(array( 'id' => null ),$atts,'bizpress_blog');
    if(empty($atts['id'])){
        return __('ID is not set for this post on the Bizpress Blog Shortcode','bizink-client');
    }
    else{
        $post = bizinkblogs_getSinglePost($atts['id']);
        $content = isset($post->content->rendered) ? $post->content->rendered : '';
        return '<div class="bizpress-blog">'.$content.'</div>';
    }

}
add_shortcode( 'bizpress_blog', 'bizpress_blog_shortcode' );