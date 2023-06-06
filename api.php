<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bizink_bace = "https://bizinkcontent.com/wp-json/wp/v2/";
$bizinkcontent_client = array(
    'timeout' => 120,
    'httpversion' => '1.1',
    'headers' => array(
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
      'Authorization' => 'Bearer OSEgUIcnTnaLAPTjkbVtwrwZzMqkpywTIYzZMnpB'
    )
);
function bizinkblogs_getCategories(){
    global $bizink_bace,$bizinkcontent_client;
    if(get_transient('bizpress_blog_categories')){
        return get_transient('bizpress_blog_categories');
    }
    $response = wp_remote_get($bizink_bace.'categories',$bizinkcontent_client);
    $status = wp_remote_retrieve_response_code($response);
    if($status < 400){
        $body = json_decode(wp_remote_retrieve_body( $response ));
        set_transient('bizpress_blog_categories', $body, DAY_IN_SECONDS);
        return $body;
    }
    else{
        return array(
            'status' => 'error',
            'type' => 'fetch_error_categories',
            'message' => 'There was an error fetching the categories.'
        );
    }
}

function bizinkblogs_getPosts($args = ['status' => 'publish','per_page' => 8]){
    global $bizink_bace,$bizinkcontent_client;
    $postUrl = add_query_arg($args,wp_slash($bizink_bace.'posts'));
    $response = wp_remote_get($postUrl ,$bizinkcontent_client);
    
    $status = wp_remote_retrieve_response_code($response);
    if($status < 400){
        $body = json_decode(wp_remote_retrieve_body( $response ));
        $totalPosts = wp_remote_retrieve_header($response,'X-WP-Total');
        $totalPages = wp_remote_retrieve_header($response,'X-WP-TotalPages');
        return array(
            'status' => 'success',
            'type' => 'get_post',
            'message' => 'Success here are the blog post you requested',
            'totalPosts' => $totalPosts,
            'totalPages' => $totalPages,
            'posts' => $body,
            'url' => $postUrl
        );
    }
    else{
        return array(
            'status' => 'error',
            'type' => 'fetch_error_posts',
            'message' => 'There was an error fetching the posts.'
        );
    }    
}

function bizpress_blogs_ajax(){
    $search = isset($_POST['search']) ? $_POST['search'] : false;
    $category = isset($_POST['category']) ? $_POST['category'] : false;
    $page = isset($_POST['blogpage']) ? $_POST['blogpage'] : 1;
    $args = array(
        'status' => 'publish',
        'per_page' => 8,
        'page' => $page
    );
    if($search) array_merge(array('search' => $search));
    if($category) array_merge(array('categories' => $category));
    wp_send_json(bizinkblogs_getPosts($args));
}
add_action( 'wp_ajax_bizpressblogs', 'bizpress_blogs_ajax' );

function bizpress_blogs_addarticle_ajax(){
    global $bizink_bace,$bizinkcontent_client;
    $bizpressPostID = $_POST['bizpressPostID'] ? $_POST['bizpressPostID'] : false;
    if($bizpressPostID){
        $previousPosts = get_option('bizpress_previousPosts',[]);
        $postUrl = wp_slash($bizink_bace.'posts/'.$_POST['bizpressPostID']);
        $response = wp_remote_get($postUrl ,$bizinkcontent_client);
        $status = wp_remote_retrieve_response_code($response);
        if($status < 400){
            $body = json_decode(wp_remote_retrieve_body( $response ));
            // Process and add blog post

            if(empty($body->title->rendered) || empty($body->content->rendered)){
                wp_send_json(array(
                    'status' => 'error',
                    'type' => 'add_error_post',
                    'message' => 'Unable to find post to add'
                ));
            }
            else{
                $post = wp_insert_post(array(
                    'post_title' => $body->title->rendered,
                    'post_content' => $body->content->rendered,
                    'post_author'  => get_current_user_id(),
                    'meta_input' => array(
                        'bizpress_id' => $body->id,
                        'bizpress_slug' => $body->slug
                    )
                ));
    
                if(!is_wp_error($post)){
                    //the post is valid
                    array_push($previousPosts,intval($_POST['bizpressPostID']));
                    update_option('bizpress_previousPosts',$previousPosts);
                    wp_send_json(array(
                        'status' => 'success',
                        'type' => 'add_post',
                        'message' => 'Success the post has been added to you blog',
                        'post_id' => $post,
                        'post' => get_post($post)
                    ));
                }
                else{
                    //there was an error in the post insertion,
                    wp_send_json(array(
                        'status' => 'error',
                        'type' => 'add_error_post',
                        'message' => $post->get_error_message()
                    ));
                }
            }
            
        }
        else{
            wp_send_json(array(
                'status' => 'error',
                'type' => 'fetch_error_post',
                'message' => 'There was an error fetching the post data.'
            ));
        }
    }
    else{
        wp_send_json(array(
            'status' => 'error',
            'type' => 'no_post_id',
            'message' => 'Do data receved to the post you wished to add.'
        ));
    }
}
add_action( 'wp_ajax_bizpressblogsarticle', 'bizpress_blogs_addarticle_ajax' );