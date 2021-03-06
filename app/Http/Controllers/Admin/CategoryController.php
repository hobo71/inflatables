<?php
namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::roots()->orderBy('name', 'desc')->paginate(50);

        return view('admin.categories.index', compact('categories'));
    }


    /**
     * Display the specified resource.
     *
     * @param Category $category
     *
     * @internal param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     *
     * @internal param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $nestedList = Category::getNestedList('name', null, '  > ');

        if ($parentId = $category->parent()->first()) {
            $parentId = $parentId->id;
        }

        return view('admin.categories.edit', compact('category', 'nestedList', 'parentId'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $nestedList = Category::getNestedList('name', null, '  > ');

        $parentId = $request->has('cat') ? $request->get('cat') : null;

        return view('admin.categories.create', compact('nestedList', 'parentId'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'bail|required|unique:categories',
        ];

        $category = $this->runSave($request, $rules);

        flash('Category Added!');

        return redirect()->route('admin.categories.show', [$category->id]);
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @param array                    $rules
     *
     * @return static
     */
    protected function runSave(Request $request, array $rules)
    {

        $this->validate($request, array_merge([
            'name' => 'required',
        ], $rules));

        if ($request->get('parent_category') == 0) {
            $category = Category::create($request->all());
            $category->makeRoot();
        } else {
            $root     = Category::whereId($request->get('parent_category'))->first();
            $category = $root->children()->create($request->all());
        }

        if ($request->has('enabled')) {
            $category->enabled = true;
        } else {
            $category->enabled = false;
        }

        if ($request->hasFile('image')) {
            $category->addMedia($request->file('image'))->preservingOriginal()->toCollection('categories');
        }

        $category->save();

        $this->clearMenuCache();

        return $category;
    }


    /**
     *
     */
    protected function clearMenuCache()
    {
        Cache::forget('menu');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \App\Category            $category
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Category $category, Request $request)
    {
        $rules = [
            'name' => 'bail|required|unique:categories,id,:id',
        ];

        if ($request->get('parent_category') == $category->id) {
            flash('You cant make a category a child of itself.');

            return redirect()->back()->withInput();
        }

        if ($request->get('parent_category') != 0 && $category->children()->count() > 0) {
            flash('Sorry, this category isn\'t allowed to have any subcategories. It is already a subcategory');

            return redirect()->back()->withInput();
        }

        $this->runUpdate($request, $rules, $category);

        flash('Category updated!');

        return redirect()->route('admin.categories.show', $category->id);
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @param array                    $rules
     * @param \App\Category            $category
     *
     * @return \App\Category|static
     */
    protected function runUpdate(Request $request, array $rules, Category $category)
    {
        $this->validate($request, array_merge([
            'name' => 'required',
        ], $rules));

        if ($request->get('parent_category') == 0) {
            $category->makeRoot();
        } else {
            $root = Category::find($request->get('parent_category'));
            $category->makeChildOf($root);
        }

        $category->fill($request->except('parent_category', 'enabled'));

    
        if ($request->has('enabled')) {
            $category->enabled = true;
        } else {
            $category->enabled = false;
        }

        $category->clearMediaCollection();
        if ($request->hasFile('image')) {
            $category->addMedia($request->file('image'))->preservingOriginal()->toCollection('categories');
        }

        $category->save();

        $this->clearMenuCache();

        return $category;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     *
     * @internal param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        $this->clearMenuCache();

        flash('Category deleted!');

        return redirect()->route('admin.categories.index');
    }


    /**
     * @param \App\Category $category
     * @param               $imageId
     *
     * @return mixed
     * @throws \Spatie\MediaLibrary\Exceptions\MediaDoesNotBelongToModel
     */
    public function deleteImage(Category $category, $imageId)
    {
        $category->deleteMedia($imageId);

        flash('Image deleted!');

        return redirect()->back();
    }


    /**
     * @param $category
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function moveUp($category)
    {
        $category->moveLeft();

        $this->clearMenuCache();

        flash("Category $category->name moved!");

        return redirect()->back();
    }


    /**
     * @param $category
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function moveDown($category)
    {
        $category->moveRight();

        $this->clearMenuCache();

        flash("Category $category->name moved!");

        return redirect()->back();
    }
}
