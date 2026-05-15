import { Controller } from '@hotwired/stimulus';
import { Chart, LineController, LineElement, PointElement, LinearScale, CategoryScale, Title, Tooltip, Legend } from 'chart.js';

export default class extends Controller {
    static targets = ['revenue', 'orders', 'form'];

    connect() {
        Chart.register(LineController, LineElement, PointElement, LinearScale, CategoryScale, Title, Tooltip, Legend);
        this.initCharts();
        this.formTarget.addEventListener('submit', this.handleSubmit);
    }

    disconnect() {
        if (this.revChart) this.revChart.destroy();
        if (this.ordChart) this.ordChart.destroy();
        this.formTarget.removeEventListener('submit', this.handleSubmit);
    }

    initCharts() {
        const existingRev = Chart.getChart(this.revenueTarget);
        if (existingRev) existingRev.destroy();
        const existingOrd = Chart.getChart(this.ordersTarget);
        if (existingOrd) existingOrd.destroy();

        this.revChart = new Chart(this.revenueTarget, {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Revenus (€)', data: [], borderColor: '#FF69B4', backgroundColor: 'rgba(255,105,180,0.2)', fill: true, tension: 0.3 }] },
            options: { responsive: true, plugins: { title: { display: true, text: 'Revenus' } } }
        });

        this.ordChart = new Chart(this.ordersTarget, {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Commandes', data: [], borderColor: '#000', backgroundColor: 'rgba(0,0,0,0.2)', fill: true, tension: 0.3 }] },
            options: { responsive: true, plugins: { title: { display: true, text: 'Commandes' } } }
        });

        this.loadData();
    }

    async loadData() {
        const params = new URLSearchParams(new FormData(this.formTarget));
        const res = await fetch('/admin/dashboard/data?' + params.toString());
        const data = await res.json();
        this.revChart.data.labels = data.labels;
        this.revChart.data.datasets[0].data = data.revenue;
        this.revChart.update();
        this.ordChart.data.labels = data.labels;
        this.ordChart.data.datasets[0].data = data.orders;
        this.ordChart.update();
    }

    handleSubmit = (e) => {
        e.preventDefault();
        this.loadData();
    };
}
