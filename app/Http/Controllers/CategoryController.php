<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;


class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create([
            'category_name' => $request->name,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('categories.index')->with('success', 'カテゴリを追加しました。');
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'category_name' => $request->name,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('categories.index')->with('success', 'カテゴリを更新しました。');        
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->events()->exists()) {
            return redirect()->back()->with('error', 'このカテゴリーはイベントで使用されているため削除できません。');
        }
        
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'カテゴリを削除しました。');        
    }
}
