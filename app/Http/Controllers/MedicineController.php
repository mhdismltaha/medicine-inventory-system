<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Medicine;

class MedicineController extends Controller
{
    public function store(Request $request)
    {
        $validator = validator::make($request->all(), [
            'business_name' => ['required', 'unique:medicines'],
            'medical_name' => ['required'],
            'price' => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
            'expire' => ['required', 'date'],
            'quantity' => ['required', 'integer'],
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
            'quantity' => 'nullable|integer|min:0',
            'price' => 'required|integer',
            'manifacture' => 'nullable|max:255',
            'category_id' => 'required',
            'expir' => 'requird|date',
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
    public function index()
    {
        //return response()->json(Medicine::with('category')->all());
        return response()->json([
            'success' => true,
            'message' => 'succcess',
            'data' => Medicine::all()
        ]);
    }

    //to get product form id
    public function show($id)
    {
        $medicine = Medicine::find($id);
        if (!$medicine) {
            return response()->json([], 404);
        }

        return response()->json($medicine);
    }
}
