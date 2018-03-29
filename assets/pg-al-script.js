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