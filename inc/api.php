<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bizink_bace = "https://bizinkcontent.com/wp-json/wp/v2/";
$bizinkcontent_client = array(
    'timeout' => 20,
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

    $product = get_option('bizpress_product',array(
        "bizpress" => true,
        "bizpress_basic" => true,
        "bizpress_standard" => false,
    ));

    $luca = false;
    if(function_exists('luca')){
        $luca = true;
    }
    else if(in_array('bizpress-luca-2/bizpress-luca-2.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
        $luca = true;
    }

    $limit = 0;
    if($product->bizpress_standard == true){
        $limit = 20;
    }
    else if($luca == true){
        $limit = -1;
    }

    $currentCount = get_transient('bizpress_blog_count');
    if($currentCount == false){
        $currentCount = $limit;
        $start = new DateTime(date('Y-m-d'));
        $end = new DateTime(date('Y-m-t'));
        set_transient( 'bizpress_blog_count', $limit, ($end->getTimestamp() - $start->getTimestamp()) );
        unset($start,$end);
    }

    if($currentCount <= 0 && $limit != -1){
        return array(
            'status' => 'error',
            'type' => 'limit_reached',
            'message' => 'You have reached your blog post limit for this month.'
        );
    }

    $postUrl = add_query_arg($args,wp_slash($bizink_bace.'posts'));
    $response = wp_remote_get($postUrl,$bizinkcontent_client);
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
            'url' => $postUrl,
            'currentCount' => $currentCount,
            'limit' => $limit
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

function bizpress_blogs_get_regons(){
    global $bizink_bace,$bizinkcontent_client;
    if(get_transient('bizpress_blog_regions')){
        return get_transient('bizpress_blog_regions');
    }
    $regionUrl = add_query_arg(array( '_fields' => 'id,name,slug','count' ),wp_slash($bizink_bace.'region'));
    $response = wp_remote_get($regionUrl,$bizinkcontent_client);
    $status = wp_remote_retrieve_response_code($response);
    if($status < 400){
        $body = json_decode(wp_remote_retrieve_body( $response ));
        set_transient('bizpress_blog_regions', $body, DAY_IN_SECONDS * 5);
        return $body;
    }
    else{
        return array(
            'status' => 'error',
            'type' => 'fetch_error_regions',
            'message' => 'There was an error fetching the regions.'
        );
    }
}

function bizpress_blogs_ajax(){
    $page = isset($_REQUEST['blogpage']) ? $_REQUEST['blogpage'] : 1;
    $args = array(
        'status' => 'publish',
        'per_page' => 8,
        'page' => $page,
        'tax_relation' => 'AND',
        '_fields' => 'id,title,content,sticky,excerpt,featured_media,featured_image,date,modified,slug,categories,region',
    );
    $options = get_option('bizink-client_basic');
    if(empty($options['content_region'])){
		$options['content_region'] = 'au';
	}
    $regionIDs = get_transient('bizpress_blog_regions');
    if(empty($regionIDs)){
        $regionIDs = bizpress_blogs_get_regons();
    }
    $myRegionID = 0;
    foreach($regionIDs as $region){
        if(strtolower($region->slug) == strtolower($options['content_region'])){
            $myRegionID = $region->id;
        }
    }
    $search = isset($_REQUEST['search']) ? $_REQUEST['search'] : false;
    $category = isset($_REQUEST['category']) ? $_REQUEST['category'] : false;
    
    if(!empty($search)) $args = array_merge(array('search' => $search),$args);
    if(!empty($category) && $category != 'all' && $category != 'other') $args = array_merge(array('categories' => $category),$args);
    if(!empty($myRegionID) && $myRegionID != 0) $args = array_merge(array('region' => $myRegionID),$args);

    wp_send_json(bizinkblogs_getPosts($args));
}
add_action( 'wp_ajax_bizpressblogs', 'bizpress_blogs_ajax' );

function bizpress_blogs_addarticle_ajax(){
    global $bizink_bace,$bizinkcontent_client;
    $bizpressPostID = $_POST['bizpressPostID'] ? $_POST['bizpressPostID'] : false;
    if($bizpressPostID){

        $product = get_option('bizpress_product',array(
            "bizpress" => true,
            "bizpress_basic" => true,
            "bizpress_standard" => false,
        ));
    
        $luca = false;
        if(function_exists('luca')){
            $luca = true;
        }
        else if(in_array('bizpress-luca-2/bizpress-luca-2.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
            $luca = true;
        }
    
        $limit = 0;
        if($product->bizpress_standard == true){
            $limit = 20;
        }
        else if($luca == true){
            $limit = -1;
        }
    
        $currentCount = get_transient('bizpress_blog_count');
        if($currentCount == false){
            $currentCount = $limit;
            $start = new DateTime(date('Y-m-d'));
            $end = new DateTime(date('Y-m-t'));
            set_transient( 'bizpress_blog_count', $limit, ($end->getTimestamp() - $start->getTimestamp()) );
            unset($start,$end);
        }

        if($currentCount <= 0 && $limit != -1){
            wp_send_json(array(
                'status' => 'error',
                'type' => 'limit_reached',
                'message' => 'You have reached your blog post limit for this month.'
            ),403);
            return;
        }

        $previousPosts = get_option('bizpress_previousPosts',[]);
        $args = array(
            '_fields' => 'id,title,content,sticky,excerpt,featured_media,featured_image,date,modified,slug,categories,region',
        );
        $postUrl = add_query_arg($args,wp_slash($bizink_bace.'posts/'.$_POST['bizpressPostID']));
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
                update_option('bizpress_blog_count',get_option('bizpress_blog_count',0)+1);

                // categories
                if(!empty($body->categories)){
                    $categories = array();
                    $currentCategories = bizinkblogs_getCategories();
                    foreach($currentCategories as $currentCategory){
                        if(in_array($currentCategory->id,$body->categories)){
                            array_push($categories,$currentCategory->name);
                        }
                    }
                    wp_create_categories($categories,$post);
                }
    
                if(!is_wp_error($post)){
                    //the post is valid
                    array_push($previousPosts,intval($_POST['bizpressPostID']));
                    update_option('bizpress_previousPosts',$previousPosts);

                    if($limit != -1){
                        $start = new DateTime(date('Y-m-d'));
                        $end = new DateTime(date('Y-m-t'));
                        set_transient( 'bizpress_blog_count', --$currentCount, ($end->getTimestamp() - $start->getTimestamp()) );
                    }
                    
                    wp_send_json(array(
                        'status' => 'success',
                        'type' => 'add_post',
                        'message' => 'Success the post has been added to you blog',
                        'post_id' => $post,
                        'post' => get_post($post),
                        'currentCount' => $currentCount
                    ),200);
                }
                else{
                    //there was an error in the post insertion,
                    wp_send_json(array(
                        'status' => 'error',
                        'type' => 'add_error_post',
                        'message' => $post->get_error_message()
                    ),403);
                }
            }
            
        }
        else{
            wp_send_json(array(
                'status' => 'error',
                'type' => 'fetch_error_post',
                'message' => 'There was an error fetching the post data.'
            ),404);
        }
    }
    else{
        wp_send_json(array(
            'status' => 'error',
            'type' => 'no_post_id',
            'message' => 'Do data receved to the post you wished to add.'
        ),400);
    }
}
add_action( 'wp_ajax_bizpressblogsarticle', 'bizpress_blogs_addarticle_ajax' );

function bizpress_blogs_product_status(){
    $product = get_option('bizpress_product',array(
        "bizpress" => true,
        "bizpress_basic" => true,
        "bizpress_standard" => false,
    ));

    $luca = false;
	if(function_exists('luca')){
		$luca = true;
	}
	elseif(in_array('bizpress-luca-2/bizpress-luca-2.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
		$luca = true;
	}

    $limit = 0;
    if($product->bizpress_standard == true){
        $limit = 20;
    }
    else if($luca == true){
        $limit = -1;
    }

    $currentCount = get_transient('bizpress_blog_count');
    if($currentCount == false){
        $currentCount = $limit;
        $start = new DateTime(date('Y-m-d'));
        $end = new DateTime(date('Y-m-t'));
        set_transient( 'bizpress_blog_count', $limit, ($end->getTimestamp() - $start->getTimestamp()) );
        unset($start,$end);
    }

    $canAddBlogs = false;
    if($product->bizpress_standard == true || $product->bizpress_premium == true || $luca == true){
        $canAddBlogs = true;
    }

    return array(
        'product' => $product,
        'canAddBlogs' => $canAddBlogs,
        'luca' => $luca,
        'limit' => $limit,
        'currentCount' => $currentCount
    );
}