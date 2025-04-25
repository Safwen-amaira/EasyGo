let wheel;
let spinning = false;
let segments = [];

// Audio elements
const rewardSound = new Audio('/sounds/reward-sound.mp3');

// Fetch rewards and init wheel
document.addEventListener("DOMContentLoaded", function () {
    fetch('/recompense/spin/rewards')
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const contentType = res.headers.get("Content-Type");
            if (!contentType.includes("application/json")) throw new Error("Invalid content type");
            return res.json();
        })
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                alert("⚠️ Aucune récompense disponible.");
                return;
            }

            segments = data;
            wheel = new Winwheel({
                canvasId: 'spinWheel',
                numSegments: segments.length,
                outerRadius: 190,
                textFontSize: 14,
                segments: segments.map((label, i) => ({
                    fillStyle: getRandomColor(i),
                    text: label
                })),
                animation: {
                    type: 'spinToStop',
                    duration: 5,
                    spins: 8,
                    callbackSound: playTick,
                    soundTrigger: 'segment',
                    callbackFinished: rewardSelected
                }
            });

            drawPointer();
        })
        .catch(err => {
            alert("❌ Erreur lors du chargement des récompenses.");
            console.error("[spin.js] Fetch error:", err);
        });
});

// Color generator
function getRandomColor(i) {
    const hue = (i * 360 / segments.length) % 360;
    return `hsl(${hue}, 70%, 70%)`;
}

// Play tick sound
function playTick() {
    if (typeof spinSound !== "undefined") {
        spinSound.currentTime = 0;
        spinSound.play().catch(() => {});
    }
}

// Reward selected
function rewardSelected(indicatedSegment) {
    spinning = false;

    let selectedLabel = indicatedSegment?.text || null;

    if (!selectedLabel && wheel) {
        const angle = wheel.rotationAngle % 360;
        const index = Math.floor((360 - angle) / (360 / segments.length)) % segments.length;
        const fallbackSegment = wheel.segments[index];
        if (fallbackSegment?.text) {
            selectedLabel = fallbackSegment.text;
        }
    }

    if (!selectedLabel) {
        console.error("❌ Could not determine selected segment.");
        alert("Impossible de déterminer la récompense sélectionnée.");
        document.getElementById("spinButton").disabled = false;
        return;
    }

    // Highlight the selected one
    for (let i = 1; i < wheel.segments.length; i++) {
        if (wheel.segments[i].text === selectedLabel) {
            wheel.segments[i].fillStyle = "rgba(255, 0, 0, 0.7)";
            break;
        }
    }
    wheel.draw();
    drawPointer();

    // Reward logic
    rewardSound.play().catch(() => {});
    fetch('/recompense/spin', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reward: selectedLabel })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert("⚠️ " + data.error);
        } else {
            // 🎉 Confetti
            confetti({
                particleCount: 150,
                spread: 80,
                origin: { y: 0.6 },
            });

            alert("🎉 Récompense attribuée : " + data.recompense);
            window.location.href = "/recompense/mon-profil";
        }
    })
    .catch(err => {
        alert("❌ Une erreur est survenue.");
        console.error("[POST error]", err);
    });

    document.getElementById("spinButton").disabled = false;
}

// Draw the red pointer
function drawPointer() {
    const canvas = document.getElementById('spinWheel');
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = 'red';
    ctx.beginPath();
    ctx.moveTo(200, 0);
    ctx.lineTo(190, 20);
    ctx.lineTo(210, 20);
    ctx.fill();
}

// Cooldown system: 1 min
window.spin = function () {
    const lastSpin = localStorage.getItem("lastSpinTime");
    const now = Date.now();
    const cooldown = 60 * 1000; // 1 minute

    if (lastSpin && now - parseInt(lastSpin) < cooldown) {
        const remaining = Math.ceil((cooldown - (now - parseInt(lastSpin))) / 1000);
        alert(`⏳ Attendez encore ${remaining}s avant de tourner à nouveau.`);
        return;
    }

    if (spinning || !wheel) return;

    localStorage.setItem("lastSpinTime", now.toString());

    spinning = true;
    document.getElementById("spinButton").disabled = true;
    wheel.stopAnimation(false);
    wheel.rotationAngle = 0;
    wheel.draw();
    drawPointer();
    wheel.startAnimation();
};

// Optional: UI countdown
setInterval(() => {
    const el = document.getElementById("cooldownTimer");
    const lastSpin = localStorage.getItem("lastSpinTime");
    if (!el || !lastSpin) return;

    const now = Date.now();
    const diff = now - parseInt(lastSpin);
    const cooldown = 60 * 1000;
    const remaining = cooldown - diff;

    if (remaining > 0) {
        el.innerText = `⏳ Prochaine tentative dans ${Math.ceil(remaining / 1000)}s`;
        document.getElementById("spinButton").disabled = true;
    } else {
        el.innerText = "";
        document.getElementById("spinButton").disabled = false;
    }
}, 1000);
