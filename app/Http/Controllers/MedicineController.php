<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Favorite;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Medicine;

class MedicineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role:admin')->except([
            'index',
            'show'
        ]);
        $this->middleware('role:pharmacist|admin')->only([
            'index',
            'show'
        ]);
    }

    public function store(Request $request)
    {
        $validator = validator::make($request->all(), [
            'business_name' => ['required', 'unique:medicines'],
            'medical_name' => ['required'],
            'price' => ['required', 'numeric'],
            'category_id' => ['required', 'numeric'],
            'expire' => ['required', 'date'],
            'quantity' => ['required', 'numeric'],
            'manifacture' => ['required'],
            'description' => ['nullable']

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::find($request->category_id);

        if (!$category) {
            $validator->errors()->add('category_id', 'Not found');
            return response()->json([
                'message' => 'Bad request',
                'errors' => $validator->errors()
            ], 422);
        }

        $medicine = Medicine::create([
            'business_name' => $request->input('business_name'),
            'medical_name' => $request->input('medical_name'),
            'price' => ((float) $request->input('price')),
            'category_id' => $request->input('category_id'),
            'expire' => $request->input('expire'),
            'manifacture' => $request->input('manifacture'),
            'quantity' => $request->input('quantity'),
            'description' => $request->input('description')
        ]);
        /* if ($medicine) {
            return response()->json([
                'success' => true,
                'message' => 'add Sucesfully',
            ]);
        }*/

        // إذا لم يتم إضافة الدواء بنجاح، فأعد رسالة JSON بها خطأ

        return response()->json($medicine, 201);
    }
    //to store midicine

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|max:200',
            'medical_name' => 'required',
            'quantity' => 'nullable|numeric|min:0',
            'price' => 'required|numeric',
            'manifacture' => 'nullable|max:255',
            'category_id' => 'required',
            'expire' => 'required|date',
            'description' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $category = Category::find($request->category_id);

        if (!$category) {
            $validator->errors()->add('category_id', 'Not found');
            return response()->json([
                'message' => 'Bad request',
                'errors' => $validator->errors()
            ], 422);
        }


        $medicine = Medicine::find($id);
        if (!$medicine) {
            return response()->json(['id not exsiting '], 404);
        }
        $medicine->update($request->all());

        return response()->json($medicine, 200);

        //  $notification = notify('product has been updated');
        // return redirect()->route('products.index')->with($notification);
    }
    // to updata info
    public function destroy($id)
    {
        $medicine = Medicine::find($id);
        if (!$medicine) {
            return response()->json(['id not exsiting'], 404);
        }

        Medicine::destroy($id);
        return response()->json([], 204);
    }
    // to delete product
    public function index(Request $request)
    {
        $search = $request->query("search");
        $category_id = $request->query("category_id");

        $query = Medicine::query()->with('category');

        if ($category_id) {
            $query = $query->where('category_id', '=', $category_id);
        }

        if ($search) {
            $query = $query->where('business_name', 'like', '%' . $search . '%')
                ->orWhere('medical_name', 'like', '%' . $search . '%');
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $query->orderBy('id', 'desc')->get()
        ]);
    }

    //to get product form id
    public function show($id)
    {
        $medicine = Medicine::with('category')->find($id);
        if (!$medicine) {
            return response()->json([], 404);
        }

        return response()->json($medicine);
    }

    public function AddToFavorites(Request $request)
    {
        $medicine = Medicine::find($request->id);
        if (!$medicine) {
            return response()->json([], 404);
        }

        Favorite::create([
            'medicine_id' => $medicine->id,
            'user_id' => auth('api')->user()->id,
        ]);

        return response()->json([], 200);
    }

    public function RemoveFromFavorites($id)
    {
        $userId = auth('api')->user()->id;
        $medicine = Favorite::where('user_id', '=', $userId)
            ->where('medicine_id', '=', $id)
            ->first();

        if (!$medicine) {
            return response()->json([], 404);
        }

        Favorite::create([
            'medicine_id' => $medicine->id,
            'user_id' => $userId,
        ]);

        return response()->json([], 200);
    }

    public function Favorites()
    {
        $user = auth()->user();

        $favorites = Favorite::with(['medicine' => function ($query) {
            $query->with('category');
        }])->where('user_id', '=', $user->id)
            ->get();

        return response()->json($favorites, 200);
    }
}
