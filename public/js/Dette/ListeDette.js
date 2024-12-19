fetch('/menu')
.then(response => response.text())
.then(data => {
    document.getElementById('menu-container').innerHTML = data;
})
.catch(error => console.error('Erreur lors du chargement du menu :', error));
document.addEventListener('DOMContentLoaded', function () {
    const detteTableBody = document.getElementById('dette-table-body');
    const pagination = document.getElementById('pagination');
    const searchButton = document.getElementById('search-button');
    const newButton = document.getElementById('new');
    const apiUrl = '/api/dettes';  // API endpoint
    let currentPage = 1;
    let statutFilter = '';

  

    // Fonction pour charger les dettes
    async function loadDettes(page = 1, statut = '') {
        const url = new URL(apiUrl, window.location.origin);
        url.searchParams.append('page', page);

        if (statut) {
            url.searchParams.append('statut', statut);
        }

        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Erreur lors du chargement des dettes');

            const data = await response.json();
            displayDettes(data.dettes);
            displayPagination(data.current_page, data.total_pages);
        } catch (error) {
            console.error(error);
            alert('Impossible de charger les dettes.');
        }
    }

    // Fonction de filtrage
    async function loadWithFiltre(page = 1) {
        const url = new URL(apiUrl, window.location.origin);
        url.searchParams.append('page', page);

        // Ajout du filtre de statut s'il est sélectionné
        if (statutFilter) {
            url.searchParams.append('statut', statutFilter);
        }

        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Erreur lors du chargement des dettes');

            const data = await response.json();
            displayDettes(data.dettes);
            displayPagination(data.current_page, data.total_pages);
        } catch (error) {
            console.error(error);
            alert('Impossible de charger les dettes.');
        }
    }

    // Fonction pour afficher les dettes
    function displayDettes(dettes) {
        detteTableBody.innerHTML = '';  // Vider la table avant de la remplir
        dettes.forEach(dette => {
            const row = document.createElement('tr');
            row.className = "hover:bg-gray-100";
            row.innerHTML = `
                <td class="px-6 py-4 text-center">${dette.id}</td>
                <td class="px-6 py-4 text-center">${dette.client.nom}</td>
                <td class="px-6 py-4 text-center">${dette.montant}</td>
                <td class="px-6 py-4 text-center">${dette.montant_restant}</td>
                <td class="px-6 py-4 text-center">
                    ${dette.montant - dette.montantVerser <= 0 ? 
                        '<span class="text-green-600">Soldé</span>' :
                        '<span class="text-red-600">Non Soldé</span>'}
                </td>
                <td class="px-6 py-4 text-center">
                    <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Details</button>
                </td>
            `;
            detteTableBody.appendChild(row);
            const button = row.querySelector('button');
            button.addEventListener('click', function() {
                showDetails(dette.id);
            });
        });
    }

    function showDetails(detteId) {
        // Rediriger vers la page des détails de la dette en utilisant l'ID
        window.location.href = `/dette/details`;
    }

    // Fonction pour afficher la pagination
    function displayPagination(current, total) {
        pagination.innerHTML = '';

        const maxButtons = 5; // Nombre maximum de boutons visibles
        const startPage = Math.floor((current - 1) / maxButtons) * maxButtons + 1;
        const endPage = Math.min(startPage + maxButtons - 1, total);

        // Bouton "Précédent"
        if (startPage > 1) {
            const prevButton = document.createElement('button');
            prevButton.textContent = 'Voir moins....';
            prevButton.className = 'px-4 py-2 border rounded bg-white text-gray-700 hover:bg-blue-100';
            prevButton.addEventListener('click', () => {
                loadDettes(startPage - 1, statutFilter);
            });
            pagination.appendChild(prevButton);
        }

        // Boutons de page
        for (let i = startPage; i <= endPage; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.className = `px-4 py-2 border rounded ${i === current ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-blue-100'}`;
            button.addEventListener('click', () => {
                loadDettes(i, statutFilter);
            });
            pagination.appendChild(button);
        }

        // Bouton "Suivant"
        if (endPage < total) {
            const nextButton = document.createElement('button');
            nextButton.textContent = 'Voir plus....';
            nextButton.className = 'px-4 py-2 border rounded bg-white text-gray-700 hover:bg-blue-100';
            nextButton.addEventListener('click', () => {
                loadDettes(endPage + 1, statutFilter);
            });
            pagination.appendChild(nextButton);
        }
    }

    // Événement du bouton de filtrage
    searchButton.addEventListener('click', function () {
        statutFilter = document.getElementById('statut').value;
        loadWithFiltre(1);  // Charger avec le filtre de statut
    });

    newButton.addEventListener('click', function (){
        alert("aaaaa")
        window.location.href = '/dette/create_dette';    
    });

    // Charger les dettes initialement
    loadDettes();
});
