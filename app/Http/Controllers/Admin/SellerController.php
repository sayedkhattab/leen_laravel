<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sellers = Seller::all();
        return view('admin.sellers.index', compact('sellers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sellers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:sellers',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $seller = new Seller();
        $seller->first_name = $request->name;
        $seller->last_name = ''; // Default empty value
        $seller->email = $request->email;
        $seller->phone = $request->phone;
        $seller->password = bcrypt($request->password);
        $seller->location = $request->address;
        $seller->status = 'active';
        $seller->request_status = $request->status ?? 'pending';

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            
            // Move the uploaded file directly to the public directory
            $image->move(public_path('images/sellers'), $imageName);
            $seller->seller_logo = 'images/sellers/' . $imageName;
        }

        $seller->save();

        return redirect()->route('admin.sellers.index')
            ->with('success', 'تم إضافة مقدم الخدمة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $seller = Seller::findOrFail($id);
        return view('admin.sellers.show', compact('seller'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $seller = Seller::findOrFail($id);
        return view('admin.sellers.edit', compact('seller'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $seller = Seller::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:sellers,email,' . $id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $seller->first_name = $request->name;
        $seller->email = $request->email;
        $seller->phone = $request->phone;
        
        if ($request->password) {
            $seller->password = bcrypt($request->password);
        }
        
        $seller->location = $request->address;
        $seller->request_status = $request->status;

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($seller->seller_logo && file_exists(public_path($seller->seller_logo))) {
                unlink(public_path($seller->seller_logo));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            
            // Move the uploaded file directly to the public directory
            $image->move(public_path('images/sellers'), $imageName);
            $seller->seller_logo = 'images/sellers/' . $imageName;
        }

        $seller->save();

        return redirect()->route('admin.sellers.index')
            ->with('success', 'تم تحديث مقدم الخدمة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $seller = Seller::findOrFail($id);

        // Delete image if exists
        if ($seller->seller_logo && file_exists(public_path($seller->seller_logo))) {
            unlink(public_path($seller->seller_logo));
        }

        $seller->delete();

        return redirect()->route('admin.sellers.index')
            ->with('success', 'تم حذف مقدم الخدمة بنجاح');
    }

    /**
     * Approve the seller.
     */
    public function approve(string $id)
    {
        $seller = Seller::findOrFail($id);
        $seller->request_status = 'approved';
        $seller->save();

        return redirect()->route('admin.sellers.index')
            ->with('success', 'تم الموافقة على مقدم الخدمة بنجاح');
    }

    /**
     * Reject the seller.
     */
    public function reject(string $id)
    {
        $seller = Seller::findOrFail($id);
        $seller->request_status = 'rejected';
        $seller->save();

        return redirect()->route('admin.sellers.index')
            ->with('success', 'تم رفض مقدم الخدمة بنجاح');
    }
} 