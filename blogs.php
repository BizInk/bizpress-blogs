<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require 'api.php';

function bizpress_blogs_plugin_styles() {
	wp_register_style( 'bizpress_blogs_css', plugins_url( 'assets/css/admin.css', __FILE__ ) );
	wp_enqueue_style( 'bizpress_blogs_css' );
    wp_register_script('bizpress_blogs_script',plugins_url( 'assets/js/admin.js', __FILE__ ),['jqurey']);
    wp_enqueue_script( 'bizpress_blogs_script' );
}

add_action('admin_enqueue_scripts', 'bizpress_blogs_plugin_styles');

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
    $posts = bizinkblogs_getPosts();
    $options = get_option( 'bizink-client_basic' );
    if(empty($options['content_region'])){
		$options['content_region'] = 'au';
	}
    ?>
    <div class="bizpress_blogs">
        <header class="bizpress_blogs_header">
            <h2 class="title">Bizpress Blogs</h2>
            <p><b><?php _e('Email','bizink-client'); ?>:</b>&nbsp;<?php echo $options['user_email']; ?>&nbsp;<b><?php _e('Region','bizink-client'); ?>:</b>&nbsp;<?php echo $options['content_region']; ?></p>
            
            <form class="bizpress_blogs_search_form">
                <label for="bizpress_blogs_search_form_search" class="bizpress_blogs_search_form_search_label">Search</label>
                <select id="bizpress_blogs_category" name="bizpress_category" class="bizpress_blogs_search_form_input bizpress_blogs_search_form_input_category">
                    <option value="any"><?php _e('Any','bizpress');?></option>
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
                <input class="bizpress_blogs_search_form_input bizpress_blogs_search_form_input_search" id="bizpress_blogs_search_form_search" type="search" placeholder="Search for Articles, Digests & News" name="bizpress_search"/>
                <input class="bizpress_blogs_search_form_input bizpress_blogs_search_form_input_submit" id="bizpress_blogs_search_form_submit" type="submit" value="<?php _e('Search','bizpress');?>"/>
            </form>
        </header>
        <section class="bizpress_blogs_posts">
            <div class="bizpress-blogs">
                <?php
                if(empty($posts) == false): 
                    foreach($posts as $post):
                ?>
                    <div class="blog" data-slug="<?php echo $post->slug; ?>" data-id="<?php echo $post->id; ?>">
                        <h3 class="title"><?php echo $post->title->rendered; ?></h3>
                        <div class="excerpt">
                            <p><?php echo $post->excerpt->rendered; ?></p>
                        </div>
                        <div class="actions">
                            <button type="button" class="bizpress_blogs_button">View Article</button>
                            <button type="button" class="bizpress_blogs_button bizpress_blogs_button_secondary">Add Article</button>
                        </div>
                    </div>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
        </section>
    </div>
    <?php
}