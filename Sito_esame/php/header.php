<header>
    <div class="logo">
        <img src="../img/logo.svg" alt="cyclink">
    </div>
    <div class="menu">
        <div class="hamburger" id="hamburger">
            <img src="../img/hamburger.svg" alt="menu">
        </div>
        <nav id="nav-menu">
            <ul>
                <div class="link" id="link">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="lista.php">Lista</a></li>
                    <li><a href="offerta.php">Offerta</a></li>
                    <li><a href="domanda.php">Domanda</a></li>
                    <li><a href="registrazione.php">Registrati</a></li>
                    <?php
                        if (isset($_SESSION['username'])) {
                        echo '<li><a href="logout.php">Logout</a></li>';
                        } else {
                            echo '<li><a href="login.php">Login</a></li>';
                        }   
                    ?>
                </div>
            </ul>
        </nav>
    </div>
    <div class="informazioni-utente" id='info_utente'>
        <?php
            if (isset($_SESSION['username'])) {
                if($_SESSION['artigiano']===1){
                    echo "<p class='credito'>Utente: " . htmlspecialchars($_SESSION['username']) . " | Saldo: " . number_format($_SESSION['saldo'], 2) . "€</p>";
                } else {
                    echo "<p class='credito'>Utente: " . htmlspecialchars($_SESSION['username']) .  "</p>";   
                }
            } else {
            echo "<p class='credito'>non loggato</p>";
            }
        ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.getElementById('hamburger');
            const navMenu = document.getElementById('nav-menu');
            const info_utente = document.getElementById('info_utente');
            const link = document.getElementById('link');

            // Toggle hamburger menu
            hamburger.addEventListener('click', function() {
                hamburger.classList.toggle('active');
                navMenu.classList.toggle('active');
                info_utente.classList.toggle('active');
                link.classList.toggle('active');
            });
            
            // Chiudi menu quando si clicca su un link (mobile)
            const navLinks = navMenu.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        hamburger.classList.remove('active');
                        navMenu.classList.remove('active');
                        info_utente.classList.remove('active');
                        link.classList.remove('active');
                    }
                });
            });
            
            // Chiudi menu quando la finestra viene ridimensionata
            window.addEventListener('resize', function() {
                if (window.innerWidth > 960) {
                    navMenu.classList.remove('active');
                    hamburger.classList.remove('active');
                    info_utente.classList.remove('active');
                    link.classList.remove('active');
                }
            });
            
            // Chiudi menu quando si clicca fuori (mobile)
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 960) {
                    if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                        hamburger.classList.remove('active');
                        navMenu.classList.remove('active');
                        info_utente.classList.remove('active');
                        link.classList.remove('active');
                    }
                }
            });
        });
    </script>
</header>
