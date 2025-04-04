<nav class="navbar">
    <a href="/TFGPeluqueria/index.php" class="logo">
        <img src="/TFGPeluqueria/imagenes/Logo2.png" alt="Logo Peluquería" width = 50px>
    </a>
    <div class="nav-links">
        <a href="/TFGPeluqueria/paginas/servicios.php">Servicios</a>
        <a href="#citas">Coger Citas</a>
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