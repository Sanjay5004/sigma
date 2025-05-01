<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package sigma_webnovel_theme
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
	
	<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="i-eye" viewBox="0 0 24 24">
    <path d="M12 5C7 5 2.7 9 2 12s5 7 10 7 10-4 10-7-4.7-7-10-7zM12 17c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zm0-8c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
  </symbol>
</svg>

	
</head>

<div class="header1" <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="header-div-body-id" class="header-div-body-class">
	<a class="skip-link screen-reader-text" href="#primary">
		<?php esc_html_e( 'Skip to content', 'sigma-webnovel-theme' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="site-branding">
			<?php
			the_custom_logo();
			if ( is_front_page() && is_home() ) :
				?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php
			else :
				?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			endif;
			$sigma_webnovel_theme_description = get_bloginfo( 'description', 'display' );
			if ( $sigma_webnovel_theme_description || is_customize_preview() ) :
				?>

				<p class="site-description"><?php echo $sigma_webnovel_theme_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
		</div>

		<!-- .widget -->
		<?php if ( is_active_sidebar( 'header-widget' ) ) : ?>
    <div class="header-widget-area">
        <?php dynamic_sidebar( 'header-widget' ); ?>
    </div>
<?php endif; ?>

		

		<!-- #site-navigation -->


		<nav id="site-navigation" class="main-navigation">
			
		<!-- Hamburger Menu Button -->
			 <a href="javascript:void(0);" class="icon" onclick="myFunction()">
        <i class="fa fa-bars"></i>
    </a>
		    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
				
		        <?php esc_html_e( 'Primary Menu', 'sigma-webnovel-theme' ); ?>
		    </button>
		
		   
			<ul class="menu">
		        <?php
		       wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					
					'menu_id'        => 'primary-menu',
					'menu_class'     => 'menu'
				)
				);
			
		        ?>
		    </ul>

		</nav>
	</header><!-- #masthead -->
