<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso negado - Promoção Modelo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --pm-primary: #6f42c1;
            --pm-gradient: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }

        .error-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .error-card {
            text-align: center;
            max-width: 520px;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            background: var(--pm-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 1rem;
        }

        .error-message {
            color: #6c757d;
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn-pm {
            background: var(--pm-gradient);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.6rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-pm:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(111, 66, 193, 0.4);
            color: #fff;
        }

        .footer-text {
            text-align: center;
            padding: 1.5rem;
            font-size: 0.8rem;
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-code">403</div>
            <h1 class="error-title">Acesso Negado</h1>
            <p class="error-message">
                Você não tem permissão para acessar esta página.
                Verifique suas credenciais ou entre em contato com o administrador.
            </p>
            <a href="/" class="btn-pm">
                <i class="bi bi-house-door"></i>Voltar ao Início
            </a>
        </div>
    </div>

    <div class="footer-text">
        <i class="bi bi-star-fill me-1"></i><strong>Promoção Modelo</strong>
        &mdash; &copy; {{ date('Y') }}
    </div>
</body>
</html>
