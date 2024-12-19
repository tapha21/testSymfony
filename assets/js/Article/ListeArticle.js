fetch('/menu')
.then(response => response.text())
.then(data => {
    document.getElementById('menu-container').innerHTML = data;
})
.catch(error => console.error('Erreur lors du chargement du menu :', error));
    document.addEventListener('DOMContentLoaded', function () {
        const articleTableBody = document.getElementById('article-table-body');
        const pagination = document.getElementById('pagination');
        const searchInput = document.getElementById('search-input');
        const searchButton = document.getElementById('search-button');
        const newButton = document.getElementById('new');
        const apiUrl = '/api/articles';

        let currentPage = 1;
        let libelleFilter = '';

        // Charger les articles
        async function loadArticles(page = 1, libelle = '') {
            const url = new URL(apiUrl, window.location.origin);
            url.searchParams.append('page', page);
            if (libelle) {
                url.searchParams.append('libelle', libelle);
            }

            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Erreur lors du chargement des articles');

                const data = await response.json();
                displayArticles(data.articles);
                displayPagination(data.current_page, data.total_pages);
            } catch (error) {
                console.error(error);
                alert('Impossible de charger les articles.');
            }
        }

        // Afficher les articles
        function displayArticles(articles) {
            articleTableBody.innerHTML = '';
            articles.forEach(article => {
                const row = document.createElement('tr');
                row.className = "hover:bg-gray-100";
                row.innerHTML = `
                    <td class="px-6 py-4">${article.id}</td>
                    <td class="px-6 py-4">${article.libelle}</td>
                    <td class="px-6 py-4">${article.reference}</td>
                    <td class="px-6 py-4">${article.prix.toFixed(2)} FCFA</td>
                    <td class="px-6 py-4">${article.quantite}</td>
                     <td class="px-6 py-4 space-x-2">
                        <button 
                            class="px-3 py-1 text-white bg-green-500 rounded hover:bg-green-600" 
                            onclick="editArticle(${article.id})"
                        >Modifier</button>
                        <button 
                            class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-600" 
                            onclick="deleteArticle(${article.id})"
                        >Supprimer</button>
                    </td>
                `;
                articleTableBody.appendChild(row);
            });
        }

        // Afficher la pagination
        function displayPagination(current, total) {
pagination.innerHTML = '';

const maxButtons = 5; // Nombre maximum de boutons visibles
const startPage = Math.floor((current - 1) / maxButtons) * maxButtons + 1;
const endPage = Math.min(startPage + maxButtons - 1, total);

// Bouton "Précédent"
if (startPage > 1) {
    const prevButton = document.createElement('button');
    prevButton.textContent = 'Voir moins...';
    prevButton.className = 'px-4 py-2 border rounded bg-white text-gray-700 hover:bg-blue-100';
    prevButton.addEventListener('click', () => {
        const prevStartPage = startPage - 1;
        loadArticles(prevStartPage, libelleFilter);
    });
    pagination.appendChild(prevButton);
}

// Boutons de page
for (let i = startPage; i <= endPage; i++) {
    const button = document.createElement('button');
    button.textContent = i;
    button.className = `px-4 py-2 border rounded ${i === current ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-blue-100'}`;
    button.addEventListener('click', () => {
        currentPage = i;
        loadArticles(currentPage, libelleFilter);
    });
    pagination.appendChild(button);
}

// Bouton "Suivant"
if (endPage < total) {
    const nextButton = document.createElement('button');
    nextButton.textContent = 'Voir plus...';
    nextButton.className = 'px-4 py-2 border rounded bg-white text-gray-700 hover:bg-blue-100';
    nextButton.addEventListener('click', () => {
        const nextStartPage = endPage + 1;
        loadArticles(nextStartPage, libelleFilter);
    });
    pagination.appendChild(nextButton);
}
}
        // Rechercher les articles
        searchButton.addEventListener('click', () => {
            libelleFilter = searchInput.value;
            currentPage = 1;
            loadArticles(currentPage, libelleFilter);
        });
        newButton.addEventListener('click', () => {
            window.location.href = '/article/create';
        });

        // Chargement initial
        loadArticles(currentPage, libelleFilter);
    });
