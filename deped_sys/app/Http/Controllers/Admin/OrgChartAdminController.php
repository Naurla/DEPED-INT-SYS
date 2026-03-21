<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\PositionAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrgChartAdminController extends Controller
{
    public function index()
    {
        // No longer fetching users since we just type the names
        $positions = Position::with(['parent', 'assignments'])->orderBy('order_index')->get();
        return view('admin.org_chart.index', compact('positions'));
    }

    public function storePosition(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slots_count' => 'required|integer|min:1',
            'parent_id' => 'nullable|exists:positions,id',
        ]);

        Position::create([
            'name' => $request->name,
            'slots_count' => $request->slots_count,
            'parent_id' => $request->parent_id,
            'order_index' => Position::max('order_index') + 1,
        ]);

        return redirect()->back()->with('success', 'Position created successfully.');
    }

    public function destroyPosition(Position $position)
    {
        $position->delete();
        return redirect()->back()->with('success', 'Position deleted successfully.');
    }

    public function assignSlot(Request $request, Position $position)
    {
        $request->validate([
            'slot_index' => 'required|integer|min:1|max:' . $position->slots_count,
            'employee_name' => 'required|string|max:255',
            'employee_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Find existing or start a new assignment for this specific slot
        $assignment = PositionAssignment::firstOrNew([
            'position_id' => $position->id,
            'slot_index' => $request->slot_index
        ]);

        $assignment->employee_name = $request->employee_name;

        // Handle the image upload
        if ($request->hasFile('employee_image')) {
            // Delete old image if replacing
            if ($assignment->employee_image) {
                Storage::disk('public')->delete($assignment->employee_image);
            }
            $assignment->employee_image = $request->file('employee_image')->store('org_chart_profiles', 'public');
        }

        $assignment->save();

        return redirect()->back()->with('success', 'Employee assigned to slot successfully.');
    }

    public function unassignSlot(PositionAssignment $assignment)
    {
        if ($assignment->employee_image) {
            Storage::disk('public')->delete($assignment->employee_image);
        }
        $assignment->delete();
        return redirect()->back()->with('success', 'Employee removed from slot.');
    }
}