<?php
/*
Template Name: Pages List
*/
?>
<?php get_header(); ?>

<div id="content">
    <h1>List of Pages</h1>
    <ul>
        <?php
        $pages = get_pages();
        foreach ($pages as $page) {
            ?>
            <li>
                <a href="<?php echo get_permalink($page->ID); ?>">
                    <?php echo $page->post_title; ?>
                </a>
            </li>
            <?php
        }
        ?>
    </ul>
</div>

<?php get_footer(); ?>
