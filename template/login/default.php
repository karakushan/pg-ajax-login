<?php
/**
 * Created by PhpStorm.
 * User: karak
 * Date: 29.03.2018
 * Time: 15:08
 */

?>
<form id="login" action="login" method="post">
  <input type="hidden" name="redirect_url" id="redirect-url" value="<?php echo esc_url( $args['redirect_url'] ) ?>">
  <p class="status"></p>
  <p><input id="username" type="text" name="username" placeholder="E-mail"></p>
  <p><input id="password" type="password" name="password" placeholder="Пароль"></p>
  <p><input id="remember" type="checkbox" name="remember" value="1" checked> <label for="remember">Запомнить
      меня</label></p>
  <p class="text-center"><input class="submit_button" type="submit" value="Вход" name="submit"></p>
  <p class="text-center"><a href="/password-reset/">Забыли пароль?</a> <br>
    <a href="#register" data-toggle="modal">Зарегистрируйтесь</a> прямо сейчас!</p>
	<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
</form>
