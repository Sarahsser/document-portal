// 1. Navigation fluide entre les pages
function navigateTo(targetPage) {
    const mainContainer = document.querySelector('.main-container');
    if (mainContainer) {
        mainContainer.style.transition = 'transform 0.8s ease-in-out';
        mainContainer.style.transform = 'translateY(-100vh)';
    }

    setTimeout(() => {
        window.location.href = targetPage;
    }, 800);
}

// 2. Afficher/masquer le mot de passe
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-password');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.textContent = 'visibility_off'; // Icône Material Design
    } else {
        passwordInput.type = 'password';
        toggleIcon.textContent = 'visibility';
    }
}

// 3. Activation/désactivation du mode sombre
const darkModeToggle = document.getElementById("dark-mode-toggle");

if (darkModeToggle) {
    darkModeToggle.addEventListener("click", () => {
        document.body.classList.toggle("dark-mode");
    });
}

// 4. Gestion des Onglets (Tabs)
document.addEventListener("DOMContentLoaded", () => {
    const tabButtons = document.querySelectorAll(".tab-btn");
    const tabContents = document.querySelectorAll(".tab-content");

    if (tabButtons.length > 0) {
        tabButtons.forEach(button => {
            button.addEventListener("click", () => {
                // Supprimer la classe « active » de tous les boutons et contenus
                tabButtons.forEach(btn => btn.classList.remove("active"));
                tabContents.forEach(content => content.classList.remove("active"));

                // Ajouter la classe « active » au bouton cliqué et au contenu correspondant
                button.classList.add("active");
                const targetTab = button.getAttribute("data-tab");
                const targetElement = document.getElementById(targetTab);
                
                if (targetElement) {
                    targetElement.classList.add("active");
                }
            });
        });

        // Définir le premier onglet actif par défaut si aucun n'est actif
        if (!document.querySelector(".tab-btn.active")) {
            tabButtons[0].classList.add("active");
            tabContents[0].classList.add("active");
        }
    }
});