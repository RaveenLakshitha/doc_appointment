// resources/js/app.js
import './bootstrap';

// 1. DATATABLES CSS — VITE-FRIENDLY (NO ROLLUP ERROR!)
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';

// 2. JQUERY — MUST BE FIRST!
import $ from 'jquery';
window.$ = window.jQuery = $;  // This line makes $ available globally

// 3. DATATABLES CORE + RESPONSIVE
import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';

// Make DataTable available globally (optional but safe)
window.DataTable = DataTable;

// 4. ALPINE.JS
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();