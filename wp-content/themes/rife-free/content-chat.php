<?php
/**
 * Template used for displaying content of "chat" format posts on archive page.
 * It is used only on page with posts list: blog, archive, search
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
?>

<div class="formatter">
    <?php
    $post_meta = apollo13framework_post_meta_under_content() . apollo13framework_post_meta_above_content();;
    if(strlen($post_meta)){
        echo '<div class="metas">'.wp_kses_post($post_meta).'</div>';
    }
    ?>
    <h2 class="post-title"><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h2>
    <div class="real-content">
        <?php echo wp_kses_post(apollo13framework_daoon_chat_post($post->post_content));?>
        <div class="clear"></div>
    </div>
</div>