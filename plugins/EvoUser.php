<?php

Event::listen('evolution.OnWebPageInit', function($params) {
    evo()->regClientScript('<script type="text/javascript" defer src="assets/plugins/evocms-user/script.js"></script>');
    return serialize($params);
});

