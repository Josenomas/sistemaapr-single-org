<?php
include("conexion.php");
require_once 'auth.php';
require_once 'ModuleManager.php';

// Inicializar ModuleManager
$moduleManager = ModuleManager::getInstance();

// NO verificar permisos específicos en index.php
// Solo verificar que esté logueado (ya lo hace auth.php automáticamente)

$usuario_actual = Auth::obtenerUsuario();
$usuario_rol = $usuario_actual['rol'];

// Para verificar permisos específicos
$puede_crear = Auth::tienePermiso('abonos.crear');
$puede_editar = Auth::tienePermiso('abonos.editar');
$puede_eliminar = Auth::tienePermiso('abonos.eliminar');

// Obtener datos del usuario actual con manejo robusto
$usuario_actual = Auth::obtenerUsuario();

// Manejar diferentes estructuras de datos del usuario
if (isset($usuario_actual['apellido'])) {
    $nombre_usuario = $usuario_actual['nombre'] . ' ' . $usuario_actual['apellido'];
} else if (isset($usuario_actual['username'])) {
    $nombre_usuario = $usuario_actual['username'];
} else if (isset($usuario_actual['nombre'])) {
    $nombre_usuario = $usuario_actual['nombre'];
} else {
    $nombre_usuario = 'Usuario Sistema';
}

// Variables adicionales que podrías necesitar
$usuario_id = isset($usuario_actual['id']) ? $usuario_actual['id'] : null;
$usuario_rol = isset($usuario_actual['rol']) ? $usuario_actual['rol'] : 'usuario';
$usuario_iniciales = substr($nombre_usuario, 0, 1) . (strpos($nombre_usuario, ' ') !== false ? substr($nombre_usuario, strpos($nombre_usuario, ' ') + 1, 1) : '');

// ✅ Definir mes pasado para mostrar datos del mes anterior
$mes_pasado = date('Y-m', strtotime('-1 month'));

// ✅ Total Clientes (socios activos y no eliminados)
$total_clientes = 0;
$res_clientes = $conexion->query("SELECT COUNT(*) AS total FROM socios WHERE estado = 'activo' AND activo = 1");
if ($res_clientes) {
    $row = $res_clientes->fetch_assoc();
    $total_clientes = $row['total'];
}

// ✅ Boletas Emitidas (mes pasado)
$res_boletas = $conexion->query("SELECT COUNT(*) AS total FROM boletas WHERE mes = '$mes_pasado'");
$boletas_emitidas_mes_actual = 0;
if ($res_boletas) {
    $row = $res_boletas->fetch_assoc();
    $boletas_emitidas_mes_actual = $row['total'];
}

// ✅ Pagos Pendientes (boletas impagas del mes pasado)
$pagos_pendientes = 0;
$sql = "
    SELECT COUNT(*) AS total
    FROM boletas b
    WHERE b.mes = '$mes_pasado'
    AND NOT EXISTS (
        SELECT 1 FROM pagosboleta p
        WHERE p.id_boleta = b.id AND p.estado_pago = 'pagado'
    )
";
$res_pendientes = $conexion->query($sql);
if ($res_pendientes) {
    $row = $res_pendientes->fetch_assoc();
    $pagos_pendientes = $row['total'];
}

// ✅ Tickets Abiertos (pendientes)
$tickets_abiertos = 0;
$res_tickets = $conexion->query("SELECT COUNT(*) AS total FROM tickets WHERE estado = 'pendiente'");
if ($res_tickets) {
    $row = $res_tickets->fetch_assoc();
    $tickets_abiertos = $row['total'];
}

// ✅ Cargar actividad reciente (últimas 5)
$actividad = [];
$res_actividad = $conexion->query("
    SELECT modulo as tipo, descripcion, fecha_creacion as fecha 
    FROM actividad_reciente 
    WHERE activo = 1
    ORDER BY fecha_creacion DESC 
    LIMIT 5
");
if ($res_actividad && $res_actividad->num_rows > 0) {
    while ($row = $res_actividad->fetch_assoc()) {
        $actividad[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Administración APR</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2563eb;
      --primary-dark: #1d4ed8;
      --primary-light: #dbeafe;
      --secondary: #06b6d4;
      --light: #f8fafc;
      --dark: #1e293b;
      --gray: #64748b;
      --gray-100: #f1f5f9;
      --gray-200: #e2e8f0;
      --gray-300: #cbd5e1;
      --gray-400: #94a3b8;
      --gray-500: #64748b;
      --gray-600: #475569;
      --gray-700: #334155;
      --gray-800: #1e293b;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --info: #06b6d4;
      --white: #ffffff;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --radius: 0.5rem;
      --radius-lg: 0.75rem;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      color: var(--dark);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      font-weight: 400;
      line-height: 1.6;
    }
    
    .container {
      width: 100%;
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 15px;
    }
    
    header {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary));
      color: white;
      padding: 1rem 0;
      box-shadow: var(--shadow-lg);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    
    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .logo i {
      font-size: 1.75rem;
      color: #60a5fa;
    }
    
    .logo h1 {
      font-size: 1.75rem;
      font-weight: 700;
      letter-spacing: -0.025em;
    }
    
    .user-menu {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .user-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .user-avatar {
      width: 42px;
      height: 42px;
      background: linear-gradient(135deg, var(--secondary), #0891b2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: white;
      font-size: 0.875rem;
      box-shadow: var(--shadow);
    }
    
    .user-details {
      display: flex;
      flex-direction: column;
    }
    
    .user-name {
      font-weight: 600;
      font-size: 0.875rem;
    }
    
    .user-role {
      font-size: 0.75rem;
      opacity: 0.8;
    }
    
    .logout-btn {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      padding: 8px 16px;
      border-radius: var(--radius);
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      font-size: 0.875rem;
      font-weight: 500;
    }
    
    .logout-btn:hover {
      background: rgba(255, 255, 255, 0.25);
      border-color: rgba(255, 255, 255, 0.3);
      transform: translateY(-1px);
      box-shadow: var(--shadow);
    }
    
    .main-content {
      display: flex;
      flex: 1;
    }
    
    .sidebar {
      width: 280px;
      background: var(--white);
      border-right: 1px solid var(--gray-200);
      height: calc(100vh - 74px);
      position: sticky;
      top: 74px;
      overflow-y: auto;
      transition: all 0.3s;
      box-shadow: var(--shadow-sm);
    }
    
    .sidebar-header {
      padding: 24px 20px;
      border-bottom: 1px solid var(--gray-200);
      background: var(--gray-50);
    }
    
    .sidebar-header h2 {
      font-size: 1.125rem;
      font-weight: 700;
      color: var(--gray-800);
    }
    
    .menu-categories {
      padding: 16px 0;
    }
    
    .menu-category {
      margin-bottom: 12px;
    }
    
    .category-title {
      font-size: 0.75rem;
      text-transform: uppercase;
      color: var(--gray-500);
      padding: 12px 20px 8px;
      letter-spacing: 0.1em;
      font-weight: 600;
    }
    
    .menu-items {
      list-style: none;
    }
    
    .menu-item {
      margin: 2px 8px;
    }
    
    .menu-link {
      display: flex;
      align-items: center;
      padding: 12px 16px;
      color: var(--gray-700);
      text-decoration: none;
      transition: all 0.2s;
      border-radius: var(--radius);
      gap: 12px;
      font-size: 0.875rem;
      font-weight: 500;
    }
    
    .menu-link:hover {
      background: var(--primary-light);
      color: var(--primary-dark);
      transform: translateX(4px);
    }
    
    .menu-link.active {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      box-shadow: var(--shadow);
    }
    
    .menu-link i {
      width: 20px;
      text-align: center;
      font-size: 1rem;
    }
    
    .content {
      flex: 1;
      padding: 32px;
      background: transparent;
    }
    
    .dashboard {
      margin-bottom: 32px;
    }
    
    .dashboard-title {
      font-size: 2rem;
      margin-bottom: 24px;
      color: var(--dark);
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 700;
    }
    
    .dashboard-title i {
      color: var(--primary);
      font-size: 1.75rem;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
      margin-bottom: 32px;
    }
    
    .stat-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      padding: 24px;
      display: flex;
      flex-direction: column;
      transition: all 0.3s ease;
      border: 1px solid var(--gray-200);
      position: relative;
      overflow: hidden;
    }
    
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: linear-gradient(180deg, var(--primary), var(--primary-dark));
      transition: width 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
    }
    
    .stat-card:hover::before {
      width: 6px;
    }
    
    .stat-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }
    
    .stat-title {
      font-size: 0.875rem;
      color: var(--gray-600);
      font-weight: 600;
    }
    
    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: white;
      box-shadow: var(--shadow);
    }
    
    .primary-bg {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }
    
    .success-bg {
      background: linear-gradient(135deg, var(--success), #059669);
    }
    
    .warning-bg {
      background: linear-gradient(135deg, var(--warning), #d97706);
    }
    
    .danger-bg {
      background: linear-gradient(135deg, var(--danger), #dc2626);
    }
    
    .stat-value {
      font-size: 2.25rem;
      font-weight: 700;
      margin-bottom: 8px;
      color: var(--gray-900);
      line-height: 1;
    }
    
    .stat-description {
      font-size: 0.875rem;
      color: var(--gray-500);
      font-weight: 500;
    }
    
    .quick-actions {
      background: var(--white);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      padding: 24px;
      margin-bottom: 32px;
      border: 1px solid var(--gray-200);
    }
    
    .section-title {
      font-size: 1.25rem;
      margin-bottom: 24px;
      color: var(--dark);
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 700;
    }
    
    .section-title i {
      color: var(--primary);
      font-size: 1.125rem;
    }
    
    .actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
    }
    
    .action-card {
      background: var(--gray-50);
      border: 1px solid var(--gray-200);
      border-radius: var(--radius-lg);
      padding: 20px;
      text-align: center;
      transition: all 0.3s ease;
      cursor: pointer;
      text-decoration: none;
      color: var(--dark);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 16px;
      position: relative;
      overflow: hidden;
    }
    
    .action-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.1), transparent);
      transition: left 0.5s ease;
    }
    
    .action-card:hover {
      background: var(--white);
      border-color: var(--primary);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
      color: var(--primary-dark);
    }
    
    .action-card:hover::before {
      left: 100%;
    }
    
    .action-icon {
      width: 56px;
      height: 56px;
      background: linear-gradient(135deg, var(--primary-light), rgba(37, 99, 235, 0.2));
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: var(--primary);
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }
    
    .action-card:hover .action-icon {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      border-color: var(--primary);
      transform: scale(1.1);
    }
    
    .action-title {
      font-weight: 600;
      font-size: 0.875rem;
    }
    
    .recent-activity {
      background: var(--white);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      padding: 24px;
      border: 1px solid var(--gray-200);
    }
    
    .activity-list {
      list-style: none;
    }
    
    .activity-item {
      padding: 16px 0;
      border-bottom: 1px solid var(--gray-100);
      display: flex;
      align-items: center;
      gap: 16px;
      transition: all 0.2s ease;
    }
    
    .activity-item:last-child {
      border-bottom: none;
    }
    
    .activity-item:hover {
      background: var(--gray-50);
      margin: 0 -12px;
      padding: 16px 12px;
      border-radius: var(--radius);
    }
    
    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      color: white;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      box-shadow: var(--shadow-sm);
      flex-shrink: 0;
    }
    
    .activity-content {
      flex: 1;
    }
    
    .activity-title {
      font-weight: 600;
      margin-bottom: 4px;
      color: var(--gray-800);
      font-size: 0.875rem;
    }
    
    .activity-time {
      font-size: 0.75rem;
      color: var(--gray-500);
      font-weight: 500;
    }
    
    .toggle-sidebar {
      display: none;
      background: none;
      border: none;
      font-size: 1.5rem;
      color: white;
      cursor: pointer;
    }
    
    .mobile-nav {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: var(--white);
      border-top: 1px solid var(--gray-200);
      z-index: 1000;
      padding: 12px 0;
      box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .mobile-nav-items {
      display: flex;
      justify-content: space-around;
      list-style: none;
    }
    
    .mobile-nav-item {
      text-align: center;
    }
    
    .mobile-nav-link {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-decoration: none;
      color: var(--gray-500);
      font-size: 0.75rem;
      font-weight: 500;
      transition: all 0.2s;
      padding: 8px;
      border-radius: var(--radius);
    }
    
    .mobile-nav-link i {
      font-size: 1.25rem;
      margin-bottom: 4px;
    }
    
    .mobile-nav-link.active,
    .mobile-nav-link:hover {
      color: var(--primary);
      background: var(--primary-light);
    }
    
    @media (max-width: 992px) {
      .sidebar {
        width: 70px;
        overflow-x: hidden;
      }
      
      .sidebar-header, .category-title, .menu-link span {
        display: none;
      }
      
      .menu-link {
        justify-content: center;
        padding: 16px;
      }
      
      .menu-link i {
        width: auto;
        font-size: 1.25rem;
      }
    }
    
    @media (max-width: 768px) {
      .toggle-sidebar {
        display: block;
      }
      
      .sidebar {
        position: fixed;
        left: -280px;
        width: 280px;
        z-index: 1010;
        background: var(--white);
        box-shadow: var(--shadow-lg);
      }
      
      .sidebar.active {
        left: 0;
      }
      
      .sidebar-header, .category-title, .menu-link span {
        display: block;
      }
      
      .menu-link {
        justify-content: flex-start;
        padding: 12px 16px;
      }
      
      .menu-link i {
        width: 20px;
        font-size: 1rem;
      }
      
      .mobile-nav {
        display: block;
      }
      
      body {
        padding-bottom: 80px;
      }
      
      .content {
        padding: 24px 16px;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
        gap: 16px;
      }
      
      .actions-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
      }
    }
    
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1005;
      display: none;
      backdrop-filter: blur(4px);
    }

    footer {
      background: linear-gradient(135deg, var(--gray-800), var(--dark));
      color: white;
      text-align: center;
      padding: 20px 0;
      margin-top: auto;
      font-weight: 500;
    }

    /* Animaciones mejoradas */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .stat-card,
    .action-card,
    .activity-item {
      animation: fadeInUp 0.6s ease-out forwards;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }

    /* Scrollbar personalizado */
    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
      background: var(--gray-100);
    }

    .sidebar::-webkit-scrollbar-thumb {
      background: var(--gray-300);
      border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
      background: var(--gray-400);
    }
  </style>
</head>
<body>

<header>
  <div class="container">
    <div class="header-content">
      <!-- Botón de toggle sidebar -->
      <button class="toggle-sidebar">
        <i class="fas fa-bars"></i>
      </button>
      
      <!-- Logo y título del sistema -->
      <div class="logo">
        <i class="fas fa-tint"></i>
        <h1>Sistema APR</h1>
      </div>
      
      <!-- Menú de usuario -->
      <div class="user-menu">
        <div class="user-info">
          <div class="user-avatar">
            <?= $usuario_iniciales ?>
          </div>
          <div class="user-details">
            <div class="user-name"><?= htmlspecialchars($nombre_usuario) ?></div>
            <div class="user-role"><?= htmlspecialchars(ucfirst($usuario_rol)) ?></div>
          </div>
        </div>
        
        <!-- Botón de salir corregido -->
        <a href="logout.php" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i>
          <span>Salir</span>
        </a>
      </div>
    </div>
  </div>
</header>

<div class="main-content">
  <div class="sidebar">
    <div class="sidebar-header">
      <h2>Menú Principal</h2>
    </div>
    <div class="menu-categories">
      <?php 
      // Generar menú dinámicamente con ModuleManager
      $menu = $moduleManager->generateMenu();
      
      foreach ($menu as $categoryId => $category): 
        $categoryInfo = $category['info'];
        $modules = $category['modules'];
      ?>
      <div class="menu-category">
        <div class="category-title">
          <i class="<?= htmlspecialchars($categoryInfo['icon']) ?>"></i>
          <?= htmlspecialchars($categoryInfo['name']) ?>
        </div>
        <ul class="menu-items">
          <?php foreach ($modules as $module): ?>
          <li class="menu-item">
            <a href="<?= htmlspecialchars($module['path']) ?>" class="menu-link" title="<?= htmlspecialchars($module['description']) ?>">
              <i class="<?= htmlspecialchars($module['icon']) ?>"></i>
              <span><?= htmlspecialchars($module['name']) ?></span>
              <?php if (!empty($module['features'])): ?>
                <span class="module-features" style="margin-left: auto; opacity: 0.6; font-size: 0.75rem;">
                  <?php 
                  $featureIcons = [];
                  if (isset($module['features']['pagination'])) $featureIcons[] = 'fas fa-list-ol';
                  if (isset($module['features']['export'])) $featureIcons[] = 'fas fa-download';
                  if (isset($module['features']['search'])) $featureIcons[] = 'fas fa-search';
                  if (count($featureIcons) > 0) echo '<i class="' . $featureIcons[0] . '"></i>';
                  ?>
                </span>
              <?php endif; ?>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endforeach; ?>
      
      <!-- Agregar entrada para administración de módulos si tiene permisos -->
      <?php if (Auth::tienePermiso('admin.modulos') || $usuario_rol === 'Administrador'): ?>
      <div class="menu-category">
        <div class="category-title">
          <i class="fas fa-puzzle-piece"></i>
          Administración del Sistema
        </div>
        <ul class="menu-items">
          <li class="menu-item">
            <a href="admin_modulos.php" class="menu-link">
              <i class="fas fa-puzzle-piece"></i>
              <span>Gestión de Módulos</span>
            </a>
          </li>
        </ul>
      </div>
      <?php endif; ?>
    </div>
  </div>
  
  <div class="content">
    <div class="dashboard">
      <h2 class="dashboard-title">
        <i class="fas fa-tachometer-alt"></i>
        Panel de Control
      </h2>
      
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-title">Total Clientes</div>
            <div class="stat-icon primary-bg">
              <i class="fas fa-users"></i>
            </div>
          </div>
          <div class="stat-value"><?= $total_clientes ?></div>
          <div class="stat-description">Socios y clientes activos</div>
        </div>
        
        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-title">Boletas Emitidas</div>
            <div class="stat-icon success-bg">
              <i class="fas fa-file-invoice"></i>
            </div>
          </div>
          <div class="stat-value"><?= $boletas_emitidas_mes_actual ?></div>
          <div class="stat-description">Del mes pasado (<?= $mes_pasado ?>)</div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-title">Pagos Pendientes</div>
            <div class="stat-icon warning-bg">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
          </div>
          <div class="stat-value"><?= $pagos_pendientes ?></div>
          <div class="stat-description">Del mes pasado (<?= $mes_pasado ?>)</div>
        </div>
        
        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-title">Tickets Abiertos</div>
            <div class="stat-icon danger-bg">
              <i class="fas fa-ticket-alt"></i>
            </div>
          </div>
          <div class="stat-value"><?= $tickets_abiertos ?></div>
          <div class="stat-description">Requieren atención</div>
        </div>
      </div>
    </div>
    
    <div class="quick-actions">
      <h3 class="section-title">
        <i class="fas fa-bolt"></i>
        Acciones Rápidas
      </h3>
      
      <div class="actions-grid">
        <a href="modules/lecturas/lecturas.php" class="action-card">
          <div class="action-icon">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <div class="action-title">Registrar Lectura</div>
        </a>
        
        <a href="modules/boletas/boletas.php" class="action-card">
          <div class="action-icon">
            <i class="fas fa-file-invoice"></i>
          </div>
          <div class="action-title">Generar Boletas</div>
        </a>
        
        <a href="modules/pago_boleta/pago_boleta.php" class="action-card">
          <div class="action-icon">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="action-title">Registrar Pago</div>
        </a>
        
        <a href="modules/deudas/deudas.php" class="action-card">
          <div class="action-icon">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
          <div class="action-title">Gestión Deudas</div>
        </a>
        
        <a href="modules/tickets/tickets.php" class="action-card">
          <div class="action-icon">
            <i class="fas fa-ticket-alt"></i>
          </div>
          <div class="action-title">Crear Ticket</div>
        </a>
        
        <a href="modules/reportes/reportes.php" class="action-card">
          <div class="action-icon">
            <i class="fas fa-chart-pie"></i>
          </div>
          <div class="action-title">Ver Reportes</div>
        </a>
      </div>
    </div>
    
    <div class="recent-activity">    
  <h3 class="section-title">
    <i class="fas fa-history"></i>
    Actividad Reciente
  </h3>

  <ul class="activity-list">
    <?php
    if (!empty($actividad)):
        foreach ($actividad as $item):
            $fecha_item = new DateTime($item['fecha']);
            $ahora = new DateTime();
            $intervalo = $ahora->diff($fecha_item);

            if ($intervalo->d > 0) {
                $tiempo = "Hace " . $intervalo->d . " día" . ($intervalo->d > 1 ? 's' : '');
            } elseif ($intervalo->h > 0) {
                $tiempo = "Hace " . $intervalo->h . " hora" . ($intervalo->h > 1 ? 's' : '');
            } elseif ($intervalo->i > 0) {
                $tiempo = "Hace " . $intervalo->i . " minuto" . ($intervalo->i > 1 ? 's' : '');
            } else {
                $tiempo = "Hace unos segundos";
            }
    ?>
      <li class="activity-item">
        <div class="activity-icon">
          <i class="fas fa-info-circle"></i>
        </div>
        <div class="activity-content">
          <div class="activity-title"><?php echo htmlspecialchars($item['descripcion']); ?></div>
          <div class="activity-time"><?php echo $tiempo; ?></div>
        </div>
      </li>
    <?php
        endforeach;
    else:
    ?>
      <li class="activity-item">
        <div class="activity-icon">
          <i class="fas fa-info-circle"></i>
        </div>
        <div class="activity-content">
          <div class="activity-title">No hay actividad reciente.</div>
          <div class="activity-time"></div>
        </div>
      </li>
    <?php endif; ?>
  </ul>
</div>

</div>
</div>

<div class="overlay"></div>

<div class="mobile-nav">
  <ul class="mobile-nav-items">
    <li class="mobile-nav-item">
      <a href="#" class="mobile-nav-link active">
        <i class="fas fa-home"></i>
        <span>Inicio</span>
      </a>
    </li>
    <li class="mobile-nav-item">
      <a href="modules/clientes/clientes.php" class="mobile-nav-link">
        <i class="fas fa-users"></i>
        <span>Clientes</span>
      </a>
    </li>
    <li class="mobile-nav-item">
      <a href="modules/lecturas/lecturas.php" class="mobile-nav-link">
        <i class="fas fa-clipboard-list"></i>
        <span>Lecturas</span>
      </a>
    </li>
    <li class="mobile-nav-item">
      <a href="pagos.php" class="mobile-nav-link">
        <i class="fas fa-money-bill-wave"></i>
        <span>Pagos</span>
      </a>
    </li>
    <li class="mobile-nav-item">
      <a href="#" class="mobile-nav-link" id="open-menu">
        <i class="fas fa-bars"></i>
        <span>Menú</span>
      </a>
    </li>
  </ul>
</div>

<footer>
  <div class="container">
    &copy; 2025 Sistema APR - Todos los derechos reservados
  </div>
</footer>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const toggleSidebarBtn = document.querySelector('.toggle-sidebar');
    const openMenuBtn = document.getElementById('open-menu');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');
    
    function toggleSidebar() {
      sidebar.classList.toggle('active');
      overlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
    }
    
    if (toggleSidebarBtn) {
      toggleSidebarBtn.addEventListener('click', toggleSidebar);
    }
    
    if (openMenuBtn) {
      openMenuBtn.addEventListener('click', toggleSidebar);
    }
    
    if (overlay) {
      overlay.addEventListener('click', toggleSidebar);
    }
    
    // Responsive menu for tablet view
    function checkScreenSize() {
      if (window.innerWidth <= 768) {
        sidebar.classList.remove('active');
        overlay.style.display = 'none';
      }
    }
    
    window.addEventListener('resize', checkScreenSize);
    
    // Current date for dashboard
    const currentDate = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = currentDate.toLocaleDateString('es-ES', options);
    
    // Simulated data loading
    setTimeout(() => {
      const dashboardTitle = document.querySelector('.dashboard-title');
      if (dashboardTitle) {
        dashboardTitle.innerHTML += `<small style="font-size: 1rem; margin-left: 10px; font-weight: normal; color: var(--gray-500);">${formattedDate}</small>`;
      }
    }, 500);
    
    // Active menu item
    const menuLinks = document.querySelectorAll('.menu-link');
    menuLinks.forEach(link => {
      if (link.getAttribute('href') === window.location.pathname.split('/').pop()) {
        link.classList.add('active');
      }
      
      link.addEventListener('click', function() {
        menuLinks.forEach(l => l.classList.remove('active'));
        this.classList.add('active');
        
        if (window.innerWidth <= 768) {
          toggleSidebar();
        }
      });
    });

    // Animación de números en las tarjetas de estadísticas
    function animateNumbers() {
      const statValues = document.querySelectorAll('.stat-value');
      
      statValues.forEach(value => {
        const finalValue = parseInt(value.textContent);
        if (isNaN(finalValue)) return;
        
        let currentValue = 0;
        const increment = finalValue / 50;
        const timer = setInterval(() => {
          currentValue += increment;
          if (currentValue >= finalValue) {
            value.textContent = finalValue;
            clearInterval(timer);
          } else {
            value.textContent = Math.floor(currentValue);
          }
        }, 30);
      });
    }

    // Iniciar animación de números después de una pequeña pausa
    setTimeout(animateNumbers, 1000);

    // Efectos hover mejorados para las tarjetas de acción
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-4px) scale(1.02)';
      });
      
      card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
      });
    });

    // Efectos para las tarjetas de estadísticas
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-6px)';
        this.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.25)';
      });
      
      card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'var(--shadow)';
      });
    });
  });
</script>
<!-- Sistema Global de Recordatorios APR -->
<link rel="stylesheet" href="assets/css/recordatorios-global.css">
<script src="assets/js/recordatorios-global.js?v=2.1"></script>
<script src="assets/js/recordatorios-init.js" 
        data-interval="300" 
        data-sound="true" 
        data-debug="false"
        data-max="5"></script>
</body>
</html>