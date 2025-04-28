function fetchExpiringContracts() {
    fetch('/contrat/api/contrats-expirant-bientot')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur lors de la récupération des contrats');
            }
            return response.json();
        })
        .then(data => {
            console.log('Contrats expirants:', data);
            // Mettre à jour ton affichage de notifications ici
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des contrats expirants:', error);
        });
}

// Récupération et affichage des notifications de contrats expirants bientôt
fetch('/notifications')
    .then(response => response.json())
    .then(data => {
        if (data.length > 0) {
            const container = document.getElementById('notification-container');
            const list = document.getElementById('notification-list');

            container.classList.remove('d-none'); // Afficher le conteneur
            list.innerHTML = ''; // Vider l'ancien contenu

            data.forEach(item => {
                const li = document.createElement('li');
                li.textContent = `Contrat de ${item.nomprenom} expirera le ${item.dateFin}`;
                list.appendChild(li);
            });
        }
    })
    .catch(error => {
        console.error("Erreur lors du fetch :", error);
    });


// Appeler les fonctions au chargement de la page
fetchExpiringContracts();
fetchNotifications();
