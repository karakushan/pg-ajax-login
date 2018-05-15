<form action="#" method="post" name="step-register-1" class="form">
	<input type="hidden" name="action" value="create_user">
  <div class="alert" style="display: none;"></div>
	<div class="row">
		<div class="col-lg-6">
			<p><input type="text" name="user_name" placeholder="Имя" required></p>
			<p><input type="text" name="user_last_name" placeholder="Фамилия" required></p>
			<p><input type="email" name="user_email" placeholder="E-mail" required></p>
		</div>
		<div class="col-lg-6">
			<p><input type="tel"  name="user_tel" placeholder="Телефон" required></p>
			<p><input type="password"  name="user_pass" placeholder="Пароль" required></p>
			<p><input type="password"  name="user_re_pass" placeholder="Повторите пароль" required></p>
		</div>
	</div>
  <div id="pg-ajax-login-recaptcha" class="g-recaptcha" data-sitekey="6Lca300UAAAAAH3xNsfwyLPPv3-V_dyG0cwL6s61"></div>
	<p class="text-center"><input type="submit" value="Регистрация"></p>
	<p class="rules">Нажимая на кнопку, вы подтверждаете свое совершеннолетие, соглашаетесь на <a href="#">обработку
			персональных данных</a>, а также соглашаетесь с условиями <a href="#">пользовательского соглашения</a>.</p>
</form>