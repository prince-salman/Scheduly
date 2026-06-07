<?php
$regions = [
    "ap-southeast-3", "eu-west-3", "ap-northeast-2", "sa-east-1", "eu-north-1", "me-central-1"
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
        echo "FAILED ($region): $msg\n";
    }
}
