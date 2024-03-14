<?php

namespace boctulus\SW\controllers;

use boctulus\SW\core\libs\DB;
use boctulus\SW\core\libs\Users;
use boctulus\SW\core\libs\Logger;
use boctulus\SW\core\libs\Strings;

class UsersController
{
    /*
        Devuelve Users en JSON
    */
    function get_list($after_id = null)
    {
        $users = table('users')
        ->when($after_id !== null, function($q) use ($after_id){
            $q->where(['ID', $after_id, '>']);
        })
        ->get();

        response()->send($users);
    }

    # /users/register
    function register(){
        $uname = $_GET['username'] ?? Strings::randomString(20);
        $email = "$uname@fakemail.com";

        $uid = Users::create($uname, null, null, 'administrator');   
        
        if (!empty($uid)){
            Users::loginNoPassword($uname);
        }
    }

    /*  
        /user/login
        
        Ej:

        http://woo4.lan/user/login?email=boc4rr35tulus@gmail.com
    */
    function login(){
        $email = $_GET['email'] ?? null;
        $uname = $_GET['username'] ?? null;

        if (empty($email) && empty($uname)){
            wp_die("email or username are required");
        }

        if (empty($uname)){
            $uname = Users::getUsernameByEmail($email);
        }

        Users::loginNoPassword($uname);
    }

     /*
        Ultimo usuario creado
    */
    function last(){
        $last_user   = Users::getLast();

        if ($last_user){
            $user_id    = $last_user->data->ID;
            $user_email = $last_user->data->user_email;

            dd($user_email, "LAST USER with ID=$user_id");
        }
    }
    
    /*
        Cambia e-mail del Admin

        /users/set_admin_email/boctulus@gmail.com
    */
    function set_admin_email($email){
        Users::setAdminEmail($email);
    }
}
