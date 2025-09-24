@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Edit Assigned Item</h1>

    <form action="{{ route('admin.assigned-items.update', $assignedItem->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="item_name" class="block text-gray-700">Item Name</label>
            <input type="text" name="item_name" id="item_name" value="{{ $assignedItem->item_name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div class="mb-4">
            <label for="assigned_to" class="block text-gray-700">Assigned To</label>
            <input type="text" name="assigned_to" id="assigned_to" value="{{ $assignedItem->assigned_to }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div class="mb-4">
            <label for="quantity" class="block text-gray-700">Quantity</label>
            <input type="number" name="quantity" id="quantity" value="{{ $assignedItem->quantity }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div class="mb-4">
            <label for="date_assigned" class="block text-gray-700">Date Assigned</label>
            <input type="date" name="date_assigned" id="date_assigned" value="{{ $assignedItem->date_assigned }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
        <a href="{{ route('admin.assigned-items.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
    </form>
</div>
@endsection
