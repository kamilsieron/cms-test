<?php

$server = "tcp:localhost,1433"; // lub tcp:.\SQLEXPRESS,1433
$conn = sqlsrv_connect($server, [
  "Database" => "cms",
  "UID" => "cms_login",
  "PWD" => "Bardzo_MocneHaslo_!234",
  "TrustServerCertificate" => true, // przy ODBC 18/17 gdy nie masz certyfikatu
  "LoginTimeout" => 5,
]);
if (!$conn) { die(print_r(sqlsrv_errors(), true)); }
