<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur de vérification - Notif App</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .error-icon {
            font-size: 64px;
            color: #ef4444;
            margin-bottom: 20px;
        }
        .title {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 10px;
        }
        .button:hover {
            background-color: #1d4ed8;
        }
        .button.secondary {
            background-color: #6b7280;
        }
        .button.secondary:hover {
            background-color: #4b5563;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">❌</div>
        <h1 class="title">Erreur de vérification</h1>
        <p class="message">
            Le lien de vérification est invalide ou a expiré. Cela peut arriver si :
        </p>
        <ul style="text-align: left; max-width: 400px; margin: 0 auto 30px;">
            <li>Le lien a déjà été utilisé</li>
            <li>Le lien a expiré (valide 60 minutes)</li>
            <li>Le lien a été modifié</li>
        </ul>
        
        <div class="warning">
            <strong>💡 Solution :</strong> Connectez-vous à votre compte pour renvoyer un nouvel email de vérification.
        </div>
        
        <div>
            <a href="/login" class="button">Se connecter</a>
            <a href="/" class="button secondary">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>