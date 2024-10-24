<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            // Return validation errors as JSON
            return response()->json($validator->errors(), 422);
        }

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Automatically log the user in after registration
        Auth::login($user);

        // Redirect to the dashboard or return a success response
        return redirect()->route('dashboard')->with('success', 'User registered successfully');
    }

    // Login user
    public function login(Request $request)
    {
        // Validate incoming request data
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // Authentication passed
            $user = Auth::user();
            return redirect()->route('dashboard')->with('success', 'Login successful');
        }

        // Redirect back with an error message if authentication fails
        return redirect()->back()->with('error', 'Invalid credentials');
    }

    // Logout user
    public function logout(Request $request)
    {
        Auth::logout();

        // Redirect back to the login page after logout
        return redirect()->route('login')->with('success', 'Logout successful');
    }
}
