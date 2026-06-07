<?php
$regions = [
    "us-east-1", "us-west-1", "eu-west-1", "eu-west-2", "eu-central-1",
    "ap-southeast-1", "ap-northeast-1", "ap-south-1", "sa-east-1", "ca-central-1", "ap-southeast-2"
];
$password = "7mN*@*wNmN7mhJZ";
$user = "postgres.kgdapksvpalgxxtubiwx";

foreach ($regions as $region) {
    $host = "aws-0-$region.pooler.supabase.com";
    $dsn = "pgsql:host=$host;port=6543;dbname=postgres;sslmode=require";
    try {
        $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_TIMEOUT => 3]);
        echo "SUCCESS: $region\n";
        exit(0);
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        if (strpos($msg, "tenant/user") === false && strpos($msg, "getaddrinfo") === false && strpos($msg, "timeout") === false) {
             echo "POSSIBLE MATCH ($region): $msg\n";
        }
    }
}
echo "NOT FOUND\n";
