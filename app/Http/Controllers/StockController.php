<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssignedItem; // Assuming you have a model for assigned items

class StockController extends Controller
{
    public function index()
    {
        $assignedItems = AssignedItem::all(); // Fetch all assigned items
        return view('admin.assigned_items.index', compact('assignedItems'));
    }

    public function edit($id)
    {
        $assignedItem = AssignedItem::findOrFail($id);
        return view('admin.assigned_items.edit', compact('assignedItem'));
    }

    public function update(Request $request, $id)
    {
        $assignedItem = AssignedItem::findOrFail($id);
        $assignedItem->update($request->all());
        return redirect()->route('admin.assigned-items.index')->with('success', 'Assigned item updated successfully.');
    }

    public function destroy($id)
    {
        $assignedItem = AssignedItem::findOrFail($id);
        $assignedItem->delete();
        return redirect()->route('admin.assigned-items.index')->with('success', 'Assigned item deleted successfully.');
    }
}
