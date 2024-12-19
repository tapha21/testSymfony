document.addEventListener('DOMContentLoaded', function () {
    // Affiche un message au chargement de la page
    console.log("Chargement du DOM terminé.");

    // Chargement du menu depuis l'API
    fetch('/menu')
        .then(response => response.text())
        .then(data => {
            document.getElementById('menu-container').innerHTML = data;
        })
        .catch(error => console.error("Erreur lors du chargement du menu :", error));

        const form = document.getElementById('articleForm');

        if (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Empêche le comportement par défaut du formulaire
    
                const formData = new FormData(this);
    
                // Préparer les données à envoyer
                const data = {
                    libelle: formData.get('libelle'),
                    reference: formData.get('reference'),
                    prix: parseFloat(formData.get('prix')),
                    quantite: parseInt(formData.get('quantite'), 10)
                };
    
                console.log("Données envoyées :", data);
    
                // Envoyer la requête POST avec les données en JSON
                fetch('/article/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        return response.json().then(errorData => { throw new Error(errorData.message) });
                    }
                })
                .then(data => {
                    console.log("Réponse du serveur :", data);
                    document.getElementById('message').textContent = data.message;
                    document.getElementById('message').classList.add('text-green-500');
                })
                .catch(error => {
                    console.error("Erreur lors de la création de l'article :", error);
                    document.getElementById('message').textContent = error.message;
                    document.getElementById('message').classList.add('text-red-500');
                });
                
            });
        } else {
            console.error("Le formulaire 'articleForm' n'existe pas.");
        }
});
