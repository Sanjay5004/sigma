<?php
/*
 Template Name: Novel Template
*/

get_header();
?>


<br>
<div id="novel-card" class="novel">
<?php
// Breadcrumb for novel Page
if (is_singular('novel')) {
    global $post;

    echo '<div class="breadcrumbs">';

    // Link to Home
    echo '<a href="' . home_url() . '">Home</a> &raquo;';

    // Display current novel name (non-clickable)
    echo ' ' . get_the_title();

    echo '</div>';
}
?>
 <br>
    
    <h2  class="novel-title">  <div id="novel-titleid"><?php the_title(); ?></div></h2>
   
  
    <div class="novel-cover-image">
        <?php echo get_the_post_thumbnail(); ?>
    </div>
    <br>
   <section>
   <?php

   // view counts
if (is_single()) {
    echo '<div class="novel-views">';
    echo '<svg width="22" height="22"><use xlink:href="#i-eye"></use></svg> ' . get_novel_post_views(get_the_ID());
    echo '</div>';
}
?>
 <div class="date"> 
<?php 
 // date show 
echo get_the_date(); ?></div>

</section>


 <!-- Recommended Implementation -->
 <div class="novel-genre">
    <?php 
    $categories = get_the_category();
    if ($categories) {
        echo '<span>Categories </span>';
        foreach ($categories as $category) {
            echo '<a href="' . get_category_link($category->term_id) . '">' 
                 . $category->name . '</a>';
        }
    }
    ?>
</div>


    <div id="novel-synopsis" class="novel-Synopsis">
        <h3>Synopsis</h3>
    <p class="novel-content">
     <?php the_content(); ?>
    </p></div>
    <br>
    
  
<div class="novel-Tags">
    <?php 
    $tags = get_the_tags();
    if ($tags) {
        echo '<span>Tags </span>';
        foreach ($tags as $tag) {
            echo '<a href="' . get_tag_link($tag->term_id) . '">' 
                 . $tag->name . '</a>';
        }
    }
    ?>
</div>


    <div class="table-of-contents">
        <h3>Table of Contents</h3>
        <br> 
        
        </div>
        <br>
        <?php
// Get total number of chapters
$total_chapters = wp_count_posts('chapter')->publish;

// Calculate chapter ranges
$chapter_ranges = array();
for ($i = 1; $i <= $total_chapters; $i += 25) {
    $start = $i;
    $end = $i + 24;
    $chapter_ranges[] = array(
        'start' => $start,
        'end' => min($end, $total_chapters)
    );
}
?>

<select id="chapter-range" onchange="window.location.href=this.value;">
    <option value="">Select Chapter Range</option>
    <?php foreach ($chapter_ranges as $range): ?>
    <option value="?start=<?php echo $range['start']; ?>&end=<?php echo $range['end']; ?>">
        Chapters <?php echo $range['start']; ?> - <?php echo $range['end']; ?>
    </option>
    <?php endforeach; ?>
</select>

<?php
// Handle chapter range query
$start = isset($_GET['start']) ? intval($_GET['start']) : 1;
$end = isset($_GET['end']) ? intval($_GET['end']) : 25;

$chapterlists = new WP_Query(array(
    'post_type' => 'chapter',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'offset' => $start - 1,
    'posts_per_page' => $end - $start + 1
));

if ($chapterlists->have_posts()) :
    echo '<ul class="chapterlist">';
    while ($chapterlists->have_posts()) : 
        $chapterlists->the_post(); 
?>
    <li class="chapterlistlink">
        <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a>
    </li>
<?php 
    endwhile;
    echo '</ul>';
    wp_reset_postdata();
else :
    echo '<li>No chapters found in this range</li>';
endif;
?>

<script>
// Optional: Preserve selected range on page load
document.addEventListener('DOMContentLoaded', function() {
    var start = <?php echo $start; ?>;
    var end = <?php echo $end; ?>;
    var select = document.getElementById('chapter-range');
    
    for (var i = 0; i < select.options.length; i++) {
        var option = select.options[i];
        if (option.value.includes('start=' + start) && option.value.includes('end=' + end)) {
            select.selectedIndex = i;
            break;
        }
    }
});
</script>




    </div>
    <br><br><br><br><br>
</div>

<?php get_footer(); ?>
