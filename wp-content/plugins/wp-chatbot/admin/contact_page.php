<?php
/**
 * template for options page
 * @uses HTCC_Admin::settings_page
 * @since 1.0.0
 */
class MobileMonkey_Contacts_List_Table extends WP_List_Table
{
    private $api;
    private $totalItems;
    private $count = 10;

    public function __construct() {
        parent::__construct();
        $this->api = new MobileMonkeyApi();
    }

    private function getApi(){
        return $this->api;
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $api = $this->getApi();
        $deleteId = filter_input( INPUT_GET, "delete", FILTER_SANITIZE_STRING );
        if ($deleteId) {
            $api->deleteContact( $deleteId );
        }

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();
        $perPage = $api->getPagination()->per_page;
        $this->totalItems = $api->getPagination()->total;
        $this->set_pagination_args( [
            'total_items' => $this->totalItems,
            'per_page'    => $perPage
        ] );
        $this->_column_headers = [$columns, $hidden, $sortable];
        $this->items = $data;
    }
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = [
            'first_name'=> __('First Name'),
            'last_name' => __('Last Name'),
            'gender'    => __('Gender'),
            'locale'    => __('Locale'),
            'timezone'  => __('Timezone'),
            'created_at'=> __('Created'),
        ];
        return $columns;
    }
    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return [];
    }
    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return [
            'first_name' => ['first_name', true],
            'last_name'  => ['last_name', true],
            'gender'     => ['gender', true],
            'locale'     => ['locale', true],
            'timezone'   => ['timezone', true],
            'created_at' => ['created_at', true]
        ];
    }
    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        $data = [];
        $contacts = $this->getApi()->getContacts();
        if($contacts) {
            foreach ( $contacts as $contact ) {
                $path = add_query_arg( [
                    'page'   => 'wp-chatbot-contact',
                    'delete' => $contact->id,
                ], admin_url( 'admin.php' ) );

                $timezone = $contact->timezone;
                if($timezone > 0) {
                    $timezone = 'GMT +'.$timezone;
                } else {
                    $timezone = 'GMT -'.$timezone;
                }

                $created = date('m/d/Y H:i', strtotime($contact->created_at));

                $data[] = [
                    'first_name'    => $contact->first_name,
                    'last_name'     => $contact->last_name,
                    'gender'        => $contact->gender,
                    'locale'        => $contact->locale,
                    'timezone'      => $timezone,
                    'created_at'    => $created,
                ];
            }
        }
        return $data;
    }
    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'first_name':
            case 'last_name':
            case 'gender':
            case 'locale':
            case 'timezone':
            case 'created_at':
                return $item[ $column_name ];
        }
    }

    /**
     * Display the table
     *
     * @since 3.1.0
     */
    public function display() {
        $singular = $this->_args['singular'];

        $this->display_tablenav( 'top' );

        $this->screen->render_screen_reader_content( 'heading_list' );
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tbody id="the-list"<?php
            if ( $singular ) {
                echo " data-wp-lists='list:$singular'";
            } ?>>
            <?php $this->display_rows_or_placeholder(); ?>
            </tbody>

            <?php
            if($this->totalItems > $this->count):
                ?>
                <tfoot>
                <tr>
                    <?php $this->print_column_headers( false ); ?>
                </tr>
                </tfoot>
            <?php endif; ?>

        </table>
        <?php
        $this->display_tablenav( 'bottom' );
    }

    public function no_items() {
        echo 'Table empty';
    }
}
