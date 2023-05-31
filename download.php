<?php
    function queryForbidden() {
        header('HTTP/1.0 403 Forbidden');
        exit();
    }

    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

	$blacklistArr = ["baidu", "discordbot", "facebookexternal", "kakaotalk"];

	for ($i = 0; $i < count($blacklistArr); $i = $i + 1) {
		preg_match("/".$blacklistArr[$i]."/", $agent, $matches);
		if ($matches) queryForbidden();
	}

    $file_name = $_GET['request'];

	// todo: change this mysqli settings to your own authentification.
    $conn = new mysqli('localhost', '<type-db-here>', '<type-id-here>', '<type-pass-here>');

    $query = "SELECT * FROM `files`
              WHERE `file_name` = '$file_name' AND `file_expired` = '0'";
    $res = $conn->query($query);
    $data = mysqli_fetch_array($res);

	$request_url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_detail = urlencode($_SERVER['HTTP_USER_AGENT']);

    $time_expire = strtotime($data['date']) + $data['file_timeout'];
    $time_now = time();
    $date = date('Y-m-d H:i:s');

	$_SERVER['HTTP_REFERER'] = str_replace(chr(92), chr(92).chr(92), $_SERVER['HTTP_REFERER']); // \
	$_SERVER['HTTP_REFERER'] = str_replace(chr(39), '&#039;', $_SERVER['HTTP_REFERER']); //

    if (!$data) {
		$query = "INSERT INTO `history`
				SET `file_id` = '-1',
					`state` = 'failed (no data)',
					`request_url` = '$request_url',
					`ref` = '$_SERVER[HTTP_REFERER]',
					`user_ip` = '$user_ip',
					`user_detail` = '$user_detail',
					`date` = '$date'";
		$conn->query($query);

        echo '404 there is no file '.$file_name;
        exit();
    }

    if ($time_expire < $time_now) {
        $query = "UPDATE `files`
                  SET `file_expired` = '1' 
                  WHERE `file_id` = '$data[file_id]'";
        $conn->query($query);

        $query = "INSERT INTO `history`
                  SET `file_id` = '$data[file_id]',
                      `state` = 'expired',
					  `request_url` = '$request_url',
					  `ref` = '$_SERVER[HTTP_REFERER]',
                      `user_ip` = '$user_ip',
                      `user_detail` = '$user_detail',
                      `date` = '$date'";
        $conn->query($query);

        unlink($data['file_path']);

        echo '404 there is no file '.$file_name;
        exit();
    }

    if (!is_file($data['file_path'])) {
		$query = "INSERT INTO `history`
				SET `file_id` = '0',
					`state` = 'failed (no file)',
					`request_url` = '$request_url',
					`ref` = '$_SERVER[HTTP_REFERER]',
					`user_ip` = '$user_ip',
					`user_detail` = '$user_detail',
					`date` = '$date'";
		$conn->query($query);

        echo '404 not found';
        exit();
    }

    $query = "INSERT INTO `history`
              SET `file_id` = '$data[file_id]',
                  `state` = 'downloaded',
				  `request_url` = '$request_url',
				  `ref` = '$_SERVER[HTTP_REFERER]',
                  `user_ip` = '$user_ip',
                  `user_detail` = '$user_detail',
                  `date` = '$date'";
    $conn->query($query);

	$query = "UPDATE `files`
				SET `file_downloaded` = `file_downloaded` + 1
				WHERE `file_id` = '$data[file_id]'";
	$conn->query($query);

    header("Content-Type: application/octet-stream");
    header("Content-Length: ".filesize($data['file_path']));
    header("Content-Disposition: attachment; filename=$data[file_name_source]");
    header("Content-Transfer-Encoding: binary");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: public");
    header("Expires: 0");

    $fp = fopen($data['file_path'], "rb");
    fpassthru($fp);
    fclose($fp);
?>