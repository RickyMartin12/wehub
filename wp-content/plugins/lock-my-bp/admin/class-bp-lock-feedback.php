<?php
/**
 * Plugin review class.
 * Prompts users to give a review of the plugin on WordPress.org after a period of usage.
 *
 * Heavily based on code by Rhys Wynne
 * https://winwar.co.uk/2014/10/ask-wordpress-plugin-reviews-week/
 *
 * @package Bp_Lock
 */

if ( ! class_exists( 'BP_Lock_Feedback' ) ) :

	/**
	 * The feedback.
	 */
	class BP_Lock_Feedback {

		/**
		 * Slug.
		 *
		 * @var string $slug
		 */
		private $slug;

		/**
		 * Name.
		 *
		 * @var string $name
		 */
		private $name;

		/**
		 * Time limit.
		 *
		 * @var string $time_limit
		 */
		private $time_limit;

		/**
		 * No Bug Option.
		 *
		 * @var string $nobug_option
		 */
		public $nobug_option;

		/**
		 * Activation Date Option.
		 *
		 * @var string $date_option
		 */
		public $date_option;

		/**
		 * Class constructor.
		 *
		 * @param string $args Arguments.
		 */
		public function __construct( $args ) {
			$this->slug = $args['slug'];
			$this->name = $args['name'];

			$this->date_option  = $this->slug . '_activation_date';
			$this->nobug_option = $this->slug . '_no_bug';

			if ( isset( $args['time_limit'] ) ) {
				$this->time_limit = $args['time_limit'];
			} else {
				$this->time_limit = WEEK_IN_SECONDS;
			}

			// Add actions.
			add_action( 'admin_init', array( $this, 'check_installation_date' ) );
			add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
		}

		/**
		 * Seconds to words.
		 *
		 * @param string $seconds Seconds in time.
		 */
		public function seconds_to_words( $seconds ) {

			// Get the years.
			$years = ( intval( $seconds ) / YEAR_IN_SECONDS ) % 100;
			if ( $years > 1 ) {
				/* translators: Number of years */
				return sprintf( __( '%s years', 'bp-lock' ), $years );
			} elseif ( $years > 0 ) {
				return __( 'a year', 'bp-lock' );
			}

			// Get the weeks.
			$weeks = ( intval( $seconds ) / WEEK_IN_SECONDS ) % 52;
			if ( $weeks > 1 ) {
				/* translators: Number of weeks */
				return sprintf( __( '%s weeks', 'bp-lock' ), $weeks );
			} elseif ( $weeks > 0 ) {
				return __( 'a week', 'bp-lock' );
			}

			// Get the days.
			$days = ( intval( $seconds ) / DAY_IN_SECONDS ) % 7;
			if ( $days > 1 ) {
				/* translators: Number of days */
				return sprintf( __( '%s days', 'bp-lock' ), $days );
			} elseif ( $days > 0 ) {
				return __( 'a day', 'bp-lock' );
			}

			// Get the hours.
			$hours = ( intval( $seconds ) / HOUR_IN_SECONDS ) % 24;
			if ( $hours > 1 ) {
				/* translators: Number of hours */
				return sprintf( __( '%s hours', 'bp-lock' ), $hours );
			} elseif ( $hours > 0 ) {
				return __( 'an hour', 'bp-lock' );
			}

			// Get the minutes.
			$minutes = ( intval( $seconds ) / MINUTE_IN_SECONDS ) % 60;
			if ( $minutes > 1 ) {
				/* translators: Number of minutes */
				return sprintf( __( '%s minutes', 'bp-lock' ), $minutes );
			} elseif ( $minutes > 0 ) {
				return __( 'a minute', 'bp-lock' );
			}

			// Get the seconds.
			$seconds = intval( $seconds ) % 60;
			if ( $seconds > 1 ) {
				/* translators: Number of seconds */
				return sprintf( __( '%s seconds', 'bp-lock' ), $seconds );
			} elseif ( $seconds > 0 ) {
				return __( 'a second', 'bp-lock' );
			}
		}

		/**
		 * Check date on admin initiation and add to admin notice if it was more than the time limit.
		 */
		public function check_installation_date() {
			if ( ! get_site_option( $this->nobug_option ) || false === get_site_option( $this->nobug_option ) ) {
				add_site_option( $this->date_option, time() );

				// Retrieve the activation date.
				$install_date = get_site_option( $this->date_option );

				// If difference between install date and now is greater than time limit, then display notice.
				if ( ( time() - $install_date ) > $this->time_limit ) {
					add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
				}
			}
		}

		/**
		 * Display the admin notice.
		 */
		public function display_admin_notice() {
			$screen = get_current_screen();

			if ( isset( $screen->base ) && 'plugins' === $screen->base ) {
				$no_bug_url = wp_nonce_url( admin_url( '?' . $this->nobug_option . '=true' ), 'bp-lock-feedback-nounce' );
				$time       = $this->seconds_to_words( time() - get_site_option( $this->date_option ) );
				?>

<style>
.notice.bp-lock-notice {
	border-left-color: #008ec2 !important;
	padding: 20px;
}

.rtl .notice.bp-lock-notice {
	border-right-color: #008ec2 !important;
}

.notice.notice.bp-lock-notice .bp-lock-notice-inner {
	display: table;
	width: 100%;
}

.notice.bp-lock-notice .bp-lock-notice-inner .bp-lock-notice-icon,
.notice.bp-lock-notice .bp-lock-notice-inner .bp-lock-notice-content,
.notice.bp-lock-notice .bp-lock-notice-inner .bp-lock-install-now {
	display: table-cell;
	vertical-align: middle;
}

.notice.bp-lock-notice .bp-lock-notice-icon {
	color: #509ed2;
	font-size: 50px;
	width: 60px;
}

.notice.bp-lock-notice .bp-lock-notice-icon img {
	width: 64px;
}

.notice.bp-lock-notice .bp-lock-notice-content {
	padding: 0 40px 0 20px;
}

.notice.bp-lock-notice p {
	padding: 0;
	margin: 0;
}

.notice.bp-lock-notice h3 {
	margin: 0 0 5px;
}

.notice.bp-lock-notice .bp-lock-install-now {
	text-align: center;
}

.notice.bp-lock-notice .bp-lock-install-now .bp-lock-install-button {
	padding: 6px 50px;
	height: auto;
	line-height: 20px;
}

.notice.bp-lock-notice a.no-thanks {
	display: block;
	margin-top: 10px;
	color: #72777c;
	text-decoration: none;
}

.notice.bp-lock-notice a.no-thanks:hover {
	color: #444;
}

@media (max-width: 767px) {

	.notice.notice.bp-lock-notice .bp-lock-notice-inner {
		display: block;
	}

	.notice.bp-lock-notice {
		padding: 20px !important;
	}

	.notice.bp-lock-noticee .bp-lock-notice-inner {
		display: block;
	}

	.notice.bp-lock-notice .bp-lock-notice-inner .bp-lock-notice-content {
		display: block;
		padding: 0;
	}

	.notice.bp-lock-notice .bp-lock-notice-inner .bp-lock-notice-icon {
		display: none;
	}

	.notice.bp-lock-notice .bp-lock-notice-inner .bp-lock-install-now {
		margin-top: 20px;
		display: block;
		text-align: left;
	}

	.notice.bp-lock-notice .bp-lock-notice-inner .no-thanks {
		display: inline-block;
		margin-left: 15px;
	}
}
</style>
			<div class="notice updated bp-lock-notice">
				<div class="bp-lock-notice-inner">
					<div class="bp-lock-notice-icon">
						<img src="<?php echo esc_url( BPLOCK_PLUGIN_URL ) . '/admin/wbcom/assets/imgs/bp_lock.png'; ?>" alt="<?php echo esc_attr__( 'BuddyPress Lock', 'bp-lock' ); ?>" />
					</div>
					<div class="bp-lock-notice-content">
						<h3><?php echo esc_html__( 'Are you enjoying BuddyPress Lock?', 'bp-lock' ); ?></h3>
						<p>
						<?php /* translators: %1$s is replaced with $this->name */ ?>
							<?php printf( esc_html__( 'We hope you\'re enjoying %1$s! Could you please do us a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?', 'bp-lock' ), esc_html( $this->name ) ); ?>
						</p>
					</div>
					<div class="bp-lock-install-now">
						<?php printf( '<a href="%1$s" class="button button-primary bp-lock-install-button" target="_blank">%2$s</a>', esc_url( 'https://wordpress.org/support/plugin/lock-my-bp/reviews#new-post' ), esc_html__( 'Leave a Review', 'bp-lock' ) ); ?>
						<a href="<?php echo esc_url( $no_bug_url ); ?>" class="no-thanks"><?php echo esc_html__( 'No thanks / I already have', 'bp-lock' ); ?></a>
					</div>
				</div>
			</div>
				<?php
			}
		}

		/**
		 * Set the plugin to no longer bug users if user asks not to be.
		 */
		public function set_no_bug() {

			// Bail out if not on correct page.
			if ( ! isset( $_GET['_wpnonce'] ) || ( ! wp_verify_nonce( $_GET['_wpnonce'], 'bp-lock-feedback-nounce' ) || ! is_admin() || ! isset( $_GET[ $this->nobug_option ] ) || ! current_user_can( 'manage_options' ) ) ) {
				return;
			}

			add_site_option( $this->nobug_option, true );
		}
	}
endif;

/*
* Instantiate the BP_Lock_Feedback class.
*/
new BP_Lock_Feedback(
	array(
		'slug'       => 'bp_lock',
		'name'       => __( 'BP Private Community', 'bp-lock' ),
		'time_limit' => WEEK_IN_SECONDS,
	)
);
