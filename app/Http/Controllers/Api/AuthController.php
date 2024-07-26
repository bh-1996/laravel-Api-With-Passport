<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Helpers\MediaHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * The MediaHelper instance for handling image uploads.
     *
     * @var \App\Helpers\MediaHelper
     */
    private $imageUpload;

    /**
     * The ApiResponse instance for standardized responses.
     *
     * @var \App\Helpers\ApiResponse
     */
    private $apiResponse;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Helpers\MediaHelper  $imageUpload
     * @param  \App\Helpers\ApiResponse  $apiResponse
     * @return void
     */
    public function __construct(MediaHelper $imageUpload, ApiResponse $apiResponse)
    {
        $this->imageUpload = $imageUpload;
        $this->apiResponse = $apiResponse;
    }

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            // Define validation rules
            $validatorUser = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Check if validation fails
            if ($validatorUser->fails()) {
                return $this->apiResponse->sendErrorResponse('Validation failed', ['error' => $validatorUser->errors()->all()], 401);
            }

            // Create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            // Generate an access token for the user
            $token = $user->createToken('LaravelAuthApp')->accessToken;

            // Prepare user data to return
            $userData = [
                'user' => $user,
                'token' => $token
            ];

            // Return successful response
            return $this->apiResponse->sendSuccessResponse($userData, 'User registered successfully.');
        } catch (Exception $e) {
            // Handle any exceptions that occur during registration
            return $this->apiResponse->sendErrorResponse('Failed to create user', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Authenticate and login the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Define validation rules
        $validatorUser = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required',
        ]);

        // Check if validation fails
        if ($validatorUser->fails()) {
            return $this->apiResponse->sendErrorResponse('Authentication failed', ['error' => $validatorUser->errors()->all()], 401);
        }

        // Attempt to authenticate the user
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Generate an access token for the user
            $logdata = [
                'user' => $user,
                'token' => $user->createToken('LaravelAuthApp')->accessToken,
                'token_type' => 'bearer'
            ];

            // Return successful response
            return $this->apiResponse->sendSuccessResponse($logdata, 'User Logged in successfully!');
        } else {
            // Return error if authentication fails
            return $this->apiResponse->sendErrorResponse('Email and password do not match.', 401);
        }
    }

    /**
     * Logout the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke all tokens associated with the user
        $request->user()->tokens()->delete();

        // Return success message
        return response()->json([
            'message' => 'User logged out successfully!'
        ]);
    }

    /**
     * Display the profile of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showProfile()
    {
        try {
            // Get the currently authenticated user
            $user = Auth::user();

            // Return successful response with user data
            return $this->apiResponse->sendSuccessResponse($user, 'User profile fetched successfully.');
        } catch (Exception $e) {
            // Handle any exceptions that occur during fetching user profile
            return $this->apiResponse->sendErrorResponse('Failed to fetch user profile', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the profile of the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            // Get the currently authenticated user
            $user = Auth::user();
    
            // Define validation rules for updating user profile
            $validatorUser = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    // Ensure email is unique, except for the current user's email
                    'unique:users,email,' . $user->id
                ],
                'password' => 'nullable|string|min:8',
            ]);
    
            // Check if validation fails
            if ($validatorUser->fails()) {
                return $this->apiResponse->sendErrorResponse('Validation failed', ['error' => $validatorUser->errors()->all()], 401);
            }
    
            // Prepare data to update
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }

            // Update user details using Eloquent update method
            $user->update($updateData);
    
            // Return successful response
            return $this->apiResponse->sendSuccessResponse($user, 'User profile updated successfully.');
        } catch (Exception $e) {
            // Handle any exceptions that occur during profile update
            return $this->apiResponse->sendErrorResponse('Failed to update user profile', ['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Delete the account of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAccount()
    {
        try {
            // Get the currently authenticated user
            $user = Auth::user();

            // Revoke all tokens associated with the user
            $user->tokens()->delete();

            // Delete the user from the database
            $user->delete();

            // Return successful response
            return $this->apiResponse->sendSuccessResponse(null, 'User account deleted successfully.');
        } catch (Exception $e) {
            // Handle any exceptions that occur during account deletion
            return $this->apiResponse->sendErrorResponse('Failed to delete user account', ['error' => $e->getMessage()], 500);
        }
    }
}
