<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Helpers\MediaHelper;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Carbon\Exceptions\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
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
     * Display a listing of all posts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve all posts from the database
            $data['posts'] = Post::all();

            // Check if posts were retrieved
            if (!$data['posts']->count()) {
                return $this->apiResponse->sendErrorResponse('Posts not found', 404);
            }

            // Return successful response with posts data
            return $this->apiResponse->sendSuccessResponse($data, 'All posts fetched.');
        } catch (Exception $e) {
            // Handle any exceptions that occur during fetching posts
            return $this->apiResponse->sendErrorResponse('Failed to fetch posts', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created post in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Define validation rules for creating a post
            $validatorPost = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|image|mimes:png,jpg,jpeg,gif',
            ]);

            // Check if validation fails
            if ($validatorPost->fails()) {
                return $this->apiResponse->sendErrorResponse('Validation failed', ['error' => $validatorPost->errors()->all()], 401);
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $this->imageUpload->uploadImage($image);
            }

            // Create new post
            $post = Post::create([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $imagePath,
                'user_id' => Auth::id(),
            ]);

            // Prepare response data with image URL
            $data = [
                'image_url' => asset('images/' . $imagePath),
                'post' => $post
            ];

            // Return successful response
            return $this->apiResponse->sendSuccessResponse($data, 'Post created successfully.');
        } catch (Exception $e) {
            // Handle any exceptions that occur during post creation
            return $this->apiResponse->sendErrorResponse('Failed to create post', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified post.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            // Retrieve the post by its ID
            $post = Post::find($id);

            // Check if post was found
            if (!$post) {
                return $this->apiResponse->sendErrorResponse('Post not found', 404);
            }

            // Return successful response with post data
            return $this->apiResponse->sendSuccessResponse($post, 'Post fetched successfully.');
        } catch (Exception $e) {
            // Handle any exceptions that occur during fetching the post
            return $this->apiResponse->sendErrorResponse('Failed to fetch post', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified post in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        try {
            // Find the post or fail if it does not exist
            $post = Post::findOrFail($id);

            // Define validation rules for updating a post
            $validatorPost = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:png,jpg,jpeg,gif',
            ]);

            // Check if validation fails
            if ($validatorPost->fails()) {
                return $this->apiResponse->sendErrorResponse('Validation failed', ['error' => $validatorPost->errors()->all()], 401);
            }

            // Handle image update
            $imagePath = $post->image;
            if ($request->hasFile('image')) {
                $path = public_path('images/');
                // Delete old image if it exists
                if ($post->image && file_exists($path . $post->image)) {
                    unlink($path . $post->image);
                }
                // Upload new image
                $image = $request->file('image');
                $imagePath = $this->imageUpload->uploadImage($image);
            }

            // Update the post with new data
            $post->update([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $imagePath,
            ]);

            // Return successful response
            return $this->apiResponse->sendSuccessResponse($post, 'Post updated successfully.');
        } catch (Exception $e) {
            // Handle any exceptions that occur during post update
            return $this->apiResponse->sendErrorResponse('Failed to update post', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified post from the database.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            // Find the post by its ID
            $post = Post::find($id);

            // Check if post was found
            if (!$post) {
                return $this->apiResponse->sendErrorResponse('Post not found', 404);
            }

            // Handle image deletion
            if ($post->image) {
                $path = public_path('images/');
                // Delete old image if it exists
                if (file_exists($path . $post->image)) {
                    unlink($path . $post->image);
                }
            }

            // Delete the post from the database
            $post->delete();

            // Return successful response
            return $this->apiResponse->sendSuccessResponse($post, 'Post deleted successfully.');
        } catch (Exception $e) {
            // Handle any exceptions that occur during post deletion
            return $this->apiResponse->sendErrorResponse('Failed to delete post', ['error' => $e->getMessage()], 500);
        }
    }
}
