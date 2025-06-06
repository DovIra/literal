<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserType;
use App\Http\Requests\CategoryRequest;


class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function store(CategoryRequest $request)
    {
        if (Auth::user()->user_type !== UserType::Admin) {
            return redirect()->route('dashboard')->with('error', '不正な操作です。');
        }

        Category::create([
            'category_name' => $request->name,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('categories.index')->with('success', 'カテゴリを追加しました。');
    }

    public function update(CategoryRequest $request,$id)
    {
        if (Auth::user()->user_type !== UserType::Admin) {
            return redirect()->route('dashboard')->with('error', '不正な操作です。');
        }

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
