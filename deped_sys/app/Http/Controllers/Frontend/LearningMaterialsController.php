<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LearningMaterials;
use Illuminate\Http\Request;

class LearningMaterialsController extends Controller // <-- CHANGED THIS TO PLURAL
{
    public function index()
    {
        // Changed from get() to paginate(10)
        $materials = LearningMaterials::latest()->paginate(5);
        return view('learning_materials.index', compact('materials'));
    }

    public function show(string $id)
    {
        $material = LearningMaterials::findOrFail($id);
        return view('learning_materials.show', compact('material'));
    }
}