// ChartWidget.js
// Chart.js widget with loading and error states
import Chart from 'chart.js/auto';
import axios from 'axios';

export default class ChartWidget {
    constructor({ target, apiUrl, type = 'bar', options = {}, dataTransform }) {
        this.target = target;
        this.apiUrl = apiUrl;
        this.type = type;
        this.options = options;
        this.dataTransform = dataTransform;
        this.chart = null;
    }

    async render() {
        const ctx = document.createElement('canvas');
        ctx.height = 200;
        const container = document.querySelector(this.target);
        container.innerHTML = '<div class="text-center p-3"><span class="spinner-border"></span> Loading...</div>';
        container.appendChild(ctx);
        try {
            const response = await axios.get(this.apiUrl);
            let chartData = response.data;
            if (this.dataTransform) chartData = this.dataTransform(chartData);
            this.chart = new Chart(ctx, {
                type: this.type,
                data: chartData,
                options: this.options
            });
            container.querySelector('.spinner-border').parentNode.remove();
        } catch (e) {
            container.innerHTML = `<div class='alert alert-danger'>Failed to load chart data</div>`;
        }
    }
}
