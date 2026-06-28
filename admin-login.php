<?php
// Démarrer la session
session_start();

// Inclure la connexion à la base de données
require 'db_connect.php';

// Initialiser le message d'erreur
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collecter et nettoyer les entrées
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Requête pour vérifier si l'utilisateur existe et possède le rôle 'admin'
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND role = 'admin'");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier le mot de passe et connecter
    if ($user && password_verify($password, $user['PASSWORD'])) {
        // Définir les variables de session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Rediriger vers le tableau de bord admin
        header("Location: admin-dashboard.php");
        exit();
    } else {
        // Message d'erreur
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('../assets/bg-login.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .login-container {
            backdrop-filter: blur(10px);
            max-width: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card login-container p-4 rounded-4 shadow border-0" style="background: rgba(255, 255, 255, 0.85)">
            <h2 class="text-center mb-4" style="font-family: 'Playfair Display', serif; color: #0f0f0f">Connexion Admin</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger p-2 text-center" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="admin-login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label"><i class="fas fa-user-shield"></i> Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Identifiant admin" required />
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><i class="fas fa-lock"></i> Mot de passe</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Entrez le mot de passe" required />
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="material-icons" id="toggleIcon" style="font-size: 18px; vertical-align: middle;">visibility</i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-dark w-100 py-2">Se connecter</button>
                <p class="text-center mt-3 mb-0">Besoin d'aide ? <a href="javascript:void(0)" onclick="showForgotPasswordModal()" class="fw-bold text-primary text-decoration-none">Cliquez ici</a>.</p>
            </form>
        </div>
    </div>

    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Accès restreint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Pour réinitialiser un compte administrateur, veuillez contacter le service informatique central de l'université.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour afficher/masquer le mot de passe
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        // Fonction pour ouvrir la modale
        function showForgotPasswordModal() {
            const myModal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
            myModal.show();
        }
    </script>
</body>
</html>