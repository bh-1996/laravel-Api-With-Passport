<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MediaHelper;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Carbon\Exceptions\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    private $imageUpload;

    /**
     * Construct method
     */

     public function __construct(MediaHelper $imageUpload)
     {
        $this->imageUpload = $imageUpload;
     }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $data['posts'] = Post::all();
            if (!$data) {
                return $this->imageUpload->sendErrorResponse('Post not found', 404);
            }
            // Return successful response
            return  $this->imageUpload->sendSuccessResponse($data, 'All post fetched.');

        } catch (Exception $e) {
            return  $this->imageUpload->sendErrorResponse('Failed to create posts', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
        
            $validatorPost = Validator::make(
                $request->all(),
                [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|image|mimes:png,jpg,jpeg,gif',
            ]);
            // Check if validation fails
            if ($validatorPost->fails()) {
                return  $this->imageUpload->sendErrorResponse('Validation failed', ['error' =>  $validatorPost->errors()->all()], 401);
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $this->imageUpload->uploadImage($image);
            }

            $post = Post::create([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $imagePath,
                'user_id' => Auth::id(),
            ]);

            // Return successful response
            $data = [
                'image_url' => asset('images/'.$imagePath),
                'post' => $post
            ];
            return  $this->imageUpload->sendSuccessResponse($data, 'Post created successfully.');
        } catch (Exception $e) {
            return  $this->imageUpload->sendErrorResponse('Failed to create posts', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return $this->imageUpload->sendErrorResponse('Post not found', 404);
        }
        // Return successful response
        return  $this->imageUpload->sendSuccessResponse($post, 'Post fetched successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $post = Post::findOrFail($id);

            if (!$post) {
                return $this->imageUpload->sendErrorResponse('Post not found', 404);
            }

            $validatorPost = Validator::make(
                $request->all(),
                [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|image|mimes:png,jpg,jpeg,gif',
            ]);
            // Check if validation fails
            if ($validatorPost->fails()) {
                return  $this->imageUpload->sendErrorResponse('Validation failed', ['error' =>  $validatorPost->errors()->all()], 401);
            }

            $imagePath = $post->image;
            if ($request->hasFile('image')) {
                $path = public_path() . '/images/';
                // Delete the old image if it exists and is within the 'images' directory
                if ($post->image && $post->image != null) {
                    $old_image = $path . $post->image;
                    if(file_exists($old_image)){
                        unlink($old_image);
                    }
                }
                //Upload image and return name of image
                $image = $request->file('image');
                $imagePath = $this->imageUpload->uploadImage($image);
            }

            // Update the post
            $post->update([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $imagePath,
            ]);
            return  $this->imageUpload->sendSuccessResponse($post, 'Post updated successfully.');
        } catch (Exception $e) {
            return  $this->imageUpload->sendErrorResponse('Failed to update posts', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $post = Post::find($id);

            if (!$post) {
                return $this->imageUpload->sendErrorResponse('Post not found', 404);
            }

            // Delete the image file if it exists
            if ($post->image) {
                $path = public_path() . '/images/';
                // Delete the old image if it exists and is within the 'images' directory
                if ($post->image && $post->image != null) {
                    $old_image = $path . $post->image;
                    if(file_exists($old_image)){
                        unlink($old_image);
                    }
                }
            }

            $post->delete();
            return  $this->imageUpload->sendSuccessResponse($post, 'Post deleted successfully.');
        } catch (Exception $e) {
            return  $this->imageUpload->sendErrorResponse('Failed to create posts', ['error' => $e->getMessage()], 500);
        }
    }
}
