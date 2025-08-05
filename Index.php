<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./CSS/StyleIndexReal.css" />
    <title>Inventory +</title>
    <link rel="icon" href="./IMG/Logo.png" type="image/x-icon" />
</head>
<header>
    <nav>
        <div class="DivNavLogo">
            <img src="./IMG/Logo.png" alt="" class="LogoNav" />
        </div>
        <div class="DivNav">
            <div class="DivButtonsNav1">
                <a href="./Index.php"><button class="ButtonNav">INICIO</button></a>

                <?php if (isset($_COOKIE["NameUserM"])): ?>
                    <?php if (isset($_COOKIE["RoleDB"]) && $_COOKIE["RoleDB"] == "Administrador"): ?>
                        <a href="./Usuarios.php"><button class="ButtonNav">USUARIOS</button></a>
                    <?php endif; ?>
                    <a href="./Equipos.php"><button class="ButtonNav">EQUIPOS</button></a>
                    <a href="./Inventario.php"><button class="ButtonNav">INVENTARIO</button></a>
                <?php endif; ?>
            </div>
            <div class="DivButtonsNav2">
                <?php if (isset($_COOKIE["NameUserM"])): ?>
                    <a href="./Data/LogOut.php"><button class="ButtonNav">CERRAR SESIÓN</button></a>
                <?php else: ?>
                    <a href="./LoginPage.php"><button class="ButtonNav">INICIAR SESIÓN</button></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
<body>
    <section>
        <article>
            <div class="ContenedorItemsMain">
                <div class="DivPrincipal">
                    <h1 class="MainTittle">Inventory +</h1>
                    <div class="DivTextInCenter">
                        <p>Bienvenido a <strong>Inventory +</strong>, el sistema integral para gestionar tu tienda de productos gamer con eficiencia, orden y seguridad. Nuestro objetivo es ayudarte a mantener el control total de tu inventario, sin complicaciones.</p>

                        <li><strong>Gestión precisa:</strong> Lleva el control de tus procesadores, memorias RAM, tarjetas gráficas, discos, fuentes de poder, gabinetes y mucho más desde un solo lugar.</li><br>

                        <li><strong>Usuarios y Roles:</strong> Administra quién puede acceder al sistema. Solo los administradores pueden modificar usuarios o realizar movimientos críticos.</li><br>

                        <li><strong>Movimientos Registrados:</strong> Cada ingreso o salida de productos queda guardado con fecha, hora, usuario responsable y detalle de los artículos involucrados.</li><br>

                        <li><strong>Interfaz amigable:</strong> Diseñada para que puedas visualizar y editar tu inventario de manera intuitiva, sin necesidad de conocimientos técnicos avanzados.</li><br>

                        <li><strong>Diseñado para crecer:</strong> Inventory + se adapta a tiendas pequeñas o medianas que buscan eficiencia, transparencia y trazabilidad en cada acción.</li><br>

                        <p>Inventory + es más que un sistema de control: es la herramienta que potencia tu tienda gamer. Optimiza tu flujo de trabajo, minimiza pérdidas y maximiza el rendimiento de tu stock. <br> ¡Explora el sistema y descubre todo lo que puedes lograr!</p>
                    </div>
                </div>
                <div class="DivBanner">
                    <div class="DivInCenter">
                        <img src="./IMG/Logo.png" width="660px" class="BorderImague" alt="Logo de Inventory +" />
                    </div>
                </div>
            </div>

            <div class="Publicidad">
                <a href="https://maxceramica.com" target="_blank">
                    <img src="./IMG/Publicidad1.jpg" alt="Publicidad 1" class="PublicidadImg" />
                </a>
                <a href="https://pegoperfecto.com" target="_blank">
                    <img src="./IMG/Publicidad2.jpg" alt="Publicidad 2" class="PublicidadImg" />
                </a>
                <a href="https://innovapack.co" target="_blank">
                    <img src="./IMG/Publicidad3.jpg" alt="Publicidad 3" class="PublicidadImg" />
                </a>
            </div>
        </article>
    </section>
</body>
<script src="./JS/En_Index/ScriptCarrito.js"></script>
<footer>
    <p>By Jhojan Danilo</p>
</footer>
</html>
