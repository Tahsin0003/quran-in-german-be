<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Auth;

class UserController extends Controller
{

    // Get all users
    public function index(Request $request)
    {
        try {
            $currentPage = ($request->page == 0) ? 1 : $request->page;
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            $query = User::select("u.*")->from("users as u")->orderBy("u.created_at", "DESC");
            $users = $query->paginate($request->limit ?? 10);

            return response()->json([
                'success'      => true,
                'message'      => "User list retrive successfully.",
                'data'         => $users->items(),
                'current_page' => $users->currentPage(),
                'limit'        => $users->perPage(),
                'last_page'    => $users->lastPage(),
                'total'        => $users->total()
            ], 200);
        
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database query error',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    // Store new user
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'nullable|string|max:20|unique:users,phone',
                'password' => 'required|string|min:6',
            ]);
            $obj = new User;
            $obj->name = $validated['name'];
            $obj->email = $validated['email'];
            $obj->phone = $validated['phone'];
            $obj->password = Hash::make($validated['password']);
            $obj->save();

            return response()->json([
                'success' => true,
                'message' => 'User successfully registered',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Show single user
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'User found successfully.',
                'data'    => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found with ID ' . $id,
                // 'error'   => $e->getMessage(),
            ], 404);
        }
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        try {
            $validated = $request->validate([
                'name'     => 'nullable|string|max:255',
                'phone'    => 'nullable|string|max:20|unique:users,phone,' . $id, // ignore current user's phone
                'password' => 'nullable|string|min:6',
            ]);

            if (isset($validated['name'])) {
                $user->name = $validated['name'];
            }

            if (isset($validated['phone'])) {
                $user->phone = $validated['phone'];
            }

            if (isset($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'data'    => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
        // $user = User::findOrFail($id);
        // try {
        //     $validated = $request->validate([
        //         'name'     => 'nullable|string|max:255',
        //         'phone'    => 'nullable|string|max:20|unique:users,phone',
        //         'password' => 'nullable|string|min:6',
        //     ]);
        //     $user->name = $validated['name'];
        //     $user->email = $validated['email'];
        //     $user->phone = $validated['phone'];
        //     $user->password = Hash::make($validated['password']);
        //     $user->save();

        //     return response()->json([
        //         'success' => true,
        //         'message' => 'User updated successfully.',
        //     ], 201);

        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Updation failed',
        //         'error' => $e->getMessage(),
        //     ], 500);
        // }
    }

    // Delete user
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found with ID ' . $id,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    // User Registration Method - POST /api/register(name, email, password, phone
    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20|unique:users,phone',
        ]);

        try {
            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
            ]);

            // $token = auth()->login($user);
            $token = auth('api')->login($user);

            return response()->json([
                'success' => true,
                'message' => 'User successfully registered',
                'user' => $user,
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
   
    // User Login Method - POST /api/login(email, password)
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (! $token = auth('api')->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                // 'user' => auth('api')->user(),
                'data'    => [
                    'user'  => auth('api')->user(),
                    'token' => $token,
                ]
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not create token',
                'error' => $e->getMessage(),
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function profile()
    {
        try {
            $user = auth('api')->user();
            return response()->json([
                'success' => true,
                'message' => 'User profile fetched successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //Refresh a token - GET(JWT Auth Token)
    public function refresh()
    {
        try {
            // $newToken = auth()->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Token successfully refreshed',
                'token' => auth()->refresh(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Logout API - GET(JWT AUth Token)
    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if ($token) {
                JWTAuth::invalidate($token); // add token to blacklist
            }

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
                'data' => (object)[]
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token is already invalidated or not valid.'
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while logging out.',
                'error'   => $e->getMessage()
            ], 500);
        }
        // try {
            // $user = JWTAuth::parseToken()->authenticate();
            // JWTAuth::invalidate($request->bearerToken());
            // return response()->json(['success' => true, 'message' => 'Logged out successfully.', 'data' => (object)[]],200);
            // $user = Auth::guard('api')->user();
            // if (!$user) {
            //     return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            // }
            // if ($token = $request->bearerToken()) {
            //     JWTAuth::invalidate($token);
            // }
            // return response()->json(['success' => true, 'data' => (object)[], 'message' => 'Successfully logged out'], 200);
        // } catch (JWTException $e) {
        //     return response()->json(['success' => false, 'message' => 'Failed to log out, please try again'], 500);
        // } catch (\Exception $e) {
        //     return response()->json(['success' => false, 'message' => 'An error occurred while logging out.'], 500);
        // }
        // try {
        //     JWTAuth::invalidate(JWTAuth::parseToken());
        //     auth()->logout();
        //     return response()->json([
        //         'success' => true,
        //         'data' => (object)[],
        //         'message' => 'Successfully logged out'
        //     ], 200);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Logout failed',
        //         'error' => $e->getMessage(),
        //     ], 500);
        // }
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        // return $user;
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "User not found."
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "User deleted successfully."
        ]);
    }

    public function getUsers(Request $request)
    {
        try {
            $currentPage = ($request->page == 0) ? 1 : $request->page;
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            $query = User::select("u.*")->from("users as u")->orderBy("u.created_at", "DESC");
            $users = $query->paginate($request->limit ?? 10);

            return response()->json([
                'success'      => true,
                'data'         => $users->items(),
                'current_page' => $users->currentPage(),
                'limit'        => $users->perPage(),
                'last_page'    => $users->lastPage(),
                'total'        => $users->total()
            ], 200);
        
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database query error',
                'error'   => $e->getMessage(),
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        return $request;
        try {
            $user = auth()->user();
            $validated = $request->validate([
                'name'     => 'sometimes|required|string|max:255',
                'phone'    => 'sometimes|nullable|string|max:20|unique:users,phone,' . $user->id,
                'password' => 'sometimes|required|string|min:6|confirmed',
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            if (isset($validated['name'])) {
                $user->name = $validated['name'];
            }
            if (isset($validated['phone'])) {
                $user->phone = $validated['phone'];
            }
            if (isset($validated['password'])) {
                $user->password = $validated['password'];
            }
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user'    => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

}
