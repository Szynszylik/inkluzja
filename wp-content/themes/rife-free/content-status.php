<?php
/**
 * Template used for displaying content of "status" format posts on archive page.
 * It is used only on page with posts list: blog, archive, search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


$content = get_the_content();
?>

<div class="formatter">
    <?php
    $post_meta = apollo13framework_post_meta_under_content() . apollo13framework_post_meta_above_content();;
    if(strlen($post_meta)){
        echo '<div class="metas">'.wp_kses_post($post_meta).'</div>';
    }
    ?>
    <h2 class="post-title"><?php the_title(); ?></h2>
    <?php echo strlen($content)? '<div class="real-content">'.wp_kses_post($content).'</div>' : ''; ?>
</div>
