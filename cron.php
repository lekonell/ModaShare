<?php
	// todo: change this mysqli settings to your own authentification.
    $conn = new mysqli('localhost', '<type-db-here>', '<type-id-here>', '<type-pass-here>');

    $query = "SELECT * FROM `files`
              WHERE `file_expired` = '0'";
    $res = $conn->query($query);

    while ($data = mysqli_fetch_array($res)) {
        $time_expire = strtotime($data['date']) + $data['file_timeout'];
        $time_now = time();
        $date = date('Y-m-d H:i:s');

        if ($time_expire < $time_now) {
            $query = "UPDATE `files`
                      SET `file_expired` = '1' 
                      WHERE `file_id` = '$data[file_id]'";
            $conn->query($query);
    
            $query = "INSERT INTO `history`
                      SET `file_id` = '$data[file_id]',
                          `state` = 'cron expired',
                          `user_ip` = '',
                          `user_detail` = '',
                          `date` = '$date'";
            $conn->query($query);
    
            unlink($data['file_path']);
        }
    }

    echo 'cron task success';
?>