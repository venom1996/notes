document.getElementById('gifts2').style = 'display:none';
document.getElementById('priz').style = 'display:none';
document.getElementById('gifts3').style = 'display:none';
document.getElementById('popup-callbac').style = 'display:none';
(function() {
    var error = document.createElement('inp__error');
    const wheel = document.querySelector('.wheel');
    const startButton = document.querySelector('.btn1');
    const popupClose = document.querySelector('.popup__close');
    var deg = 0;
    const input = document.querySelector('#popup-callback-phone');
    const inputnames = document.querySelector('#consumers-feedback-name');
    startButton.addEventListener('click', func);
    function func() {
        //проверяем инпуты
        if (input.checkValidity() & inputnames.checkValidity()) {
            document.getElementById('inp__error').style = 'display: none;'
            startButton.style.pointerEvents = 'none';
            var x = Math.floor(Math.random() * (100)) + 1;
            if ((x % 33) == 0) {

                deg = 300;
            } else if ((x % 14) == 0) {

                deg = 70;
            } else if ((x % 3) == 0) {

                deg = 240;
            } else {


                x = Math.floor(Math.random() * (4)) + 1;
                // 15 %
                if (x == 1) {
                    //15%
                    deg = 150;
                } if (x == 2) {

                    deg = 120;
                } if (x == 3) {

                    deg = 30;

                } if (x == 4) {

                    deg = 340;
                }
            }
            document.getElementById('closepops').style = 'display:none';
            wheel.style.transition = 'all 8s ease-out';
            wheel.style.transform = `rotate(${deg + 1080}deg)`;
            // wheel.classList.add('blur');


            startButton.removeEventListener('click', func);
        }else {

            document.getElementById('inp__error').style = 'display: block;'
        }

    }


    wheel.addEventListener('transitionend',  () =>  {

        document.cookie = "played=true;max-age=10000000";
        wheel.classList.remove('blur');
        startButton.style.pointerEvents = 'auto';
        wheel.style.transition = 'none';
        var actualDeg = deg % 360;
        wheel.style.transform = `rotate(${actualDeg}deg)`;
        var gifts;
        if (actualDeg >= 0 && actualDeg <= 45 ) {
            gifts = '1';
        } else if (actualDeg >= 45 && actualDeg <= 90 ) {
            gifts = '2';
        } else if (actualDeg >= 90  &&  actualDeg <= 135) {
            gifts = '3';
        } else if (actualDeg >= 135  && actualDeg <= 180) {
            gifts = '4';
        } else if (actualDeg >= 181 && actualDeg <= 225) {
            gifts = '5';
        } else if (actualDeg >= 225 && actualDeg <= 270) {
            gifts = '6';
        } else if (actualDeg >= 270  && actualDeg <= 315) {
            gifts = '7';
        } else if (actualDeg >= 315 && actualDeg < 360) {
            gifts = '8';
        } else { alert('net podarka');}



        $(document).one("transitionend", ".js-form", function(e) {

            var errors = 0;
            var form = $(this).closest("form");

            if (errors == 0) {

                var b24trace = b24Tracker.guest.getTrace();

                var phone = '';
                phone = $(this).closest("form").find("input[name=phone]").val();

                var name = '';
                name = $(this).closest("form").find("input[name=name]").val(); //имя в форме

                //alert(id_user);

                jQuery.ajax({
                    type: "POST",
                    url: "/send",
                    data: {
                        phone: phone,
                        name: name,
                        gifts: gifts,
                        trace: b24trace,
                        region: daCityNum,

                    },
                    success: function success() {
                        /* Sending target to Yandex Metrika */
                        try {
                            ym(11111,'reachGoal','order_wheel');
                            ym(11111111,'reachGoal','order');
                        } catch (e) {}
                    },
                    error: function error() {
                        $("#preloader").hide();
                        alert("При отправке произошла ошибка!");
                    }

                });

            }

        });

        document.getElementById("gifts").innerHTML = gifts;

        setTimeout(function() {
            document.getElementById('closepops').style = 'display:block';
            document.getElementById('wheel').style = 'display:none';
            document.getElementById('marker').style = 'display:none';
            document.getElementById('priz').style = 'display: content';
            document.getElementById('gifts2').style = 'display: block';

        }, 1500);
        // setTimeout(function() {

        //     document.getElementById('gifts2').style = 'display: block';

        // }, 1200);
        // setTimeout(function() {

        //   document.getElementById('priz').style = 'display: content';

        // }, 1200);
    });


// проверяем, есть ли кука


    setTimeout(function() {

        $.fancybox.open({
            src: "#popup-callbac",
            hash: "popup-callbac",
            'touch' : false,
            clickSlide : 'false',
            clickOutside : 'false',
            hideOnContentClick: false,
            afterClose: function afterClose() {
                $("#popup-calc-result .popup__in .main-calc-list").remove();

            }
        });
        if (getCookie("played")) {
            $.fancybox.close();
        }else if (getCookie("clouses")) {
            $.fancybox.close();
        }
    }, 4000);


    if (getCookie("played")) {

        document.getElementById('gifts3').style = 'display:block';

    }
    $(document).on("click", popupClose, function() {
        document.cookie = "clouses=popup;max-age=1000000";

    })

})();