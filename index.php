<?php
$host = "pg-279acc-inti-d1ee.e.aivencloud.com";
$port = "24118";
$dbname = "defaultdb";
$user = "avnadmin";
$password = "AVNS_SPGUjEEaDTCDh9X1Nbq";
$sslmode = "require";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password sslmode=$sslmode");

if (!$conn) {
    die("❌ Error de conexión: " . pg_last_error());
}

// Consulta para mostrar nombres de tablas
$query = "SELECT tablename FROM pg_tables WHERE schemaname = 'public'";
$result = pg_query($conn, $query);

if (!$result) {
    die("❌ Error en la consulta.");
}

echo "<h2>📋 Tablas disponibles en la base de datos:</h2>";
echo "<ul>";
while ($row = pg_fetch_assoc($result)) {
    echo "<li>" . $row['tablename'] . "</li>";
}
echo "</ul>";

pg_close($conn);
?>


