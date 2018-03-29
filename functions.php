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
