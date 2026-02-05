@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="dashboard-header">
        <h2>Dashboard</h2>
    </div>
    <div class="stat-cards" id="stat-cards"></div>
    <div class="table-responsive" id="datatable"></div>
    <div class="chart-container" id="chart-widget"></div>
</div>
@endsection
@push('styles')
<link href="{{ mix('css/app.css') }}" rel="stylesheet">
<link href="{{ mix('css/dashboard.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script src="{{ mix('js/app.js') }}"></script>
<script>
import StatCard from './components/StatCard';
import DataTable from './components/DataTable';
import ChartWidget from './components/ChartWidget';

document.addEventListener('DOMContentLoaded', function() {
    // Example StatCards
    new StatCard({ title: 'Users', value: 120, icon: '👤', color: 'primary', target: '#stat-cards' }).render();
    new StatCard({ title: 'Revenue', value: '$5,000', icon: '💰', color: 'success', target: '#stat-cards' }).render();
    new StatCard({ title: 'Errors', value: 2, icon: '⚠️', color: 'danger', target: '#stat-cards' }).render();

    // Example DataTable
    new DataTable({
        columns: ['ID', 'Name', 'Email'],
        data: [
            [1, 'Alice', 'alice@example.com'],
            [2, 'Bob', 'bob@example.com'],
            [3, 'Charlie', 'charlie@example.com'],
            [4, 'David', 'david@example.com'],
            [5, 'Eva', 'eva@example.com'],
            [6, 'Frank', 'frank@example.com']
        ],
        target: '#datatable',
        pageSize: 3
    }).render();

    // Example ChartWidget
    new ChartWidget({
        target: '#chart-widget',
        apiUrl: '/api/dashboard/chart',
        type: 'bar',
        dataTransform: data => ({
            labels: data.labels,
            datasets: [{
                label: 'Sample Data',
                data: data.values,
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        })
    }).render();
});
</script>
@endpush
