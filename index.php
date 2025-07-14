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

// Obtener lista de tablas
$tablas = [];
$result = pg_query($conn, "SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
while ($row = pg_fetch_assoc($result)) {
    $tablas[] = $row['tablename'];
}

// Tabla seleccionada
$tabla = $_GET['tabla'] ?? ($tablas[0] ?? '');
$filtro = $_POST['filtro'] ?? '';
$columnaSeleccionada = $_POST['columna'] ?? '';

$mensaje = '';
$error = '';

// Insertar datos si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insertar'])) {
    $campos = ['id', 'nombre', 'apellido', 'email', 'telefono', 'fecha', 'hora', 'duracion', 'reserva', 'n_pers'];
    $valores = [];
    foreach ($campos as $campo) {
        $valores[] = $_POST[$campo] ?? null;
    }

    $sql = "INSERT INTO $tabla (id, nombre, apellido, email, telefono, fecha, hora, duracion, reserva, n_pers) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)";
    $res = pg_query_params($conn, $sql, $valores);

    if ($res) {
        $mensaje = "✅ Registro insertado correctamente.";
    } else {
        $error = "❌ Error al insertar: " . pg_last_error($conn);
    }
}

// Obtener columnas
$columnas = [];
$resCol = pg_query($conn, "SELECT * FROM $tabla LIMIT 1");
if ($resCol) {
    $columnas = array_keys(pg_fetch_assoc($resCol));
}

// Obtener datos filtrados
$datos = [];
$query = "SELECT * FROM $tabla";
$params = [];

if ($filtro !== '' && in_array($columnaSeleccionada, $columnas)) {
    $query .= " WHERE $columnaSeleccionada ILIKE $1";
    $params[] = "%$filtro%";
}

$query .= " LIMIT 100";
$resData = pg_query_params($conn, $query, $params);

if ($resData) {
    while ($fila = pg_fetch_assoc($resData)) {
        $datos[] = $fila;
    }
}

pg_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>📋 Reservas - <?= htmlspecialchars($tabla) ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
        h1 { background: #333; color: white; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #555; color: white; }
        form { margin-top: 20px; }
        label { display: block; margin-top: 10px; }
        input, select { padding: 5px; width: 100%; }
        .mensaje { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<h1>💂 Reservas - Tabla <?= htmlspecialchars($tabla) ?></h1>

<?php if ($mensaje): ?><p class="mensaje"><?= $mensaje ?></p><?php endif; ?>
<?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>

<form method="GET">
    <label>Seleccionar tabla:
        <select name="tabla" onchange="this.form.submit()">
            <?php foreach ($tablas as $t): ?>
                <option value="<?= $t ?>" <?= $tabla == $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</form>

<?php if ($tabla && $columnas): ?>
<form method="POST">
    <label>Buscar por columna:
        <select name="columna">
            <?php foreach ($columnas as $col): ?>
                <option value="<?= $col ?>" <?= $col == $columnaSeleccionada ? 'selected' : '' ?>><?= $col ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <input type="text" name="filtro" placeholder="Texto a buscar" value="<?= htmlspecialchars($filtro) ?>">
    <button type="submit">🔍 Buscar</button>
</form>

<table>
    <thead>
        <tr>
            <?php foreach ($columnas as $col): ?>
                <th><?= $col ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (count($datos) === 0): ?>
            <tr><td colspan="<?= count($columnas) ?>">Sin resultados</td></tr>
        <?php else: ?>
            <?php foreach ($datos as $fila): ?>
                <tr>
                    <?php foreach ($fila as $valor): ?>
                        <td><?= htmlspecialchars($valor) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<h2>➕ Agregar nuevo registro</h2>
<form method="POST">
    <input type="hidden" name="insertar" value="1">
    <label>ID: <input type="number" name="id" required></label>
    <label>Nombre: <input type="text" name="nombre" required></label>
    <label>Apellido: <input type="text" name="apellido"></label>
    <label>Email: <input type="email" name="email"></label>
    <label>Teléfono: <input type="text" name="telefono"></label>
    <label>Fecha: <input type="date" name="fecha"></label>
    <label>Hora: <input type="time" name="hora"></label>
    <label>Duración (min): <input type="number" name="duracion"></label>
    <label>Reserva: <input type="text" name="reserva"></label>
    <label>N° Personas: <input type="number" name="n_pers"></label>
    <button type="submit">Guardar</button>
</form>
<?php else: ?>
    <p class="error">❌ No hay tablas disponibles.</p>
<?php endif; ?>
</body>
</html>



