<?php
/*
Template Name: Homepage
*/
?>

<?php get_header(); ?>

<body>
<div id="content"> 
    <h1 class="novelcard">Popular Novels</h1>
    
    <div id="novelCarousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <?php
            $args = array(
                'post_type' => 'novel', // Replace with your custom post type name
                'posts_per_page' => 14, // Fetch more posts for multiple slides
            );

            $custom_query = new WP_Query($args);

            if ($custom_query->have_posts()) :
                $post_count = 0;
                while ($custom_query->have_posts()) : $custom_query->the_post();
                    // Start a new carousel item every 5 posts
                    if ($post_count % 5 === 0) {
                        echo '<div class="carousel-item ' . ($post_count === 0 ? 'active' : '') . '">';
                        echo '<div class="row">';
                    }
            ?>
                    <div class="col-12 col-sm-6 col-md-2 text-center">
                        <a href="<?php the_permalink(); ?>">
                            <div class="cover">
                                <?php
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('medium', ['alt' => esc_attr(get_the_title()), 'loading' => 'lazy']);
                                } else {
                                    echo '<img src="default-image.jpg" alt="Default Cover" style="width: 120px; height: 175px;">';
                                }
                                ?>
                            </div>
                            <div class="novelname">
                                <p><?php echo esc_html(get_the_title()); ?></p>
                            </div>
                        </a>
                    </div>
            <?php
                    $post_count++;
                    // Close the row and carousel item after 5 posts
                    if ($post_count % 5 === 0) {
                        echo '</div></div>';
                    }
                endwhile;

                // Close any unclosed rows/items
                if ($post_count % 5 !== 0) {
                    echo '</div></div>';
                }

                wp_reset_postdata();
            else :
            ?>
                <div class="carousel-item active">
                    <p>No novels available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Controls  button-->
        <a class="carousel-control-prev previous round" href="#novelCarousel" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
</a>
<a class="carousel-control-next next round" href="#novelCarousel" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
</a>

    </div>
</div>

<!-- site-content -->
<div class="site-content">
  <!-- main-column -->

<div class="main-column">
    <!-- Main latest post -->
<?php
// Query the latest novel (novels) posts to show their thumbnail and name
$args_novel = array(
    'post_type'      => 'novel',  // Custom post type: novel
    'posts_per_page' => 20,         // Number of novel to display
    'orderby'        => 'date',    // Order by date (latest first)
    'order'          => 'DESC'    // Descending order (latest posts first)
);
$latest_novel = new WP_Query($args_novel);

if ($latest_novel->have_posts()) : ?>

    <div class="latest-updates-container">
        <h2>Latest Novels</h2>
        <?php while ($latest_novel->have_posts()) : $latest_novel->the_post(); ?>
           
        <div class="series-post">

                <!-- Featured Image (Thumbnail) -->
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium'); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Title (Novel name) -->
                <h3 class="post-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>

                <!-- Query for chapters related to this novel -->
                <?php
                // Get terms from the correct taxonomy
                $terms = wp_get_post_terms(get_the_ID(), 'novel_selector', array('fields' => 'slugs'));

                if (!empty($terms) && is_array($terms)) {
                    $chapter_args = array(
                        'post_type'      => 'chapter',
                        'posts_per_page' => 1, // Retrieve one chapter
                        'tax_query'      => array(
                            array(
                                'taxonomy' => 'novel_selector',
                                'field'    => 'slug',
                                'terms'    => $terms,
                            ),
                        ),
                    );

                    $chapter_query = new WP_Query($chapter_args);

                    if ($chapter_query->have_posts()) {
                        echo '<div class="chapter-list-container">'; // Added class for chapter section
                        echo '<ul class="chapter-list">';
                        while ($chapter_query->have_posts()) {
                            $chapter_query->the_post();
                            ?>
                            <li class="chapter-item">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </li>
                            <?php
                        }
                        echo '</ul>';
                        echo '</div>'; // Close chapter-list-container
                        wp_reset_postdata();
                    } else {
                        echo '<p class="no-chapters-message">No chapters found for this novel.</p>';
                    }
                } else {
                    echo '<p class="no-novel-message">No associated novel found for this novel.</p>';
                }
                ?>

            </div>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    </div>
<?php else : ?>
    <p>No novels found.</p>
<?php endif; ?>


</div>
<!-- secondary-column -->
<div class="secondary-column">

<?php get_sidebar(); ?>

</div>
</div>

<!-- Bootstrap JS (ensure it's included if not already) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>$(document).ready(function () {
  $('#novelCarousel').carousel({
    interval: false, // Disable auto-slide
  });
});
</script>

</body>


<?php get_footer(); ?>
