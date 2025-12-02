<?php
// app/DataTables/DoctorsDataTable.php

namespace App\DataTables;

use App\Models\Doctor;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class DoctorsDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($doctor) {
                return view('doctors.actions', compact('doctor'))->render();
            })
            ->editColumn('is_active', function ($doctor) {
                return $doctor->is_active
                    ? '<span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Active</span>'
                    : '<span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Inactive</span>';
            })
            ->editColumn('appointments_count', function ($doctor) {
                return '<span class="font-medium">'.$doctor->appointments_count.'</span>';
            })
            ->rawColumns(['action', 'is_active', 'appointments_count'])
            ->setRowId('id');
    }

    public function query(Doctor $model)
    {
        return $model->newQuery()->withCount('appointments');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('doctors-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(3, 'desc') // Sort by appointments by default
            ->lengthMenu([10, 25, 50, 100])
            ->buttons([
                Button::make('excel')->text('<i class="fas fa-file-excel"></i> Excel'),
                Button::make('csv')->text('<i class="fas fa-file-csv"></i> CSV'),
                Button::make('pdf')->text('<i class="fas fa-file-pdf"></i> PDF'),
                Button::make('print')->text('<i class="fas fa-print"></i> Print'),
                Button::make('reset')->text('<i class="fas fa-undo"></i> Reset'),
                Button::make('reload')->text('<i class="fas fa-sync"></i> Reload'),
            ])
            ->parameters([
                'language' => [
                    'processing' => 'Loading...',
                    'search'     => 'Search doctors:',
                    'lengthMenu' => 'Show _MENU_ entries',
                    'info'       => 'Showing _START_ to _END_ of _TOTAL_ doctors',
                    'paginate'   => ['previous' => 'Previous', 'next' => 'Next'],
                ],
                'dom' => 'B<"float-left"l><"float-right"f>t<"float-left"i><"float-right"p>',
                'initComplete' => "function() {
                    $('.dataTables_length select').addClass('rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm');
                    $('.dataTables_filter input').addClass('rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm pl-10');
                    $('.dataTables_filter').addClass('relative');
                    $('.dataTables_filter input').before('<svg class=\"absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z\"/></svg>');
                }"
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('doctor_info')
                ->title('Doctor')
                ->orderable(true)
                ->searchable(true)
                ->width(300),
            Column::make('primary_specialty')->title('Specialty'),
            Column::make('is_active')->title('Status'),
            Column::make('appointments_count')->title('Appointments')->orderable(true),
            Column::make('phone')->title('Contact'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-right'),
        ];
    }

    protected function filename(): string
    {
        return 'Doctors_' . date('YmdHis');
    }
}