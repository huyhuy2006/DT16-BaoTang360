<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        return Language::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:5', 'unique:languages,code'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        return response()->json(Language::create($data), 201);
    }
}
