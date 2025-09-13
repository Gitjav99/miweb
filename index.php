<?php
require_once 'includes/functions.php';
ensure_logged_in();          // Si está logeado, pasa
header('Location: public/login.php');   // Si no, lo lleva a login
