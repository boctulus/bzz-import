<?php
// ADD NEW ADMIN USER TO WORDPRESS

require_once __DIR__ . '/../../../../wp-blog-header.php';
// require_once __DIR__ . '/../../../../wp-includes/registration.php';

// ----------------------------------------------------
// CONFIG VARIABLES
$username = 'boctulus1';
$password = 'gogogo2k!';
$email    = 'boctulus@gmail.com';
// ----------------------------------------------------

/*
    Ahora intento logueo
*/

$user_data = array(
    'user_login'    => $username,
    'user_password' => $password,
    'remember'      => true, // Opcional, si se quiere recordar al usuario
);

$user = wp_signon($user_data, false);

if (is_wp_error($user)) {
    // Error al iniciar sesión
    $error_message = $user->get_error_message();
    echo 'Error al iniciar sesión: ' . $error_message;
} else {
    // Inicio de sesión exitoso
    echo 'Inicio de sesión exitoso';
}