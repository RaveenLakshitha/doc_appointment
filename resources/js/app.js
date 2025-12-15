import './bootstrap';

// 1. jQuery — must be first
import $ from 'jquery';
window.$ = window.jQuery = $;

// 2. DataTables Core + Responsive
import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';

// 3. Buttons + HTML5 + Print
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';

// 4. CSS Imports
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';
import 'datatables.net-buttons-dt/css/buttons.dataTables.css';

// 5. Required Dependencies for Excel & PDF
import JSZip from 'jszip';
import * as pdfMake from 'pdfmake/build/pdfmake';  // ← Named import for pdfMake
import * as pdfFonts from 'pdfmake/build/vfs_fonts.js';  // ← Named import (FIXES undefined)

// 6. Expose to window (REQUIRED!)
// JSZip for Excel
window.JSZip = JSZip;
// pdfMake for PDF (now with vfs)
window.pdfMake = pdfMake;
pdfMake.vfs = pdfFonts;  // ← Direct assignment (no .pdfMake.vfs — fixes the error)

// 7. Make DataTable global
window.DataTable = DataTable;

// Optional: nicer button styling
DataTable.Buttons.defaults.dom.container.className = 'dt-buttons flex gap-2';

// 8. Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();