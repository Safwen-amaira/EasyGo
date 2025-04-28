import { Controller } from '@hotwired/stimulus';
import { Chart } from 'chart.js/auto';

export default class extends Controller {
    connect() {
        new Chart(this.element, JSON.parse(this.element.dataset.chart));
    }
}
