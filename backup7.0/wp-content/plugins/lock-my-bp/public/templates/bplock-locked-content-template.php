<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $post,$bplock;
get_header();
$pg_title = __( 'Logged in members only!', 'bp-lock' );

$general_settings = get_option( 'bplock_general_settings', true );



$lr_form             = ( isset( $general_settings['lr-form'] ) ) ? $general_settings['lr-form'] : 'plugin_form';
$custom_form_content = ( isset( $general_settings['custom_form_content'] ) ) ? $general_settings['custom_form_content'] : '';
?>
<div class="wrap">
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<article id="post-<?php echo esc_attr( $post->ID ); ?>" class="post-<?php echo esc_attr( $post->ID ); ?> page type-page status-publish hentry">
				<header class="entry-header">
					<h1 class="entry-title"><?php echo wp_kses_post( apply_filters( 'bplock_locked_template_pg_title', $pg_title ) ); ?></h1>
				</header><!-- .entry-header -->
				<div class="entry-content">
					<?php
						do_action( 'bplock_before_login_form' );
						$locked_content = ( isset( $general_settings['locked_content'] ) ) ? $general_settings['locked_content'] : '';
					if ( empty( $locked_content ) ) {
						$locked_content = apply_filters( 'bploc_default_locked_message', esc_html__( 'Hey Member! Thanks for checking this page out -- however, it’s restricted to logged members only. If you’d like to access it, please login to the website.', 'bp-lock' ) );
					}
					?>
						<div class="bplock-content"></div>
						<p class="bplock-locked-message"><?php echo wp_kses_post( $locked_content ); ?></p>
						<?php
						if ( 'plugin_form' === $lr_form ) {
							echo do_shortcode( '[bplock_login_form]' );
						} elseif ( 'custom_form_content' !== $lr_form ) {
							echo do_shortcode(  $custom_form_content);
						}
						do_action( 'bplock_after_login_form' );
						?>
				</div><!-- .entry-content -->
		</article><!-- #post-## -->
	</main><!-- .site-main -->
</div>
</div>
<?php get_footer(); ?>
