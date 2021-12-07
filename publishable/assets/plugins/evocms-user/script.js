var EvoCmsUser = {
    init: function() {
        this.bindForms();
    },
    bindForms: function() {
        let self = this;
        $(document).on("submit", "form[data-evocms-user-action]", function(e){
            e.preventDefault();
            let form = $(this);
            let action = $(this).data("evocmsUserAction");
            let fd = new FormData(form[0]);
            //console.log(action);
            $.ajax({
                url: "/evocms-user/" + action,
                data: fd,
                type: "POST",
                cache: false,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function () {
                    form.css({'opacity':'.5'});
                    form.find('[data-error]').html('');
                    $(document).trigger("evocms-user-" + action + "-before", [ form ]);
                },
                success: function (msg) {
                    //console.log(msg);
                    form.animate({'opacity':'1'}, 250);
                    if (msg.status == "error") {
                        let errors = msg.errors || {};
                        let fieldErrors = errors.validation || {};
                        let customErrors = errors.customErrors || {};
                        let commonErrors = errors.common || [];
                        for(k in customErrors) {
                            //console.log(k + ' ' + customErrors[k]);
                            form.find('[data-error-' + k + ']').html(customErrors[k]);
                        }
                        for(k in fieldErrors) {
                            //console.log(k + ' ' + fieldErrors[k]);
                            form.find('[data-error-' + k + ']').html(fieldErrors[k]);
                        }
                        form.find('[data-error-common').html(commonErrors.join("<br>"));
                        $(document).trigger("evocms-user-" + action + "-error", [ form, msg ]);
                    } else {
                        $(document).trigger("evocms-user-" + action + "-success", [ form, msg ]);
                        let redirect = msg.redirect || '';
                        if(redirect != '') {
                            location.href = redirect;
                        }
                    }
                }
            })
        })
    }
}
$(document).ready(function(){
    EvoCmsUser.init();
/*
    //// event examples ////
    $(document).on("evocms-user-auth-error", function(e, element, msg){
        console.log("evocms-user-auth-error");
        console.log(element);
        console.log(msg);
    })
    $(document).on("evocms-user-auth-success", function(e, element, msg){
        console.log("evocms-user-auth-success");
        console.log(element);
        console.log(msg);
        location.reload();
    })
    $(document).on("evocms-user-auth-before", function(e, element){
        console.log("evocms-user-auth-before");
        console.log(element);
    })
    $(document).on("evocms-user-profile-success", function(e, element, msg){
        alert('профиль успешно отредактирован');
    })
*/
})