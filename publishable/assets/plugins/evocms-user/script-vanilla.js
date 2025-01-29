var EvoCmsUser = {
    init: function () {
        this.bindForms();
    },

    bindForms: function () {
        document.querySelectorAll('[data-evocms-user-action]').forEach((form) => {
            form.addEventListener('submit', (ev) => {
                ev.preventDefault();

                const action = form.dataset.evocmsUserAction;
                const actionUser = form.dataset.evocmsUserUser;
                const actionId = form.dataset.evocmsUserId;
                let fd = new FormData(form);

                document.addEventListener(`evocms-user-${action}-data-processed`, (e) => {
                    if (typeof e.detail.changed != undefined) {
                        fd = e.detail.changed;
                    }
                });

                document.dispatchEvent(new CustomEvent(`evocms-user-${action}-data`, { detail: { action, actionUser, actionId, form, fd } }));

                let url = action;
                if (typeof actionUser !== 'undefined') {
                    url += '/' + actionUser;
                }
                if (typeof actionId !== 'undefined') {
                    url += '/' + actionId;
                }
                url = '/evocms-user/' + url;

                let options = {
                    method: 'POST', // или 'PUT'
                    cache: 'no-cache',
                    body: fd,
                };

                if (typeof fd == 'string') {
                    options.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
                }

                try {
                    form.style.opacity = '.5'
                    form.querySelectorAll('[data-error]').forEach(el => {
                        el.innerHTML = ''
                    });

                    document.dispatchEvent(new CustomEvent(`evocms-user-${action}-before`, { detail: { actionUser, actionId, form } }))

                    fetch(url, options).then((response) => {
                        return response.json();
                    }).then((response) => {
                        // console.log('Успех:', response);

                        form.style.animation = 'formopacity .25s';
                        form.style.opacity = '1';

                        if (response.hasOwnProperty('error')) {
                            form.querySelectorAll('[data-error-common]').forEach(el => {
                                el.innerHTML = response.error;
                            })
                        } else {
                            if (response.status == 'error') {
                                const errors = response.errors || {};
                                const fieldErrors = errors.validation || {};
                                const customErrors = errors.customErrors || {};
                                const commonErrors = errors.common || [];

                                Object.keys(fieldErrors).forEach(k => {
                                    // console.error(k + ': ' + fieldErrors[k]);
                                    form.querySelectorAll('[data-error-' + k + ']')[0].textContent = fieldErrors[k].join(' ');
                                })

                                Object.keys(customErrors).forEach(k => {
                                    // console.error(k + ': ' + customErrors[k]);
                                    form.querySelectorAll('[data-error-' + k + ']')[0].textContent = customErrors[k].join(' ');
                                })

                                form.querySelectorAll('[data-error-common]').forEach(el => {
                                    let values = [];
                                    Object.keys(commonErrors).forEach(k => values.push(commonErrors[k]));
                                    el.innerHTML = values.join('<br />');
                                })

                                document.dispatchEvent(new CustomEvent(`evocms-user-${action}-error`, { detail: { actionUser, actionId, form, response } }));
                            } else {
                                document.dispatchEvent(new CustomEvent(`evocms-user-${action}-success`, { detail: { actionUser, actionId, form, response: {} } }));

                                if (response.hasOwnProperty('redirect')) {
                                    location.href = response.redirect;
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error(error.response);
                }
            });
        })
    }
}

window.onload = function () {
    EvoCmsUser.init();

    // --- event examples

    /*
    document.addEventListener('evocms-user-auth-data', (e) => {
        // e.detail = { action, actionUser, actionId, form, fd }
        //console.log('evocms-user-auth-data', e.detail);
        //console.log(e.detail.form);
        //console.log(e.detail.fd);

        let changed = e.detail.fd

        document.dispatchEvent(new CustomEvent(`evocms-user-${e.detail.action}-data-processed`, { detail: { changed } }));
    });
    */

    /*
    document.addEventListener('evocms-user-auth-before', (e) => {
        // e.details = { actionUser, actionId, form }
        //console.log('evocms-user-auth-before', e.detail);
        //console.log(e.detail.form);
    });
    */

    /*
    document.addEventListener('evocms-user-auth-error', (e) => {
        // e.detail = { actionUser, actionId, form, response }
        //console.log('evocms-user-auth-error', e.detail);
        //console.log(e.detail.form);
        //console.log(e.detail.response);
    });
    */
    document.addEventListener('evocms-user-auth-success', (e) => {
        // e.detail = { actionUser, actionId, form, response }
        //console.log('evocms-user-auth-success', e.detail);
        //console.log(e.detail.form);
        //console.log(e.detail.response);

        location.reload();
    });

    /*
    document.addEventListener('evocms-user-profile-success', (e) => {
        // e.details = { actionUser, actionId, form, response }
        alert('профиль успешно отредактирован');
    });
    */

    /*
    document.addEventListener('evocms-user-order/repeat-success', (e) => {
        // e.details = { actionUser, actionId, form, response }
        Commerce.reloadCarts();
    });
    */
}
