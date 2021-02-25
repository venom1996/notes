let sended = false;
$(document).ready(function() {
    $("#save").click(function(e) {
        let validate = document.getElementById('printdisplanone')
        let validateTableTwo = document.getElementById('collaps')
        let validateTable = document.getElementById('validatecontact')
        if((validate.querySelector(':invalid')) && (validateTableTwo.querySelector(':invalid'))){
            alert('Заполните поля');
        }else{
            e.preventDefault();
            if (!sended) {
                var btn = $(this);
                var arr = [];
                $('#table4 .record').each(function() {
                    arr.push({"nameban":$(this).find(".nameban").val(), "numzadoljen":$(this).find(".numzadoljen").val(), "datedogovor":$(this).find(".datedogovor").val(), "dateprosrochen":$(this).find(".dateprosrochen").val(), "summplatcredit":$(this).find(".summplatcredit").val()});
                });
                BX24.callMethod('crm.deal.list', {
                        filter: {
                            ID: id,
                        },
                        select: ["CONTACT_ID"]
                    },
                    function idContact(result)
                    {
                        $.ajax({
                                method: "POST",
                                url: "www",
                                dataType: "html",
                                data: {idContact: result.data(),
                                    fio: $('#fio').val(),
                                    adresregis: $('#adresregis').val(),
                                    id: id,
                                <?=isset($arFields['ID'])?'id_anketa: '.$arFields['ID'].",":''?>
                            famalegirl: $('#famalegirl').val(),
                        adressproj: $('#adressproj').val(),
                        telelich: $('#telelich').val(),
                        teleraboch: $('#teleraboch').val(),
                        email: $('#email').val(),
                        uznalicompany: $('#uznalicompany').val(),
                    },
                        beforeSend:
                            function () { $(".loader").fadeIn();
                                $(".loader_inner").fadeIn();

                            },
                        success: function(response) {
                            BX24.callMethod(
                                "crm.lead.update",
                                {
                                    id: id,
                                    fields:
                                        {
                                            "UF_CRM_1612683618": response,
                                            "UF_CRM_1612946020": 'true'
                                        },
                                    params: { "REGISTER_SONET_EVENT": "Y" }
                                },
                                function(result)
                                {
                                    if(result.error())
                                        console.error(result.error());
                                    else
                                    {
                                        console.log(idAnketPars)
                                        console.info(result.data());
                                    }
                                }
                            );
                            $(".loader").fadeOut();
                            $(".loader_inner").fadeOut();

                        },
                        error: function(er) {
                            console.log(er);
                        }
                    });
                    }
                )
                sended = true }else{alert('Вы уже сохранили!')}}})
});