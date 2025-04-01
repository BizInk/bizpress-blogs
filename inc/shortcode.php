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
        $content = isset($post['post']->content->rendered) ? $post['post']->content->rendered : '';
        $publisherName = isset($post['publisher']) ? sprintf(__('This article was writen by: %s','bizink-client'),$post['publisher']->name) : '';
        return '<div class="bizpress-publisher">'.$publisherName.'</div><br/><div class="bizpress-blog" oncopy="return false" oncut="return false" onpaste="return false">'.$content.'</div>';
    }

}
add_shortcode( 'bizpress_blog', 'bizpress_blog_shortcode' );