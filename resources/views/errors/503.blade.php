<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60">
    <title>Manutenção - Promoção Modelo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --pm-primary: #6f42c1;
            --pm-primary-dark: #5a32a3;
            --pm-secondary: #fd7e14;
            --pm-gradient: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #0f0c29;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            color: #fff;
            overflow: hidden;
        }

        .maintenance-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        .maintenance-card {
            text-align: center;
            max-width: 600px;
            width: 100%;
            position: relative;
            z-index: 2;
        }

        .brand {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .brand i { color: var(--pm-secondary); }

        .gear-icon {
            font-size: 5rem;
            display: inline-block;
            animation: spin 4s linear infinite;
            background: var(--pm-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .maintenance-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: var(--pm-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .maintenance-message {
            font-size: 1.15rem;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .status-box {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.5rem 0;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .status-item i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        .status-item .bi-check-circle-fill { color: #20c997; }
        .status-item .bi-arrow-repeat { color: var(--pm-secondary); animation: spin 2s linear infinite; }
        .status-item .bi-clock-fill { color: #6ea8fe; }

        .progress-bar-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            height: 6px;
            overflow: hidden;
            margin-top: 1.5rem;
        }

        .progress-bar-animated {
            height: 100%;
            border-radius: 50px;
            background: var(--pm-gradient);
            animation: progress 2s ease-in-out infinite;
            width: 40%;
        }

        @keyframes progress {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(350%); }
        }

        .countdown-text {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 1.5rem;
        }

        .countdown-text span {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
        }

        /* Floating particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(111, 66, 193, 0.3);
            border-radius: 50%;
            animation: float linear infinite;
        }

        .particle:nth-child(1) { left: 10%; animation-duration: 12s; animation-delay: 0s; width: 6px; height: 6px; }
        .particle:nth-child(2) { left: 25%; animation-duration: 18s; animation-delay: 2s; }
        .particle:nth-child(3) { left: 40%; animation-duration: 15s; animation-delay: 4s; width: 5px; height: 5px; }
        .particle:nth-child(4) { left: 55%; animation-duration: 20s; animation-delay: 1s; }
        .particle:nth-child(5) { left: 70%; animation-duration: 14s; animation-delay: 3s; width: 7px; height: 7px; }
        .particle:nth-child(6) { left: 85%; animation-duration: 16s; animation-delay: 5s; }
        .particle:nth-child(7) { left: 5%;  animation-duration: 22s; animation-delay: 6s; width: 3px; height: 3px; }
        .particle:nth-child(8) { left: 95%; animation-duration: 13s; animation-delay: 2s; width: 5px; height: 5px; }

        @keyframes float {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1.2); opacity: 0; }
        }

        .footer-text {
            text-align: center;
            padding: 1.5rem;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.35);
            position: relative;
            z-index: 2;
        }

        @media (max-width: 576px) {
            .maintenance-title { font-size: 1.6rem; }
            .maintenance-message { font-size: 1rem; }
            .gear-icon { font-size: 3.5rem; }
            .status-box { padding: 1rem 1.2rem; }
        }
    </style>
</head>
<body>
    <!-- Partículas decorativas -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="maintenance-container">
        <div class="maintenance-card">
            <div class="brand">
                <i class="bi bi-star-fill me-2"></i>Promoção Modelo
            </div>

            <div class="gear-icon">
                <i class="bi bi-gear-fill"></i>
            </div>

            <h1 class="maintenance-title">Estamos em Manutenção</h1>

            <p class="maintenance-message">
                Nosso sistema está passando por uma atualização programada
                para melhorar sua experiência. Voltaremos em breve!
            </p>

            <div class="status-box">
                <div class="status-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Seus dados e números da sorte estão seguros</span>
                </div>
                <div class="status-item">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Atualização em andamento...</span>
                </div>
                <div class="status-item">
                    <i class="bi bi-clock-fill"></i>
                    <span>A página será recarregada automaticamente</span>
                </div>

                <div class="progress-bar-container">
                    <div class="progress-bar-animated"></div>
                </div>
            </div>

            <p class="countdown-text">
                Esta página será atualizada em <span id="countdown">60</span> segundos.
                <br>
                Em caso de dúvidas: <strong>contato@promocaomodelo.com.br</strong>
            </p>
        </div>
    </div>

    <div class="footer-text">
        &copy; {{ date('Y') }} Promoção Modelo. Todos os direitos reservados.
    </div>

    <script>
        let seconds = 60;
        const el = document.getElementById('countdown');
        setInterval(() => {
            seconds--;
            if (el) el.textContent = seconds;
            if (seconds <= 0) location.reload();
        }, 1000);
    </script>
</body>
</html>
