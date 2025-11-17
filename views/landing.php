<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luminous Spa - Relajación y Bienestar</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Iconos Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="/build/img/favicon.png" type="image/png">

    <style>
        /* Variables de diseño */
        :root {
            --primary-color: #ff7f00;
            --secondary-color: #ffa07a;
            --accent-color: #ff7f00;
            --dark-color: #2c2c2c;
            --light-color: #fff8f0;
            --hover-color: #ff6347;
            --black: rgb(0, 0, 0);
        }

        /* Estilos generales */
        body {
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
            color: black;
            background-color: rgb(255, 255, 255);
            padding-top: 90px; /* Espacio para navbar fijo */
        }

        /* Estilo para botón naranja */
        .boton-naranja {
            background-color: var(--primary-color);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }

        .boton-naranja:hover {
            background-color: var(--hover-color);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .section-padding {
            padding: 100px 0;
        }

        /* Header/Navbar MEJORADO */
        .navbar {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            padding: 0.8rem 1rem;
            transition: all 0.4s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        /* NUEVA estructura del navbar - sin posicionamiento absoluto del logo */
        .navbar-brand {
            margin-right: 2rem;
        }

        .logo {
            background-image: url('/build/img/luminous_negro.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            width: 200px;
            height: 60px;
            transition: all 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 0 8px rgba(255, 127, 0, 0.3));
        }

        /* Navegación */
        .nav-link {
            position: relative;
            font-weight: 500;
            color: #000 !important;
            padding: 0.5rem 1.2rem !important;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.95rem;
            background-color: transparent !important;
        }

        .nav-link:hover {
            color: var(--accent-color) !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: "";
            position: absolute;
            left: 1.2rem;
            bottom: 0;
            width: calc(100% - 2.4rem);
            height: 2px;
            background-color: var(--accent-color);
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.3s ease, opacity 0.3s ease;
            opacity: 0;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            transform: scaleX(1);
            opacity: 1;
        }

        /* Botón Login */
        .btn-login {
            border-radius: 50px;
            padding: 0.7rem 1rem !important;
            transition: all 0.3s ease;
            background-color: transparent;
            color: var(--black) !important;
            font-size: 1.3rem;
        }

        .btn-login:hover {
            background-color: var(--accent-color);
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Sección Inicio/Hero */
        .inicio {
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .hero-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('/build/img/Imagen_inicio.png');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            z-index: 1;
        }

        .hero-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: 2;
        }

        .hero-text {
            position: absolute;
            top: 50%;
            right: 10%;
            transform: translateY(-50%);
            width: 40%;
            max-width: 600px;
            padding: 0 20px;
            text-align: center;
            color: black;
            z-index: 3;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-text h1 {
            font-family: "Cormorant Garamond", serif;
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .hero-text p {
            font-size: 1.5rem;
            line-height: 1.4;
            text-align: center;
        }

        /* Sección Nosotros */
        .about-text h2 span {
            color: var(--primary-color);
            font-weight: 700;
        }

        .about-img img {
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* NUEVO: Carrusel de Servicios */
        .services-carousel {
            padding: 2rem 0;
        }

        .services .card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 15px;
            overflow: hidden;
            height: 100%;
            margin: 0 10px;
        }

        .services .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .services .card-body i {
            color: var(--primary-color) !important;
            margin-bottom: 15px;
        }

        /* Botones del carrusel personalizados */
        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
            color: var(--primary-color);
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: var(--primary-color);
            border-radius: 50%;
            padding: 20px;
        }

        .carousel-indicators [data-bs-target] {
            background-color: var(--primary-color);
        }

        /* Sección Contacto */
        .contact form {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .contact .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px 30px;
        }

        /* Footer */
        footer {
            background-color: var(--dark-color);
            padding: 30px 0;
        }

        /* ESTILOS RESPONSIVE MEJORADOS */
        @media (max-width: 991.98px) {
            body {
                padding-top: 120px;
            }

            .navbar {
                padding: 1rem;
            }

            .logo {
                width: 160px;
                height: 50px;
            }

            .navbar-collapse {
                background-color: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                padding: 1rem;
                margin-top: 0.5rem;
                border-radius: 0.5rem;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .nav-item {
                margin: 0.3rem 0;
                text-align: center;
            }

            .hero-text {
                top: 50%;
                left: 50%;
                right: auto;
                transform: translate(-50%, -50%);
                width: 90%;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .hero-text p {
                font-size: 1.3rem;
            }

            .section-padding {
                padding: 60px 0;
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 140px;
            }

            .hero-text h1 {
                font-size: 2.2rem;
            }

            .hero-text p {
                font-size: 1.2rem;
            }

            .services .card {
                margin: 0 5px;
            }
        }

        #nosotros {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        @media (max-width: 768px) {
            #nosotros {
                min-height: auto;
                padding: 4rem 0;
            }
        }

        /* Chatbot personalizado */
        .chatbot-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .chatbot-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .chatbot-button i {
            color: white;
            font-size: 1.5rem;
        }
    </style>
</head>

<body>
<!-- Navbar mejorado -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <!-- Logo ahora como navbar-brand normal -->
        <a class="navbar-brand" href="/">
            <div class="logo"></div>
        </a>

        <!-- Botón Hamburguesa -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenido del navbar -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="#inicio">INICIO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#nosotros">SOBRE NOSOTROS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#servicios">SERVICIOS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contacto">CONTACTO</a>
                </li>
            </ul>

            <!-- Login -->
            <div class="d-flex">
                <a class="btn btn-login" href="/login">
                    <i class="bi bi-person-circle"></i>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Sección Inicio -->
<section id="inicio" class="inicio">
    <div class="hero-image"></div>
    <div class="hero-text">
        <h1 class="fw-bold mb-3">BIENVENIDOS</h1>
        <h1 class="fw-bold mb-3">AL SPA</h1>
        <p class="mb-4">Relájese y renuévese con nuestros tratamientos</p>
        <div class="mt-4">
            <a href="/login" class="boton-naranja btn btn-lg px-4">Iniciar sesión</a>
        </div>
    </div>
</section>

<!-- Sección Nosotros -->
<section id="nosotros" class="about section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12">
                <div class="about-img">
                    <img src="/build/img/lumin_sin_fondo.png" alt="Sobre Luminous Spa" class="img-fluid rounded">
                </div>
            </div>
            <div class="col-lg-6 col-md-12 ps-lg-5 mt-md-5">
                <div class="about-text">
                    <h2 class="fw-bold mb-4">BIENVENIDOS A <span>Luminous Spa</span></h2>
                    <p class="lead mb-4">Con más de 15 años de experiencia en el sector del bienestar, en Luminous Spa hemos perfeccionado el arte de la relajación y el cuidado personal. Nuestro equipo de terapeutas certificados combina técnicas tradicionales con los últimos avances en cosmetología para ofrecerte una experiencia única.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección Servicios con Carrusel -->
<section id="servicios" class="services section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header text-center pb-5">
                    <h2 class="fw-bold">NUESTROS SERVICIOS</h2>
                    <p class="lead">Descubre nuestros servicios especializados para nuestros clientes.</p>
                </div>
            </div>
        </div>

        <!-- Carrusel de Servicios -->
        <div id="servicesCarousel" class="carousel slide services-carousel" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="1"></button>
            </div>

            <div class="carousel-inner">
                <!-- Slide 1 - Primeros 3 servicios -->
                <div class="carousel-item active">
                    <div class="row g-4">
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card text-center pb-2">
                                <div class="card-body py-4">
                                    <i class="bi bi-flower1" style="font-size: 2.5rem;"></i>
                                    <h3 class="card-title py-2">Aromaterapia</h3>
                                    <p class="card-text">Terapias con aceites esenciales para equilibrar mente y cuerpo.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card text-center pb-2">
                                <div class="card-body py-4">
                                    <i class="bi bi-droplet" style="font-size: 2.5rem;"></i>
                                    <h3 class="card-title py-2">Hidroterapia</h3>
                                    <p class="card-text">Tratamientos acuáticos para aliviar tensiones y mejorar circulación.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card text-center pb-2">
                                <div class="card-body py-4">
                                    <i class="bi bi-hand-index" style="font-size: 2.5rem;"></i>
                                    <h3 class="card-title py-2">Masajes</h3>
                                    <p class="card-text">Desde relajantes hasta terapéuticos, con técnicas especializadas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 - Siguientes 3 servicios -->
                <div class="carousel-item">
                    <div class="row g-4">
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card text-center pb-2">
                                <div class="card-body py-4">
                                    <i class="bi bi-gem" style="font-size: 2.5rem;"></i>
                                    <h3 class="card-title py-2">Tratamientos Faciales</h3>
                                    <p class="card-text">Cuidado especializado para rejuvenecer y nutrir tu piel facial.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card text-center pb-2">
                                <div class="card-body py-4">
                                    <i class="bi bi-heart-pulse" style="font-size: 2.5rem;"></i>
                                    <h3 class="card-title py-2">Reflexología</h3>
                                    <p class="card-text">Técnicas de presión en puntos específicos para el bienestar integral.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card text-center pb-2">
                                <div class="card-body py-4">
                                    <i class="bi bi-sun" style="font-size: 2.5rem;"></i>
                                    <h3 class="card-title py-2">Sauna y Vapor</h3>
                                    <p class="card-text">Experiencias de calor terapéutico para desintoxicar y relajar.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controles del carrusel -->
            <button class="carousel-control-prev" type="button" data-bs-target="#servicesCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#servicesCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
</section>

<!-- Sección Contacto -->
<section id="contacto" class="contact section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header text-center pb-5">
                    <h2 class="fw-bold">CONTÁCTANOS</h2>
                    <p class="lead">Visítanos o comunícate directamente</p>
                </div>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <img src="/build/img/lumin_sin_fondo.png" alt="Logo de la empresa" class="img-fluid mb-4" style="max-height: 150px;">
                        <h3 class="card-title py-2">LUMINOUS SPA</h3>
                        <p class="text-muted">Ven y Relajate con nuestro personal especializado</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-5">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title py-2">¿Dónde estamos ubicados?</h3>
                        <ul class="list-unstyled text-start mx-auto" style="max-width: 300px;">
                            <li class="mb-3 d-flex">
                                <i class="bi bi-geo-alt-fill me-3" style="font-size: 1.2rem; color: #ff7f00;"></i>
                                <span>Dirección: Av. Principal #123, Pereira</span>
                            </li>
                            <li class="mb-3 d-flex">
                                <i class="bi bi-telephone-fill me-3" style="font-size: 1.2rem; color: #ff7f00;"></i>
                                <span>Teléfono: (315) 223-0758</span>
                            </li>
                            <li class="mb-3 d-flex">
                                <i class="bi bi-envelope-fill me-3" style="font-size: 1.2rem; color: #ff7f00;"></i>
                                <span>Email: spaluminous2025@gmail.com</span>
                            </li>
                            <li class="mb-3 d-flex">
                                <i class="bi bi-clock-fill me-3" style="font-size: 1.2rem; color: #ff7f00;"></i>
                                <span>Horario: Lunes a Sábado, 10:00 AM - 6:00 PM</span>
                            </li>
                        </ul>
                        <a href="https://wa.me/3152230758" class="btn btn-success mt-3 px-4">
                            <i class="bi bi-whatsapp me-2"></i> Contactar por WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0">© 2025 Luminous Spa. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Botón del Chatbot mejorado
<div class="chatbot-button" onclick="toggleChat()">
    <i class="bi bi-chat-dots"></i>
</div>
-->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script del chatbot original -->
<script>
    (function() {
        if (!window.chatbase || window.chatbase("getState") !== "initialized") {
            window.chatbase = (...arguments) => {
                if (!window.chatbase.q) {
                    window.chatbase.q = []
                }
                window.chatbase.q.push(arguments)
            };
            window.chatbase = new Proxy(window.chatbase, {
                get(target, prop) {
                    if (prop === "q") {
                        return target.q
                    }
                    return (...args) => target(prop, ...args)
                }
            })
        }

        const onLoad = function() {
            const script = document.createElement("script");
            script.src = "https://www.chatbase.co/embed.min.js";
            script.id = "aq56n2EjcLyTiUysOTwSQ";
            script.domain = "www.chatbase.co";
            document.body.appendChild(script)
        };

        if (document.readyState === "complete") {
            onLoad()
        } else {
            window.addEventListener("load", onLoad)
        }
    })();
</script>


<!-- Scripts para navegación y chatbot -->
<script>
    // Función para el chatbot
    function toggleChat() {
        // Aquí se activará el chatbot cuando el usuario haga clic
        if (window.chatbase) {
            window.chatbase('open');
        }
    }

    // Navegación suave
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                    history.pushState(null, null, this.getAttribute('href'));
                }
            });
        });

        // Manejar recarga y navegación
        if (window.location.hash) {
            setTimeout(() => {
                document.querySelector(window.location.hash)?.scrollIntoView();
            }, 100);
        }

        // Manejar botón atrás/adelante
        window.addEventListener('popstate', function(event) {
            if (window.location.hash) {
                document.querySelector(window.location.hash)?.scrollIntoView({ behavior: 'smooth' });
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });

    // Auto-play del carrusel (opcional)
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = new bootstrap.Carousel(document.querySelector('#servicesCarousel'), {
            interval: 5000,
            wrap: true
        });
    });
</script>
</body>
</html>