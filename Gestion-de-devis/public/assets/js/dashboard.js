(function () {
    if (!window.dashboardChartData || !document.getElementById('chartDevis')) {
        return;
    }

    new Chart(document.getElementById('chartDevis'), {
        type: 'bar',
        data: {
            labels: window.dashboardChartData.labels,
            datasets: [{
                label: 'Devis',
                data: window.dashboardChartData.counts,
                backgroundColor: 'rgba(79,70,229,0.15)',
                borderColor: '#4f46e5',
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.parsed.y + ' devis'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#9ca3af', font: { size: 11 } },
                    grid: { color: '#f3f4f6' },
                    border: { display: false }
                },
                x: {
                    ticks: { color: '#9ca3af', font: { size: 11 } },
                    grid: { display: false },
                    border: { display: false }
                }
            }
        }
    });
})();
