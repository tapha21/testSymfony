fetch('/menu')
.then(response => response.text())
.then(data => {
    document.getElementById('menu-container').innerHTML = data;
})
    document.addEventListener('DOMContentLoaded', async () => {
        const selectedArticles = [];
        const selectedArticlesTable = document.getElementById('selectedArticles');

        const updateTable = () => {
            selectedArticlesTable.innerHTML = selectedArticles.map(article => `
                <tr>
                    <td class="border px-4 py-2">${article.name}</td>
                    <td class="border px-4 py-2 text-center">
                        <button type="button" class="bg-red-500 text-white px-2 py-1 rounded" data-id="${article.id}">Retirer</button>
                    </td>
                </tr>
            `).join('');
        };

            // Charger les clients
            const clients = await fetch('/api/client').then(res => res.json());
            const clientSelect = document.getElementById('client');
            clients.forEach(client => {
                const option = document.createElement('option');
                option.value = client.id;
                option.textContent = client.name;
                clientSelect.appendChild(option);
            });

            // Charger les articles
            const articles = await fetch('/api/articles').then(res => res.json());
            const articleSelect = document.getElementById('articles');
            articles.forEach(article => {
                const option = document.createElement('option');
                option.value = article.id;
                option.textContent = article.name;
                articleSelect.appendChild(option);
            });

            // Ajouter un article au tableau
            document.getElementById('addArticle').addEventListener('click', () => {
                const articleId = articleSelect.value;
                const articleName = articleSelect.options[articleSelect.selectedIndex].text;

                if (articleId && !selectedArticles.find(a => a.id === articleId)) {
                    selectedArticles.push({ id: articleId, name: articleName });
                    updateTable();
                }
            });

            // Retirer un article
            selectedArticlesTable.addEventListener('click', (event) => {
                if (event.target.tagName === 'BUTTON') {
                    const articleId = event.target.getAttribute('data-id');
                    const index = selectedArticles.findIndex(article => article.id === articleId);
                    if (index !== -1) {
                        selectedArticles.splice(index, 1);
                        updateTable();
                    }
                }
            });

            // Soumettre le formulaire
            document.getElementById('debtForm').addEventListener('submit', async (event) => {
                event.preventDefault();
                const debtData = {
                    montant: document.getElementById('montant').value,
                    montantVerser: document.getElementById('montantVerser').value,
                    client: document.getElementById('client').value,
                    articles: selectedArticles.map(a => a.id),
                };
                const response = await fetch('http://127.0.0.1:8000/dette/store', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(debtData),
                });
                alert((await response.json()).message || 'Dette créée avec succès !');
            });
        } 
    );