<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Modules;

class ModulesController extends Controller {
    public function index() {
        $items = Modules::latest()->paginate(10);
        return view('modules.index', compact('items'));
    }

    public function show($id) {
        $item = Modules::findOrFail($id);
        return view('modules.show', compact('item'));
    }
}