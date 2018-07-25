<?php
/**
 * Functions that are connected to handling posts content
 */

if(!function_exists( 'apollo13framework_post_meta_above_content' )){
	/**
	 * Serves some post meta above post content in posts list
	 *
	 */
    function apollo13framework_post_meta_above_content() {
        global $apollo13framework_a13;

        $types      = apollo13framework_what_page_type_is_it();
        $post       = $types['post'];
        $post_list  = $types['blog_type'];
        $categories = '';

        //when call was made for something else then post or blog, then lets treat it as blog
        //it will enable showing this module in Visual Composer in Post grid
        if(!$post && !$post_list){
            $post_list = true;
        }

	    //Categories
        if(
            ($post && $apollo13framework_a13->get_option( 'post_cats') === 'on')
            ||
            ($post_list && $apollo13framework_a13->get_option( 'blog_cats') === 'on')
        ){
	        $categories = get_the_category_list(', ');
        }

        if(strlen($categories)){
            return '<div class="post-meta above_content">'.$categories.'</div>';
        }

        return '';
    }
}


if(!function_exists( 'apollo13framework_post_meta_under_content' )){
    /**
     * Displays some elements after post content
     */
    function apollo13framework_post_meta_under_content() {
        global $apollo13framework_a13;

        $types      = apollo13framework_what_page_type_is_it();
        $post       = $types['post'];//general type of page
        $post_list  = $types['blog_type'];//general type of page
        $page       = 'page' === get_post_type();//in loop type
        $return     = '';

        //when call was made for something else then post or blog, then lets treat it as blog
        //it will enable showing this module in Visual Composer in Post grid
        if(!$post && !$post_list){
            $post_list = true;
        }

	    //return date
        if(
            ($post && $apollo13framework_a13->get_option( 'post_date') === 'on')
            ||
            ($post_list && $apollo13framework_a13->get_option( 'blog_date') === 'on')
        ){
            $return .= apollo13framework_posted_on();
        }

	    //return updated date
        if(
            ($post && $apollo13framework_a13->get_option( 'post_date') === 'updated')
            ||
            ($post_list && $apollo13framework_a13->get_option( 'blog_date') === 'updated')
        ){
            $return .= esc_html__( 'Updated: ', 'rife-free' ).apollo13framework_modified_on();
        }

	    //return author
	    if( $page ){}
        elseif(
		    ($post && $apollo13framework_a13->get_option( 'post_author') === 'on')
		    ||
		    ($post_list && $apollo13framework_a13->get_option( 'blog_author') === 'on')
	    ){
		    $return .= apollo13framework_posted_by_author();
	    }

        //return comments number
        if(
            ($post && $apollo13framework_a13->get_option( 'post_comments') === 'on')
            ||
            ($post_list && $apollo13framework_a13->get_option( 'blog_comments') === 'on')
        ){
            $return .= apollo13framework_post_comments();
        }

	    if(strlen($return)){
		    return '<div class="post-meta under_content">'.$return.'</div>';
	    }

        return '';
    }
}


if(!function_exists('apollo13framework_posted_on')){
	/**
	 * get HTML for date of post
     * @return string
     */
    function apollo13framework_posted_on() {
        return '<time class="entry-date published updated" datetime="'.esc_attr( get_the_date( 'c' ) ).'">'.get_the_date().'</time> ';
    }
}


if(!function_exists('apollo13framework_modified_on')){
	/**
	 * get HTML for date of post
     * @return string
     */
    function apollo13framework_modified_on() {
        return '<time class="entry-date updated" datetime="'.esc_attr( get_the_modified_date( 'c' ) ).'">'.get_the_modified_date().'</time> ';
    }
}


if(!function_exists('apollo13framework_posted_by_author')){
	/**
	 * Author of post
	 * @return string
	 */
	function apollo13framework_posted_by_author() {
		return
            /* translators: %s - author name */
			sprintf(  esc_html__( 'by %s ', 'rife-free' ),
                sprintf('<a class="vcard author" href="%1$s" title="%2$s"><span class="fn">%3$s</span></a>',
				    esc_url(get_author_posts_url( get_the_author_meta( 'ID' ) )),
                    /* translators: %s - author name */
				    sprintf( esc_attr(  esc_html__( 'View all posts by %s', 'rife-free' ) ), get_the_author() ),
				    get_the_author()
                )
			);
	}
}


if(!function_exists('apollo13framework_post_comments')){
	/**
	 * comments link
	 *
     * @return string HTML
     */
    function apollo13framework_post_comments() {
        return '<a class="comments" href="' . esc_url(get_comments_link()) . '"><i class="fa fa-comment-o"></i> '
            .get_comments_number(). '</a>';
    }
}


if(!function_exists('apollo13framework_post_categories')){
	/**
	 * Categories that post was posted in
	 *
     * @return string HTML
     */
    function apollo13framework_post_categories( ) {
        $cats = '';
        $cat_list = get_the_category_list(', ');
        if ( $cat_list ) {
            $cats = '<span class="cats"><i class="fa fa-folder-open-o"></i> '.$cat_list.'</span>';
        }

        return $cats;
    }
}


if(!function_exists('apollo13framework_subtitle')){
	/**
	 * Return subtitle for page/post
	 *
     * @param string $tag   HTML tag that will surround subtitle
     * @param int    $id    post ID
     *
     * @return string HTML
     */
    function apollo13framework_subtitle($tag = 'h2', $id = 0) {
        if($id === 0){
            $id = get_the_ID();
        }

        $s = get_post_meta($id, '_subtitle', true);
        if(strlen($s))
            $s = '<'.$tag.'>'.$s.'</'.$tag.'>';

        return $s;
    }
}



if(!function_exists('apollo13framework_under_post_content')){
    function apollo13framework_under_post_content() {
        global $apollo13framework_a13;

        $types      = apollo13framework_what_page_type_is_it();
        $post       = $types['post'];
        $post_list  = $types['blog_type'];

        //when call was made for something else then post or blog, then lets treat it as blog
        //it will enable showing this module in Visual Composer in Post grid
        if(!$post && !$post_list){
            $post_list = true;
        }

        //links to other subpages
        wp_link_pages( array(
                'before' => '<div id="page-links"><span class="page-links-title">'. esc_html__( 'Pages: ', 'rife-free' ).'</span>',
                'after'  => '</div>'
	        )
        );

        //Tags under content
        if(
            ($post && $apollo13framework_a13->get_option( 'post_tags' ) === 'on')
            ||
            ($post_list && $apollo13framework_a13->get_option( 'blog_tags' ) === 'on')
        ){
            $tag_list = get_the_tag_list( '',' ' );
            if ( $tag_list ) {
                echo '<p class="under_content_tags">'.wp_kses_post( $tag_list ).'</p>';
            }
        }
    }
}



if(!function_exists('apollo13framework_author_info')){
	/**
     * Displays author info in posts(if enabled)
     */
    function apollo13framework_author_info() {
        global $apollo13framework_a13;

        $author_description =  get_the_author_meta( 'description' );

        if( ( strlen($author_description) > 0 ) && $apollo13framework_a13->get_option( 'post_author_info' ) === 'on'): ?>
            <div class="about-author comment">
                <div class="comment-body">
                    <?php $author_ID = get_the_author_meta( 'ID' );
                        echo '<a href="'.esc_url( get_author_posts_url($author_ID) ).'" class="avatar">'.get_avatar( $author_ID, 90 ).'</a>';
                        echo '<strong class="comment-author">'.get_the_author();
                        $u_url = get_the_author_meta( 'user_url' );
                        if( ! empty( $u_url ) ){
                            echo '<a href="' . esc_url($u_url) . '" class="url">(' . esc_html( $u_url ) . ')</a>';
                        }
                        echo '</strong>';
                    ?>
                    <div class="comment-content">
                        <?php
                        echo wp_kses_post( $author_description );
                        ?>
                    </div>
                </div>
            </div>
        <?php endif;
    }
}


if(!function_exists('apollo13framework_posts_navigation')){
	/**
     * Displays navigation to next and previous post
     */
    function apollo13framework_posts_navigation() {
        global $apollo13framework_a13;

        if($apollo13framework_a13->get_option( 'post_navigation' ) === 'on'){
            //posts navigation
            $prev_post = get_previous_post();
            $next_post = get_next_post();
            $is_next = is_object($next_post);
            $is_prev = is_object($prev_post);

            if($is_prev || $is_next){
                echo '<div class="posts-nav">';

                if($is_prev){
                    $id = $prev_post->ID;
                    echo '<a href="'.esc_url( get_permalink($id) ).'" class="item prev">'
                         .'<span><i class="fa fa-long-arrow-left"></i> '.esc_html__( 'Previous article', 'rife-free' ).'</span>'
//                         .apollo13framework_make_post_image( $id, array( 245, 100 ))
                         .'<span class="title">'.esc_html( $prev_post->post_title ).'</span>'
                         .'</a>';
                }
                if($is_next){
                    $id = $next_post->ID;
                    echo '<a href="'.esc_url( get_permalink($id) ).'" class="item next">'
                         .'<span>'.esc_html__( 'Next article', 'rife-free' ).' <i class="fa fa-long-arrow-right"></i></span>'
//                         .apollo13framework_make_post_image( $id, array( 245, 100 ))
                         .'<span class="title">'.esc_html( $next_post->post_title ).'</span>'
                         .'</a>';
                }

                echo '</div>';
            }
        }
    }
}


if(!function_exists('apollo13framework_password_form')){
	/**
	 * Modify password form
	 *
     * @return string new HTML
     */
    function apollo13framework_password_form() {
        //copy of function get_the_password_form() from \wp-includes\post-template.php ~1570
        //with small changes
        return
            '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
            <p class="inputs"><input name="post_password" type="password" size="20" placeholder="' . esc_attr( esc_html__( 'password', 'rife-free' ) ) . '" /><input type="submit" name="Submit" value="' . esc_attr( esc_html__( 'Submit', 'rife-free' ) ) . '" /></p>
            </form>
            ';
    }
}


if(!function_exists('apollo13framework_custom_password_form')){
	/**
	 * Print password page template
	 *
     * @return string HTML
     */
    function apollo13framework_custom_password_form() {
        //we get template to buffer and return it so other filters can do something with it
        ob_start();
        get_template_part('password-template');
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}
add_filter( 'the_password_form', 'apollo13framework_custom_password_form');


if(!function_exists('apollo13framework_excerpt_length')){
	/**
	 * Sets the post excerpt length to 30 words.
	 *
     * @return int number of words
     */
    function apollo13framework_excerpt_length() {
        global $apollo13framework_a13;
        return $apollo13framework_a13->get_option( 'blog_excerpt_length');
    }
}
add_filter( 'excerpt_length', 'apollo13framework_excerpt_length' );


if(!function_exists('apollo13framework_new_excerpt_more')){
	/**
	 * This filter is used by wp_trim_excerpt() function.
	 * By default it set to echo '[...]' more string at the end of the excerpt.
	 *
     * @return string HTML
     */
    function apollo13framework_new_excerpt_more() {
        global $post;
        return '&hellip;
 <p> <a class="more-link" href="'. esc_url( get_permalink($post->ID) ) . '">'.esc_html__( 'Read more', 'rife-free' ).'</a></p>';
    }
}
add_filter( 'excerpt_more', 'apollo13framework_new_excerpt_more' );



if(!function_exists('apollo13framework_has_excerpt_read_more')){
	/**
	 * Adds read more when excerpt is provided by user
	 *
     * @return string HTML
     */
    function apollo13framework_has_excerpt_read_more($content) {
        global $post;
        if(has_excerpt()){
            $content .= '<p> <a class="more-link" href="'. esc_url( get_permalink($post->ID) ) . '">'.esc_html__( 'Read more', 'rife-free' ).'</a></p>';
        }
        return $content;
    }
}
add_filter( 'the_excerpt', 'apollo13framework_has_excerpt_read_more' );


if(!function_exists('apollo13framework_read_more_new_line')){
	/**
	 * Wraps read more in new paragraph so it will land in new line
	 *
	 * @param string $link current link in HTML
	 *
	 * @return string HTML
	 */
    function apollo13framework_read_more_new_line($link) {
        return '<p>'.$link.'</p>';
    }
}
add_filter( 'the_content_more_link', 'apollo13framework_read_more_new_line' );




if(!function_exists('apollo13framework_get_comment_excerpt')){
	/**
	 *
	 * Make excerpt for comments
	 * used in widgets
	 *
     * @param int $comment_ID
     * @param int $num_words    words number to cut after
     *
     * @return string cut comment text
     */
    function apollo13framework_get_comment_excerpt($comment_ID = 0, $num_words = 20) {
        /** @noinspection PhpParamsInspection wrong detection */
        $comment = get_comment( $comment_ID );
        $comment_text = strip_tags($comment->comment_content);
        $blah = explode(' ', $comment_text);
        if (count($blah) > $num_words) {
            $k = $num_words;
            $use_three_dots = 1;
        } else {
            $k = count($blah);
            $use_three_dots = 0;
        }
        $excerpt = '';
        for ($i=0; $i<$k; $i++) {
            $excerpt .= $blah[$i] . ' ';
        }
        $excerpt .= ($use_three_dots) ? '[...]' : '';
        return apply_filters('get_comment_excerpt', $excerpt);
    }
}


if(!function_exists('apollo13framework_comments_navigation')){
	/**
     * Comments navigation
     */
    function apollo13framework_comments_navigation() {
        if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
            ?>
            <nav class="navigation comment-navigation">
                <h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'rife-free' ); ?></h2>
                <div class="nav-links">
                    <?php
                    if ( $prev_link = get_previous_comments_link( esc_html__( 'Older Comments', 'rife-free' ) ) ) :
                        printf( '<div class="nav-previous">%s</div>', wp_kses_post( $prev_link ) );
                    endif;

                    if ( $next_link = get_next_comments_link( esc_html__( 'Newer Comments', 'rife-free' ) ) ) :
                        printf( '<div class="nav-next">%s</div>', wp_kses_post( $next_link ) );
                    endif;
                    ?>
                </div><!-- .nav-links -->
            </nav><!-- .comment-navigation -->
        <?php
        endif;
    }
}



if(!function_exists('apollo13framework_daoon_chat_post')){
    /**
     * Creates chat transcript from post content
     * Credits to http://hirizh.name/blog/styling-chat-transcript-for-custom-post-format/
     * @param string $content Current post content
     *
     * @return string
     */
    function apollo13framework_daoon_chat_post($content) {
        $chatoutput = "<div class=\"chat\">\n";
        $split = preg_split("/(\r?\n)+|(<br\s*\/?>\s*)+/", $content);
        foreach($split as $haystack) {
            if (strpos($haystack, ":")) {
                $string = explode(":", trim($haystack), 2);
                $who = strip_tags(trim($string[0]));
                $what = strip_tags(trim($string[1]));
                $row_class = empty($row_class)? " class=\"chat-highlight\"" : "";
                $chatoutput .= "<p><strong class=\"who\">$who:</strong> $what</p>\n";
            } else {
                $chatoutput .= $haystack . "\n";
            }
        }

        // print our new formated chat post
        $content = $chatoutput . "</div>\n";
        return $content;
    }
}


if(!function_exists('apollo13framework_add_password_form_to_template')) {
    function apollo13framework_add_password_form_to_template( $content ) {
        return $content . apollo13framework_password_form();
    }
}



if(!function_exists('apollo13framework_display_items_from_query_post_list')) {
    /**
     * @param bool|WP_Query $query
     * @param array         $args
     */
    function apollo13framework_display_items_from_query_post_list($query = false, $args = array()){
        global $apollo13framework_a13;

        if($query === false){
            global $wp_query;
            $query = $wp_query;
        }

        $default_args = array(
            'columns' => $apollo13framework_a13->get_option('blog_brick_columns'),
            'filter' => false,
            'display_post_id' => true,
        );

        $args = wp_parse_args($args, $default_args);

        /* show filter? */
        if($args['filter']){
            $query_args = array(
                'hide_empty' => true,
                'object_ids' => wp_list_pluck( $query->posts, 'ID' ),
                'taxonomy'   => 'category'
            );

            /** @noinspection PhpInternalEntityUsedInspection */
            $terms = get_terms( $query_args );

            apollo13framework_make_post_grid_filter($terms, 'posts-filter');
        }


        /* If there are no posts to display, such as an empty archive page */

        if ( ! $query->have_posts() ):
            ?>
            <div class="formatter">
                <div class="real-content empty-blog">
                    <?php
                    echo '<p>'.esc_html__( 'Apologies, but no results were found for the requested archive.', 'rife-free' ).'</p>';
                    get_template_part( 'no-content');
                    ?>
                </div>
            </div>
            <?php

        else:



            $lazy_load      = $apollo13framework_a13->get_option('blog_lazy_load') === 'on';
            $lazy_load_mode = $apollo13framework_a13->get_option('blog_lazy_load_mode');

            $posts_classes = '';
            $posts_layout_class = $apollo13framework_a13->get_option( 'blog_post_look');
            $posts_classes .= ' posts_'.$posts_layout_class;
            $columns_number = ($posts_layout_class === 'horizontal') ? 1 : $args['columns'];
            $posts_classes .= ' posts-columns-'.$columns_number;

            echo '<div class="bricks-frame posts-bricks'.esc_attr( $posts_classes ).'"><div class="posts-grid-container" data-lazy-load="'.esc_attr($lazy_load).'" data-lazy-load-mode="'.esc_attr($lazy_load_mode).'"' .'>';
            echo '<div class="grid-master"></div>';

            while ( $query->have_posts() ) : $query->the_post();
                $post_id = get_the_ID();

                //get post categories
                $terms = wp_get_post_categories($post_id, array("fields" => "all"));

                //we style different when some post formats are used
                $special_post_formats   = array('link', 'status', 'quote', 'chat');
                $post_format            = get_post_format();
                $is_special_post_format = (in_array($post_format, $special_post_formats));
                $post_classes           = $is_special_post_format? 'archive-item special-post-format' : 'archive-item';
                $link_it                = $is_special_post_format? false : true;

                //size of brick
                $brick_size = ($posts_layout_class === 'horizontal') ? 1 : $apollo13framework_a13->get_meta( '_brick_ratio_x' );
                $post_classes .= strlen( $brick_size ) ? ' w' . $brick_size : '';

                /* 3 echos fo easier sniffs */
                echo '<div';
                echo $args['display_post_id']? ' id="post-'.esc_attr(get_the_ID()).'"' : '';
                echo ' class="'.esc_attr( join( ' ', get_post_class($post_classes) ) ).'"';
                //get all categories that item belongs to
                if( count( $terms ) ){
                    foreach($terms as $term) {
                        echo ' data-category-'.esc_attr( $term->term_id ).'="1"';
                    }
                }
                echo '>';

                    if(post_password_required()){
                        apollo13framework_top_image_video($link_it, array('page_type' => 'blog_type', 'brick_columns' => $columns_number));
                        ?>
                        <div class="formatter">
                            <?php the_title('<h2 class="post-title entry-title"><a href="'. esc_url( get_permalink() ) . '"><span class="fa fa-lock"></span>', '</a></h2>'); ?>
                            <div class="real-content">
                                <p><?php esc_html_e( 'To view it please enter your password below', 'rife-free' ); ?></p>
                                <?php echo apollo13framework_password_form(); ?>
                            </div>
                        </div>
                        <?php
                    }
                    else{
                        //classic layout of post
                        apollo13framework_top_image_video($link_it, array('page_type' => 'blog_type', 'brick_columns' => $columns_number));

                        get_template_part( 'content', get_post_format() );

                        if($posts_layout_class === 'horizontal') {
                            echo '<div class="clear"></div>';
                        }
                    }

            echo '</div>';

            endwhile;

            echo '</div></div>';

        endif;
    }
}


/**
 * Remove 'hentry' from post_class()
 * it is added manually in different container
 */
function apollo13framework_remove_hentry( $class ) {
    $class = array_diff( $class, array( 'hentry' ) );
    return $class;
}
add_filter( 'post_class', 'apollo13framework_remove_hentry' );
