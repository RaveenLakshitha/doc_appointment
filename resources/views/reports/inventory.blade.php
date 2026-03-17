@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')

    <div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">Inventory Report</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Track inventory levels, usage patterns, and supply chain metrics
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filters</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Category
                    </label>
                    <select id="category_id" name="category_id"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Supplier
                    </label>
                    <select id="supplier_id" name="supplier_id"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="button" id="generate_report"
                    class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                    Refresh Report
                </button>
                <button type="button" id="reset_filters"
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Reset
                </button>
            </div>
        </div>

        <div id="loading" class="hidden text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <p class="text-gray-500 dark:text-gray-400 mt-3">Loading inventory data...</p>
        </div>

        <div id="report_content" class="hidden space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Total Items</h3>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-4a2 2 0 00-2 2v3m-8-5H4">
                            </path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold" id="total_items">0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Low Stock Items</h3>
                        <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400" id="low_stock_items">0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Expiring Soon</h3>
                        <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="expiring_soon">0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Inventory Value</h3>
                        <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold" id="inventory_value">{{ $currency_code }}0</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Stock Status Distribution</h3>
                    <div class="h-64">
                        <canvas id="stock_status_chart"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Inventory by Category</h3>
                    <div class="h-64">
                        <canvas id="category_chart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Low Stock Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Item Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    SKU</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Category</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Current Stock</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Min. Stock Level</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Value</th>
                            </tr>
                        </thead>
                        <tbody id="low_stock_table" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Expiring Soon (Next 90 Days)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Item Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Batch Number</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Expiry Date</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Days Left</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="expiring_table" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let stockStatusChart = null;
                let categoryChart = null;

                document.getElementById('generate_report').addEventListener('click', generateReport);

                document.getElementById('reset_filters').addEventListener('click', function () {
                    document.getElementById('category_id').value = '';
                    document.getElementById('supplier_id').value = '';
                    generateReport();
                });

                function generateReport() {
                    const categoryId = document.getElementById('category_id').value;
                    const supplierId = document.getElementById('supplier_id').value;

                    document.getElementById('loading').classList.remove('hidden');
                    document.getElementById('report_content').classList.add('hidden');

                    const params = new URLSearchParams();
                    if (categoryId) params.append('category_id', categoryId);
                    if (supplierId) params.append('supplier_id', supplierId);

                    fetch(`{{ route('reports.inventory.summary') }}?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            updateSummaryCards(data.summary);
                            updateStockStatusChart(data.stock_status);
                            updateCategoryChart(data.by_category);
                            updateLowStockTable(data.top_low_stock);
                            updateExpiringTable(data.expiring_items);

                            document.getElementById('loading').classList.add('hidden');
                            document.getElementById('report_content').classList.remove('hidden');
                        })
                        .catch(err => {
                            console.error(err);
                            showNotification('Failed to load report. Please try again.', 'error');
                            document.getElementById('loading').classList.add('hidden');
                        });
                }

                function updateSummaryCards(summary) {
                    document.getElementById('total_items').textContent = summary.total_items.toLocaleString();
                    document.getElementById('low_stock_items').textContent = summary.low_stock_items;
                    document.getElementById('expiring_soon').textContent = summary.expiring_soon;
                    const val = parseFloat(summary.inventory_value) || 0;
                    document.getElementById('inventory_value').textContent = '{{ $currency_code }}' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                function updateStockStatusChart(data) {
                    const ctx = document.getElementById('stock_status_chart').getContext('2d');
                    if (stockStatusChart) stockStatusChart.destroy();

                    stockStatusChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                            datasets: [{
                                data: [data['In Stock'], data['Low Stock'], data['Out of Stock']],
                                backgroundColor: [
                                    'rgb(34, 197, 94)',
                                    'rgb(249, 115, 22)',
                                    'rgb(239, 68, 68)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                }

                function updateCategoryChart(data) {
                    const ctx = document.getElementById('category_chart').getContext('2d');
                    if (categoryChart) categoryChart.destroy();

                    categoryChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(data),
                            datasets: [{
                                label: 'Items',
                                data: Object.values(data),
                                backgroundColor: 'rgb(59, 130, 246)',
                                borderColor: 'rgb(37, 99, 235)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, ticks: { stepSize: 1 } }
                            },
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    });
                }

                function updateLowStockTable(items) {
                    const tbody = document.getElementById('low_stock_table');
                    tbody.innerHTML = '';

                    if (!items?.length) {
                        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No low stock items found</td></tr>';
                        return;
                    }

                    items.forEach(item => {
                        tbody.innerHTML += `
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">${item.name}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">${item.sku}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">${item.category}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-red-600 dark:text-red-400">${item.current_stock}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">${item.minimum_stock_level}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $currency_code }}${item.value}</td>
                                </tr>
                            `;
                    });
                }

                function updateExpiringTable(items) {
                    const tbody = document.getElementById('expiring_table');
                    tbody.innerHTML = '';

                    if (!items?.length) {
                        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No items expiring soon</td></tr>';
                        return;
                    }

                    items.forEach(item => {
                        const daysClass = item.days_left <= 30 ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-900 dark:text-white';
                        tbody.innerHTML += `
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">${item.item_name}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">${item.batch_number}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">${item.expiry_date}</td>
                                    <td class="px-6 py-4 text-sm ${daysClass}">${item.days_left}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${item.quantity}</td>
                                </tr>
                            `;
                    });
                }

                generateReport();
            });
        </script>
    @endpush

@endsection