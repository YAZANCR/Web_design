<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/app.php';

logout_user();

flash('success', 'تم تسجيل الخروج بنجاح.');

redirect_to('login.php');