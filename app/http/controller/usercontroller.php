<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Order;
use App\Mail\NewUserConfirmation;
use App\Mail\NewUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create(Request $request)
    {
        // Validasi Input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'name' => 'required|min:3|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Insert ke users table
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password), // Encrypt password
            'name' => $request->name,
        ]);

        // Kirim email konfirmasi ke pengguna
        Mail::to($user->email)->send(new NewUserConfirmation($user));

        // Kirim email notifikasi ke admin
        Mail::to('admin@example.com')->send(new NewUserNotification($user));

        // Return response tanpa password
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'created_at' => $user->created_at->toISOString(),
        ], 201);
    }

    public function index(Request $request)
    {
        $query = User::where('active', true);

        // Filtering
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sortBy', 'created_at');
        $query->orderBy($sortBy);

        // Pagination
        $users = $query->paginate(10);
        
        // Add orders_count field
        $users->getCollection()->transform(function($user) {
            $user->orders_count = $user->orders()->count();
            return $user;
        });

        return response()->json([
            'page' => $users->currentPage(),
            'users' => $users->items()
        ]);
    }
}

