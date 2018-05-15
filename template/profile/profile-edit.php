<form action="#" method="post" name="pgal-profile-edit" class="form pgal-profile-edit" enctype="multipart/form-data">
  <!--  <pre><?php /*print_r($user_meta) */ ?></pre>-->
	<?php wp_nonce_field() ?>
  <input type="hidden" name="action" value="pg_al_edit_user">
  <div class="alert" style="display: none;"></div>
  <div class="row">
    <div class="col-lg-4">

      <div class="avatar-block">
        <h5
          class="card-title text-center"><?php echo $current_user->first_name ?>&nbsp;<?php echo $current_user->last_name ?></h5>
        <p class="user-avatar">

			<?php
			global $wpdb;
			$user_avatar = wp_get_attachment_image_url( get_user_meta( $current_user->ID, $wpdb->get_blog_prefix() . 'user_avatar', 1 ), 'full' );
			if ( ! $user_avatar ) {
				$user_avatar = get_avatar_url( $current_user->ID );
			}
			// echo get_avatar( $current_user->ID, 150, $user_avatar, $current_user->first_name, array() ); ?>
          <span class="ava" style="background-image: url(<?php echo esc_url( $user_avatar ) ?>);">
                <img src="/wp-content/plugins/ajax-login/assets/img/preloader.svg" alt="preloader" class="preloader">
              </span>
        </p>

        <p class="text-center"><label href="#change-avatar" class="change-avatar">Сменить фото <input type="file"
                                                                                                      name="avatar"></label>
          <button class="btn btn-sm btn-warning" id="pg-al-save-avatar">Сохранить</button>
        </p>
      </div>
      <p class="text-center">
        <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#pg-al-revers">Обратная
          связь
        </button>
      </p>
      <p class="text-center">
        <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#pg-al-change-password">
          Смена пароля
        </button>
      </p>
      <p class="text-center">
        <button type="button" class="btn btn-light btn-sm " data-toggle="modal" data-target="#pg-al-delete-account">
          Удаление аккаунта
        </button>
      </p>

    </div>
    <div class="col-lg-8">
      <p>
        <label for="user_name">Имя</label>
        <input type="text" name="user_name" id="user_name" value="<?php echo esc_attr( $current_user->first_name ) ?>"
               required></p>
      <p>
        <label for="user_last_name">Фамилия</label>
        <input type="text" name="user_last_name" id="user_last_name"
               value="<?php echo esc_attr( $current_user->last_name ) ?>" required>
      </p>
      <p>
        <label for="user_email">E-mail</label>
        <input type="email" name="user_email" id="user_email"
               value="<?php echo esc_attr( $current_user->user_email ) ?>"
               required disabled></p>
      <p>
        <label for="user_tel">Телефон</label>
        <input type="tel" name="user_tel" id="user_tel"
               value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'user_tel', 1 ) ) ?>" required>
      </p>
      <p>
        <label for="full_address">Адрес проживания</label>
        <input type="text" name="full_address" id="full_address"
               value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'full_address', 1 ) ) ?>" required>
      </p>
      <p>
        <label for="user_birthday">Дата рождения</label>
        <input type="text" name="user_birthday" id="user_birthday" class="datepick"
               value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'user_birthday', 1 ) ) ?>" required>
      </p>
      <p>
        <label for="user_scool">Образование</label>
        <input type="text" name="user_scool" id="user_scool"
               value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'user_scool', 1 ) ) ?>" required>
      </p>
      <p>
        <label for="profesion">Работа</label>
        <input type="text" name="profesion" id="profesion"
               value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'profesion', 1 ) ) ?>" required>
      </p>
      <p>
        <label for="year_ball">Дебютировал(а) на Венском балу в</label>
        <select name="year_ball" id="year_ball">
          <option value="">Выберите год</option>
			<?php for ( $count = 2003; $count <= 2050; $count ++ ) {
				echo ' <option value="' . $count . '" ' . selected( $count, get_user_meta( $current_user->ID, 'year_ball', 1 ), 0 ) . '>' . $count . '</option>';
			} ?>
        </select>
      </p>

    </div>
  </div>

  <p class="text-center">
    <button type="submit" value="Сохранить">Сохранить <img src="/wp-content/plugins/ajax-login/assets/img/preloader.svg"
                                                           alt="preloader" width="26" class="preloader"
                                                           style="margin-left: 10px;"></button>
  </p>
</form>
<!--модальное окно обратной связи-->
<div class="modal fade" id="pg-al-revers">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Обратная связь</h4>
      </div>
      <div class="modal-body">
        <form name="pg-al-revers" class="form">
			<?php wp_nonce_field() ?>
          <input type="hidden" name="action" value="pg_al_revers">
          <div class="message-box"></div>
          <label for="pg-al-revers-message">Сообщение</label>
          <p><textarea name="message" id="pg-al-revers-message" rows="10" required></textarea></p>
          <p class="text-center"><input type="submit" value="Отправить"></p>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!--модальное окно удаления аккаунта-->
<div class="modal fade" id="pg-al-delete-account">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Удаление аккаунта</h4>
      </div>
      <div class="modal-body">
        <form name="pg-al-delete-account" class="form">
			<?php wp_nonce_field() ?>
          <input type="hidden" name="action" value="pg_al_delete_account">
          <div class="message-box"></div>
          <label for="pg-al-delete-account-message">Причина удаления аккаунта</label>
          <p><textarea name="message" id="pg-al-delete-account-message" rows="10" required></textarea></p>
          <p class="text-center"><input type="submit" value="Отправить запрос"></p>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!--модальное окно смены пароля-->
<div class="modal fade" id="pg-al-change-password">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Смена пароля</h4>
      </div>
      <div class="modal-body">
        <form name="pg-al-change-password" class="form">
			<?php wp_nonce_field() ?>
          <input type="hidden" name="action" value="pg_al_change_password">
          <input type="hidden" name="redirect" value="<?php echo esc_url($_SERVER['REQUEST_URI']) ?>">
          <div class="message-box"></div>

          <p>
            <label for="pg-al-old-pass">Старый пароль</label>
            <input type="password" name="old-pass" id="pg-al-old-pass" required>
          </p>
          <p>
            <label for="pg-al-new-pass">Новый пароль</label>
            <input type="password" name="new-pass" id="pg-al-new-pass" required>
          </p>
          <p class="text-center"><input type="submit" value="Отправить запрос"></p>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

