<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       github.com/abuzer
 * @since      1.0.0
 *
 * @package    Wp_Export_Db_Sql_File
 * @subpackage Wp_Export_Db_Sql_File/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Export_Db_Sql_File
 * @subpackage Wp_Export_Db_Sql_File/admin
 * @author     GreeLogix <abuzer@greelogix.com>
 */
class Wp_Export_Db_Sql_File_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Export_Db_Sql_File_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Export_Db_Sql_File_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-export-db-sql-file-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Export_Db_Sql_File_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Export_Db_Sql_File_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-export-db-sql-file-admin.js', array('jquery'), $this->version, false);
    }

}

/**
 * Generated by the WordPress Option Page generator
 * at http://jeremyhixon.com/wp-tools/option-page/
 */
class WPEDBFExportDB {

    private $export_db_options;

    public function __construct() {
        add_action('admin_menu', array($this, 'export_db_add_plugin_page'));
        add_action('admin_init', array($this, 'export_db_page_init'));
    }

    public function export_db_add_plugin_page() {
        add_management_page(
                'Export DB', // page_title
                'Export DB', // menu_title
                'manage_options', // capability
                'export-db', // menu_slug
                array($this, 'export_db_create_admin_page') // function
        );
    }

    public function backup_database($con, $tables = "", $backup_file_name) {
        if (empty($tables)) {
            $tables_in_database = mysqli_query($con, "SHOW TABLES");
            if (mysqli_num_rows($tables_in_database) > 0) {
                while ($row = mysqli_fetch_row($tables_in_database)) {
                    array_push($tables, $row[0]);
                }
            }
        } else {
            // Checking for any table that doesn't exists in the database
            $existed_tables = array();
            foreach ($tables as $table) {
                if (mysqli_num_rows(mysqli_query($con, "SHOW TABLES LIKE '" . $table . "'")) == 1) {
                    array_push($existed_tables, $table);
                }
            }
            $tables = $existed_tables;
        }
        $contents = "--\n-- Database: `" . DB_NAME . "`\n--\n-- --------------------------------------------------------\n\n\n\n";
        foreach ($tables as $table) {
            $result = mysqli_query($con, "SELECT * FROM " . $table);
            $no_of_columns = mysqli_num_fields($result);
            $no_of_rows = mysqli_num_rows($result);
            //Get the query for table creation
            $table_query = mysqli_query($con, "SHOW CREATE TABLE " . $table);
            $table_query_res = mysqli_fetch_row($table_query);
            $contents .= "--\n-- Table structure for table `" . $table . "`\n--\n\n";
            $contents .= $table_query_res[1] . ";\n\n\n\n";
            /**
             *  $insert_limit -> Limits the number of row insertion in a single INSERT query. 
             *           Maximum 100 rowswe will insert in a single INSERT query.
             *  $insert_count -> Counts the number of rows are added to the INSERT query. 
             *                   When it will reach the insert limit it will set to 0 again.
             *  $total_count  -> Counts the overall number of rows are added to the INSERT query of a single table.
             */
            $insert_limit = 100;
            $insert_count = 0;
            $total_count = 0;
            while ($result_row = mysqli_fetch_row($result)) {
                /**
                 * For the first time when $insert_count is 0 and when $insert_count reached the $insert_limit 
                 * and again set to 0 this if condition will execute and append the INSERT query in the sql file. 
                 */
                if ($insert_count == 0) {
                    $contents .= "--\n-- Dumping data for table `" . $table . "`\n--\n\n";
                    $contents .= "INSERT INTO " . $table . " VALUES ";
                }
                //Values part of an INSERT query will start from here eg. ("1","mitrajit","India"),
                $insert_query = "";
                $contents .= "\n(";
                for ($j = 0; $j < $no_of_columns; $j++) {
                    //Replace any "\n" with "\\n" escape character.
                    //addslashes() function adds escape character to any double quote or single quote eg, \" or \'
                    $insert_query .= "'" . str_replace("\n", "\\n", addslashes($result_row[$j])) . "',";
                }
                //Remove the last unwanted comma (,) from the query.
                $insert_query = substr($insert_query, 0, -1) . "),";
                /*
                 *  If $insert_count reached to the insert limit of a single INSERT query
                 *  or $insert count reached to the number of total rows of a table
                 *  or overall total count reached to the number of total rows of a table
                 *  this if condition will exceute.
                 */
                if ($insert_count == ($insert_limit - 1) || $insert_count == ($no_of_rows - 1) || $total_count == ($no_of_rows - 1)) {
                    //Remove the last unwanted comma (,) from the query and append a semicolon (;) to it
                    $contents .= substr($insert_query, 0, -1);
                    $contents .= ";\n\n\n\n";
                    $insert_count = 0;
                } else {
                    $contents .= $insert_query;
                    $insert_count++;
                }
                $total_count++;
            }
        }
        //Set the HTTP header of the page.
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . $backup_file_name . "\"");
        echo $contents;
        exit;
    }

    public function export_db_create_admin_page() {
        $this->export_db_options = get_option('export_db_option_name');
        ?>
        <div class="wrap">
            <h2>Export DB</h2>
            <p></p>
        <?php settings_errors(); ?>

            <form method="post" action="<?php echo site_url() ?>/wp-admin/tools.php?page=export-db">
                <input type="text" name="export_filename" value="<?php echo $_SERVER['SERVER_NAME'] . '-' . time(); ?>" /> 
                <input type="submit" value="Download file" />
            </form>
        </div>
        <?php
    }

    public function export_db_page_init() {

        if (isset($_POST['export_filename'])) {

            $con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("Error : ");
            if (mysqli_connect_errno($con)) {
                echo "Failed to connect MySQL: " . mysqli_connect_error();
            } else {
                //If you want to export or backup the whole database then leave the $table variable as it is
                //If you want to export or backup few table then mention the names of the tables within the $table array like below
                //eg, $tables = array("wp_commentmeta", "wp_comments", "wp_options");
                $tables = array();
                if (!empty($_POST['export_filename'])) {
                    $export_filename = sanitize_title( sanitize_text_field( $_POST['export_filename'] ) );
                    $backup_file_name = $export_filename.'.sql';
                } else {
                    $backup_file_name = DB_NAME . ".sql";
                }

                $this->backup_database($con, $tables, $backup_file_name);
            }
        }


        register_setting(
                'export_db_option_group', // option_group
                'export_db_option_name', // option_name
                array($this, 'export_db_sanitize') // sanitize_callback
        );

        add_settings_section(
                'export_db_setting_section', // id
                'Settings', // title
                array($this, 'export_db_section_info'), // callback
                'export-db-admin' // page
        );

        add_settings_field(
                'filename_0', // id
                'Filename', // title
                array($this, 'filename_0_callback'), // callback
                'export-db-admin', // page
                'export_db_setting_section' // section
        );
    }

    public function export_db_sanitize($input) {
        $sanitary_values = array();
        if (isset($input['filename_0'])) {
            $sanitary_values['filename_0'] = sanitize_text_field($input['filename_0']);
        }

        return $sanitary_values;
    }

    public function export_db_section_info() {
        
    }

    public function filename_0_callback() {
        printf(
                '<input class="regular-text" type="text" name="export_db_option_name[filename_0]" id="filename_0" value="%s">',
                isset($this->export_db_options['filename_0']) ? esc_attr($this->export_db_options['filename_0']) : ''
        );
    }

}

if (is_admin())
    $export_db = new WPEDBFExportDB();

/* 
 * Retrieve this value with:
 * $export_db_options = get_option( 'export_db_option_name' ); // Array of All Options
 * $filename_0 = $export_db_options['filename_0']; // Filename
 */
