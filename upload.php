<?php
    /*
     * upload.php details (ko)
     * 
     * param @result : 성공/실패 여부 반환
     *      성공(1), 실패(0)
     * 
     * param @url : 업로드 성공 시 다운로드 경로 반환
     * 
     * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * - * -
     * 
     * upload.php details (en)
     * 
     * param @result : returns success/failure
     *      success(1), failure(0)
     * 
     * param @url : returns download url when upload successes
     */

    if (!$_FILES) {
        echo '{"result":"0", "message":"not a file"}';
        exit();
    }

    $date = date('Y-m-d H:i:s');

    $encode_code = 'TbpSQWwCc46XOBd9aJiN1rMFLjoIPVvtgumezkfHUAlYyx2Z8Dhsn305RGEqK7';
    $code = '';

    for ($i = 0; $i < 8; $i++) {
        $code = $code.$encode_code[mt_rand(21437, 92417264) % 62];
    }

    $file_tmp = $_FILES['file']['tmp_name'];
    $file_realname = $_FILES['file']['name'];
    $file_name_encrypted = '';

    for ($i = 0; $i < 6; $i++) {
        $file_name_encrypted = $file_name_encrypted.$encode_code[mt_rand(21437, 92417264) % 62];
    }

    $file_size = $_FILES['file']['size'];
    $file_ext = '';

    if (strpos($file_realname, '.') !== false) {
        $file_ext = substr($file_realname, strrpos($file_realname, '.') + 1);
    }

    $file_name = 'ModaShare@'.date('Ymd').'_'.$code;
    if ($file_ext) $file_name = $file_name.'.'.$file_ext;

	// todo: change this path to your own directory.
    $upload_folder = '/root/www/html/shares/uploads/';
    $file_path = $upload_folder.$file_name;

    if (!move_uploaded_file($file_tmp, $file_path)) {
        echo '{"result":"0", "message":"failed to move"}';
        exit();
    };

    $conn = new mysqli('localhost', 'app_modashare', 'app_modashare', 'app_modashare');

    $uploader_ip = $_SERVER['REMOTE_ADDR'];
    $uploader_detail = urlencode($_SERVER['HTTP_USER_AGENT']);

    $query = "INSERT INTO `files` SET `file_name_source` = '$file_realname',
                                      `file_name` = '$file_name_encrypted',
                                      `file_ext` = '$file_ext',
                                      `file_size` = '$file_size',
                                      `file_path` = '$file_path',
                                      `file_timeout` = '$_POST[timeout]',
									  `file_password` = '',
                                      `uploader_ip` = '$uploader_ip',
                                      `uploader_detail` = '$uploader_detail',
                                      `date` = '$date'";
    $conn->query($query);

    $file_id = $conn->insert_id;

	// todo: change this url to your own url.
    $site_url = "https://modaweb.kr/shares/";
    $file_url = $site_url.$file_name_encrypted;

	$_SERVER['HTTP_REFERER'] = str_replace(chr(92), chr(92).chr(92), $_SERVER['HTTP_REFERER']); // \
	$_SERVER['HTTP_REFERER'] = str_replace(chr(39), '&#039;', $_SERVER['HTTP_REFERER']); //

    $query = "INSERT INTO `history`
              SET `file_id` = '$file_id',
                  `state` = 'uploaded',
				  `request_url` = '',
				  `ref` = '$_SERVER[HTTP_REFERER]',
                  `user_ip` = '$uploader_ip',
                  `user_detail` = '$uploader_detail',
                  `date` = '$date'";
    $conn->query($query);

    echo '{"result":"1", "url":"'.$file_url.'"}';
?>