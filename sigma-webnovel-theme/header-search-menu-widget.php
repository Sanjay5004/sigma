<?php
class Header_Search_Menu_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'header_search_menu_widget',
            __( 'Header Search and Menu', 'your-theme-textdomain' ),
            array( 'description' => __( 'A widget that displays a search bar and a navigation menu.', 'your-theme-textdomain' ) )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        ?>

        <div class="header-search-menu-widget">
            <!-- Search Form -->
            <form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
                <label>
                    <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search...', 'placeholder' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                </label>
                <button type="submit" class="search-submit"><?php echo esc_attr_x( 'Search', 'submit button' ); ?></button>
            </form>

          
        </div>

        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        // Add settings form if needed (optional)
    }

    public function update( $new_instance, $old_instance ) {
        return $new_instance;
    }
}

// Register the widget
function register_header_search_menu_widget() {
    register_widget( 'Header_Search_Menu_Widget' );
}
add_action( 'widgets_init', 'register_header_search_menu_widget' );
?>
