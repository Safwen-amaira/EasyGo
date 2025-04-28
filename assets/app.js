import './styles/app.css';
import './bootstrap.js';
import Chart from 'chart.js/auto';

// Si tu veux ajouter du JS, commence ici :
console.log('🧠 Hello from app.js');

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('myChart');
    if (canvas) {
        fetch('/api/stats')
            .then(response => response.json())
            .then(data => {
                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Récompenses',
                            data: data.data,
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            });
    }
});
