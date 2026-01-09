<?php
// Dashboard V2
require_once 'config.php';
session_start();

if (isset($_POST['pass']) && $_POST['pass'] === 'admin123') $_SESSION['admin'] = true;
if (!isset($_SESSION['admin'])) {
    echo '<form method="POST" style="text-align:center;margin-top:20%;font-family:sans-serif;">
          <h2>Acceso C4</h2><input type="password" name="pass" placeholder="Password" style="padding:10px;"><br><br>
          <button style="padding:10px 20px;background:#1e3a8a;color:white;border:none;">Entrar</button></form>';
    exit;
}

// Filtros
$where = "1=1";
$params = [];
if (!empty($_GET['supervisor'])) {
    $where .= " AND supervisor = ?";
    $params[] = $_GET['supervisor'];
}

// Datos Tabla
$stmt = $pdo->prepare("SELECT * FROM reportes WHERE $where ORDER BY fecha_registro DESC LIMIT 50");
$stmt->execute($params);
$reportes = $stmt->fetchAll();

// Datos Gráficas (Totales Globales)
// 1. Áreas
$stmt = $pdo->query("SELECT area, COUNT(*) as c FROM reportes GROUP BY area");
$areasData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // ['Area' => count]

// 2. Ubicaciones (Top 5)
$stmt = $pdo->query("SELECT ubicacion, COUNT(*) as c FROM reportes GROUP BY ubicacion ORDER BY c DESC LIMIT 5");
$ubiData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

function webPath($p) { return str_replace(__DIR__, '.', $p); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard C4 | SESCA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800">
    <nav class="bg-blue-900 text-white px-6 py-4 flex justify-between items-center shadow">
        <span class="font-bold text-lg"><i class="fas fa-chart-line mr-2"></i>DASHBOARD C4</span>
        <a href="logout.php" class="text-xs bg-blue-800 px-3 py-1 rounded">Salir</a>
    </nav>

    <div class="container mx-auto px-4 py-6">
        
        <!-- Filtros -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <form class="flex gap-4 items-end">
                <div class="w-full md:w-1/3">
                    <label class="text-xs font-bold text-gray-500">Filtrar por Supervisor</label>
                    <select name="supervisor" class="w-full border p-2 rounded text-sm">
                        <option value="">Todos</option>
                        <option value="José Guadalupe Córdoba Ventura">José Guadalupe Córdoba Ventura</option>
                        <option value="Olegario Reyes Arnabar">Olegario Reyes Arnabar</option>
                        <option value="Uriel Francisco Garfias Vela">Uriel Francisco Garfias Vela</option>
                        <!-- Agregar resto... -->
                    </select>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Filtrar</button>
                <a href="dashboard.php" class="text-blue-600 text-sm py-2">Limpiar</a>
            </form>
        </div>

        <!-- Gráficas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-center font-bold text-gray-600 mb-2">Incidentes por Área</h3>
                <div class="h-64"><canvas id="chartAreas"></canvas></div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-center font-bold text-gray-600 mb-2">Top Ubicaciones Críticas</h3>
                <div class="h-64"><canvas id="chartUbi"></canvas></div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Supervisor</th>
                        <th class="px-4 py-3">Incidente</th>
                        <th class="px-4 py-3">Ubicación</th>
                        <th class="px-4 py-3 text-center">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($reportes as $r): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><?php echo date('d/m H:i', strtotime($r['fecha_suceso'])); ?></td>
                        <td class="px-4 py-3 font-medium text-gray-900"><?php echo $r['supervisor']; ?></td>
                        <td class="px-4 py-3 text-blue-600"><?php echo $r['titulo_suceso']; ?></td>
                        <td class="px-4 py-3 text-xs text-gray-500"><?php echo $r['ubicacion']; ?></td>
                        <td class="px-4 py-3 text-center">
                            <a href="<?php echo webPath($r['pdf_path']); ?>" target="_blank" class="text-red-500 hover:text-red-700"><i class="fas fa-file-pdf fa-lg"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Chart Áreas
        new Chart(document.getElementById('chartAreas'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_keys($areasData)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($areasData)); ?>,
                    backgroundColor: ['#1e3a8a', '#3b82f6', '#93c5fd']
                }]
            },
            options: { maintainAspectRatio: false }
        });

        // Chart Ubicaciones
        new Chart(document.getElementById('chartUbi'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($ubiData)); ?>,
                datasets: [{
                    label: 'Reportes',
                    data: <?php echo json_encode(array_values($ubiData)); ?>,
                    backgroundColor: '#ef4444'
                }]
            },
            options: { maintainAspectRatio: false, indexAxis: 'y' }
        });
    </script>
</body>
</html>