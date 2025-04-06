document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('wheelCanvas');
    const ctx = canvas.getContext('2d');
    const spinButton = document.getElementById('spinButton');
    const userIdField = document.getElementById('userIdField');

    let segments = [];
    const colors = ['#ff7675', '#74b9ff', '#55efc4', '#ffeaa7'];

    // 📦 Récupérer les récompenses dès le chargement
    fetch('/recompense/list-json')
        .then(response => response.json())
        .then(data => {
            segments = data.rewards;
            if (segments.length === 0) {
                alert("❌ Aucune récompense disponible.");
                spinButton.disabled = true;
            } else {
                drawWheel();
            }
        })
        .catch(err => {
            alert("❌ Erreur lors du chargement des récompenses.");
            console.error(err);
        });

    function drawWheel() {
        const radius = canvas.width / 2;
        const angle = 2 * Math.PI / segments.length;

        for (let i = 0; i < segments.length; i++) {
            // Dessiner le segment
            ctx.beginPath();
            ctx.moveTo(radius, radius);
            ctx.fillStyle = colors[i % colors.length];
            ctx.arc(radius, radius, radius, i * angle, (i + 1) * angle);
            ctx.fill();

            // Écrire le texte dans le segment
            ctx.save();
            ctx.translate(radius, radius);
            ctx.rotate(i * angle + angle / 2);
            ctx.fillStyle = '#2d3436';
            ctx.font = '14px Arial';
            ctx.fillText(segments[i], radius / 2.5, 0);
            ctx.restore();
        }
    }

    spinButton.addEventListener('click', () => {
        const userId = userIdField.value.trim();
        if (!userId) {
            alert("⛔ Veuillez entrer un ID utilisateur.");
            return;
        }

        spinButton.disabled = true;

        // Lancer la roue (3 tours + angle aléatoire)
        const rotation = 1080 + Math.floor(Math.random() * 360);
        canvas.style.transition = 'transform 3s ease-out';
        canvas.style.transform = `rotate(${rotation}deg)`;

        // Après l'animation, appeler l'API
        setTimeout(() => {
            fetch('/recompense/spin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ userId: userId })
            })
            .then(response => response.json())
            .then(data => {
                spinButton.disabled = false;
                if (data.reward) {
                    alert("🎁 Vous avez gagné : " + data.reward);
                } else {
                    alert("❌ Erreur : " + (data.error || "Récompense non attribuée."));
                }
            })
            .catch(err => {
                spinButton.disabled = false;
                alert("❌ Erreur réseau : " + err.message);
                console.error(err);
            });
        }, 3000);
    });
});
