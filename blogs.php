<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require 'api.php';

function bizpress_blogs_plugin_styles($hook) {
	wp_register_style( 'bizpress_blogs_css', plugins_url( 'assets/css/admin.css', __FILE__ ) );
	wp_enqueue_style( 'bizpress_blogs_css' );
}
add_action('admin_enqueue_scripts', 'bizpress_blogs_plugin_styles');

function bizpress_blogs_plugin_scripts($hook){
    if ('toplevel_page_bizpress_blogs' !== $hook) {
        return;
    }
    wp_register_script('bizpress_blogs_script',plugins_url( 'assets/js/admin.js', __FILE__ ),['jquery','wp-i18n']);
    wp_localize_script('bizpress_blogs_script', 'bizpress_blogs_ajax_object',array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_script('bizpress_blogs_script');
}
add_action('admin_enqueue_scripts', 'bizpress_blogs_plugin_scripts');

add_action('init', 'bizpress_blogs_menu');
function bizpress_blogs_menu(){
    add_menu_page(
        'Bizpress Blogs',
        'Bizpress Blogs',
        'edit_posts',
        'bizpress_blogs',
        'bizpress_blogs_page',
        'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGlkPSJmNjU0YmNiZS03MzYwLTQxZGUtOWM3ZC1lYjE3ODcwYmRjOGYiIGRhdGEtbmFtZT0iTGF5ZXIgMSIgdmlld0JveD0iMTc5Ljc3IDE1MC4xOSAyOTcuNDEgMjM0LjQ1Ij48ZGVmcz48c3R5bGU+LmE2OTM0ZWRiLWVkZGItNDlhYi1iNjljLTg0NDllMzkzODBlZXtmaWxsOiMzMzNiNjE7fS5lYzBhYzQ1OS05ZDU5LTQ2ZDItODU5NC05ZGY1ZDcwM2Q2MDV7ZmlsbDojZjdhODAwO308L3N0eWxlPjwvZGVmcz48cGF0aCBjbGFzcz0iYTY5MzRlZGItZWRkYi00OWFiLWI2OWMtODQ0OWUzOTM4MGVlIiBkPSJNMjM5LjI1LDIzMy42MmM0Ny43LTI4Ljc3LDEwNywzLjY0LDEwOC42NSw1Ny44My42OCwyMS44Mi0xLjUsNDIuOTMtMTUuMTYsNjAuODEtMTkuMzgsMjUuMzktNDUuNTQsMzUuNTQtNzcsMzAtNy41MS0xLjMyLTE0LjM1LTYuNDctMjIuODctMTAuNTEtOC4yMywxMS42NC0yMS4yMywxNS4yNS0zNy42NywxMS40MXYtOS4zOHEwLTg5LjkzLDAtMTc5Ljg1YzAtMTAuMDYtLjEtMTkuNTItMTIuMzQtMjMuNTMtMS44My0uNi0xLjkyLTYuNDQtMy4wOS0xMC45LDE4Ljc4LDAsMzYtLjE1LDUzLjE4LjA4LDUuNTUuMDgsNi4yOSw0LjQsNi4yOCw4LjkzcS0uMDYsMjcuNDgsMCw1NC45NVptMCw3MC40YzAsMTYuMzYtLjE0LDMyLjcyLjEzLDQ5LjA4LjA1LDMuMDcuODksNi43OSwyLjc1LDkuMDgsMTEuODksMTQuNTksMzEuNDMsMTMuODYsNDIuMjItMS40Nyw4Ljc2LTEyLjQ0LDExLjUtMjYuODIsMTIuMzYtNDEuNjIsMS4wOC0xOC42Ny40MS0zNy4xOC04LjgzLTU0LjE1LTktMTYuNDUtMjYuNDctMjMuODktNDIuNzctMTktNC40OSwxLjM1LTYsMy40OC02LDguMThDMjM5LjQyLDI3MC43NSwyMzkuMjUsMjg3LjM5LDIzOS4yNSwzMDRaIi8+PHBhdGggY2xhc3M9ImVjMGFjNDU5LTlkNTktNDZkMi04NTk0LTlkZjVkNzAzZDYwNSIgZD0iTTQxNy4xOCwyMTcuNDRhNjUuNzksNjUuNzksMCwwLDAsMjMuNjctNC4zNGMxMC42LTQsMTkuOTMtMTAuMTIsMjguNjItMTcuMzMsNS43Ni00Ljc4LDguMDctMTAuODUsNy42Ni0xOC4xMmEyMC4wOSwyMC4wOSwwLDAsMC00LjQ0LTEyQTIyLjA2LDIyLjA2LDAsMCwwLDQ0MiwxNjIuMzFhODQuMjcsODQuMjcsMCwwLDEtMTQuMzIsOS4yLDIxLjU2LDIxLjU2LDAsMCwxLTEzLjQzLDIuMjUsNDAuNjYsNDAuNjYsMCwwLDEtMTQuODEtNS4yNGMtNS4zNi0zLjMxLTEwLjczLTYuNjItMTYuMjQtOS42OGE2NS43OSw2NS43OSwwLDAsMC0zMC40OS04LjYxLDQ4LjYyLDQ4LjYyLDAsMCwwLTE5LjczLDRjLTguODEsMy41Ny0xNi43LDguNzUtMjQuNTYsMTRhMjYuMDcsMjYuMDcsMCwwLDAtNy41OCw3LjM2Yy0zLjI2LDQuOTMtNC43NCwxMC4zLTMuNjcsMTYuMmEyMC4xNSwyMC4xNSwwLDAsMCw3LjU0LDEyLjEyYzcuMTIsNS44NSwxNy41OCw3LjgzLDI2LjE3LDEuNjNhMTE4LjE0LDExOC4xNCwwLDAsMSwxOC44NS0xMS4wNyw2LjU0LDYuNTQsMCwwLDEsMy4wNi0uNjcsMTcuNTEsMTcuNTEsMCwwLDEsNy44NCwyLjM2YzUuMjIsMy4wNywxMC4zNyw2LjI3LDE1LjYsOS4zMkE4OS4yNyw4OS4yNywwLDAsMCw0MTcuMTgsMjE3LjQ0WiIvPjxwYXRoIGNsYXNzPSJlYzBhYzQ1OS05ZDU5LTQ2ZDItODU5NC05ZGY1ZDcwM2Q2MDUiIGQ9Ik00MTcuMTgsMjE3LjQ0YTg5LjI3LDg5LjI3LDAsMCwxLTQxLTEyYy01LjIzLTMuMDUtMTAuMzgtNi4yNS0xNS42LTkuMzJhMTcuNTEsMTcuNTEsMCwwLDAtNy44NC0yLjM2LDYuNTQsNi41NCwwLDAsMC0zLjA2LjY3LDExOC4xNCwxMTguMTQsMCwwLDAtMTguODUsMTEuMDdjLTguNTksNi4yLTE5LDQuMjItMjYuMTctMS42M2EyMC4xNSwyMC4xNSwwLDAsMS03LjU0LTEyLjEyYy0xLjA3LTUuOS40MS0xMS4yNywzLjY3LTE2LjJhMjYuMDcsMjYuMDcsMCwwLDEsNy41OC03LjM2YzcuODYtNS4yMiwxNS43NS0xMC40LDI0LjU2LTE0YTQ4LjYyLDQ4LjYyLDAsMCwxLDE5LjczLTQsNjUuNzksNjUuNzksMCwwLDEsMzAuNDksOC42MWM1LjUxLDMuMDYsMTAuODgsNi4zNywxNi4yNCw5LjY4YTQwLjY2LDQwLjY2LDAsMCwwLDE0LjgxLDUuMjQsMjEuNTYsMjEuNTYsMCwwLDAsMTMuNDMtMi4yNSw4NC4yNyw4NC4yNywwLDAsMCwxNC4zMi05LjIsMjIuMDYsMjIuMDYsMCwwLDEsMzAuNzMsMy4zOCwyMC4wOSwyMC4wOSwwLDAsMSw0LjQ0LDEyYy40MSw3LjI3LTEuOSwxMy4zNC03LjY2LDE4LjEyLTguNjksNy4yMS0xOCwxMy4zMS0yOC42MiwxNy4zM0E2NS43OSw2NS43OSwwLDAsMSw0MTcuMTgsMjE3LjQ0WiIvPjwvc3ZnPg==',
        7
    );
}

function bizpress_blogs_page(){
    $categories = false;
    $categories = bizinkblogs_getCategories();
    $args = array(
        'status' => 'publish',
        'per_page' => 8,
        'page' => isset($_GET['blogpage']) ? $_GET['blogpage'] : 1
    );
    $postResponce = bizinkblogs_getPosts($args);
    $options = get_option('bizink-client_basic');
    if(empty($options['content_region'])){
		$options['content_region'] = 'au';
	}
    $nonce = wp_create_nonce("bizpress_blogs");
    ?>
    <div class="bizpress_blogs" data-nonce="<?php echo $nonce; ?>">
        <header class="bizpress_blogs_header">
            <h2 class="title"><img width="250" src="<?php echo plugin_dir_url(__FILE__).'/assets/img/bizpress_logo_white.png'; ?>" /><span class="text"><?php _e('Blogs','bizink-client'); ?></span></h2>
            <p><b><?php if(empty($options['user_email']) == false): _e('Email','bizink-client'); ?>:</b>&nbsp;<?php echo $options['user_email']; endif; ?>&nbsp;<b><?php _e('Region','bizink-client'); ?>:</b>&nbsp;<?php echo $options['content_region']; ?></p>   
            <form class="bizpress_blogs_search_form">
                <label for="bizpress_blogs_search_form_search" class="bizpress_blogs_search_form_search_label"><?php _e('Search','bizink-client'); ?></label>
                <div class="bizpress_blogs_search_form_input_category_wrap">
                    <select id="bizpress_blogs_category" name="category" class="bizpress_blogs_search_form_input bizpress_blogs_search_form_input_category">
                        <option value="all"><?php _e('All','bizpress');?></option>
                        <?php
                            if(empty($categories) == false){
                                foreach(bizinkblogs_getCategories() as $category){
                                    if($category->slug != 'uncategorized'): //uncategorized
                                    echo '<option value="'.__($category->slug,'bizpress').'">'.__($category->name,'bizpress').'</option>';
                                    endif;
                                }
                            }
                        ?>
                    </select>
                </div>
                <input class="bizpress_blogs_search_form_input bizpress_blogs_search_form_input_search" id="bizpress_blogs_search_form_search" type="search" placeholder="<?php _e('Search for Articles, Digests & News','bizink-client');?>" name="search"/>
                <input class="bizpress_blogs_search_form_input bizpress_blogs_search_form_input_submit" id="bizpress_blogs_search_form_submit" type="submit" value="<?php _e('Search','bizink-client');?>"/>
            </form>
        </header>
        <?php $prevPosts = get_option('bizpress_previousPosts',[]); ?>
        <section id="bizpress_blogs_posts" class="bizpress_blogs_posts" data-posts='<?php echo json_encode($prevPosts); ?>' data-page="<?php echo $_GET['blogpage'] ? $_GET['blogpage'] : 1; ?>" data-totalpages="<?php echo $postResponce['totalPages']; ?>">
            <div class="bizpress_blogs_pagenation">
                <div class="pagenation">
                    <button type="button" disabled class="pagenation_button prev_button"><span class="pagenation_button_text"><?php _e('Previous','bizink-client'); ?></span></button>
                    <div class="pagenation_pages">
                        <?php
                        if($postResponce['totalPages'] < 10){
                            $i=1;
                            while($i <= $postResponce['totalPages']){
                                $selected = '';
                                if($i == 1){
                                    $selected = 'selected';
                                }
                                echo '<button type="button" data-page="'.$i.'" class="pagenation_button pagenation_page_button '.$selected.'"><span class="pagenation_button_text">'.$i.'</span></button>';
                                $i++;
                            }
                        }
                        else{
                            $has_echo_elipics = false;
                            $i=1;
                            while($i <= $postResponce['totalPages']){
                                $selected = '';
                                if($i == 1){
                                    $selected = 'selected';
                                }
                                if($i < 4 || $i > $postResponce['totalPages'] - 2){
                                    echo '<button type="button" data-page="'.$i.'" class="pagenation_button pagenation_page_button '.$selected.'"><span class="pagenation_button_text">'.$i.'</span></button>';
                                }
                                elseif($has_echo_elipics == false){
                                    $has_echo_elipics = true;
                                    echo '<div class="pagenation_button pagenation_elipics_button"><span class="pagenation_button_text">...</span></div>';
                                }                           
                                $i++;
                            }
                        }
                        
                        ?>
                    </div>
                    <button type="button" class="pagenation_button next_button"><span class="pagenation_button_text"><?php _e('Next','bizink-client'); ?></span></button>
                </div>
            </div>
            <div id="main_loader_section" class="loader_section">
                <div id="bizink_blogs_loader" class="bizink_blogs_loader"><div></div><div></div><div></div></div>
            </div>
            <div id="bizpress_blog_items" class="bizpress_blog_items">
                <?php
                if(empty($postResponce['posts']) == false): 
                    foreach($postResponce['posts'] as $post):
                ?>
                    <div class="blog <?php if(in_array($post->id,$prevPosts)){echo 'post_in_library';} ?>" id="bizpress_blog_<?php echo $post->id; ?>" data-slug="<?php echo $post->slug; ?>" data-id="<?php echo $post->id; ?>" data-title="<?php echo $post->title->rendered; ?>">
                        <div class="in_library_text"><?php _e('In Library','bizink-client'); ?></div>
                        <div class="blog_text">
                            <h3 class="blog_title"><?php echo $post->title->rendered; ?></h3>
                            <div class="blog_excerpt" onmousedown="return false" onselectstart="return false">
                                <?php echo $post->excerpt->rendered; ?>
                            </div>
                        </div>
                        <div class="actions">
                            <button type="button" class="bizpress_blogs_button view_article"><?php _e('View Article','bizink-client'); ?></button>
                            <button type="button" class="bizpress_blogs_button bizpress_blogs_button_secondary import_article" data-id="<?php echo $post->id; ?>" data-title="<?php echo $post->title->rendered; ?>"><?php _e('Import Article','bizink-client'); ?></button>
                        </div>
                        <div class="content" style="display: none;" onmousedown="return false" onselectstart="return false">
                            <?php echo $post->content->rendered; ?>
                        </div>
                    </div>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
            <div class="bizpress_blogs_model" id="bizpress_blogs_model">
                <div class="model">
                    <div class="model_close close_model"><span class="model_close_x">X</span></div>
                    <h2 class="model_title"></h2>
                    <div class="model_content model_content_blog" onmousedown="return false" onselectstart="return false">
                    </div>
                    <div class="model_actions">
                        <button class="bizpress_blogs_button import_model import_article" data-id="" data-title=""><?php _e('Import Article','bizink-client'); ?></button>
                        <button type="button" class="bizpress_blogs_button bizpress_blogs_button_secondary close_model"><?php _e('Close','bizink-client'); ?></button>
                    </div>
                </div>
            </div>
            <div class="bizpress_blogs_model" id="bizpress_blogs_addpost_model">
                <div class="model">
                    <div class="model_close close_model"><span class="model_close_x">X</span></div>
                    <h2 class="model_title"><?php _e('Adding Article','bizink-client'); ?></h2>
                    <div class="model_content">
                        <div class="details">
                            <h3 class="article_title"></h3>
                            <p class="article_status"><?php _e('Processing...','bizink-client'); ?></p>
                        </div>
                        <div class="loader_section">
                            <div id="bizink_blogs_loader" class="bizink_blogs_loader"><div></div><div></div><div></div></div>
                        </div>
                    </div>
                    <div class="model_actions">
                        <button disabled class="bizpress_blogs_button view_model view_article" data-postid="0"><?php _e('View Article','bizink-client'); ?></button>
                        <button disabled type="button" class="bizpress_blogs_button bizpress_blogs_button_secondary close_model"><?php _e('Close','bizink-client'); ?></button>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
}