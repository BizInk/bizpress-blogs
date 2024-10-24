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
function bizpress_blogs_getCategories($publisher = 'bizink'){
    global $bizink_bace,$bizinkcontent_client;
    if(get_transient('bizpress_blog_categories_'.$publisher)){
        return array(
            'status' => 'success',
            'type' => 'get_categories',
            'categories' => get_transient('bizpress_blog_categories_'.$publisher)
        );
    }
    if($publisher != 'bizink'){
        $url = 'publisher-topic';
    }
    else{
        $url = 'categories';
    }
    $response = wp_remote_get($bizink_bace.$url.'?_fields=id,count,name,slug,parent',$bizinkcontent_client);
    $status = wp_remote_retrieve_response_code($response);
    if($status < 400){
        $body = json_decode(wp_remote_retrieve_body( $response ));
        set_transient('bizpress_blog_categories_'.$publisher, $body, DAY_IN_SECONDS);
        return array(
            'status' => 'success',
            'type' => 'get_categories',
            'categories' => $body
        );
    }
    else{
        return array(
            'status' => 'error',
            'type' => 'fetch_error_categories',
            'message' => 'There was an error fetching the categories.'
        );
    }
}

function bizpress_blogs_getCategories_ajax(){
    $publisher = $_POST['publisher'] ? htmlspecialchars($_POST['publisher']) : 'bizink';
    wp_send_json(bizpress_blogs_getCategories($publisher));
}
add_action( 'wp_ajax_bizpressblogscategories', 'bizpress_blogs_getCategories_ajax' );

function bizpressblogs_getPublishers(){
    global $bizink_bace,$bizinkcontent_client;
    if(get_transient('bizpress_blog_publishers')){
        return get_transient('bizpress_blog_publishers');
    }
    $response = wp_remote_get($bizink_bace.'publisher-publisher?_fields=name,id,acf,slug',$bizinkcontent_client);
    $status = wp_remote_retrieve_response_code($response);
    if($status < 400){
        $body = json_decode(wp_remote_retrieve_body( $response ));
        set_transient('bizpress_blog_publishers', $body, DAY_IN_SECONDS);
        return $body;
    }
    else{
        return array(
            'status' => 'error',
            'type' => 'fetch_error_publishers',
            'message' => 'There was an error fetching the publishers.'
        );
    }
}

function bizinkblogs_getPosts($args = ['status' => 'publish','per_page' => 8],$publisher = 'bizink'){
    global $bizink_bace,$bizinkcontent_client;
   
    if($publisher != 'bizink'){
        $args['filter[publisher-publisher]'] = $publisher;
        $postUrl = add_query_arg($args,wp_slash($bizink_bace.'publisher-content'));
    }
    else{
        $postUrl = add_query_arg($args,wp_slash($bizink_bace.'posts'));
    }
    
    $response = wp_remote_get($postUrl,$bizinkcontent_client);
    $status = wp_remote_retrieve_response_code($response);
    if($status < 400){
        $body = json_decode(wp_remote_retrieve_body( $response ));
        $totalPosts = wp_remote_retrieve_header($response,'X-WP-Total');
        $totalPages = wp_remote_retrieve_header($response,'X-WP-TotalPages');
        return array(
            'status' => 'success',
            'type' => 'get_posts',
            'message' => 'Success here are the blog post you requested',
            'totalPosts' => $totalPosts,
            'totalPages' => $totalPages,
            'posts' => $body,
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

function bizinkblogs_getSinglePost($id){
    global $bizink_bace,$bizinkcontent_client;

    $publishers = bizpressblogs_getPublishers();
    $postUrl = add_query_arg(array( '_fields' => 'id,title,content,sticky,excerpt,featured_media,featured_image,date,modified,slug,categories,region,publisher-publisher,publisher-topic,publisher-type' ),wp_slash($bizink_bace.'publisher-content/'.$id));
    $response = wp_remote_get($postUrl,$bizinkcontent_client);
    $status = wp_remote_retrieve_response_code($response);
    if($status < 400){
        $body = json_decode(wp_remote_retrieve_body( $response ));
        $publisher = false;
        if(!empty($body->{'publisher-publisher'}[0])){
            foreach($publishers as $pub){
                if($pub->id == $body->{'publisher-publisher'}[0]){
                    $publisher = $pub;
                }
            }
        }
        return array(
            'status' => 'success',
            'type' => 'get_post',
            'post' => $body,
            'publisher' => $publisher
        );
    }
    else{
        return array(
            'status' => 'error',
            'type' => 'fetch_error_post',
            'message' => 'There was an error fetching the post data.'
        );
    }
}

function bizpress_blogs_ajax(){
    $page = isset($_REQUEST['blogpage']) ? $_REQUEST['blogpage'] : 1;
    $publisher = isset($_POST['publisher']) ? htmlspecialchars($_POST['publisher']) : 'bizink';
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

    wp_send_json(bizinkblogs_getPosts($args,$publisher));
}
add_action( 'wp_ajax_bizpressblogs', 'bizpress_blogs_ajax' );

function bizpress_blogs_addarticle_ajax(){
    global $bizink_bace,$bizinkcontent_client;
    $bizpressPostID = isset($_POST['bizpressPostID']) ? htmlspecialchars($_POST['bizpressPostID']) : false;
    $publisher = isset($_POST['publisher']) ? htmlspecialchars($_POST['publisher']) : 'bizink';

    if($bizpressPostID){

        $args = array(
            '_fields' => 'id,title,content,excerpt,featured_media,featured_image,date,modified,slug,categories,region',
        );
        if($publisher != 'bizink'){
            $postUrl = add_query_arg($args,wp_slash($bizink_bace.'publisher-content/'.$bizpressPostID));
        }
        else{
            $postUrl = add_query_arg($args,wp_slash($bizink_bace.'posts/'.$bizpressPostID));
        }
        
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
                $content = $body->content->rendered;
                if($publisher != 'bizink'){
                    $content = '[bizpress_blog id="'.$body->id.'"]';
                }
                $post = wp_insert_post(array(
                    'post_title' => $body->title->rendered,
                    'post_content' => $content,
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
                    $currentCategories = bizpress_blogs_getCategories();
                    if(!empty($currentCategories) && $currentCategories['status'] == 'success'){
                        foreach($currentCategories['categories'] as $currentCategory){
                            if(in_array($currentCategory->id,$body->categories)){
                                array_push($categories,$currentCategory->name);
                            }
                        }
                        wp_create_categories($categories,$post);
                    }
                    
                }
    
                if(!is_wp_error($post)){
                    $previousPosts = get_option('bizpress_previousPosts',[]);
                    array_push($previousPosts, $bizpressPostID);
                    update_option('bizpress_previousPosts',$previousPosts);
                    
                    wp_send_json(array(
                        'status' => 'success',
                        'type' => 'add_post',
                        'message' => __('Success the post has been added to your blog','bizink-client'),
                        'post_id' => $post,
                        'post' => get_post($post),
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
                'message' => __('There was an error fetching the post data.','bizink-client')
            ),404);
        }
    }
    else{
        wp_send_json(array(
            'status' => 'error',
            'type' => 'no_post_id',
            'message' => __('Do data receved to the post you wished to add.','bizink-client')
        ),400);
    }
}
add_action( 'wp_ajax_bizpressblogsarticle', 'bizpress_blogs_addarticle_ajax' );

// TODO: MAKE This function work
function bizpress_blogs_product_status(){
    $product = get_option('bizpress_product',array(
        "bizpress" => true,
        "bizpress_standard" => true,
    ));

    $luca = false;
	if(function_exists('luca')){
		$luca = true;
	}
	elseif(in_array('bizpress-luca-2/bizpress-luca-2.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
		$luca = true;
	}

    $limit = 20;
    if($product->bizpress_standard == true){
        $limit = 20;
    }
    else if($luca == true){
        $limit = -1;
    }

    $currentCount = get_transient('bizpress_blog_count') ?? $limit;
    if($currentCount == false){
        $currentCount = $limit;
        $start = new DateTime(date('Y-m-d'));
        $end = new DateTime(date('Y-m-t'));
        set_transient( 'bizpress_blog_count', $limit, ($end->getTimestamp() - $start->getTimestamp()) );
        unset($start,$end);
    }

    $canAddBlogs = true;
    if($product->bizpress_standard == true || $luca == true){
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