jQuery(document).ready(function($){
	'use strict';

	$('ul.bplock-login-shortcode-tabs li').click(function(){
		var tab_id = $(this).attr('data-tab');
		$('ul.bplock-login-shortcode-tabs li').removeClass('current');
		$('.tab-content').removeClass('current');

		$(this).addClass('current');
		$("#"+tab_id).addClass('current');
	});

	/**
	 * User Login
	 */
	$(document).on('click', '#bplock-login-btn', function(){
		var btn = $(this);
		$('.bplock-message').hide();
		var btn_txt = btn.html();
		var username = $('#bplock-login-username').val();
		var password = $('#bplock-login-password').val();
		if( username == '' || password == '' ){
			$('#bplock-login-details-empty').show();
		} else {
			btn.html('<i class="fa fa-refresh fa-spin"></i> Logging in...');
			var data = {
				'action'	: 'bplock_login',
				'username'	: username,
				'password'	: password
			};
			$.ajax({
				dataType: "JSON",
				url: bplock_public_js_object.ajaxurl,
				type: 'POST',
				data: data,
				success: function( response ) {
					btn.html( btn_txt );
					if( response['data']['login_success'] == 'no' ) {
						$('#bplock-login-error').append( response['data']['message'] ).show();
					} else {
						$('#bplock-login-success').append( response['data']['message'] ).show();
						location.reload();
					}
				}
			});
		}
	});

	/**
	 * User Register
	 */
	$(document).on('click', '#bplock-register-btn', function(){
		var btn = $(this);
		$('.bplock-message').hide();
		var btn_txt = btn.html();
		var email = $('#bplock-register-email').val();
		var username = $('#bplock-register-username').val();
		var password = $('#bplock-register-password').val();
		if( email == '' || username == '' || password == '' ){
			$('#bplock-register-details-empty').append('Either of the detail is empty!').show();
		} else {
			var email_regex = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			if( !email_regex.test( email ) ) {
				$('#bplock-register-details-empty').append('Invalid Email!').show();	
			} else {
				btn.html('<i class="fa fa-refresh fa-spin"></i> Registering...');
				var data = {
					'action'	: 'bplock_register',
					'email'		: email,
					'username'	: username,
					'password'	: password
				};
				$.ajax({
					dataType: "JSON",
					url: bplock_public_js_object.ajaxurl,
					type: 'POST',
					data: data,
					success: function( response ) {
						btn.html( btn_txt );
						if( response['data']['register_success'] == 'no' ) {
							$('#bplock-register-error').append( response['data']['message'] ).show();
						} else {
							$('#bplock-register-success').append( response['data']['message'] ).show();
							location.reload();
						}
					}
				});
			}
		}
	});

	$(document).on('click', '#bplock-user-register', function(){
		$('#bplock-register-tab').click();
	});
	$(document).on('click', '#bplock-user-login', function(){
		$('#bplock-login-tab').click();
	});
});
