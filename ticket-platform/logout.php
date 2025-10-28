<?php
/**
 * Çıkış Yapma
 */

require_once __DIR__ . '/config/config.php';

logoutUser();

setFlashMessage('Başarıyla çıkış yaptınız!', 'success');
redirect('/index.php');