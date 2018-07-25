<?php

/**
 * Framework class, to keep all settings encapsulated
 * Access to this singleton is via global $apollo13framework_a13
 */
class Apollo13Framework
{

    /**
     * current theme settings
     * @var array
     */
    private $theme_options = array();

    /**
     * Array of meta fields that depends on global settings
     * @var array
     */
    private $parents_of_meta = array();

    /**
     * structure of customizer panels, sections & fields
     * @var array
     */
    private $customizer_sections = array();

    /**
     * all default values for theme options
     * @var array
     */
    private $theme_options_defaults = array();

    /**
     * Array of default values of meta fields on current screen
     * @var array
     */
    public $defaults_of_meta = array();


    private $reset_user_css = false;


    /**
     * kind of constructor
     */
    function start()
    {
        /**
         * Define bunch of helpful paths and settings
         */
        define('A13FRAMEWORK_TPL_SLUG', 'rife-free');//it is not always same as directory of theme
        define('A13FRAMEWORK_OPTIONS_NAME_PART', 'Rife Free');
        define('A13FRAMEWORK_THEME_ID_NUMBER', '66');
        define('A13FRAMEWORK_OPTIONS_NAME', 'apollo13_option_rife');

        //theme root
        define('A13FRAMEWORK_TPL_URI', get_template_directory_uri());
        define('A13FRAMEWORK_TPL_DIR', get_template_directory());

        //plugins bundled with theme
        define('A13FRAMEWORK_TPL_PLUGINS', A13FRAMEWORK_TPL_URI . '/advance/plugins');
        define('A13FRAMEWORK_TPL_PLUGINS_DIR', A13FRAMEWORK_TPL_DIR . '/advance/plugins');

        //custom post type settings
        define('A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM', 'album');
//		define('A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM_SLUG', 'album'); //just to show it also exist - defined in real a bit lower
        define('A13FRAMEWORK_CUSTOM_POST_TYPE_WORK', 'work');
//		define('A13FRAMEWORK_CUSTOM_POST_TYPE_WORK_SLUG', 'work'); //just to show it also exist - defined in real a bit lower
        define('A13FRAMEWORK_CUSTOM_POST_TYPE_NAV_A', 'nava');
        define('A13FRAMEWORK_CUSTOM_POST_TYPE_PEOPLE', 'people');
        define('A13FRAMEWORK_CPT_WORK_TAXONOMY', 'work_genre');
        define('A13FRAMEWORK_CPT_ALBUM_TAXONOMY', 'genre');
        define('A13FRAMEWORK_CPT_PEOPLE_TAXONOMY', 'group');

        //misc theme globals
        define('A13FRAMEWORK_INPUT_PREFIX', 'a13_');
        define('A13FRAMEWORK_CONTENT_WIDTH', 800);

        $upload_dir = wp_upload_dir();
        define('A13FRAMEWORK_FILES', trailingslashit( $upload_dir['baseurl'] ) . 'apollo13_framework_files');
        define('A13FRAMEWORK_FILES_DIR', trailingslashit( $upload_dir['basedir'] ) . 'apollo13_framework_files');

        define('A13FRAMEWORK_IMPORTER_TMP_DIR', trailingslashit( $upload_dir['basedir'] ) . 'apollo13_tmp');
        define('A13FRAMEWORK_IMPORT_SERVER', 'https://api.apollo13.eu/file_sender'); //should be moved into apollo13_framework_files

        //generated css file directory
        define('A13FRAMEWORK_GENERATED_CSS', A13FRAMEWORK_FILES . '/css');
        define('A13FRAMEWORK_GENERATED_CSS_DIR', A13FRAMEWORK_FILES_DIR . '/css');



        //check for theme version(we try to get parent theme version)
        $theme_data = wp_get_theme();
        $have_parent = $theme_data->parent();
        //Using child theme
        if ($have_parent) {
            /** @noinspection PhpUndefinedFieldInspection */
            $t_ver = $theme_data->parent()->Version;
        }
        //Using default theme
        else {
            /** @noinspection PhpUndefinedFieldInspection */
            $t_ver = $theme_data->Version;
        }
        define('A13FRAMEWORK_THEME_VER', $t_ver);

        // ADD CUSTOMIZER SUPPORT
        if(is_customize_preview()){
            /** @noinspection PhpIncludeInspection */
            require_once( get_theme_file_path( 'advance/customizer.php' ));
            add_action( 'wp_loaded', array( $this, 'customizer_wp_loaded' ) );
            //perform option save while using customizer
            add_action('customize_save_after', array($this, 'customizer_customize_save_after'));
        }

        // ADMIN PART
        if (is_admin()) {
            /** @noinspection PhpIncludeInspection */
            require_once( get_theme_file_path( 'advance/admin/admin.php' ) );
            /** @noinspection PhpIncludeInspection */
            require_once( get_theme_file_path( 'advance/admin/admin-ajax.php' ) );
            /** @noinspection PhpIncludeInspection */
            require_once( get_theme_file_path( 'advance/admin/metaboxes.php' ) );
            /** @noinspection PhpIncludeInspection */
            require_once( get_theme_file_path( 'advance/admin/print-options.php' ) );


            // ADD ADMIN THEME PAGES
            /** @noinspection PhpIncludeInspection */
            require_once( get_theme_file_path( 'advance/apollo13-pages.php' ));
            /** @noinspection PhpIncludeInspection */
            require_once( get_theme_file_path( 'advance/apollo13-pages-functions.php' ));

            //ADD EXTERNAL PLUGINS
            /** @noinspection PhpIncludeInspection */
            require_once( get_theme_file_path( 'advance/inc/class-tgm-plugin-activation.php'));
            /** @noinspection PhpIncludeInspection */
            require_once( get_theme_file_path( 'advance/plugins/plugins-list.php' ) );

            // Warnings and notices that only admin should handle
            if (current_user_can('update_core')) {
                add_action( 'admin_notices', array(&$this, 'check_for_warnings') );
            }
        }
        //only for front-end
        else{
            // THEME FRONT-END SCRIPTS & STYLES
            /** @noinspection PhpIncludeInspection */
            require_once(get_theme_file_path( 'advance/head-scripts-styles.php'));

            //Images library Apollo13_Image_Resize
            /** @noinspection PhpIncludeInspection */
            require_once(get_theme_file_path( 'advance/inc/class-apollo13-image-resize.php'));

        }

        // ADD WPBakery Page Builder ADDONS
        //since VC 5.5.2 it should be load always
        /** @noinspection PhpIncludeInspection */
        require_once( get_theme_file_path( 'advance/vc-extend.php' ));

        // AFTER SETUP(supports for thumbnails, menus, languages, RSS etc.)
        add_action('after_setup_theme', array(&$this, 'setup'));

        //special files depending on framework generator needs
        if( is_file( get_theme_file_path( 'advance/envy.php' )) ){
            /** @noinspection PhpIncludeInspection */
            require_once get_theme_file_path( 'advance/envy.php');
        }
        if( is_file( get_theme_file_path( 'advance/rife.php') ) ){
            /** @noinspection PhpIncludeInspection */
            require_once get_theme_file_path( 'advance/rife.php');
        }

        // ADD MEGA MENU
        /** @noinspection PhpIncludeInspection */
        require_once(get_theme_file_path( 'advance/mega-menu.php'));

        // FUNCTION FOR MANAGING ALBUMS/WORKS
        /** @noinspection PhpIncludeInspection */
        require_once(get_theme_file_path( 'advance/cpt-admin.php'));

        // ADD SIDEBARS & WIDGETS
        /** @noinspection PhpIncludeInspection */
        require_once(get_theme_file_path( 'advance/widgets.php'));


        // UTILITIES
        /** @noinspection PhpIncludeInspection */
        require_once(get_theme_file_path( 'advance/utilities/_manager.php'));

        //get theme options from database
        $this->theme_options = get_option(A13FRAMEWORK_OPTIONS_NAME);

        //set default values for all fields & collect sections
        /** @noinspection PhpIncludeInspection */
        require_once(get_theme_file_path( 'advance/theme-options.php'));

        //set default setting if there is none(fresh install)
        if($this->theme_options === false){
            $file = get_theme_file_path( 'default-settings/default.php');
            if(file_exists($file)){
                $file_contents = include $file;
                $options = json_decode($file_contents, true);

                //SET THEME OPTIONS
                $this->set_options($options);
                $this->reset_user_css = true;
            }
        }
        //normal flow, setup options
        else{
            $this->load_options();
        }


        //defined Theme constants after getting theme options
        if( isset($this->theme_options['cpt_post_type_album']) && isset($this->theme_options['cpt_post_type_work']) ){
            define('A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM_SLUG', $this->theme_options['cpt_post_type_album']);
            define('A13FRAMEWORK_CUSTOM_POST_TYPE_WORK_SLUG', $this->theme_options['cpt_post_type_work']);
        }else{
            define('A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM_SLUG', 'album');
            define('A13FRAMEWORK_CUSTOM_POST_TYPE_WORK_SLUG', 'work');
        }

        //used to compere with global options
        $this->collect_meta_parents();

	    /**
         * Remove Menu Panel from customizer
         * @param WP_Customize_Manager $wp_customize
         */
        function apollo13framework_remove_customizer_settings( $wp_customize ){
            //due to https://core.trac.wordpress.org/ticket/33411

            //$wp_customize->remove_panel('nav_menus');
            $wp_customize->get_panel( 'nav_menus' )->active_callback = '__return_false';

        }
        add_action( 'customize_register', 'apollo13framework_remove_customizer_settings', 20 );
        add_action( 'admin_init', array($this,'autoimport_wpbakery_theme_grid_items') );
    }

    /**
     * registers panels, sections & fields for customizer. Prepares default values for theme options
     *
     * @param $section array of panel details OR section details & fields
     */
    function set_sections($section){
        //we need whole structure only when customizer is used
        if(is_customize_preview()){
            //section
            if(isset($section['subsection'])){
                end($this->customizer_sections);
                $key = key($this->customizer_sections);
                $this->customizer_sections[$key]['sections'][] = $section;
            }
            //panel
            else{
                $this->customizer_sections[] = $section;
            }
        }

        //collect default values
        if(isset($section['fields']) && is_array($section['fields']) && ! empty( $section['fields'] )){
            foreach($section['fields'] as $params ){
                $this->theme_options_defaults[$params['id']] = isset($params['default'])? $params['default'] : '';
            }
        }
    }


    /**
     * returns panels, sections & fields for customizer
     */
    function get_sections(){
        return $this->customizer_sections;
    }

    function autoimport_wpbakery_theme_grid_items() {
        // get the file
        $check = get_option( 'apollo13_grid_elements_loaded' );

        //done or VC not active
        if($check == 1 || !defined( 'WPB_VC_VERSION' ) ){
            return;
        }

        $import_file = A13FRAMEWORK_TPL_DIR . '/autoimport/import.xml';
        if( !file_exists($import_file) ){
            return;
        }

        /** @noinspection PhpIncludeInspection */
        require_once( get_theme_file_path( 'advance/admin/a13-wordpress-importer/class-apollo13-framework-import.php' ) );

        $importer = new Apollo13Framework_Import( );
        $importer->import( $import_file, 1);

        update_option( 'apollo13_grid_elements_loaded', '1', false);
    }

    /**
     * used in customizer to prepare settings after refresh in customizer
     */
    function customizer_wp_loaded() {
        $this->theme_options = get_option(A13FRAMEWORK_OPTIONS_NAME);
        $this->load_options();
    }

    /**
     * Refresh options and generate user.css file after save in customizer
     */
    function customizer_customize_save_after()
    {
        $this->theme_options = get_option(A13FRAMEWORK_OPTIONS_NAME);

        //remember what are slugs before save
        $pre_save_album = $this->get_option('cpt_post_type_album') ;
        $pre_save_work = $this->get_option('cpt_post_type_work') ;

        //check slugs after save
        $after_save_album = $this->get_option('cpt_post_type_album') ;
        $after_save_work = $this->get_option('cpt_post_type_work') ;

        $this->generate_user_css();

        //compare slugs and flush if there is a difference
        if( !($pre_save_album === $after_save_album && $pre_save_work === $after_save_work) ){
            //write option to force rewrite flush
            update_option('a13_force_to_flush','on');
        }
    }

    /**
     * Various setup actions for setting up theme for WordPress
     */
    function setup()
    {
        global $content_width;
        //content width
        if (!isset($content_width)) {
            $content_width = A13FRAMEWORK_CONTENT_WIDTH;
        }


        if (
            //forced refresh
            $this->reset_user_css ||
            //on fresh theme install/update
            !file_exists($this->user_css_name())
            //or customizer update after giving creds to FTP
             || (is_admin() && get_option('a13_user_css_update') === 'on')
        ) {
            $this->generate_user_css();
        }

        //if file system is not in direct mode, we need to ask for FTP creds to create user.css file
        if(is_admin() && get_option('a13_user_css_update') === 'on'){
            add_action( 'admin_notices', array(&$this, 'notice_about_user_css'), 0 );
        }

        //LANGUAGE
        load_theme_textdomain( 'rife-free', get_theme_file_path( 'languages' ) );

        // Featured image support
        add_theme_support('post-thumbnails');

        // Add default posts and comments RSS feed links to head
        add_theme_support('automatic-feed-links');

        //Let WordPress manage the document title.
        add_theme_support('title-tag');

        // Add post formats
        add_theme_support('post-formats', array(
            'aside',
            'chat',
            'gallery',
            'image',
            'link',
            'quote',
            'status',
            'video',
            'audio'
        ));

        // Switches default core markup for search form, comment form, and comments
        // to output valid HTML5.
        add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));

        // WooCommerce support
        add_theme_support('woocommerce');
        //new thumbs in WooCommerce 3.0.0
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );

        // Indicate widget sidebars can use selective refresh in the Customizer.
        add_theme_support( 'customize-selective-refresh-widgets' );

        //Header Footer Elementor Plugin support
        add_theme_support( 'header-footer-elementor' );

        // Register custom menu positions
        register_nav_menus(array(
            'header-menu' => __( 'Site Navigation', 'rife-free' ),
            'top-bar-menu' => __( 'Alternative short top bar menu', 'rife-free' ),
        ));
    }

    /**
     * Displays FTP form in case of wrong permission or ownership to user directory
     */
    function notice_about_user_css(){
        echo '<div class="notice-warning notice">';
        echo '<p>'.
             sprintf(
                /* translators: %s: user CSS file name */
                esc_html__( 'Creating %s file requires access to your FTP account. It is caused by the way your server is configured. This file is required so changes done in theme settings can take effect.', 'rife-free' ),
                '<strong>'.esc_html( $this->user_css_name() ).'</strong>'
             ).
             '</p>';
        echo '<p>'.
             sprintf(
                /* translators: %1$s: directory path, %2$s: directory path */
                esc_html__( 'Changing permissions or ownership to directory: %1$s and/or %2$s should fix the issue for need of your FTP credentials.', 'rife-free' ),
                '<strong>'.esc_html( A13FRAMEWORK_TPL_DIR ).'</strong>',
                '<strong>'.esc_html( A13FRAMEWORK_GENERATED_CSS_DIR ).'</strong>'
             ).
             '</p>';

        //this will cause form from WP_Filesystem to display
        $this->generate_user_css(false);
        echo '</div>';
    }

    /**
     * Function for warnings that should be displayed in admin area
     */
    function check_for_warnings()
    {
        $notices = array();
        $valid_tags = array(
            'a' => array(
                'href' => array(),
            ),
        );
        // Notice if dir for user settings is no writable
        if (!is_writeable(A13FRAMEWORK_GENERATED_CSS_DIR)) {
            /* translators: %s: directory path */
            $notices['user-css'] = sprintf( esc_html__( 'Warning - directory %s is not writable.', 'rife-free' ), A13FRAMEWORK_GENERATED_CSS_DIR);
        }

        //NOTICE IF CPT SLUG IS TAKEN
        // albums
        $r = new WP_Query(array('post_type' => array('post', 'page'), 'name' => A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM_SLUG));
        if ($r->have_posts() && strlen(A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM_SLUG) ) {
            /* translators: %s: URL */
            $notices['albums-slug'] = sprintf( __( 'Warning - slug for Albums is taken by page or post! It may cause problems with displaying albums. Edit slug of <a href="%s">this post</a> to make sure everything works good.', 'rife-free' ), esc_url( site_url( '/' . A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM_SLUG ) ) );
        }
        // Reset the global $the_post as this query have stomped on it
        wp_reset_postdata();

        // works
        $r = new WP_Query(array('post_type' => array('post', 'page'), 'name' => A13FRAMEWORK_CUSTOM_POST_TYPE_WORK_SLUG));
        if ($r->have_posts() && strlen(A13FRAMEWORK_CUSTOM_POST_TYPE_WORK_SLUG)) {
            /* translators: %s: URL */
            $notices['works-slug'] = sprintf( __( 'Warning - slug for Works is taken by page or post! It may cause problems with displaying works. Edit slug of <a href="%s">this post</a> to make sure everything works good.', 'rife-free' ), esc_url( site_url( '/' . A13FRAMEWORK_CUSTOM_POST_TYPE_WORK_SLUG ) ) );
        }
        // Reset the global $the_post as this query have stomped on it
        wp_reset_postdata();

        // Display all error notices
        foreach ($notices as $id => $notice) {
            //show notice only if it wasn't dismissed by user
            if( !apollo13framework_is_admin_notice_active($id) ){
                return;
            }
            echo '<div class="a13fe-admin-notice notice notice-error is-dismissible" data-notice_id="'.esc_attr($id).'"><p>' . wp_kses( $notice, $valid_tags ) . '</p></div>';
        }

        do_action( 'apollo13framework_theme_notices' );
    }

    /**
     * Prepare all theme settings to be ready for read
     */
    public function load_options()
    {
        //prepare custom sidebars
        if ( isset($this->theme_options['custom_sidebars']) && is_array($this->theme_options['custom_sidebars'])) {
            $tmp = array();
            foreach ($this->theme_options['custom_sidebars'] as $id => $sidebar) {
                //skip if left empty or not set name
                if($sidebar === NULL || strlen($sidebar) === 0){
                    continue;
                }
                array_push($tmp, array('id' => 'apollo13-sidebar_' . (1 + $id), 'name' => $sidebar));
            }
            $this->theme_options['custom_sidebars'] = $tmp;
        }

        //save memory
        unset($this->theme_options_defaults);

        //finally loaded options
    }


    /**
     * Overwrite current theme settings
     *
     * @param array $overload_options options we want to set
     */
    public function set_options( $overload_options = array() )
    {
        if( is_array($overload_options) && count($overload_options) > 0){
            update_option(A13FRAMEWORK_OPTIONS_NAME, $overload_options);

            $this->theme_options = $overload_options;

            //refresh
            $this->load_options();
        }
    }

    /**
     * Get one of theme settings
     *
     * @param string $index   setting id
     *
     * @param string $default default setting when option is not present
     *
     * @param bool   $filter should filter be used
     *
     * @return mixed
     */
    public function get_option($index, $default = '', $filter = true)
    {
        $option_to_return = $default;
        if ($index != '' && isset($this->theme_options[$index])) {
            $option_to_return = $this->theme_options[$index];
        }

        //for customizer we don't use filters as it mess controls behaviour.
        //JavaScript can't know about changes in filters, so it hides/shows options, and PHP then reverts this cause of filter actions
        //good and only example is vertical header in boxed layout
        if(!$filter){
            return $option_to_return;
        }
        //apply filters to returned value if some special treating is needed
        return apply_filters('a13_options_'.$index, $option_to_return );
    }

    /**
     * Get url only from media type theme setting
     *
     * @param string $index setting id
     *
     * @return string URL
     */
    public function get_option_media_url($index)
    {
        $option = $this->get_option($index);
        if (is_array($option)) {
            if (isset($option['url'])) {
                return $option['url']; //we got URL
            } else {
                return ''; //empty string as it is probably not set yet
            }
        }

        return $option;//not an array? then probably it is saved as string
    }


    /**
     * Get rgba only from color type theme setting
     *
     * @param string $index setting_id
     *
     * @return string URL
     */
    public function get_option_color_rgba( $index )
    {
        $option = $this->get_option( $index );
        if ( is_array( $option ) ) {
            if ( isset( $option['rgba'] ) ) {
                return $option['rgba']; //we got RGBA
            } elseif ( isset( $option['color'] ) && isset( $option['alpha'] ) ) {
                return apollo13framework_hex2rgba( $option['color'], $option['alpha'] ); //we got RGBA
            } else {
                return ''; //empty string as it is probably not set yet
            }
        }

        return $option;//not an array? then probably it is saved as string
    }

    /**
     * Get all settings. Used for exporting theme options
     *
     * @return array
     */
    public function get_options_array()
    {
        return $this->theme_options;
    }

    /**
     * Prepares var $parents_of_meta
     */
    private function collect_meta_parents()
    {
        /** @noinspection PhpIncludeInspection */
        require_once(get_theme_file_path( 'advance/meta.php'));

        $option_func = array(
            'post',
            'page',
            'album',
            'work',
            'people',
//            'images_manager' //no parent options here
        );

        foreach ($option_func as $function) {
            $function_to_call = 'apollo13framework_meta_boxes_' . $function;
            $family = str_replace('_layout', '', $function); //for consistent families

            foreach ($function_to_call() as $meta) {
                if (isset($meta['global_value'])) {
                    $this->parents_of_meta[$family][$meta['id']]['global_value'] = $meta['global_value'];
                }
                if (isset($meta['parent_option'])) {
                    $this->parents_of_meta[$family][$meta['id']]['parent_option'] = $meta['parent_option'];
                }
            }
        }
    }

    /**
     * Prepares list off all meta fields that have visibility dependencies and second list of possible switches with dependent fields
     */
    public function get_meta_required_array() {
        global $pagenow;
        $list_of_requirements = array();
        $list_of_dependent    = array();
        $meta_boxes           = array();


        $post_type = '';
        if ('post.php' == $pagenow && isset($_GET['post']) ) {
            // Will occur only in this kind of screen: /wp-admin/post.php?post=285&action=edit
            // and it can be a Post, a Page or a CPT
            $post_type = get_post_type( sanitize_text_field( wp_unslash( $_GET['post'] ) ) );
        }
        //if it is "new post" page
        elseif('post-new.php' == $pagenow ) {
            $post_type = isset($_GET['post_type']) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : 'post';
        }

        if(strlen($post_type)){
            switch ( $post_type ) {
                case 'post':
                    $meta_boxes = apollo13framework_meta_boxes_post();
                    break;
                case 'page':
                    $meta_boxes = apollo13framework_meta_boxes_page();
                    break;
                case A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM:
                    $meta_boxes = array_merge( apollo13framework_meta_boxes_album(), apollo13framework_meta_boxes_images_manager() );
                    break;
                case A13FRAMEWORK_CUSTOM_POST_TYPE_WORK:
                    $meta_boxes = array_merge( apollo13framework_meta_boxes_work(), apollo13framework_meta_boxes_images_manager() );
                    break;
            }

            foreach ( $meta_boxes as &$meta ) {
                //check is it prototype
                if ( isset( $meta['required'] ) ) {
                    $required = $meta['required'];

                    //fill list of required condition for each control
                    $list_of_requirements[ $meta['id'] ] = $required;

                    //fill list of controls that activate/deactivate other
                    //we have more then one required condition
                    if(is_array($required[0]) ){
                        foreach($required as $dependency){
                            $list_of_dependent[$dependency[0]][] = $meta['id'];
                        }
                    }
                    //we have only one required condition
                    else{
                        $list_of_dependent[$required[0]][] = $meta['id'];
                    }
                }
            }
        }

        return array($list_of_requirements, $list_of_dependent);
    }

    /**
     * Get name of user CSS file in case of multi site or switcher
     *
     * @param bool|false $public if false it will return path for include,
     *                           if true it will return path for source(http://path.to.file)
     *
     * @return string path to file
     */
    function user_css_name($public = false)
    {
        $name = ($public ? A13FRAMEWORK_GENERATED_CSS : A13FRAMEWORK_GENERATED_CSS_DIR) . '/user'; /* user.css - comment just for easier searching */
        if (is_multisite()) {
            //add blog id to file
            $name .= '_' . get_current_blog_id();
        }

        return $name . '.css';
    }

    /**
     * Make user CSS file from theme layout options
     *
     * @param bool $hide_errors
     *
     */
    function generate_user_css( $hide_errors = true ) {
        $save_result = 1;

        if($hide_errors){
            ob_start();
        }

        //prepare file system

        //just in case have these files included
        require_once(ABSPATH . '/wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/template.php');

        //we are checking if file system can operate without FTP creds
        $url = wp_nonce_url(admin_url(),'');
        if (false === ($creds = request_filesystem_credentials($url, '', false, false, null) ) ) {
            $save_result = 0;
        }
        elseif ( ! WP_Filesystem($creds) ) {
            request_filesystem_credentials($url, '', true, false, null);
            $save_result = 0;
        }

        //if we have good FTP creds or system operates with "direct" method
        if($save_result === 1){
            global $wp_filesystem;
            /* @var $wp_filesystem WP_Filesystem_Base */

            //make dir if it doesn't exist yet
            if ( ! is_dir( A13FRAMEWORK_GENERATED_CSS_DIR ) ) {
                wp_mkdir_p( A13FRAMEWORK_GENERATED_CSS_DIR );
            }

            if (is_writable(A13FRAMEWORK_GENERATED_CSS_DIR) ) {
                $file = $this->user_css_name();

                //in case of FTP access we need to make sure we have proper path
                $file = str_replace(ABSPATH, $wp_filesystem->abspath(), $file);

                /** @noinspection PhpIncludeInspection */
                require_once(get_theme_file_path( 'advance/user-css.php'));
                $css = apollo13framework_get_user_css();
                $wp_filesystem->put_contents(
                    $file,
                    $css,
                    FS_CHMOD_FILE
                );

                //remove any pending update request
                update_option('a13_user_css_update','off');
            }
        }
        //we couldn't save
        else{
            update_option('a13_user_css_update','on');
        }

        if($hide_errors){
            ob_end_clean();
        }
    }

    /**
     * Retrieves meta setting with checking for parent settings, and global settings
     *
     * @param string $field name of meta setting
     * @param bool|false $id ID of post. If not passed it will try to get one for current loop
     *
     * @return bool|mixed|null|string field value
     */
    function get_meta($field, $id = false)
    {
        $family = '';

        if (!$id && apollo13framework_is_no_property_page()) {
            return null; //we can't get meta field for that page
        } else {
            if (!$id) {
                $id = get_the_ID();
            }

            $meta = trim(get_post_meta($id, $field, true));
        }

        if ($id) {
            $post_type = get_post_type($id);
            //get family to check for parent option
            if ($post_type == A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM) {
                $family = 'album';
            } else if ($post_type == A13FRAMEWORK_CUSTOM_POST_TYPE_WORK) {
                $family = 'work';
            } elseif ($post_type === 'page' ) {
                $family = 'page';
            } elseif (is_single($id)) {
                $family = 'post';
            }

            $field = substr($field, 1); //remove '_'

            //if has any parent
            if (isset($this->parents_of_meta[$family][$field])) {
                $parent = $this->parents_of_meta[$family][$field];

                //meta points to global setting
                if (isset($parent['global_value']) && ($meta == $parent['global_value'] || strlen($meta) == 0)) {
                    if (isset($parent['parent_option'])) {
                        $meta = $this->get_option($parent['parent_option']);
                    }
                }
            }

            return $meta;
        }

        return false;
    }

    /**
     * Returns list of all available in theme social icons with need additional info
     *
     * @param string $what - what should array consist of:
     *                     names    : Readable names
     *                     classes  : CSS classes used on front-end
     *                     empty    : only IDs are returned
     *
     * @return array requested list of social icons
     */
    function get_social_icons_list($what = 'names'){
        $icons = array(
            /* id         => array(class, label)*/
            '500px'       => array( 'fa fa-500px', '500px' ),
            'behance'     => array( 'fa fa-behance', 'Behance' ),
            'bitbucket'   => array( 'fa fa-bitbucket', 'Bitbucket' ),
            'codepen'     => array( 'fa fa-codepen', 'CodePen' ),
            'delicious'   => array( 'fa fa-delicious', 'Delicious' ),
            'deviantart'  => array( 'fa fa-deviantart', 'Deviantart' ),
            'digg'        => array( 'fa fa-digg', 'Digg' ),
            'dribbble'    => array( 'fa fa-dribbble', 'Dribbble' ),
            'dropbox'     => array( 'fa fa-dropbox', 'Dropbox' ),
            'mailto'      => array( 'fa fa-envelope-o', 'E-mail' ),
            'facebook'    => array( 'fa fa-facebook', 'Facebook' ),
            'flickr'      => array( 'fa fa-flickr', 'Flickr' ),
            'foursquare'  => array( 'fa fa-foursquare', 'Foursquare' ),
            'github'      => array( 'fa fa-git', 'Github' ),
            'googleplus'  => array( 'fa fa-google-plus', 'Google Plus' ),
            'instagram'   => array( 'fa fa-instagram', 'Instagram' ),
            'lastfm'      => array( 'fa fa-lastfm', 'Lastfm' ),
            'linkedin'    => array( 'fa fa-linkedin', 'Linkedin' ),
            'paypal'      => array( 'fa fa-paypal', 'Paypal' ),
            'pinterest'   => array( 'fa fa-pinterest-p', 'Pinterest' ),
            'reddit'      => array( 'fa fa-reddit-alien', 'Reddit' ),
            'rss'         => array( 'fa fa-rss', 'RSS' ),
            'sharethis'   => array( 'fa fa-share-alt', 'Sharethis' ),
            'skype'       => array( 'fa fa-skype', 'Skype' ),
            'slack'       => array( 'fa fa-slack', 'Slack' ),
            'snapchat'    => array( 'fa fa-snapchat-ghost', 'Snapchat' ),
            'spotify'     => array( 'fa fa-spotify', 'Spotify' ),
            'steam'       => array( 'fa fa-steam', 'Steam' ),
            'stumbleupon' => array( 'fa fa-stumbleupon', 'Stumbleupon' ),
            'tripadvisor' => array( 'fa fa-tripadvisor', 'TripAdvisor' ),
            'tumblr'      => array( 'fa fa-tumblr', 'Tumblr' ),
            'twitter'     => array( 'fa fa-twitter', 'Twitter' ),
            'viadeo'      => array( 'fa fa-viadeo', 'Viadeo' ),
            'vimeo'       => array( 'fa fa-vimeo', 'Vimeo' ),
            'vine'        => array( 'fa fa-vine', 'Vine' ),
            'vkontakte'   => array( 'fa fa-vk', 'VKontakte' ),
            'whatsapp'    => array( 'fa fa-whatsapp', 'Whatsapp' ),
            'wordpress'   => array( 'fa fa-wordpress', 'WordPress' ),
            'xing'        => array( 'fa fa-xing', 'Xing' ),
            'yahoo'       => array( 'fa fa-yahoo', 'Yahoo' ),
            'yelp'        => array( 'fa fa-yelp', 'Yelp' ),
            'youtube'     => array( 'fa fa-youtube', 'YouTube' ),
        );

        $icons = apply_filters('apollo13framework_social_icons_list', $icons );

        /* SAMPLE USAGE */
        /*
        add_filter('apollo13framework_social_icons_list', function($icons){
            $icons['youtube']     =  array( 'fa fa-youtube-play', 'Youtube' );
            $icons['new_service'] =  array( 'fa fa-star', 'My social' );

            return $icons;
        });
         *
        */

        $result = array();

        //return classes
        if($what === 'classes'){
            foreach( $icons as $id => $icon ){
                $result[$id] = $icon[0];
            }
        }

        //empty values
        elseif($what === 'empty'){
            foreach( $icons as $id => $icon ){
                $result[$id] = '';
            }
        }

        //return names
        else{
            foreach( $icons as $id => $icon ){
                $result[$id] = $icon[1];
            }
        }

        return $result;
    }


    function get_standard_fonts_list(){
        return array(
            "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" => "System Font(Native)",
            "Arial, Helvetica, sans-serif"                                                        => "Arial",
            "'Arial Black', Gadget, sans-serif"                                                   => "Arial Black",
            "'Bookman Old Style', serif"                                                          => "Bookman Old Style",
            "'Comic Sans MS', cursive"                                                            => "Comic Sans MS",
            "Courier, monospace"                                                                  => "Courier",
            "Garamond, serif"                                                                     => "Garamond",
            "Georgia, serif"                                                                      => "Georgia",
            "Impact, Charcoal, sans-serif"                                                        => "Impact",
            "'Lucida Console', Monaco, monospace"                                                 => "Lucida Console",
            "'Lucida Sans Unicode', 'Lucida Grande', sans-serif"                                  => "Lucida Sans Unicode",
            "'MS Sans Serif', Geneva, sans-serif"                                                 => "MS Sans Serif",
            "'MS Serif', 'New York', sans-serif"                                                  => "MS Serif",
            "'Palatino Linotype', 'Book Antiqua', Palatino, serif"                                => "Palatino Linotype",
            "Tahoma,Geneva, sans-serif"                                                           => "Tahoma",
            "'Times New Roman', Times,serif"                                                      => "Times New Roman",
            "'Trebuchet MS', Helvetica, sans-serif"                                               => "Trebuchet MS",
            "Verdana, Geneva, sans-serif"                                                         => "Verdana",
        );
    }

    function check_for_valid_license(){
        return apply_filters('apollo13framework_valid_license', false);
    }

    function check_is_import_allowed(){
        return apply_filters('apollo13framework_is_import_allowed', $this->check_for_valid_license());
    }

    function register_new_license_code($code){
        $out = array();
        return apply_filters('apollo13framework_register_license', $out, $code);
    }

    function get_license_code(){
        return apply_filters('apollo13framework_get_license', false);
    }

    function get_docs_link($location = ''){
        $locations = apply_filters( 'apollo13framework_docs_locations', array(
            'license-code'           => 'docs/getting-started/where-i-can-find-license-code/',
            'header-color-variants'  => 'docs/customizing-the-theme/header/variant-light-dark-overwrites/',
            'importer-configuration' => 'docs/installation-updating/importing-designs/importer-configuration/',
            'export'                 => 'docs/installation-updating/exporting-theme-options/',
        ) );

        if(strlen($location) && array_key_exists($location, $locations)){
            $location = $locations[$location];
        }

        return apply_filters('apollo13framework_docs_address', 'https://rifetheme.com/apollo13-framework/').$location;
    }

    /**
     * Returns hidden code to protect it from easy copy
     *
     * @param $code string
     *
     * @return string "stared code"
     */
    function mask_code($code){
        //"star" code
        $changed_code = preg_replace('/[a-z0-9]/i', '*', $code);

        //parts of original code
        $first_chars = substr($code, 0, 2);
        $last_chars = substr($code, -2, 2);

        //merge
        $changed_code = substr_replace($changed_code, $first_chars, 0, 2);
        $changed_code = substr_replace($changed_code, $last_chars, -2, 2);

        return $changed_code;
    }
}
