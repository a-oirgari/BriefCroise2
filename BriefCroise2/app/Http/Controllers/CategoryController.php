<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;

class CategoryController extends Controller
{
    public function index(Colocation $colocation)
    {
        $user = User::find(session('user_id'));
        $this->assertOwner($colocation, $user);

        $categories = $colocation->categories()->withCount('expenses')->get();

        return view('categories.index', compact('colocation', 'user', 'categories'));
    }

    public function store(StoreCategoryRequest $request, Colocation $colocation)
    {
        $user = User::find(session('user_id'));
        $this->assertOwner($colocation, $user);

        Category::create([
            'colocation_id' => $colocation->id,
            'name'          => $request->name,
        ]);

        return back()->with('success', 'Catégorie créée.');
    }

    public function destroy(Colocation $colocation, Category $category)
    {
        $user = User::find(session('user_id'));
        $this->assertOwner($colocation, $user);

        if ($category->colocation_id !== $colocation->id) {
            abort(403, 'Cette catégorie n\'appartient pas à cette colocation.');
        }

        $category->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }

    private function assertOwner(Colocation $colocation, User $user): void
    {
        $membership = $colocation->activeMembers()->where('user_id', $user->id)->first();
        if (!$membership || $membership->role !== 'owner') {
            abort(403, 'Action réservée à l\'owner.');
        }
    }
}