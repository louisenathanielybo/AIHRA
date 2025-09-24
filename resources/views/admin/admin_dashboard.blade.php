@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold mb-2">Assigned Items</h2>
            <p class="text-gray-600 mb-4">Manage assigned items</p>
            <a href="{{ route('admin.assigned-items.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded">View Assigned Items</a>
        </div>

        <!-- Add more dashboard items as needed -->
    </div>
</div>
@endsection
