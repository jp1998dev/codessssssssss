<div class="mb-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Senior High Collection per Strand</h6>
        </div>
        <div class="card-body">
            <div class="chart-container" style="position: relative; height: 450px;">
                <canvas id="shsPaidChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("/api/shs-full-collections")
            .then(response => response.json())
            .then(result => {
                const data = result.collections;

                const labels = data.map(item => item.program);
                const amounts = data.map(item => item.total_amount);

                const ctx = document.getElementById("shsPaidChart").getContext("2d");

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Collection (₱)',
                            data: amounts,
                            backgroundColor: 'rgba(99, 141, 255, 0.7)',
                            borderColor: 'rgba(99, 141, 255, 0.7)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Amount (₱)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Strand'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return '₱' + context.formattedValue;
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(err => {
                console.error("Failed to load SHS data:", err);
            });
    });
</script>