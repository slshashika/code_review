<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\Post;
use App\Http\Controllers\Controller;
use Auth;
use Intervention\Image\Facades\Image as CompressImage;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('view_categories');

        if ($hasPermission) {

            $searchKey = $request->searchKey;

            $categories = Category::getCategoriesForFilters($searchKey);

            $all_categories = Category::all();

            return view('admin.categories.all_categories', compact('categories', 'searchKey', 'all_categories'));
        } else {
            return redirect('admin/not_allowed');
        }
    }


    public function allSubCategoriesLevel1(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('view_categories');

        if ($hasPermission) {

            $searchKey = $request->searchKey;

            $sub_categories = SubCategory::getSubCategoriesForFilters($searchKey);
            $sub_category_mode = true;
            $all_categories = Category::all();

            return view('admin.categories.all_sub_categories_l1', compact('sub_categories', 'searchKey', 'sub_category_mode', 'all_categories'));
        } else {
            return redirect('admin/not_allowed');
        }
    }

    public function allSubCategoriesLevel2(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('view_categories');

        if ($hasPermission) {

            $searchKey = $request->searchKey;

            $l2_sub_categories = ChildCategory::getCategoriesForFilters($searchKey);
            $all_sub_categories = SubCategory::all();

            return view('admin.categories.all_sub_categories_l2', compact('l2_sub_categories', 'searchKey', 'all_sub_categories'));
        } else {
            return redirect('admin/not_allowed');
        }
    }

    public function UpdateSubCategoriesLevel2(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('edit_categories');

        if ($hasPermission) {

            try {
                $validated = $request->validate([
                    'category_name' => ['required', 'max:255'],
                    'slug' => ['required', 'max:255']
                ]);

                $sub_category = ChildCategory::where('id', $request->sub_category_id)->get()->first();

                if ($sub_category != null) {

                    $sub_category->sub_category_id = $request->parent_category;
                    $sub_category->child_category_name = $request->category_name;
                    $sub_category->child_category_description = $request->category_description;
                    $sub_category->slug = $request->slug;
                    $sub_category->page_title = $request->page_title;
                    $sub_category->meta_tag_description = $request->meta_tag_description;
                    $sub_category->meta_keywords = $request->meta_keywords;
                    $sub_category->canonical_url = $request->canonical_url;

                    if ($request->file('image')) {

                        $imageName = time() . '.' . $request->image->extension();
                        $request->image->move(public_path('images/uploads/categories/'), $imageName);
                        $imageUrl = 'images/uploads/categories/' . $imageName;

                        $sub_category->child_category_image = $imageUrl;
                    }

                    $sub_category->save();

                    return back()->with('success', 'Sub category updated successfully !');
                } else {
                    return back()->with('error', 'Could not find the category');
                }
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {
            return redirect('admin/not_allowed');
        }
    }

    public function newCategoryUI()
    {
        return view('admin.categories.new_category');
    }

    public function store(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('add_categories');

        if ($hasPermission) {

            $validated = $request->validate(
                [
                    'category_name' => ['required', 'max:255'],
                    'slug' => ['required', 'max:255'],
                    'type' => ['required'],
                    'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000',
                ],
                [
                    'image.required' => 'Category image required.',
                    'image.mimes' => 'Image types should be jpg,png,jpeg.',
                    'image.dimensions' => 'Please upload the images with the mentioned image dimentions.',

                ]
            );


             if ($request->file('image')) {

                $destinationPath ="images/uploads/categories/";
                $imageName =  date("YmdHis") . '_' . $request->image->getClientOriginalName();

                // Open and resize the image
                $image = CompressImage::make($request->image->getRealPath());
                $image->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                // Save the compressed image
                $image->save($destinationPath . $imageName, 50);
                $imageUrl = $destinationPath . $imageName;
            }

            if ($request->category_level == "1") {

                //level 1 category

                $category = new Category;

                $category->slug = $request->slug;
                $category->status = Category::ACTIVE;
                $category->type = $request->type;
                $category->page_title = $request->page_title;
                $category->meta_tag_description = $request->meta_tag_description;
                $category->meta_keywords = $request->meta_keywords;
                $category->canonical_url = $request->canonical_url;
                $category->category_name = $request->category_name;
                $category->category_description = $request->category_description;
                $category->category_image = $imageUrl;

                $savedCategory = Category::create($category->toArray());
            } else if ($request->category_level == "2") {

                //level 2 category

                $category = new SubCategory;

                $category->slug = $request->slug;
                $category->category_id = $request->parent_id;
                $category->status = SubCategory::ACTIVE;
                $category->type = $request->type;
                $category->page_title = $request->page_title;
                $category->meta_tag_description = $request->meta_tag_description;
                $category->meta_keywords = $request->meta_keywords;
                $category->canonical_url = $request->canonical_url;
                $category->sub_category_name = $request->category_name;
                $category->sub_category_description = $request->category_description;
                $category->sub_category_image = $imageUrl;

                $savedCategory = SubCategory::create($category->toArray());
            } else {

                //level 3 category

                $category = new ChildCategory;

                $category->slug = $request->slug;
                $category->sub_category_id = $request->parent_id;
                $category->status = ChildCategory::ACTIVE;
                $category->type = $request->type;
                $category->page_title = $request->page_title;
                $category->meta_tag_description = $request->meta_tag_description;
                $category->meta_keywords = $request->meta_keywords;
                $category->canonical_url = $request->canonical_url;
                $category->child_category_name = $request->category_name;
                $category->child_category_description = $request->category_description;
                $category->child_category_image = $imageUrl;

                $savedCategory = ChildCategory::create($category->toArray());
            }


            return back()->with('success', 'Category created successfully !');
        } else {
            return redirect('admin/not_allowed');
        }
    }

    public function update(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('edit_categories');

        if ($hasPermission) {

            $validated = $request->validate([
                'category_name' => ['required', 'max:255'],
                'slug' => ['required', 'max:255'],
                'type' => ['required']
            ]);

            $category = Category::where('id', $request->category_id)->get()->first();

            if ($category != null) {

                $category->category_name = $request->category_name;
                $category->category_description = $request->category_description;
                $category->slug = $request->slug;
                $category->type = $request->type;
                $category->page_title = $request->page_title;
                $category->meta_tag_description = $request->meta_tag_description;
                $category->meta_keywords = $request->meta_keywords;
                $category->canonical_url = $request->canonical_url;

                if ($request->file('image')) {

                    // $imageName = time().'.'.$request->image->extension();
                    // $request->image->move(public_path('images/uploads/categories/'), $imageName);
                    // $imageUrl = 'images/uploads/categories/' . $imageName;

                    $destinationPath = "images/uploads/categories/";
                    $imageName =  date("YmdHis") . '_' . $request->image->getClientOriginalName();

                    // Open and resize the image
                    $image = CompressImage::make($request->image->getRealPath());
                    $image->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    // Save the compressed image
                    $image->save($destinationPath . $imageName, 50);

                    $imageUrl = $destinationPath . $imageName;

                    $category->category_image = $imageUrl;
                }

                $category->save();

                return back()->with('success', 'Category updated successfully !');
            } else {
                return back()->with('error', 'Could not find the category');
            }
        } else {
            return redirect('admin/not_allowed');
        }
    }

    public function updateSubCategory(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('edit_categories');

        if ($hasPermission) {

            try {
                $validated = $request->validate([
                    'category_name' => ['required', 'max:255'],
                    'slug' => ['required', 'max:255']
                ]);

                $sub_category = SubCategory::where('id', $request->sub_category_id)->get()->first();

                if ($sub_category != null) {

                    $sub_category->sub_category_name = $request->category_name;
                    $sub_category->sub_category_description = $request->category_description;
                    $sub_category->slug = $request->slug;
                    $sub_category->page_title = $request->page_title;
                    $sub_category->meta_tag_description = $request->meta_tag_description;
                    $sub_category->meta_keywords = $request->meta_keywords;
                    $sub_category->canonical_url = $request->canonical_url;

                    if ($request->file('image')) {

                        $imageName = time() . '.' . $request->image->extension();
                        $request->image->move(public_path('images/uploads/categories/'), $imageName);
                        $imageUrl = 'images/uploads/categories/' . $imageName;

                        $sub_category->sub_category_image = $imageUrl;
                    }

                    $sub_category->save();

                    return back()->with('success', 'Sub category updated successfully !');
                } else {
                    return back()->with('error', 'Could not find the category');
                }
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {
            return redirect('admin/not_allowed');
        }
    }

    public function remove($id)
    {

        $hasPermission = Auth::user()->hasPermission('delete_categories');

        if ($hasPermission) {

            try {

                Post::where('category_id', $id)->update(['category_id' => null]);

                Category::where('id', $id)->delete();

                return back()->with('success', 'Category removed successfully !');
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {
            return redirect('admin/not_allowed');
        }
    }

    public function removeSubCategory($id)
    {

        $hasPermission = Auth::user()->hasPermission('delete_categories');

        if ($hasPermission) {

            try {

                Post::where('sub_category_id', $id)->update(['sub_category_id' => null]);

                SubCategory::where('id', $id)->delete();

                return back()->with('success', 'Sub category removed successfully !');
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {
            return redirect('admin/not_allowed');
        }
    }

    public function getCategoryForId($id)
    {

        $category = Category::where('id', $id)->get()->first();

        return array(
            'category' => $category
        );
    }

    public function getMainCategories(Request $request)
    {

        $categories = Category::where('status', 1)->where('type', 1)->get();

        return array(
            'status' => true,
            'categories' => $categories
        );
    }

    public function getSubCategories(Request $request)
    {

        $subCategories = SubCategory::where('status', 1)->get();

        return array(
            'status' => true,
            'categories' => $subCategories
        );
    }

    public function getSubCategoriesForCategory($id)
    {

        $subCategories = SubCategory::where('status', 1)->where('category_id', $id)->get();

        return array(
            'status' => true,
            'categories' => $subCategories
        );
    }

    public function getChildCategoriesForSubCategory($id)
    {

        $childCategories = ChildCategory::where('status', 1)->where('sub_category_id', $id)->get();

        return array(
            'status' => true,
            'categories' => $childCategories
        );
    }
}
