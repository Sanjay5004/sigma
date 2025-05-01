<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package sigma-webnovel-theme
 */

get_header(); // Include the header
?>

<div id="primary" class="primary-class">  


<?php
// Breadcrumb for Chapter Page
if (is_singular('chapter')) {
    global $post;

    // Retrieve the taxonomy terms associated with this chapter
    $novel_terms = get_the_terms($post->ID, 'novel_selector');

    echo '<div class="breadcrumbs">';

    // Link to Home
    echo '<a href="' . home_url() . '">Home</a> &raquo;';

    if (!empty($novel_terms) && !is_wp_error($novel_terms)) {
        // Get the first term (assuming one-to-one relationship)
        $novel_term = reset($novel_terms);

        // Query the associated novel post
        $novel_query = new WP_Query([
            'post_type'      => 'novel',
            'tax_query'      => [
                [
                    'taxonomy' => 'novel_selector',
                    'field'    => 'slug',
                    'terms'    => $novel_term->slug,
                ],
            ],
            'posts_per_page' => 1,
        ]);

        if ($novel_query->have_posts()) {
            $novel_query->the_post();

            // Display novel name as clickable link
            echo ' <a href="' . get_permalink() . '">' . get_the_title() . '</a> &raquo;';

            // Reset post data
            wp_reset_postdata();
        }
    }

    // Display current chapter name (non-clickable)
    echo ' ' . get_the_title();

    echo '</div>';
}
?>


<main id="secondary" class="site-main">

<!-- #main -->

    <?php
    while ( have_posts() ) :
        the_post();

        // Load the template part for the content
        get_template_part( 'template-parts/content', get_post_type() );
       ?>

		 <!-- Navigation for Previous and Next --> 
         <nav class="post-navigation">
             <a href="<?php echo get_permalink(get_adjacent_post(false, '', true)); ?>" class="previous">&laquo; Previous </a>

          <?php
        // Check if the current post is a chapter
        if (is_singular('chapter')) {
            // Get the current chapter's post
            $chapter_post = get_post();
        
            // Retrieve the associated novel via the 'novel_selector' taxonomy
            $novel_terms = get_the_terms($chapter_post->ID, 'novel_selector');
        
            if ($novel_terms && !is_wp_error($novel_terms)) {
                // Get the first term (assuming one novel per chapter)
                $novel_term = reset($novel_terms);
        
                // Query the associated novel post
                $novel_query = new WP_Query([
                    'post_type' => 'novel',
                    'tax_query' => [
                        [
                            'taxonomy' => 'novel_selector',
                            'field'    => 'slug',
                            'terms'    => $novel_term->slug,
                        ],
                    ],
                    'posts_per_page' => 1,
                ]);
        
                if ($novel_query->have_posts()) {
                    $novel_query->the_post(); // Set up the novel post
        
                    // Output the "Index" button
                    echo '<div class="chapter-index-button">';
                    echo '<a class="btn-index" href="' . esc_url(get_permalink()) . '">Index</a>';
                    echo '</div>';
        
                    wp_reset_postdata(); // Reset post data
                }
            }
        }
        ?>

<a href="<?php echo get_permalink(get_adjacent_post(false, '', false)); ?>" class="next">Next  &raquo;</a> 
</nav>


        <?php 
        // Check if comments are open or there is at least one comment, then load the comment template
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;

    endwhile; // End of the loop
    ?>

</main>

</div>
<div class="footer_chapter">
<?php
get_footer(); // Include the footer
?>
</div>