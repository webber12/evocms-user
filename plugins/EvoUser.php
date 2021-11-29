<?php

Event::listen('evolution.OnWebPageInit', function($params) {
    evo()->regClientScript("assets/plugins/evocms-user/script.js");
    return serialize($params);
});

