<?php
add_action( 'wp_ajax_ajaxlogin', 'pg_ajax_login_callback' );
add_action( 'wp_ajax_nopriv_ajaxlogin', 'pg_ajax_login_callback' );
function pg_ajax_login_callback() {

	// First check the nonce, if it fails the function will break
	check_ajax_referer( 'ajax-login-nonce', 'security' );

	// Nonce is checked, get the POST data and sign user on
	$info                  = array();
	$info['user_login']    = $_POST['username'];
	$info['user_password'] = $_POST['password'];
	$info['remember']      = ! empty( $_POST['remember'] ) && $_POST['remember'] == '1' ? true : false;

	$user_signon = wp_signon( $info, false );
	if ( is_wp_error( $user_signon ) ) {
		echo json_encode( array(
			'loggedin' => false,
			'message'  => __( 'Неправильные имя пользователя или пароль.' )
		) );
	} else {
		echo json_encode( array(
			'loggedin'     => true,
			'redirect_url' => esc_url( $_POST['redirect_url'] ),
			'message'      => __( 'Данные приняты, идёт переадресация...' )
		) );
	}

	die();
}

function pg_ajax_login_scripts() {
	wp_enqueue_style( 'pg-al-style', PG_AL_PLUGIN_URL . '/assets/css/pg-al-style.css' );

	wp_enqueue_script( 'password-strength-meter' );
	wp_enqueue_script( 'pg-jquery-validation', PG_AL_PLUGIN_URL . '/assets/js/jquery-validation/jquery.validate.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'pg-ajax-recaptcha', 'https://www.google.com/recaptcha/api.js', array( 'jquery' ), null, true );

	wp_enqueue_script( 'pg-ajax-login', PG_AL_PLUGIN_URL . 'assets/pg-al-script.js', array( 'jquery' ), null, true );
	wp_localize_script( 'pg-ajax-login', 'ajax_login_object', array(
		'ajaxurl'        => admin_url( 'admin-ajax.php' ),
		'loadingmessage' => __( 'Отправляем данные, подождите немного...' )
	) );
}

add_action( 'wp_enqueue_scripts', 'pg_ajax_login_scripts' );


/**
 * Форма входа
 *
 * @param array $args
 */
function pg_al_login_form( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'redirect_url' => home_url()
		)
	);
	include( PG_AL_PLUGIN_PATH . 'template/login/default.php' );
}

add_action( 'pg_login_form', 'pg_al_login_form', 10, 1 );

/**
 * Форма регистрации
 *
 * @param array $args
 */
function pg_al_register_form( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'redirect_url' => home_url()
		)
	);
	include( PG_AL_PLUGIN_PATH . 'template/register/default.php' );
}

add_action( 'pg_register_form', 'pg_al_register_form', 10, 1 );
/**
 * Форма регистрации
 *
 * @param array $args
 */
function pg_al_profile_form() {

	$current_user = wp_get_current_user();
	include( PG_AL_PLUGIN_PATH . 'template/profile/profile-edit.php' );
}

add_action( 'pg_al_profile_form', 'pg_al_profile_form', 10, 1 );

function pg_al_profile_edit_callback() {
	ob_start();
	do_action( 'pg_al_profile_form' );

	return ob_get_clean();
}

add_shortcode( 'pg_al_profile_edit', 'pg_al_profile_edit_callback' );

/* Редактирования профиля */
add_action( 'wp_ajax_pg_al_edit_user', 'pg_al_edit_user_callback' );
add_action( 'wp_ajax_nopriv_pg_al_edit_user', 'pg_al_edit_user_callback' );
function pg_al_edit_user_callback() {
	if ( ! wp_verify_nonce( $_POST['_wpnonce'] ) && ! is_user_logged_in() ) {
		echo json_encode(
			array(
				'status'  => 0,
				'message' => __( 'Возникла ошибка', 'wc-tickets' )
			)
		);
		wp_die();
	}

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}
	global $wpdb;
	$current_user    = wp_get_current_user();
	$user_data['ID'] = $current_user->ID;
	$file            = &$_FILES['file'];
	$movefile        = [];
	$overrides       = array( 'test_form' => false );
	if ( $file ) {
		$movefile = wp_handle_upload( $file, $overrides );
	}

	if ( ! empty( $movefile ) ) {
		$attachment = array(
			'guid'           => basename( $movefile['url'] ),
			'post_mime_type' => $movefile['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $movefile['url'] ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $movefile['url'] );
		if ( $attach_id ) {
			update_user_meta( $current_user->ID, $wpdb->get_blog_prefix() . 'user_avatar', $attach_id );

		}
	}


	if ( ! empty( $_POST['user_name'] ) ) {
		$user_data['first_name'] = esc_sql( trim( $_POST['user_name'] ) );
	}
	if ( ! empty( $_POST['user_last_name'] ) ) {
		$user_data['last_name'] = esc_sql( trim( $_POST['user_last_name'] ) );
	}
	if ( ! empty( $_POST['user_tel'] ) ) {
		update_user_meta( $current_user->ID, 'user_tel', trim( $_POST['user_tel'] ) );
	}
	if ( ! empty( $_POST['full_address'] ) ) {
		update_user_meta( $current_user->ID, 'full_address', trim( $_POST['full_address'] ) );
	}
	if ( ! empty( $_POST['user_birthday'] ) ) {
		update_user_meta( $current_user->ID, 'user_birthday', trim( $_POST['user_birthday'] ) );
	}
	if ( ! empty( $_POST['user_scool'] ) ) {
		update_user_meta( $current_user->ID, 'user_scool', trim( $_POST['user_scool'] ) );
	}
	if ( ! empty( $_POST['profesion'] ) ) {
		update_user_meta( $current_user->ID, 'profesion', trim( $_POST['profesion'] ) );
	}
	if ( ! empty( $_POST['year_ball'] ) ) {
		update_user_meta( $current_user->ID, 'year_ball', trim( $_POST['year_ball'] ) );
	}
	wp_update_user( $user_data );

	wp_die();
}

// обработка формы обратной связи в кабинете
add_action( 'wp_ajax_pg_al_revers', 'pg_al_revers_callback' );
add_action( 'wp_ajax_nopriv_pg_al_revers', 'pg_al_revers_callback' );
function pg_al_revers_callback() {
	if ( ! wp_verify_nonce( $_POST['_wpnonce'] ) && ! is_user_logged_in() ) {
		echo json_encode(
			array(
				'status'  => 0,
				'message' => __( 'Возникла ошибка', 'wc-tickets' )
			)
		);
		wp_die();
	}
	if ( wp_mail( get_bloginfo( 'admin_email' ), 'Сообщение с формы обратной связи', strip_tags( $_POST['message'] ) ) ) {
		echo json_encode(
			array(
				'status'  => 1,
				'message' => __( 'Ваше сообщение успешно отправлено', 'wc-tickets' )
			)
		);
	} else {
		echo json_encode(
			array(
				'status'  => 0,
				'message' => __( 'Возникла ошибка с отправкой сообщения', 'wc-tickets' )
			)
		);
	}

	wp_die();
}

// обработка формы удаления аккаунта в кабинете
add_action( 'wp_ajax_pg_al_delete_account', 'pg_al_delete_account_callback' );
add_action( 'wp_ajax_nopriv_pg_al_delete_account', 'pg_al_delete_account_callback' );
function pg_al_delete_account_callback() {
	if ( ! wp_verify_nonce( $_POST['_wpnonce'] ) && ! is_user_logged_in() ) {
		echo json_encode(
			array(
				'status'  => 0,
				'message' => __( 'Возникла ошибка', 'wc-tickets' )
			)
		);
		wp_die();
	}
	$current_user = wp_get_current_user();
	$message      = sprintf( 'Пользователь %s создал запрос на удаление аккаунта. Ссылка на профиль %s', $current_user->user_login, esc_url( admin_url( 'user-edit.php?user_id=' . $current_user->ID ) ) );
	if ( wp_mail( get_bloginfo( 'admin_email' ), 'Запрос на удаление аккаунта', $message ) ) {
		echo json_encode(
			array(
				'status'  => 1,
				'message' => __( 'Ваш запрос успешно отправлен', 'wc-tickets' )
			)
		);
	} else {
		echo json_encode(
			array(
				'status'  => 0,
				'message' => sprintf( __( 'Возникла ошибка с отправкой запроса. Напишите администратору на почту %s', 'wc-tickets' ), get_bloginfo( 'admin_email' ) )
			)
		);
	}

	wp_die();
}

// обработка формы удаления аккаунта в кабинете
add_action( 'wp_ajax_pg_al_change_password', 'pg_al_change_password_callback' );
add_action( 'wp_ajax_nopriv_pg_al_change_password', 'pg_al_change_password_callback' );
function pg_al_change_password_callback() {
	if ( ! wp_verify_nonce( $_POST['_wpnonce'] ) && ! is_user_logged_in() ) {
		echo json_encode(
			array(
				'status'  => 0,
				'message' => __( 'Возникла ошибка', 'wc-tickets' )
			)
		);
		wp_die();
	}
	$current_user = wp_get_current_user();
	$user         = get_userdata( $current_user->ID );
	$password     = trim( $_POST['new-pass'] );

	if ( $_POST['old-pass'] == $password ) {
		echo json_encode(
			array(
				'status'  => 0,
				'message' => __( 'Старый и новый пароли совпадают', 'wc-tickets' )
			)
		);
		wp_die();
	}

	if ( wp_check_password( $_POST['old-pass'], $user->data->user_pass ) ) {
		global $wpdb;

		$hash       = wp_hash_password( $password );
		$update_pwd = $wpdb->update( $wpdb->users, array(
			'user_pass'           => $hash,
			'user_activation_key' => ''
		), array( 'ID' => $current_user->ID ) );

		if ( $update_pwd ) {
			echo json_encode(
				array(
					'status'   => 1,
					'message'  => __( 'Пароль изменён. Перезагрузка интерфейса.', 'wc-tickets' ),
					'redirect' => $_POST['redirect']
				)
			);
		}


	} else {
		echo json_encode(
			array(
				'status'  => 0,
				'message' => __( 'Введённый вами текущий пароль неправильный', 'wc-tickets' )
			)
		);
	}

	wp_die();
}


