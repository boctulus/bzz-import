<?php

/*
    @author  Pablo Bozzolo boctulus@gmail.com
*/

namespace boctulus\SW\core\libs;

use boctulus\SW\core\libs\Logger;

class Suscriptions
{    
    /**
     * Verificar si un usuario tiene una suscripción activa en WooCommerce Suscriptions
     *
     * @param int $user_id El ID del usuario.
     * @return bool True si el usuario tiene una suscripción activa, de lo contrario, False.
     * 
     * Nombre previo: isActive
     */
    static function hasActive($user_id = null) {
        if (!function_exists('wcs_get_users_subscriptions'))
        {
            admin_notice("WooCommerce Subscriptions es requerido", 'error');
            Logger::log("WooCommerce Subscriptions es requerido");

            return false;
        }

        if ($user_id === null){
            $user_id = Users::getCurrentUserId();

            // If it's Guest
            if ($user_id === 0){
                return false;
            }
        }
    
        // Comprobar si el usuario tiene suscripciones activas excluyendo los estados "on-hold" y "cancelled".
        $subscriptions = wcs_get_users_subscriptions( $user_id, array( 'status' => 'active', 'limit' => -1 ) );
        $active_subscriptions = array();
    
        foreach ($subscriptions as $subscription) {
            // Verificar que el estado de la suscripción no sea "on-hold" ni "cancelled".
            if ( $subscription->get_status() == 'active' ) {
                $active_subscriptions[] = $subscription;
            }
        }
    
        // Si hay suscripciones activas después de excluir los estados "on-hold" y "cancelled", el usuario tiene una suscripción activa.
        if ( ! empty( $active_subscriptions ) ) {
            return true;
        }
    
        return false;
    }
    
}