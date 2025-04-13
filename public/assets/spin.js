document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('wheelCanvas');
    const ctx = canvas.getContext('2d');
    const spinButton = document.getElementById('spinButton');
    const userIdField = document.getElementById('userIdField');

    let segments = [];
    const colors = ['#ff7675', '#74b9ff', '#55efc4', '#ffeaa7', '#a29bfe', '#fab1a0'];

    // 🎁 Charger les récompenses au chargement
    fetch('/recompense/list-json')
        .then(response => response.json())
        .then(data => {
            segments = data.rewards;
            if (!segments || segments.length === 0) {
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

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        for (let i = 0; i < segments.length; i++) {
            const startAngle = i * angle;
            const endAngle = startAngle + angle;

            // Segment
            ctx.beginPath();
            ctx.moveTo(radius, radius);
            ctx.fillStyle = colors[i % colors.length];
            ctx.arc(radius, radius, radius, startAngle, endAngle);
            ctx.fill();

            // Texte
            ctx.save();
            ctx.translate(radius, radius);
            ctx.rotate(startAngle + angle / 2);
            ctx.fillStyle = '#2d3436';
            ctx.font = '14px Arial';
            ctx.textAlign = 'right';
            ctx.fillText(segments[i], radius - 10, 0);
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

        // 🎡 Rotation visuelle
        const rotation = 1080 + Math.floor(Math.random() * 360);
        canvas.style.transition = 'transform 3s ease-out';
        canvas.style.transform = `rotate(${rotation}deg)`;

        // Après animation, appel API pour attribuer récompense
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
                console.log("✅ Réponse du serveur :", data); // Ajoute ceci
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
