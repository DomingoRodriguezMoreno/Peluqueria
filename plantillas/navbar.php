<?php session_start();?>
<nav class="navbar">
    <a href="/TFGPeluqueria/index.php" class="logo">
        <img src="/TFGPeluqueria/imagenes/Logo2.png" alt="Logo Peluquería" width = 50px>
    </a>

    <div class="nav-links">
    <?php // Mostrar "Servicios" a todos excepto empleados
        if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'empleado'): ?>
            <a href="/TFGPeluqueria/paginas/servicios.php">Servicios</a>
        <?php endif; ?>

        <?php // Mostrar "Coger Citas" solo a clientes logueados
        if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cliente'): ?>
            <a href="/TFGPeluqueria/paginas/coger_citas.php">Coger Citas</a>
        <?php endif; ?>

        <?php if(isset($_SESSION['tipo_usuario'])): ?>
            <!-- Mostrar perfil si está logueado -->
            <a href="/TFGPeluqueria/paginas/panel_<?= $_SESSION['tipo_usuario'] ?>.php" class="profile-btn">
                <?= htmlspecialchars($_SESSION['nombre']) ?> <!-- Sanitizar salida -->
            </a>
        <?php else: ?>
            <!-- Mostrar botón de login si no está logueado -->
            <button class="login-btn" onclick="mostrarLogin()">Login/Registro</button>
        <?php endif; ?>
    </div>

      <!-- Modal Login -->
    <div id="loginModal" class="modal">
        <div class="modal-contenido">
            <span class="cerrar" onclick="cerrarLogin()">&times;</span>
            <h2>Iniciar Sesión</h2>

    	    <?php
      		if (session_status() == PHP_SESSION_NONE) session_start();
      		if (!empty($_SESSION['login_error'])) {
        		echo "<div class='error-mensaje'>" . $_SESSION['login_error'] . "</div>";
        		echo "<script>window.addEventListener('DOMContentLoaded', () => mostrarLogin());</script>";
        		unset($_SESSION['login_error']);
      		}
    	    ?>

            <form action="/TFGPeluqueria/funcionalidades/login.php" method="POST">
                <label for="identificador">DNI o Teléfono:</label>
                <input type="text" id="identificador" name="identificador" required>
                
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Ingresar</button>
            </form>
            <p>¿No tienes cuenta? <a href="/TFGPeluqueria/paginas/registro_cliente.php">Regístrate aquí</a></p>
        </div>
    </div>

  <script src="/TFGPeluqueria/js/script.js"></script>
</nav>
