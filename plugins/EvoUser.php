<?php

Event::listen('evolution.OnWebPageInit', function($params) {
    die('evoUser on web page init');
    return serialize($params);
});

