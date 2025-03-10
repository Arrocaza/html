<?php
include('conexion.php');

// Crear usuario
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];

    $sql = "SELECT MAX(id) as max_id FROM usuarios";
    $stmt = sqlsrv_query($conn, $sql);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $nuevo_id = $row['max_id'] + 1;

    $sql = "INSERT INTO usuarios (id, nombre, email) VALUES (?, ?, ?)";
    $params = array($nuevo_id, $nombre, $email);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        echo "Nuevo usuario creado con éxito";
    } else {
        echo "Error: " . print_r(sqlsrv_errors(), true);
    }
    header("Location: index.php");
    exit();
}

// Leer usuarios
$sql = "SELECT * FROM usuarios";
$result = sqlsrv_query($conn, $sql);

// Actualizar usuario
if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $sql = "UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?";
    $params = array($nombre, $email, $id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        echo "Usuario actualizado con éxito";
    } else {
        echo "Error: " . print_r(sqlsrv_errors(), true);
    }
    header("Location: index.php");
    exit();
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $params = array($id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        echo "Usuario eliminado con éxito";

        $sql = "SELECT COUNT(*) as total FROM usuarios";
        $result = sqlsrv_query($conn, $sql);
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        if ($row['total'] == 0) {
            $sql = "DBCC CHECKIDENT ('usuarios', RESEED, 0)";
            sqlsrv_query($conn, $sql);
        }
    } else {
        echo "Error: " . print_r(sqlsrv_errors(), true);
    }
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Simple en PHP</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2>Crear Usuario</h2>
<form action="index.php" method="POST">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" required><br><br>
    <label for="email">Email:</label>
    <input type="email" name="email" required><br><br>
    <button type="submit" name="crear">Crear</button>
</form>

<h2>Usuarios</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Acciones</th>
    </tr>

    <?php while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['nombre']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td>
                <a href="index.php?editar=<?php echo $row['id']; ?>">Editar</a> | 
                <a href="index.php?eliminar=<?php echo $row['id']; ?>">Eliminar</a>
            </td>
        </tr>
    <?php } ?>
</table>

<?php
// Editar usuario
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $params = array($id);
    $result = sqlsrv_query($conn, $sql, $params);
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
?>

<h2>Editar Usuario</h2>
<form action="index.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" required><br><br>
    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo $row['email']; ?>" required><br><br>
    <button type="submit" name="actualizar">Actualizar</button>
</form>

<?php } ?>

</body>
</html>

<?php sqlsrv_close($conn); ?>