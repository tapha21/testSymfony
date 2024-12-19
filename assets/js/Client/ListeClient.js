fetch('/menu')
.then(response => response.text())
.then(data => {
    document.getElementById('menu-container').innerHTML = data;
})
.catch(error => console.error('Erreur lors du chargement du menu :', error));
    document.addEventListener('DOMContentLoaded', function () {
        const clientTableBody = document.getElementById('client-table-body');
        const pagination = document.getElementById('pagination');
        const searchButton = document.getElementById('search-button');
        const newButton = document.getElementById('new');
        const apiUrl = '/api/client';  // Endpoint API pour récupérer les clients
        let currentPage = 1;
        let nomFilter = '';
        let telephoneFilter = '';

        // Fonction pour charger les clients
        async function loadClients(page = 1, nom = '', telephone = '') {
            const url = new URL(apiUrl, window.location.origin);
            url.searchParams.append('page', page);
            if (nom) {
                url.searchParams.append('nom', nom);
            }
            if (telephone) {
                url.searchParams.append('telephone', telephone);
            }

            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Erreur lors du chargement des clients');

                const data = await response.json();
                displayClients(data.clients);
                displayPagination(data.current_page, data.total_pages);
            } catch (error) {
                console.error(error);
                alert('Impossible de charger les clients.');
            }
        }

        // Fonction pour afficher les clients dans le tableau
        function displayClients(clients) {
            clientTableBody.innerHTML = '';
            clients.forEach(client => {
                const row = document.createElement('tr');
                row.className = "hover:bg-gray-100";
                row.innerHTML = `
                    <td class="px-6 py-4">${client.prenom}</td>
                    <td class="px-6 py-4">${client.nom}</td>
                    <td class="px-6 py-4">${client.telephone}</td>
                    <td class="px-6 py-4">${client.adresse}</td>
                    <td class="px-6 py-4 space-x-2">
                        <a href="/client/details/${client.id}" class="px-3 py-1 text-white bg-blue-500 rounded hover:bg-blue-600">Détails</a>
                        <a href="/client/modifier/${client.id}" class="px-3 py-1 text-white bg-yellow-500 rounded hover:bg-yellow-600">Modifier</a>
                        <button class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-600" onclick="deleteClient(${client.id})">Supprimer</button>
                    </td>
                `;
                clientTableBody.appendChild(row);
            });
        }

        // Fonction pour afficher la pagination
        function displayPagination(current, total) {
            pagination.innerHTML = '';

            const maxButtons = 5;
            const startPage = Math.floor((current - 1) / maxButtons) * maxButtons + 1;
            const endPage = Math.min(startPage + maxButtons - 1, total);

            // Bouton Précédent
            if (startPage > 1) {
                const prevButton = document.createElement('button');
                prevButton.textContent = 'Voir moins....';
                prevButton.className = 'px-4 py-2 border rounded bg-white text-gray-700 hover:bg-blue-100';
                prevButton.addEventListener('click', () => {
                    loadClients(startPage - 1, nomFilter, telephoneFilter);
                });
                pagination.appendChild(prevButton);
            }

            // Boutons de page
            for (let i = startPage; i <= endPage; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.className = `px-4 py-2 border rounded ${i === current ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-blue-100'}`;
                button.addEventListener('click', () => {
                    loadClients(i, nomFilter, telephoneFilter);
                });
                pagination.appendChild(button);
            }

            // Bouton Suivant
            if (endPage < total) {
                const nextButton = document.createElement('button');
                nextButton.textContent = 'Voir plus...';
                nextButton.className = 'px-4 py-2 border rounded bg-white text-gray-700 hover:bg-blue-100';
                nextButton.addEventListener('click', () => {
                    loadClients(endPage + 1, nomFilter, telephoneFilter);
                });
                pagination.appendChild(nextButton);
            }
        }

        // Fonction pour gérer le filtrage
        searchButton.addEventListener('click', function () {
            nomFilter = document.getElementById('search-nom').value;
            telephoneFilter = document.getElementById('search-telephone').value;
            loadClients(1, nomFilter, telephoneFilter);
        });
        newButton.addEventListener('click', () => {
            window.location.href = '/client/forms';
        });
        // Charger les clients au départ
        loadClients();
    });

    // Fonction pour supprimer un client
    function deleteClient(clientId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce client ?')) {
            fetch(`/api/clients/${clientId}`, {
                method: 'DELETE',
            }).then(response => {
                if (response.ok) {
                    alert('Client supprimé avec succès.');
                    window.location.reload();  // Recharger la page
                } else {
                    alert('Erreur lors de la suppression du client.');
                }
            });
        }
    }