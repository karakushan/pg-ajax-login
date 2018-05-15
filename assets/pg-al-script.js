jQuery(document).ready(function ($) {
    // Show the login dialog box on click
    $('a#show_login').on('click', function (e) {
        $('body').prepend('<div class="login_overlay"></div>');
        $('form#login').fadeIn(500);
        $('div.login_overlay, form#login a.close').on('click', function () {
            $('div.login_overlay').remove();
            $('form#login').hide();
        });
        e.preventDefault();
    });

    // Perform AJAX login on form submit
    $('form#login').on('submit', function (e) {
        $('form#login p.status').show().text(ajax_login_object.loadingmessage);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_login_object.ajaxurl,
            data: {
                'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                'username': $('form#login #username').val(),
                'password': $('form#login #password').val(),
                'remember': $('form#login #remember').val(),
                'redirect_url': $('form#login #redirect-url').val(),
                'security': $('form#login #security').val()
            },
            success: function (data) {
                $('form#login p.status').text(data.message);
                if (data.loggedin == true) {
                    document.location.href = data.redirect_url;
                }
            }
        });
        e.preventDefault();
    });

    // Предзагрузка фото
    $('[name="pgal-profile-edit"]').on('change', 'input[type="file"]:first', function (event) {
        var file = $(this).prop('files')[0];
        var fr = new FileReader();
        var dataImage = fr.readAsDataURL(this.files[0]);
        fr.addEventListener("load", function () {
            $('.pgal-profile-edit').find('.ava').css({
                "background-image": "url(" + fr.result + ")"
            })
        }, false);
        $("#pg-al-save-avatar").css('display', 'inline-block');

    });

    // Сохраняем форму
    $("#pg-al-save-avatar").on('click', function (event) {
        event.preventDefault();
        $('[name="pgal-profile-edit"]').submit();

    })

    // Форма редактирования профиля
    $(document).on('submit', '[name="pgal-profile-edit"]', function (event) {
        event.preventDefault();
        var el = $(this);
        // создадим объект данных формы
        var formData = new FormData(this);
        var file = el.find('input[type="file"]:first').prop('files')[0];
        if (file != undefined) formData.append('file', file);

        $.ajax({
            type: 'POST',
            url: allData.ajaxurl,
            data: formData,
            contentType: false,
            dataType: 'json',
            cache: false,
            async: true,
            processData: false,
            enctype: 'multipart/form-data',
            beforeSend: function () {
                el.find(".preloader").fadeIn();
            },
            success: function (data) {
                console.log(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('error...', xhr);
                //error logging
            },
            complete: function () {
                el.find(".preloader").fadeOut();
                $("#pg-al-save-avatar").css('display', 'none');

            }
        });

        return false;
    });

    // отправка формы обратной связи с кабинета
    $(document).on('submit', '[name="pg-al-revers"]', function (event) {
        event.preventDefault;
        send_data($(this));
        return false;
    });

    // отправка формы удаления аккаунта
    $(document).on('submit', '[name="pg-al-delete-account"]', function (event) {
        event.preventDefault;
        send_data($(this));
        return false;
    });

    // отправка формы смены пароля

    $("[name=pg-al-change-password]").validate({
        rules: {
            "old-pass": {required: true},
            "new-pass": {
                required: true,
                minlength: 6
            }
        },
        messages: {
            "old-pass": {required: "Поле обязательно к заполнению"},
            "new-pass": {
                required: "Поле обязательно к заполнению",
                minlength: "Минимальная длина 6 символов"
            }
        },
        submitHandler: function (form) {
            send_data($(form));
        }
    });


    // отправка формы аякс
    function send_data(el) {
        var form = el.serialize();
        $.ajax({
            type: 'POST',
            url: allData.ajaxurl,
            //data: JSON.stringify(parameters),
            data: form,
            cache: false,
            success: function (data) {
                console.log(data);
                try {
                    var json = JSON.parse(data);
                    if (json.status) {
                        el.find(".message-box").html('<div class="alert alert-success" role="alert">' + json.message + '</div>');
                        if (json.redirect) {
                            document.location.href = json.redirect;
                        }
                    } else {
                        el.find(".message-box").html('<div class="alert alert-danger" role="alert">' + json.message + '</div>');
                    }
                } catch (e) {
                    console.log('Ошибка ' + e.name + ":" + e.message + "\n" + e.stack);
                }

            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('error...', xhr);
                //error logging
            },
            complete: function () {
                //afer ajax call is completed
            }
        });

        return false;
    }


    /* ==== Мульти-шаговая форма регистрации ====*/
    $(document).on('submit', '[name="step-register-1"]', function (event) {
        event.preventDefault();
        var form = $(this);
        var $captcha = form.find('#pg-ajax-login-recaptcha');
        var response = grecaptcha.getResponse();

        if (response.length === 0) {
            form.find('.alert').fadeIn().addClass('alert-danger').text("Пожалуйста подтвердите что вы человек!");

        } else {
            $.ajax({
                type: 'POST',
                url: allData.ajaxurl,
                //data: JSON.stringify(parameters),
                data: form.serialize(),
                cache: false,
                success: function (data) {
                    try {
                        var json = JSON.parse(data);
                        if (json.status) {
                            setTimeout(function (e) {
                                document.location.href = json.redirect;
                            }, json.timeout);
                        } else {
                            form.find('.alert').fadeIn().addClass('alert-danger').text(json.message);
                        }

                    } catch (e) {
                        console.log('Ошибка ' + e.name + ":" + e.message + "\n" + e.stack);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('error...', xhr);
                    //error logging
                },
                complete: function () {
                    //afer ajax call is completed
                }
            });
        }

    });


});