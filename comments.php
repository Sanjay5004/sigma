<?php
/**
 * The template for displaying comments
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sigma-_webnovel_theme
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password, return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php
    if (have_comments()) :
        ?>
        <h2 class="comments-title">
            <?php
            $sigma_webnovel_theme_comment_count = get_comments_number();
            if ('1' === $sigma_webnovel_theme_comment_count) {
                printf(
                    /* translators: 1: title. */
                    esc_html__('One thought on &ldquo;%1$s&rdquo;', 'sigma-webnovel-theme'),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            } else {
                printf(
                    /* translators: 1: comment count number, 2: title. */
                    esc_html(_nx('%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $sigma_webnovel_theme_comment_count, 'comments title', 'sigma-webnovel-theme')),
                    number_format_i18n($sigma_webnovel_theme_comment_count), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            }
            ?>
        </h2><!-- .comments-title -->

        <?php the_comments_navigation(); ?>

        <ol class="comment-list">
            <?php
            wp_list_comments(
                array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 50, // Optional: Adjust avatar size as needed.
                    'callback'    => 'sigma_webnovel_theme_comment_callback', // Custom callback function.
                )
            );
            ?>
        </ol><!-- .comment-list -->

        <?php the_comments_navigation(); ?>

        <?php if (!comments_open()) : ?>
            <p class="no-comments"><?php esc_html_e('Comments are closed.', 'sigma-webnovel-theme'); ?></p>
        <?php endif; ?>

    <?php endif; // Check for have_comments(). ?>

    <?php comment_form(); ?>

</div><!-- #comments -->

<?php
// Custom callback function for styling comments
function sigma_webnovel_theme_comment_callback($comment, $args, $depth) {
    ?>
    <li <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?> id="comment-<?php comment_ID(); ?>">
        <div class="comment-body">
            <div class="comment-author vcard">
                <?php echo get_avatar($comment, $args['avatar_size']); ?>
                <?php printf('<cite class="fn">%s</cite>', get_comment_author_link()); ?>
            </div>
            <div class="comment-meta commentmetadata">
                <a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)); ?>">
                    <?php printf(__('%1$s at %2$s', 'sigma-webnovel-theme'), get_comment_date(), get_comment_time()); ?>
                </a>
                <?php edit_comment_link(__('(Edit)', 'sigma-webnovel-theme'), '  ', ''); ?>
            </div>
            <?php if ($comment->comment_approved == '0') : ?>
                <em><?php _e('Your comment is awaiting moderation.', 'sigma-webnovel-theme'); ?></em>
                <br />
            <?php endif; ?>
            <?php comment_text(); ?>
            <div class="reply">
                <?php
                comment_reply_link(
                    array_merge(
                        $args,
                        array(
                            'add_below' => 'comment',
                            'depth'     => $depth,
                            'max_depth' => $args['max_depth'],
                        )
                    )
                );
                ?>
            </div>
        </div>
    </li>
    <?php
}
?>
