fetch('/menu')
.then(response => response.text())
.then(data => {
    document.getElementById('menu-container').innerHTML = data;
})
.catch(error => console.error('Erreur lors du chargement du menu :', error));

document.addEventListener('DOMContentLoaded', () => {
    const userTableBody = document.getElementById('user-table-body');
    const paginationContainer = document.getElementById('pagination');
    const filterNom = document.getElementById('filter-nom');
    const searchButton = document.getElementById('search-button');
    const newButton = document.getElementById('new');
    const apiUrl = '/api/users';  // API endpoint
    let currentPage = 1;
    let statutFilter = '';

    // Fonction pour charger les utilisateurs
    const loadUsers = async (page = 1, nom = '') => {
        const url = new URL(apiUrl, window.location.origin);
        url.searchParams.append('page', page);
        url.searchParams.append('nom', nom);

        try {
            userTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Chargement...</td></tr>';
            const response = await fetch(url);
            const data = await response.json();

            // Mise à jour du tableau
            userTableBody.innerHTML = data.users.map((user, index) => ` 
                <tr class="hover:bg-gray-100">
                    <td class="px-6 py-4 text-center">${(page - 1) * 6 + index + 1}</td>
                    <td class="px-6 py-4 text-center">${user.nom}</td>
                    <td class="px-6 py-4 text-center">${user.prenom}</td>
                    <td class="px-6 py-4 text-center">${user.email}</td>
                    <td class="px-6 py-4 text-center">${user.roles.join(', ')}</td>
                </tr>
            `).join('');

            // Mise à jour de la pagination
            paginationContainer.innerHTML = '';
            const totalPages = data.total_pages;
            const maxButtons = 3; // Nombre maximum de boutons visibles
            const startPage = Math.floor((page - 1) / maxButtons) * maxButtons + 1;
            const endPage = Math.min(startPage + maxButtons - 1, totalPages);

            // Bouton "Précédent"
            if (startPage > 1) {
                const prevButton = document.createElement('button');
                prevButton.textContent = 'Voir moins...';
                prevButton.className = 'px-4 py-2 border rounded bg-white text-gray-700 hover:bg-blue-100';
                prevButton.addEventListener('click', () => {
                    loadUsers(startPage - 1, statutFilter);
                });
                paginationContainer.appendChild(prevButton);
            }

            // Boutons de page
            for (let i = startPage; i <= endPage; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.className = `px-4 py-2 border rounded ${i === page ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-blue-100'}`;
                button.addEventListener('click', () => {
                    loadUsers(i, nom);
                });
                paginationContainer.appendChild(button);
            }

            // Bouton "Suivant"
            if (endPage < totalPages) {
                const nextButton = document.createElement('button');
                nextButton.textContent = 'Voir plus...';
                nextButton.className = 'px-4 py-2 border rounded bg-white text-gray-700 hover:bg-blue-100';
                nextButton.addEventListener('click', () => {
                    loadUsers(endPage + 1, statutFilter);
                });
                paginationContainer.appendChild(nextButton);
            }

            currentPage = page;
        } catch (error) {
            console.error('Erreur lors du chargement des utilisateurs :', error);
            userTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Erreur lors du chargement des utilisateurs.</td></tr>';
        }
    };

    newButton.addEventListener('click', () => {
            window.location.href = '/dette/create';
        });
    // Chargement initial des utilisateurs
    loadUsers();

    // Recherche
    searchButton.addEventListener('click', () => {
        const nom = filterNom.value;
        loadUsers(1, nom);
    });
});