<?php
include 'automation_table.php';

function bizpress_blogs_automations_init(){
    add_submenu_page(
        'bizpress_blogs',
        __('Automations', 'bizink-content'),
        __('Automations', 'bizink-content'),
        'manage_options',
        'admin.php?page=bizpress_automations',
        'bizpress_blogs_automations_page'
    );
}
//add_action('admin_menu', 'bizpress_blogs_automations_init');

function bizpress_blogs_automations_page(){
    if($_GET['automation'] == 'edit' || $_GET['automation'] == 'new'){
        bizpress_blogs_automations_page_edit();
    } else {
        bizpress_blogs_automations_page_list();
    }
    
}

function bizpress_blogs_automations_page_list(){
    $table = new Bizpress_Blogs_Automation_Table();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('Automations', 'bizink-content'); ?></h1>
        <a class="button-secondary page-title-action" href="<?php echo admin_url('admin.php?page=admin.php?page=bizpress_automations&automation=new'); ?>" title="<?php esc_attr_e( 'Add New Automation','bizink-content'); ?>"><?php esc_attr_e( 'Add New Automation','bizink-content' ); ?></a>
        <hr class="wp-header-end">
        <?php 
            $table->prepare_items();
            // Display table
            $table->display();
        ?>
    </div>
    <?php
}

function bizpress_blogs_automations_page_edit(){
    ?>
    <div class="wrap">
        <h1><?php if($_GET['automation'] == 'new'): _e('Add New Automation', 'bizink-content'); else: _e('Edit Automation', 'bizink-content'); endif; ?></h1>
        <form method="post" action="admin.php?page=bizpress_automations">
            <input type="hidden" name="automation" value="<?php echo $_GET['automation']; ?>">
            <div id="titlewrap">
                <label class="screen-reader-text" id="title-prompt-text" for="title">Name this feed</label>
                <input type="text" name="post_title" size="30" value="Bizink Blog Posts" id="title" spellcheck="true" autocomplete="off">
            </div>
        </form>
    </div>
    <?php
}