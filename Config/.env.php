<?php
  $variables = [
      'APP_URL' => 'http://custom.test',
      'DB_HOST' => 'localhost',
      'DB_USERNAME' => 'root',
      'DB_PASSWORD' => 'root',
      'DB_NAME' => 'laravel',
      'DB_PORT' => '3306',
      'HOST_URL'=>'http://localhost:3000'
  ];

  foreach ($variables as $key => $value) {
      putenv("$key=$value");
  }
?>