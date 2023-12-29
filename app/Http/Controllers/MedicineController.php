<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Support\Facades\Validator;

class MedicineController extends Controller
{
    public function Create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'unique:medicines'],
            'namebus' => ['required'],
            'price' => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
            'expair' => ['required,date'],
            'qty' => ['required', 'integer'],
            'mainfactor' => ['required'],

        ]);

        $medicine = Medicine::create([
            'name' => $request->input('name'),
            'namebus' => $request->input('namebus'),
            'price' => ((float) $request->input('price')),
            'category_id' => $request->input('category_id'),
            'expair' => $request->input('expair'),
            'mainfactor' => $request->input('mainfactor'),
            'qty' => $request->input('qty'),
        ]);
        if ($medicine) {
            return response()->json([
                'success' => true,
                'message' => 'add Sucesfully',
            ]);
        }

        // إذا لم يتم إضافة الدواء بنجاح، فأعد رسالة JSON بها خطأ
        else {
            return response()->json([
                'success' => false,
                'message' => 'Wrong data ',
            ]);
        }
    }
    //to create midicine

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|max:200',
            'medical_name' => 'required',
            'qty' => 'nullable|integer|min:0',
            'price' => 'required|integer',
            'mainfactor' => 'nullable|max:255',
            'category_id' => 'required',
            'expir' => 'requird|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $medicine = Medicine::find($id);
        if (!$medicine) {
            return response()->json([], 404);
        }
        $medicine->update($request->all());

        return response()->json($medicine, 200);

        //  $notification = notify('product has been updated');
        // return redirect()->route('products.index')->with($notification);
    }
    // to updata info
    public function destory($id)
    {
        $medicine = Medicine::find($id);
        if (!$medicine) {
            return response()->json([], 404);
        }

        Medicine::destroy($id);
        return response()->json([], 204);
    }
    // to delete product
    public function index()
    {
        return response()->json(Medicine::with('category')->all());
    }

    //to get product form id
    public function Getid($id)
    {
        $medicine = Medicine::find($id);
        if (!$medicine) {
            return response()->json([], 404);
        }

        return response()->json($medicine);
    }
}
