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
            let actionUser = $(this).data("evocmsUserUser");
            let actionId = $(this).data("evocmsUserId");
            let fd = new FormData(form[0]);
            let change = $(document).triggerHandler("evocms-user-" + action + "-data", [ actionUser, actionId, form, fd ]);
            if(typeof change != "undefined") {
                fd = change;
            }
            let url = action;
            if(typeof actionUser !== "undefined") {
                url += '/' + actionUser;
            }
            if(typeof actionId !== "undefined") {
                url += '/' + actionId;
            }
            let contentType = false;
            if(typeof fd == 'string') {
                contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
            }
            $.ajax({
                url: "/evocms-user/" + url,
                data: fd,
                type: "POST",
                cache: false,
                processData: false,
                contentType: contentType,
                dataType: 'json',
                beforeSend: function () {
                    form.css({'opacity':'.5'});
                    form.find('[data-error]').html('');

                    $(document).trigger("evocms-user-" + action + "-before", [ actionUser, actionId, form ]);
                },
                success: function (msg) {
                    //console.log(msg);
                    form.animate({'opacity':'1'}, 250);
                    if (msg.status == "error") {
                        let errors = msg.errors || {};
                        let fieldErrors = errors.validation || {};
                        let customErrors = errors.customErrors || {};
                        let commonErrors = errors.common || [];

                        for(k in fieldErrors) {
                            //console.log(k + ' ' + fieldErrors[k]);
                            form.find('[data-error-' + k + ']').html(self.makeString(fieldErrors[k]));
                        }

                        for(k in customErrors) {
                            //console.log(k + ' ' + customErrors[k]);
                            form.find('[data-error-' + k + ']').html(self.makeString(customErrors[k]));
                        }

                        form.find('[data-error-common]').html(self.makeString(commonErrors));

                        $(document).trigger("evocms-user-" + action + "-error", [ actionUser, actionId, form, msg ]);
                    } else {
                        $(document).trigger("evocms-user-" + action + "-success", [ actionUser, actionId, form, msg ]);

                        let redirect = msg.redirect || '';
                        if(redirect != '') {
                            location.href = redirect;
                        }
                    }
                }
            })
        })
    },
    makeString: function(value, sep = "<br />") {
        if (typeof value === 'string' || value instanceof String) {
            return value;
        } else {
            return value.join(sep);
        }
    }
}
$(document).ready(function(){

    EvoCmsUser.init();

/*
    //// event examples ////
    $(document).on("evocms-user-auth-error", function(e, actionUser, actionId, element, msg){
        console.log("evocms-user-auth-error");
        console.log(element);
        console.log(msg);
    })
    $(document).on("evocms-user-auth-success", function(e, actionUser, actionId, element, msg){
        console.log("evocms-user-auth-success");
        console.log(element);
        console.log(msg);
        location.reload();
    })
    $(document).on("evocms-user-auth-before", function(e, actionUser, actionId, element){
        console.log("evocms-user-auth-before");
        console.log(element);
    })
    $(document).on("evocms-user-profile-success", function(e, actionUser, actionId, element, msg){
        alert('профиль успешно отредактирован');
    })
    $(document).on("evocms-user-order/repeat-success", function(e, actionUser, actionId, element, msg){
        Commerce.reloadCarts();
    })
*/
})