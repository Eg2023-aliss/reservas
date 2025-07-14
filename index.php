<?php
// Cambia estos valores según tu URL de Aiven
$host = "pg-279acc-inti-d1ee.e.aivencloud.com";
$port = "24118";
$dbname = "defaultdb";
$user = "avnadmin";
$password = "AVNS_SPGUjEEaDTCDh9X1Nbq";
$sslmode = "require";

// Conectar a Aiven PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password sslmode=$sslmode");

if (!$conn) {
    die("❌ Error de conexión: " . pg_last_error());
}

// Cambia 'tu_tabla' por tu tabla real
$tabla = "sucursal1";  // ← REEMPLAZA esto

$result = pg_query($conn, "SELECT * FROM $tabla LIMIT 20");

if (!$result) {
    die("❌ Error en la consulta.");
}

echo "<h2>📊 Datos desde Aiven</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr>";
$fields = pg_num_fields($result);
for ($i = 0; $i < $fields; $i++) {
    echo "<th>" . pg_field_name($result, $i) . "</th>";
}
echo "</tr>";

while ($row = pg_fetch_assoc($result)) {
    echo "<tr>";
    foreach ($row as $col) {
        echo "<td>$col</td>";
    }
    echo "</tr>";
}
echo "</table>";

pg_close($conn);
?>

