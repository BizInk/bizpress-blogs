<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if(!class_exists('Bizpress_Blogs_Automation_Table')){
    class Bizpress_Blogs_Automation_Table extends WP_List_Table
    {
        private $table_data;

        /**
         * Get a list of columns.
         *
         * @return array
         */
        public function get_columns()
        {
            return array(
                'cb' => '<input type="checkbox" />',
                'post_status' => __('Status','bizink-client'),
                'post_title' => __('Name','bizink-client'),
                'frequency' => __('Frequency','bizink-client'),
                'post_type' => __('Type','bizink-client'),
            );
        }

        private function get_table_data( $search = '' ){

            return array(
                0 => array(
                    'ID' => 1,
                    'post_status' => 'publish',
                    'post_title' => 'Add New Client',
                    'frequency' => 'daily',
                    'post_type' => 'post'
                ),
                1 => array(
                    'ID' => 2,
                    'post_status' => 'publish',
                    'post_title' => 'Test 2',
                    'frequency' => 'monthly',
                    'post_type' => 'post'
                ),
                2 => array(
                    'ID' => 3,
                    'post_status' => 'draft',
                    'post_title' => 'Test 3',
                    'frequency' => 'weekly',
                    'post_type' => 'weekly-digest'
                ),
            );
        }

        /**
         * Prepares the list of items for displaying.
         */
        public function prepare_items()
        {
            //data
            if ( isset($_POST['s']) ) {
                $this->table_data = $this->get_table_data($_POST['s']);
            } 
            else {
                $this->table_data = $this->get_table_data();
            }

            $columns = $this->get_columns();
            $hidden = ( is_array(get_user_meta( get_current_user_id(), 'bizpress_automations_columnshidden', true)) ) ? get_user_meta( get_current_user_id(), 'bizpress_forms_columnshidden', true) : array();
            $sortable = $this->get_sortable_columns();
            $primary = 'post_title';
            $this->_column_headers = array($columns, $hidden, $sortable, $primary);

            usort($this->table_data, array(&$this, 'usort_reorder'));
            
            /* pagination */
            $per_page = $this->get_items_per_page('elements_per_page', 10);
            $current_page = $this->get_pagenum();
            $total_items = count($this->table_data);

            $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

            $this->set_pagination_args(array(
                'total_items' => $total_items, // total number of items
                'per_page'    => $per_page, // items to show on a page
                'total_pages' => ceil( $total_items / $per_page ) // use ceil to round up
            ));

            $this->items = $this->table_data;
        }

         // To show bulk action dropdown
        function get_bulk_actions()
        {
                $actions = array(
                    'delete_all' => __('Delete All', 'bizink-client'),
                    'draft_all'  => __('Disable All', 'bizink-client')
                );
                return $actions;
        }

        /**
         * Generates content for a single row of the table.
         *
         * @param object $item The current item.
         * @param string $column_name The current column name.
         */
        protected function column_default($item, $column_name)
        {
            switch ($column_name) {
                case 'post_status':
                    $enabled = ($item[$column_name] == 'publish') ? 'checked' : '';
                    return '<div class="bizpress_switch"><input type="checkbox" id="status-'.$item['ID'].'" class="bizpress_switch_checkbox" '.$enabled.'/><label for="status-'.$item['ID'].'">Enabled</label></div>';
                case 'post_type':
                    $post_type_object = get_post_type_object($item[$column_name]);
                    return $post_type_object->labels->singular_name ? $post_type_object->labels->singular_name : __("Unkowen Post Type ",'bizink-client').'('.$item[$column_name].')';
                case 'frequency':
                    return ucfirst($item[$column_name]);
                default:
                    return $item[$column_name];
            }
            
        }

        function column_post_title($item)
        {
            $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=edit&automation=%s">' . __('Edit', 'bizink-client') . '</a>', $_REQUEST['page'], $item['ID']),
                'delete'    => sprintf('<a href="?page=%s&action=delete&automation=%s">' . __('Delete', 'bizink-client') . '</a>', $_REQUEST['page'], $item['ID']),
            );
            return sprintf('%1$s %2$s', $item['post_title'], $this->row_actions($actions));
        }

        protected function get_edit_link( $args, $link_text, $css_class = '' ){
            $args['page'] = 'bizpress_automations';
            $url = add_query_arg( $args, 'admin.php' );
            if ( ! empty( $css_class ) ) {
                $class_html = sprintf(
                    ' class="%s"',
                    esc_attr( $css_class )
                );
        
                if ( 'current' === $css_class ) {
                    $aria_current = ' aria-current="page"';
                }
            }
        
            return sprintf(
                '<a href="%s"%s%s>%s</a>',
                esc_url( $url ),
                $class_html,
                $aria_current,
                $link_text
            );
        }

        /**
         * Generates custom table navigation to prevent conflicting nonces.
         *
         * @param string $which The location of the bulk actions: 'top' or 'bottom'.
         */
        protected function display_tablenav($which)
        {
            ?>
            <div class="tablenav <?php echo esc_attr($which); ?>">
                <div class="alignleft actions bulkactions">
                    <?php $this->bulk_actions($which); ?>
                </div>
                <?php
                $this->extra_tablenav($which);
                $this->pagination($which);
                ?>
                <br class="clear" />
            </div>
            <?php
        }
        /**
         * Generates content for a single row of the table.
         *
         * @param object $item The current item.
         */
        public function single_row($item)
        {
            echo '<tr>';
            $this->single_row_columns($item);
            echo '</tr>';
        }

        /**
        * Controls the indevual collum checkbox
        */
        protected function column_cb($item)
        {
            return sprintf('<input type="checkbox" name="element[]" value="%s" />',$item['id']);
        }

        /**
        * Controls the sortable columns
        */
        protected function get_sortable_columns()
        {
            $sortable_columns = array(
                'id' => array('id', true),
                'post_title'  => array('post_title', false),
                'post_status' => array('post_status', false),
                'post_type' => array('post_type', false),
            );
            return $sortable_columns;
        }

          // Sorting function
        function usort_reorder($a, $b)
        {
            // If no sort, default to user_login
            $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'id';

            // If no order, default to asc
            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

            // Determine sort order
            $result = strcmp($a[$orderby], $b[$orderby]);

            // Send final sort direction to usort
            return ($order === 'asc') ? $result : -$result;
        }
    }
}