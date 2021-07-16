<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wbcom-tab-content">
<div class="bplock-adming-setting">
	<div class="bplock-tab-header"><h3><?php esc_html_e( 'Have some questions?', 'bp-lock' ); ?></h3></div>
		<div class="bplock-admin-settings-block">
		<div id="bplock-settings-tbl">
			<div class="bplock-admin-row">
				<div>
					<button class="bplock-accordion"><?php esc_html_e( 'Is this plugin requires another plugin?', 'bp-lock' ); ?></button>
					<div class="panel">
						<p>
							<?php esc_html_e( 'This plugin does not require any specific plugin. Although this plugin supports BuddyPress, if active.', 'bp-lock' ); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="bplock-admin-row">
				<div>
					<button class="bplock-accordion"><?php esc_html_e( 'What does this plugin work?', 'bp-lock' ); ?></button>
					<div class="panel">
						<p><?php esc_html_e( 'This plugin allows the site administrator to secure components of BuddyPress (if active) and WordPress pages from non-logged in users.', 'bp-lock' ); ?></p>
						<p><?php esc_html_e( 'You can lockdown WordPress Pages and any BuddyPress Component and can have some content displayed like if you want to show any shortcode content or any simple message.', 'bp-lock' ); ?></p>
					</div>
				</div>
			</div>

			<div class="bplock-admin-row">
				<div>
					<button class="bplock-accordion"><?php esc_html_e( 'Why BuddyPress Components setting tab and Page setting tab not displayed in plugin settings after plugin activate?', 'bp-lock' ); ?></button>
					<div class="panel">
						<p><?php esc_html_e( 'These setting tabs will be displayed after enable Lock option in general tab in plugin setting page.', 'bp-lock' ); ?></p>
					</div>
				</div>
			</div>

			<div class="bplock-admin-row">
				<div>
					<button class="bplock-accordion"><?php esc_html_e( 'If user locked BuddyPress \'groups\' component, what will be happen?', 'bp-lock' ); ?></button>
					<div class="panel">
						<p><?php esc_html_e( 'If the user will lock BuddyPress \'groups\' component, then groups page will not display groups list and restrict access on the single group page too and show content which will be set by admin in Locked Content.', 'bp-lock' ); ?></p>
					</div>
				</div>
			</div>

			<div class="bplock-admin-row">
				<div>
					<button class="bplock-accordion"><?php esc_html_e( 'If user locked BuddyPress \'members\' component, what will be happen?', 'bp-lock' ); ?></button>
					<div class="panel">
						<p><?php esc_html_e( 'If the user will lock BuddyPress \'members\' component, then groups page will not display members list and restrict access on the single member page too and show content which will be set by admin in \'Locked Content\'.', 'bp-lock' ); ?></p>
					</div>
				</div>
			</div>

			<div class="bplock-admin-row">
				<div>
					<button class="bplock-accordion"><?php esc_html_e( 'In any archive page locked page content will display or not?', 'bp-lock' ); ?></button>
					<div class="panel">
						<p>
							<?php esc_html_e( 'No, archive page will not be displayed any content of the locked page.', 'bp-lock' ); ?>
						</p>
					</div>
				</div>
			</div>

			<!-- <div class="bplock-admin-row">
				<div>
					<button class="bplock-accordion"><?php // _e( 'Can we access single page of locked custom post type?', 'bp-lock' ); ?></button>
					<div class="panel">
						<p>
							<?php // esc_html_e( 'We can\'t access single page of locked custom post type.', 'bp-lock' ); ?>
						</p>
					</div>
				</div>
			</div> -->
			<div class="bplock-admin-row">
				<div>
					<button class="bplock-accordion"><?php esc_html_e( 'Can we override the template files?', 'bp-lock' ); ?></button>
					<div class="panel">
						<p>
							<?php esc_html_e( 'Yes, the path to override is theme or child-theme/bp-lock/thempale.php ', 'bp-lock' ); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="bplock-admin-row">
				<div>
					<button class="bplock-accordion"><?php esc_html_e( 'When we search anything related to locked component and page, it\'s content will be displayed or not?', 'bp-lock' ); ?></button>
					<div class="panel">
						<p>
							<?php esc_html_e( 'No, anything will not be displayed in search content if related page or component already blocked', 'bp-lock' ); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="bplock-admin-row">
				<div>
					<button class="bplock-accordion"><?php esc_html_e( 'How to contact support?', 'bp-lock' ); ?></button>
					<div class="panel">
						<p><?php esc_html_e( 'Please submit details at ', 'bp-lock' ); ?><a href="http://wbcomdesigns.com/contact" rel="nofollow" target="_blank"><?php esc_html_e( 'Wbcom Designs', 'bp-lock' ); ?></a> <?php esc_html_e( 'for any query related to the plugin and BuddyPress. ', 'bp-lock' ); ?></p>
					</div>
				</div>
			</div>
			<div class="bplock-prmotion-row">
				<p><?php esc_html_e( 'To get more functionality purchase the pro version. You can purchase pro version from', 'bp-lock' ); ?> <a href="https://wbcomdesigns.com/downloads/buddypress-private-community-pro/" target="_blank"><?php esc_html_e( 'Here', 'bp-lock' ); ?></a></p>
		</div>
	</div>
</div>
</div>
