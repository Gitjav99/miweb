<?php
require_once 'includes/functions.php';
ensure_logged_in();          // Si está logeado, pasa
header('Location: chat_list.php');   // Si no, lo lleva a login
