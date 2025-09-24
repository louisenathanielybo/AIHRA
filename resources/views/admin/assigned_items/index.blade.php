@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Assigned Items</h1>

    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">ID</th>
                <th class="py-2 px-4 border-b">Item Name</th>
                <th class="py-2 px-4 border-b">Assigned To</th>
                <th class="py-2 px-4 border-b">Quantity</th>
                <th class="py-2 px-4 border-b">Date Assigned</th>
                <th class="py-2 px-4 border-b">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assignedItems as $item)
            <tr>
                <td class="py-2 px-4 border-b">{{ $item->id }}</td>
                <td class="py-2 px-4 border-b">{{ $item->item_name }}</td>
                <td class="py-2 px-4 border-b">{{ $item->assigned_to }}</td>
                <td class="py-2 px-4 border-b">{{ $item->quantity }}</td>
                <td class="py-2 px-4 border-b">{{ $item->date_assigned }}</td>
                <td class="py-2 px-4 border-b">
                    <a href="{{ route('admin.assigned-items.edit', $item->id) }}" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</a>
                    <form action="{{ route('admin.assigned-items.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
