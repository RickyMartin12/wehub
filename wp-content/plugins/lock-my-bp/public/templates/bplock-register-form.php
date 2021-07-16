<div class="bplock-register-form-container">
	<div class="isa_success  bplock-message" id="bplock-register-success">
		<i class="fa fa-check"></i>
	</div>
	<div class="isa_error  bplock-message" id="bplock-register-error">
		<i class="fa fa-times-circle"></i>
	</div>
	<div class="isa_info bplock-message" id="bplock-register-details-empty">
		<i class="fa fa-info-circle"></i>
	</div>

	<p><input type="email" placeholder="<?php esc_attr_e( 'Email', 'bp-lock' ); ?>" id="bplock-register-email"></p>
	<p><input type="text" placeholder="<?php esc_attr_e( 'Username', 'bp-lock' ); ?>" id="bplock-register-username"></p>
	<p><input type="password" placeholder="<?php esc_attr_e( 'Password', 'bp-lock' ); ?>" id="bplock-register-password"></p>
	<p><button id="bplock-register-btn"><?php esc_attr_e( 'Register', 'bp-lock' ); ?></button>
	<p><?php esc_html_e( 'Already a member?', 'bp-lock' ); ?><a href="javascript:void(0);" id="bplock-user-login"><?php esc_html_e( 'Login', 'bp-lock' ); ?></a> <?php esc_html_e( 'here!', 'bp-lock' ); ?></p>
</div>
