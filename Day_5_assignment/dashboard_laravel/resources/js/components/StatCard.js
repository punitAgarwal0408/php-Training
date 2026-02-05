// StatCard.js
// Displays a metric card with title, value, and optional icon
import $ from 'jquery';
import 'bootstrap';

export default class StatCard {
    constructor({ title, value, icon, color = 'primary', target }) {
        this.title = title;
        this.value = value;
        this.icon = icon;
        this.color = color;
        this.target = target;
    }

    render() {
        const card = $(
            `<div class="card text-white bg-${this.color} mb-3" style="max-width: 18rem;">
                <div class="card-header d-flex align-items-center">
                    ${this.icon ? `<span class="mr-2">${this.icon}</span>` : ''}
                    <span>${this.title}</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">${this.value}</h5>
                </div>
            </div>`
        );
        $(this.target).append(card);
    }
}
