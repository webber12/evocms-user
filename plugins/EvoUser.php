<?php

Event::listen('evolution.OnWebPageInit', function ($params) {
    $frontjs = config("evocms-user.FrontJS", 'assets/plugins/evocms-user/script.js');
    evo()->regClientScript('<script type="text/javascript" defer src="' . $frontjs . '"></script>');
    return serialize($params);
});
