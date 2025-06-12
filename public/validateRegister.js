document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('register-form');
    const message = document.getElementById('message');

    // Si il n'y a pas de formulaire
    if (!form) return;

    // système de validation
    const validate = (data) => {
        // instancie une promesse 
        return new Promise((resolve, reject) => {
            // si le nom d'utilisateur est plus grand que 20
            if (data.username.length > 20) {
                return reject("Le pseudo est trop long (max 20 caractères)");
            }

            // si le mot de passe ne comporte pas de caractère spéciaux
            const specialCharRegex = /[!#$%^&*(),.?":{}|<>]/;
            if (!specialCharRegex.test(data.password)) {
                return reject("Doit contenir des caractères spéciaux");
            }

            // si le mot de passe fais moins de 6 caractères
            if (data.password.length < 6) {
                return reject("Le mot de passe doit faire 6 caractères minimum")
            }

            // si aucune non-validation est déclenchée alors on retourne une réponse positive
            resolve(data);
        });
    };

    // système d'envoi de données
    const sendData = (data) => {
        return fetch('process_register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams(data)
        }).then(res => res.text());
    };

    form.addEventListener('submit', function (e) {
        // prévenir du double click
        e.preventDefault();

        // on remplit la data avec les clés du formulaire pour la validation
        const data = {
            username: form.username.value.trim(),
            email: form.email.value.trim(),
            password: form.password.value.trim()
        };

        // vide le conteneur message d'info
        message.textContent = "";
        message.className = "";

        // on vérifie la data
        validate(data)
            // si la promesse nous reviens en positif (resolve)
            .then(validData =>
                // envoie la data à la page concernée
                sendData(validData))

            // envoie la réponse à la page actuelle
            .then(response => {
                message.textContent = response;
                message.className = "success";
            })

            // affiche l'erreur technique
            .catch(err => {
                message.textContent = err;
                message.className = "error";
            });
    });
});