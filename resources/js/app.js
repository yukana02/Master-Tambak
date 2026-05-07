import './bootstrap';
import 'gridstack/dist/gridstack.min.css';

import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';
import { GridStack } from 'gridstack';

window.Alpine = Alpine;
window.Chart = Chart;
window.GridStack = GridStack;

Chart.register(...registerables);

Alpine.start();
