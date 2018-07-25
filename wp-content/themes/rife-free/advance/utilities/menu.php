<?php
/**
 * CUSTOM MENU WALKER CLASSES
 *
 * Functions that are connected to handling menus
 */


/**
 * Class A13FRAMEWORK_menu_walker
 * Used in main menu
 */
class A13FRAMEWORK_menu_walker extends Walker_Nav_Menu {

	/**
	 * @see   Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string       $output Passed by reference. Used to append additional content.
	 * @param object       $item   Menu item data object.
	 * @param int          $depth  Depth of menu item. Used for padding.
	 * @param array|object $args
	 * @param int          $id     Menu item ID.
	 */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $apollo13framework_a13;
        global $wp_query;

        static $mega_menu = false; //are we printing mega menu right now
        static $mega_menu_counter = 0; //we count columns of mega menu
        static $mm_columns = 1; //to remember how many columns this mega menu has
        static $displaying_html = false; //we don't display descendants if displaying html
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        //mega_menu

        if($depth === 0){
            $displaying_html = false; //reset
            if($item->a13_mega_menu === '1'){
                $mega_menu_counter = 0;
                $mega_menu = true;
                $mm_columns = $item->a13_mm_columns;
                $classes[] = 'mega-menu';
                $classes[] = 'mm_columns_'.$item->a13_mm_columns;
            }
            else{
                $mega_menu = false;
                $classes[] = 'normal-menu';
            }
        }
        if($depth === 1 && $mega_menu){
            if($mega_menu_counter % $mm_columns === 0){
                $classes[] = 'mm_new_row';
            }
            if($item->a13_mm_remove_item === '1'){
                $classes[] = 'mm_dont_show';
            }
            if(strlen($item->a13_mm_html)){
                $displaying_html = true;
                $classes[] = 'html_item';
            }
            else{
                $displaying_html = false;
            }

            $mega_menu_counter++;
        }

        //don't display descendants if displaying html
        if($depth > 1 && $displaying_html){
            //we print opening of list element cause wordpress will print close tag of this element anyway.
            $output .= '<li>';
            return;
        }

        //check if this element is a OnePage Navigation Pointer
        $is_nava = $item->object === 'nava';
        if( $is_nava ){
            $home_page_id = get_option('page_on_front');
            $frontpage = get_post( $home_page_id );

            $current_page_slug = $wp_query->post->post_name;
            $nava_page_slug = get_post_meta( $item->object_id, 'a13_nava_page_slug', true );
            $nava_item_anchor = get_the_title( $item->object_id );
            $classes[] = 'a13_one_page';

            //it is on different page - absolute path
            if( $nava_page_slug != $current_page_slug ){
                //if nava leads to front-page
                if( $nava_page_slug == $frontpage->post_name ){
                    $url = get_home_url();
                }
                //it is on sub-page
                else{
                    $url = get_site_url(null, $nava_page_slug);
                }
                $item->url = $url.'/#'.$nava_item_anchor;
            }
            //it is on current page - just anchor
            else{
                $item->url = '#'.$nava_item_anchor;
            }
        }

        //checks if this element is parent element
        $is_parent    = (bool) array_search( 'menu-parent-item', $classes );
        $is_current_ancestor = false;
        $icon         = trim( $item->a13_item_icon );
        $dont_link    = $item->a13_unlinkable === '1';
        $name         = apply_filters( 'the_title', $item->title, $item->ID );
        $hover_effect = $apollo13framework_a13->get_option( 'menu_hover_effect' );
        $excluded_effect = in_array( $hover_effect, array('none','show_icon') );

        //check if it is vertical header and should sub-menu be open
        if( $apollo13framework_a13->get_option( 'header_type' ) === 'vertical' &&
            $apollo13framework_a13->get_option( 'submenu_active_open', 'off' ) === 'on' &&
            (bool) array_search( 'current-menu-ancestor', $classes ) )
        {
            $classes[] = 'to-open';
            $is_current_ancestor = true;
        }

        //if icon will be hiding on hover/active
        if( strlen($icon) && $hover_effect === 'show_icon' && $depth === 0){
            $classes[] = 'hidden-icon';
        }

        if($is_current_ancestor){
            $caret_class = 'fa-'.$apollo13framework_a13->get_option( $depth > 0 ? 'submenu_third_lvl_closer' : 'submenu_closer' );
        }
        else{
            $caret_class = 'fa-'.$apollo13framework_a13->get_option( $depth > 0 ? 'submenu_third_lvl_opener' : 'submenu_opener' );
        }

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';


        $output .= $indent . '<li' . $id . $value . $class_names .'>';

        if($displaying_html){
            $output .= $item->a13_mm_html;
            return;
        }

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_url( $item->url        ) .'"' : '';

        $item_output = $args->before;
        $item_output .= $dont_link? '<span class="title">' : '<a'. $attributes .'>';
        $item_output .= ($excluded_effect && strlen($icon))? '<i class="fa fa-'.$icon.'"></i>' : '';

        $item_output .= ! $excluded_effect && $depth === 0 ? $args->link_before . '<em>' : $args->link_before;
        $item_output .= ( ( ! $excluded_effect && strlen( $icon ) ) ? '<i class="fa fa-' . $icon . '"></i>' : '' ) . trim( $name );
        $item_output .= ! $excluded_effect && $depth === 0 ? '</em>' . $args->link_after : $args->link_after;

        $item_output .= $dont_link? '</span>' : '</a>';
	    $item_output .= $is_parent? '<i class="fa sub-mark '.$caret_class.'"></i>' : '';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}



/**
 * A13FRAMEWORK_menu_one_line_left_walker
 * Used in main menu for one line header
 */
class A13FRAMEWORK_menu_one_line_left_walker extends A13FRAMEWORK_menu_walker {
    /**
     * @param array $elements all menu items
     * @param int   $max_depth
     *
     * @return string items html
     */
    public function walk( $elements, $max_depth ) {
        $number_of_root_elements = $this->get_number_of_root_elements($elements);
        //copy of source of parent::walk function

        $args = array_slice(func_get_args(), 2);
        $output = '';

        //invalid parameter or nothing to walk
        if ( $max_depth < -1 || empty( $elements ) ) {
            return $output;
        }

        $parent_field = $this->db_fields['parent'];

        // flat display
        if ( -1 == $max_depth ) {
            $empty_array = array();
            foreach ( $elements as $e )
                $this->display_element( $e, $empty_array, 1, 0, $args, $output );
            return $output;
        }

        /*
		 * Need to display in hierarchical order.
		 * Separate elements into two buckets: top level and children elements.
		 * Children_elements is two dimensional array, eg.
		 * Children_elements[10][] contains all sub-elements whose parent is 10.
		 */
        $top_level_elements = array();
        $children_elements  = array();
        foreach ( $elements as $e) {
            if ( empty( $e->$parent_field ) )
                $top_level_elements[] = $e;
            else
                $children_elements[ $e->$parent_field ][] = $e;
        }

        /*
		 * When none of the elements is top level.
		 * Assume the first one must be root of the sub elements.
		 */
        if ( empty($top_level_elements) ) {

            $first = array_slice( $elements, 0, 1 );
            $root = $first[0];

            $top_level_elements = array();
            $children_elements  = array();
            foreach ( $elements as $e) {
                if ( $root->$parent_field == $e->$parent_field )
                    $top_level_elements[] = $e;
                else
                    $children_elements[ $e->$parent_field ][] = $e;
            }
        }

        $current_index = 1;
        foreach ( $top_level_elements as $e ){
            if( $current_index <= ceil($number_of_root_elements/2) ){
                $this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
            }
            $current_index++;
        }

        return $output;
	}
}



/**
 * A13FRAMEWORK_menu_one_line_right_walker
 * Used in main menu for one line header
 */
class A13FRAMEWORK_menu_one_line_right_walker extends A13FRAMEWORK_menu_walker {
	/**
     * @param array $elements all menu items
     * @param int   $max_depth
     *
     * @return string items html
     */
    public function walk( $elements, $max_depth ) {
        $number_of_root_elements = $this->get_number_of_root_elements($elements);
        //copy of source of parent::walk function

        $args = array_slice(func_get_args(), 2);
        $output = '';

        //invalid parameter or nothing to walk
        if ( $max_depth < -1 || empty( $elements ) ) {
            return $output;
        }

        $parent_field = $this->db_fields['parent'];

        // flat display
        if ( -1 == $max_depth ) {
            $empty_array = array();
            foreach ( $elements as $e )
                $this->display_element( $e, $empty_array, 1, 0, $args, $output );
            return $output;
        }

        /*
		 * Need to display in hierarchical order.
		 * Separate elements into two buckets: top level and children elements.
		 * Children_elements is two dimensional array, eg.
		 * Children_elements[10][] contains all sub-elements whose parent is 10.
		 */
        $top_level_elements = array();
        $children_elements  = array();
        foreach ( $elements as $e) {
            if ( empty( $e->$parent_field ) )
                $top_level_elements[] = $e;
            else
                $children_elements[ $e->$parent_field ][] = $e;
        }

        /*
		 * When none of the elements is top level.
		 * Assume the first one must be root of the sub elements.
		 */
        if ( empty($top_level_elements) ) {

            $first = array_slice( $elements, 0, 1 );
            $root = $first[0];

            $top_level_elements = array();
            $children_elements  = array();
            foreach ( $elements as $e) {
                if ( $root->$parent_field == $e->$parent_field )
                    $top_level_elements[] = $e;
                else
                    $children_elements[ $e->$parent_field ][] = $e;
            }
        }

        $current_index = 1;
        foreach ( $top_level_elements as $e ){
            if( $current_index > ceil($number_of_root_elements/2) ){
                $this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
            }
            $current_index++;
        }

        return $output;
	}
}



/**
 * A13FRAMEWORK_page_tree_one_line_left_walker
 * Used in main menu for one line header when no menu is defined
 */
class A13FRAMEWORK_page_tree_one_line_left_walker extends Walker_Page {
    /**
     * @param array $elements all menu items
     * @param int   $max_depth
     *
     * @return string items html
     */
    public function walk( $elements, $max_depth ) {
        $number_of_root_elements = $this->get_number_of_root_elements($elements);
        //copy of source of parent::walk function

        $args = array_slice(func_get_args(), 2);
        $output = '';

        //invalid parameter or nothing to walk
        if ( $max_depth < -1 || empty( $elements ) ) {
            return $output;
        }

        $parent_field = $this->db_fields['parent'];

        // flat display
        if ( -1 == $max_depth ) {
            $empty_array = array();
            foreach ( $elements as $e )
                $this->display_element( $e, $empty_array, 1, 0, $args, $output );
            return $output;
        }

        /*
		 * Need to display in hierarchical order.
		 * Separate elements into two buckets: top level and children elements.
		 * Children_elements is two dimensional array, eg.
		 * Children_elements[10][] contains all sub-elements whose parent is 10.
		 */
        $top_level_elements = array();
        $children_elements  = array();
        foreach ( $elements as $e) {
            if ( empty( $e->$parent_field ) )
                $top_level_elements[] = $e;
            else
                $children_elements[ $e->$parent_field ][] = $e;
        }

        /*
		 * When none of the elements is top level.
		 * Assume the first one must be root of the sub elements.
		 */
        if ( empty($top_level_elements) ) {

            $first = array_slice( $elements, 0, 1 );
            $root = $first[0];

            $top_level_elements = array();
            $children_elements  = array();
            foreach ( $elements as $e) {
                if ( $root->$parent_field == $e->$parent_field )
                    $top_level_elements[] = $e;
                else
                    $children_elements[ $e->$parent_field ][] = $e;
            }
        }

        $current_index = 1;
        foreach ( $top_level_elements as $e ){
            if( $current_index <= ceil($number_of_root_elements/2) ){
                $this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
            }
            $current_index++;
        }

        return $output;
	}
}



/**
 * A13FRAMEWORK_page_tree_one_line_right_walker
 * Used in main menu for one line header when no menu is defined
 */
class A13FRAMEWORK_page_tree_one_line_right_walker extends Walker_Page {
	/**
     * @param array $elements all menu items
     * @param int   $max_depth
     *
     * @return string items html
     */
    public function walk( $elements, $max_depth ) {
        $number_of_root_elements = $this->get_number_of_root_elements($elements);
        //copy of source of parent::walk function

        $args = array_slice(func_get_args(), 2);
        $output = '';

        //invalid parameter or nothing to walk
        if ( $max_depth < -1 || empty( $elements ) ) {
            return $output;
        }

        $parent_field = $this->db_fields['parent'];

        // flat display
        if ( -1 == $max_depth ) {
            $empty_array = array();
            foreach ( $elements as $e )
                $this->display_element( $e, $empty_array, 1, 0, $args, $output );
            return $output;
        }

        /*
		 * Need to display in hierarchical order.
		 * Separate elements into two buckets: top level and children elements.
		 * Children_elements is two dimensional array, eg.
		 * Children_elements[10][] contains all sub-elements whose parent is 10.
		 */
        $top_level_elements = array();
        $children_elements  = array();
        foreach ( $elements as $e) {
            if ( empty( $e->$parent_field ) )
                $top_level_elements[] = $e;
            else
                $children_elements[ $e->$parent_field ][] = $e;
        }

        /*
		 * When none of the elements is top level.
		 * Assume the first one must be root of the sub elements.
		 */
        if ( empty($top_level_elements) ) {

            $first = array_slice( $elements, 0, 1 );
            $root = $first[0];

            $top_level_elements = array();
            $children_elements  = array();
            foreach ( $elements as $e) {
                if ( $root->$parent_field == $e->$parent_field )
                    $top_level_elements[] = $e;
                else
                    $children_elements[ $e->$parent_field ][] = $e;
            }
        }

        $current_index = 1;
        foreach ( $top_level_elements as $e ){
            if( $current_index > ceil($number_of_root_elements/2) ){
                $this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
            }
            $current_index++;
        }

        return $output;
	}
}



/**
 * Class A13FRAMEWORK_top_bar_menu_walker
 * Used in top bar of horizontal header
 */
class A13FRAMEWORK_top_bar_menu_walker extends Walker_Nav_Menu {

	/**
	 * @see   Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string       $output Passed by reference. Used to append additional content.
	 * @param object       $item   Menu item data object.
	 * @param int          $depth  Depth of menu item. Used for padding.
	 * @param array|object $args
	 * @param int          $id     Menu item ID.
	 */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $wp_query;
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        //check if this element is a OnePage Navigation Pointer
        $is_nava = $item->object === 'nava';
        if( $is_nava ){
            $home_page_id = get_option('page_on_front');
            $frontpage = get_post( $home_page_id );

            $current_page_slug = $wp_query->post->post_name;
            $nava_page_slug = get_post_meta( $item->object_id, 'a13_nava_page_slug', true );
            $nava_item_anchor = str_replace(' ', '_', $item->title);
            $classes[] = 'a13_one_page';

            //it is on different page - absolute path
            if( $nava_page_slug != $current_page_slug ){
                //if nava leads to front-page
                if( $nava_page_slug == $frontpage->post_name ){
                    $url = get_home_url();
                }
                //it is on sub-page
                else{
                    $url = get_site_url(null, $nava_page_slug);
                }
                $item->url = $url.'/#'.$nava_item_anchor;
            }
            //it is on current page - just anchor
            else{
                $item->url = '#'.$nava_item_anchor;
            }
        }

        $icon = trim( $item->a13_item_icon );
        $name = apply_filters( 'the_title', $item->title, $item->ID );


        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';


        $output .= $indent . '<li' . $id . $value . $class_names .'>';

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_url( $item->url        ) .'"' : '';

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= strlen($icon)? '<i class="fa fa-'.$icon.'"></i>' : '';
        $item_output .= strlen(trim($name))? ($args->link_before . $name . $args->link_after) : '';
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}


/**
 * Class A13FRAMEWORK_menu_overlay_walker
 * Used in menu overlay in header
 */
class A13FRAMEWORK_menu_overlay_walker extends Walker_Nav_Menu {

    /**
     * Starts the element output.
     *
     * @since 3.0.0
     * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
     *
     * @see Walker::start_el()
     *
     * @param string   $output Passed by reference. Used to append additional content.
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     * @param int      $id     Current item ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $wp_query;

        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
        } else {
            $t = "\t";
        }
        $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $is_nava = $item->object === 'nava';
        if( $is_nava ){
            $home_page_id = get_option('page_on_front');
            $frontpage = get_post( $home_page_id );

            $current_page_slug = $wp_query->post->post_name;
            $nava_page_slug = get_post_meta( $item->object_id, 'a13_nava_page_slug', true );
            $nava_item_anchor = get_the_title( $item->object_id );
            $classes[] = 'a13_one_page';

            //it is on different page - absolute path
            if( $nava_page_slug != $current_page_slug ){
                //if nava leads to front-page
                if( $nava_page_slug == $frontpage->post_name ){
                    $url = get_home_url();
                }
                //it is on sub-page
                else{
                    $url = get_site_url(null, $nava_page_slug);
                }
                $item->url = $url.'/#'.$nava_item_anchor;
            }
            //it is on current page - just anchor
            else{
                $item->url = '#'.$nava_item_anchor;
            }
        }

        /**
         * Filters the arguments for a single nav menu item.
         *
         * @since 4.4.0
         *
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param WP_Post  $item  Menu item data object.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

        /**
         * Filters the CSS class(es) applied to a menu item's list item element.
         *
         * @since 3.0.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        /**
         * Filters the ID applied to a menu item's list item element.
         *
         * @since 3.0.1
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names .'>';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        /**
         * Filters the HTML attributes applied to a menu item's anchor element.
         *
         * @since 3.6.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         *     @type string $title  Title attribute.
         *     @type string $target Target attribute.
         *     @type string $rel    The rel attribute.
         *     @type string $href   The href attribute.
         * }
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        /** This filter is documented in wp-includes/post-template.php */
        $title = apply_filters( 'the_title', $item->title, $item->ID );

        /**
         * Filters a menu item's title.
         *
         * @since 4.4.0
         *
         * @param string   $title The menu item's title.
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        /**
         * Filters a menu item's starting output.
         *
         * The menu item's starting output only includes `$args->before`, the opening `<a>`,
         * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
         * no filter for modifying the opening and closing `<li>` for a menu item.
         *
         * @since 3.0.0
         *
         * @param string   $item_output The menu item's starting HTML output.
         * @param WP_Post  $item        Menu item data object.
         * @param int      $depth       Depth of menu item. Used for padding.
         * @param stdClass $args        An object of wp_nav_menu() arguments.
         */
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}


/**
 * Class A13FRAMEWORK_list_pages_walker
 * Used in Children Navigation Menu in sidebar
 */
class A13FRAMEWORK_list_pages_walker extends Walker_Page {
	/**
	 * @see   Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output       Passed by reference. Used to append additional content.
	 * @param object $page         Page data object.
	 * @param int    $depth        Depth of page. Used for padding.
	 * @param array  $args
	 * @param int    $current_page Page ID.
	 */
    function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
        if ( $depth )
            $indent = str_repeat("\t", $depth);
        else
            $indent = '';

	    $link_before = $link_after = $date_format = '';
        extract($args, EXTR_SKIP);
        $css_class = array('page_item', 'page-item-'.$page->ID);
        if ( !empty($current_page) ) {
            $_current_page = get_post( $current_page );
            if ( in_array( $page->ID, $_current_page->ancestors ) )
                $css_class[] = 'current_page_ancestor';
            if ( $page->ID == $current_page )
                $css_class[] = 'current_page_item';
            elseif ( $_current_page && $page->ID == $_current_page->post_parent )
                $css_class[] = 'current_page_parent';
        } elseif ( $page->ID == get_option('page_for_posts') ) {
            $css_class[] = 'current_page_parent';
        }

        $css_class = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

        $output .= $indent . '<li class="' . $css_class . '"><a href="' . esc_url( get_permalink($page->ID) ) . '">'
            . $link_before . apply_filters( 'the_title', $page->post_title, $page->ID ) . $link_after . '</a>';

        //$show_date & $link_before are from extract function running
        if ( !empty($show_date) ) {
            if ( 'modified' == $show_date )
                $time = $page->post_modified;
            else
                $time = $page->post_date;

	        $output .= " " . mysql2date($date_format, $time);
        }
    }
}


/**
 * Class A13FRAMEWORK_custom_menu_widget_walker
 * Used in Apollo13 Custom Menu widget in sidebar
 */
class A13FRAMEWORK_custom_menu_widget_walker extends Walker_Nav_Menu {
    /**
     * Starts the element output.
     *
     * @since 3.0.0
     * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
     *
     * @see Walker::start_el()
     *
     * @param string   $output Passed by reference. Used to append additional content.
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     * @param int      $id     Current item ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
        } else {
            $t = "\t";
        }
        $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        /**
         * Filters the arguments for a single nav menu item.
         *
         * @since 4.4.0
         *
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param WP_Post  $item  Menu item data object.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

        /**
         * Filters the CSS class(es) applied to a menu item's list item element.
         *
         * @since 3.0.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        /**
         * Filters the ID applied to a menu item's list item element.
         *
         * @since 3.0.1
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names .'>';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        /**
         * Filters the HTML attributes applied to a menu item's anchor element.
         *
         * @since 3.6.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         *     @type string $title  Title attribute.
         *     @type string $target Target attribute.
         *     @type string $rel    The rel attribute.
         *     @type string $href   The href attribute.
         * }
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        /** This filter is documented in wp-includes/post-template.php */
        $title = apply_filters( 'the_title', $item->title, $item->ID );

        /**
         * Filters a menu item's title.
         *
         * @since 4.4.0
         *
         * @param string   $title The menu item's title.
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );


        $icon = trim( $item->a13_item_icon );

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= strlen($icon)? '<i class="fa fa-'.$icon.'"></i>' : '';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        /**
         * Filters a menu item's starting output.
         *
         * The menu item's starting output only includes `$args->before`, the opening `<a>`,
         * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
         * no filter for modifying the opening and closing `<li>` for a menu item.
         *
         * @since 3.0.0
         *
         * @param string   $item_output The menu item's starting HTML output.
         * @param WP_Post  $item        Menu item data object.
         * @param int      $depth       Depth of menu item. Used for padding.
         * @param stdClass $args        An object of wp_nav_menu() arguments.
         */
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}
/* END OF: CUSTOM MENU WALKER CLASSES */
/* ********************************** */



if(!function_exists( 'apollo13framework_is_sub_page' )){
	/**
	 * Check if current page is sub page
	 *
	 * @return bool|int ID of parent element, or false if it is not sub page
	 */
	function apollo13framework_is_sub_page() {
        global $post;                              // load details about this page

        if ( is_page() && $post->post_parent ) {   // test to see if the page has a parent
            return $post->post_parent;             // return the ID of the parent post

        } else {                                   // there is no parent so ...
            return false;                          // ... the answer to the question is false
        }
    }
}


if(!function_exists('apollo13framework_add_menu_parent_class')){
	/**
	 * Adds menu-parent-item class to parent elements in menu
	 *
	 * @param array $items menu items
	 *
	 * @return array
	 */
	function apollo13framework_add_menu_parent_class( $items ) {

        $parents = array();
        foreach ( $items as $item ) {
            if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
                $parents[] = $item->menu_item_parent;
            }
        }

        foreach ( $items as $item ) {
            if ( in_array( $item->ID, $parents ) ) {
                $item->classes[] = 'menu-parent-item';
            }
        }

        return $items;
    }
}
add_filter( 'wp_nav_menu_objects', 'apollo13framework_add_menu_parent_class' );


if(!function_exists('apollo13framework_page_menu')){
	/**
	 * Prints side menu for static pages that has parents or children
	 *
	 * @param bool|false $only_check if true then it wont print anything
	 *
	 * @return bool if menu have sub pages
	 */
	function apollo13framework_page_menu($only_check = false) {
        global $post;

        $there_is_menu = false;

        $has_children_args = array(
            'post_parent' => $post->ID,
            'post_status' => 'publish',
            'post_type' => 'any',
        );

        $list_pages_params = array(
            'child_of'      => $post->post_parent,
            'sort_column'   => 'menu_order',
            'depth'         => 0,
            'title_li'      => '',
            'walker'        => new A13FRAMEWORK_list_pages_walker
        );

        if(apollo13framework_is_sub_page()){
            if($only_check){ return true; }
            $there_is_menu = true;
        }
        elseif(get_children( $has_children_args )){
            if($only_check){ return true; }
            $list_pages_params['child_of'] = $post->ID;
            $there_is_menu = true;
        }

        //display menu
        if($there_is_menu){
            echo '<div class="widget a13_page_menu widget_nav_menu">
                    <ul>';

            wp_list_pages($list_pages_params);

            echo '</ul>
                </div>';
        }
        return false;
    }
}




/* WPML MENU SWITCHER - turn off default one */
if(defined( 'ICL_SITEPRESS_VERSION')){
	global $sitepress_settings, $icl_language_switcher;

	//we are removing default filter for language switcher and alter it a bit
	if(!empty($sitepress_settings['display_ls_in_menu']) && ( !function_exists( 'wpml_home_url_ls_hide_check' ) || !wpml_home_url_ls_hide_check() ) ) {
		remove_filter( 'wp_nav_menu_items', array( $icl_language_switcher, 'wp_nav_menu_items_filter' ), 10 );
		add_filter( 'wp_nav_menu_items', 'apollo13framework_wpml_add_custom_menu', 10, 2 );
	}
}

/**
 * We add here some customization so language switcher will work with our main menu
 * It is copy of default WPML function, see above remove_filter
 *
 * @see SitePressLanguageSwitcher::wp_nav_menu_items_filter()
 *
 * @param $items
 * @param $args
 *
 * @return string
 */
function apollo13framework_wpml_add_custom_menu($items, $args){
	global $sitepress_settings, $sitepress;

	$current_language = $sitepress->get_current_language();
	$default_language = $sitepress->get_default_language();
	// menu can be passed as integer or object
	if(isset($args->menu->term_id)) $args->menu = $args->menu->term_id;

	$abs_menu_id = wpml_object_id_filter($args->menu, 'nav_menu', false, $default_language );
	$settings_menu_id = wpml_object_id_filter( $sitepress_settings[ 'menu_for_ls' ], 'nav_menu', false, $default_language );

	if ( $abs_menu_id == $settings_menu_id  || false === $abs_menu_id ) {

		$languages = $sitepress->get_ls_languages();

		$items .= '<li class="menu-item menu-item-language menu-item-language-current'.(sizeof($languages) > 1 ? ' menu-parent-item' : '').'">';
		if(isset($args->before)){
			$items .= $args->before;
		}
		$items .= '<span class="title">';
		if(isset($args->link_before)){
			$items .= $args->link_before;
		}

		$language_name = '';
		if ( $sitepress_settings[ 'icl_lso_native_lang' ] ) {
			$language_name .= $languages[ $current_language ][ 'native_name' ];
		}
		if ( $sitepress_settings[ 'icl_lso_display_lang' ] && $sitepress_settings[ 'icl_lso_native_lang' ] ) {
			$language_name .= ' (';
		}
		if ( $sitepress_settings[ 'icl_lso_display_lang' ] ) {
			$language_name .= $languages[ $current_language ][ 'translated_name' ];
		}
		if ( $sitepress_settings[ 'icl_lso_display_lang' ] && $sitepress_settings[ 'icl_lso_native_lang' ] ) {
			$language_name .= ')';
		}

		$alt_title_lang = esc_attr($language_name);

		if( $sitepress_settings['icl_lso_flags'] ){
			$items .= '<img class="iclflag" src="' . $languages[ $current_language ][ 'country_flag_url' ] . '" width="18" height="12" alt="' . $alt_title_lang . '" title="' . esc_attr( $language_name ) . '" />';
		}

		$items .= $language_name;

		if(isset($args->link_after)){
			$items .= $args->link_after;
		}
		$items .= '</span>';
		if(sizeof($languages) > 1 ){
			$items .= '<i class="fa sub-mark fa-angle-down"></i>';
		}
		if(isset($args->after)){
			$items .= $args->after;
		}

		unset($languages[ $current_language ]);
		$sub_items = false;
		$menu_is_vertical = !isset($sitepress_settings['icl_lang_sel_orientation']) || $sitepress_settings['icl_lang_sel_orientation'] == 'vertical';
		if(!empty($languages)){
			foreach($languages as $lang){
				$sub_items .= '<li class="menu-item menu-item-language menu-item-language-current">';
				$sub_items .= '<a href="'.esc_url( $lang['url'] ).'">';

				$language_name = '';
				if ( $sitepress_settings[ 'icl_lso_native_lang' ] ) {
					$language_name .= $lang[ 'native_name' ];
				}
				if ( $sitepress_settings[ 'icl_lso_display_lang' ] && $sitepress_settings[ 'icl_lso_native_lang' ] ) {
					$language_name .= ' (';
				}
				if ( $sitepress_settings[ 'icl_lso_display_lang' ] ) {
					$language_name .= $lang[ 'translated_name' ];
				}
				if ( $sitepress_settings[ 'icl_lso_display_lang' ] && $sitepress_settings[ 'icl_lso_native_lang' ] ) {
					$language_name .= ')';
				}
				$alt_title_lang = esc_attr($language_name);

				if( $sitepress_settings['icl_lso_flags'] ){
					$sub_items .= '<img class="iclflag" src="'.$lang['country_flag_url'].'" width="18" height="12" alt="'.$alt_title_lang.'" title="' . $alt_title_lang . '" />';
				}
				$sub_items .= $language_name;

				$sub_items .= '</a>';
				$sub_items .= '</li>';

			}
			if( $sub_items && $menu_is_vertical ) {
				$sub_items = '<ul class="sub-menu submenu-languages">' . $sub_items . '</ul>';
			}
		}
		if( $menu_is_vertical ) {
			$items .= $sub_items;
			$items .= '</li>';
		} else {
			$items .= '</li>';
			$items .= $sub_items;
		}

	}

	return $items;
}