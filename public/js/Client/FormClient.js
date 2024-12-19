fetch('/menu')
.then(response => response.text())
.then(data => {
    document.getElementById('menu-container').innerHTML = data;
})
    document.addEventListener('DOMContentLoaded', function () {
        // Cacher ou afficher le formulaire utilisateur en fonction de l'état du checkbox
        document.getElementById('toggleUser').addEventListener('change', function() {
            const userForm = document.getElementById('userFormContainer');
            userForm.style.display = this.checked ? 'block' : 'none';
        });

        // Soumettre le formulaire
        document.getElementById('clientForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Empêcher le rechargement de la page

            const formData = new FormData(this);
            const data = {
                client: {
                    prenom: formData.get('prenom'),
                    nom: formData.get('nom'),
                    email: formData.get('email'),
                    telephone: formData.get('telephone'),
                    adresse: formData.get('adresse')
                },
                user: null
            };
            console.log(data)

            // Si l'utilisateur est créé, ajouter les données de l'utilisateur
            if (document.getElementById('toggleUser').checked) {
                data.user = {
                    nom: formData.get('userNom'),
                    prenom: formData.get('userPrenom'),
                    login: formData.get('login'),
                    password: formData.get('password')
                };
            }

            // Appel API pour créer le client
            fetch('http://127.0.0.1:8000/client/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message); // Afficher le message de succès ou d'erreur
            })
            .catch(error => {
                alert("Erreur lors de la création du client");
            });
        });
    });