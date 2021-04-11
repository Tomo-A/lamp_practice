<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();

if(is_logined() === false){
    redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);
$order_id = get_get('order_id');
$token = get_get('token');

if(is_valid_csrf_token($token)){
    if(is_admin($user) === false){
        $histories = get_fetch_order_history($db, $user['user_id']);
        $details = get_order_detail($db, $user['user_id'], $order_id);
    }else{
        $histories = get_fetch_admin_order_history($db);
        var_dump($histories);
        $details = get_admin_order_detail($db, $order_id);
    }
}else{
    set_error('不正な操作が行われました。');
    redirect_to(CART_URL);
}

include_once VIEW_PATH . 'order_detail_view.php'
?>