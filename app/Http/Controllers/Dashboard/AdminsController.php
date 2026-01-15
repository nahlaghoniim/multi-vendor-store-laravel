<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Admin::class, 'admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::paginate(10); // better to limit per page
        return view('dashboard.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('dashboard.admins.create', [
            'admin' => new Admin(),
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // 1. Validate only the fields we actually want
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:admins,email',
        'password' => 'required|string|min:8|confirmed',
        'phone_number' => 'nullable|string|max:20',
        'super_admin' => 'nullable|boolean',
        'status' => 'nullable|boolean',
        'roles' => 'required|array',
    ]);

    // 2. Only pick the fields that exist in $fillable
    $adminData = $request->only([
        'name', 'email', 'password', 'phone_number', 'super_admin', 'status'
    ]);

    // 3. Hash the password before storing
    $adminData['password'] = bcrypt($adminData['password']);

    // 4. Create the admin
    $admin = Admin::create($adminData);

    // 5. Attach roles safely
    $admin->roles()->attach($request->roles);

    return redirect()
        ->route('dashboard.admins.index')
        ->with('success', 'Admin created successfully');
}

public function update(Request $request, Admin $admin)
{
    // 1. Validate the fields
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:admins,email,' . $admin->id,
        'password' => 'nullable|string|min:8|confirmed',
        'phone_number' => 'nullable|string|max:20',
        'super_admin' => 'nullable|boolean',
        'status' => 'nullable|boolean',
        'roles' => 'required|array',
    ]);

    // 2. Only pick the fields that exist in $fillable
    $adminData = $request->only([
        'name', 'email', 'password', 'phone_number', 'super_admin', 'status'
    ]);

    // 3. Hash password if provided
    if (!empty($adminData['password'])) {
        $adminData['password'] = bcrypt($adminData['password']);
    } else {
        unset($adminData['password']); // keep old password if not updated
    }

    // 4. Update admin
    $admin->update($adminData);

    // 5. Sync roles safely
    $admin->roles()->sync($request->roles);

    return redirect()
        ->route('dashboard.admins.index')
        ->with('success', 'Admin updated successfully');
}

}
