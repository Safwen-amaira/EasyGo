import { Application } from '@hotwired/stimulus';
import ChartjsController from './controllers/chartjs_controller';

const app = Application.start();
app.register('chartjs', ChartjsController);
