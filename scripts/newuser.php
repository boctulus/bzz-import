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


if ( !username_exists($username) && !email_exists($email) )
{
	$user_id = wp_create_user( $username, $password, $email);
	if ( is_int($user_id) )
	{
		$wp_user_object = new WP_User($user_id);
		$wp_user_object->set_role('administrator');
		echo 'Successfully created new admin user. Now delete this file!';
	}
	else {
		echo 'Error with wp_insert_user. No users were created.';
	}
}
else {
	echo 'This user or email already exists. Nothing was done.';
}

/*
    Ahora intento logueo
*/

$user_data = array(
    'user_login'    => $username,
    'user_password' => $password,
    'remember'      => true, // Opcional, si quieres recordar al usuario
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