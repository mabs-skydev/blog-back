<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Models\Post;
use App\Models\Comment;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with(['users', 'comments.users'])->latest()->get();

        return json()->response([
            'success'   =>  true,
            'posts'     =>  $posts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required',
        ]);
   
        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'errors'   => $validator->errors()
            ], 400);     
        }
        
        $post = auth()->user()->posts()->create($validator->validated());

        return response()->json([
            'success'   => true,
            'post'   => $post
        ]);  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response()->json([
            'success'   => true,
            'post'      =>  $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required',
        ]);
   
        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'errors'   => $validator->errors()
            ], 400);     
        }
        
        $post->update($validator->validated());

        return response()->json([
            'success'   => true,
            'post'   => $post
        ]);  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if (auth()->user()->cannot('delete', $post)) {
            return response()->json([
                'success'   => false,
                'message'   => 'Unauthorized.'
            ], 403); 
        }

        $post->delete();

        return response()->json([
            'success'   => true,
            'post'      =>  $post,
        ]);
    }

    /**
     * Store a newly created post comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function comment(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required',
        ]);
   
        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'errors'   => $validator->errors()
            ], 400);     
        }
        
        $comment = Comment::create([
            'user_id'   =>  auth()->id(),
            'post_id'   =>  $post->id,
            'body'      =>  $request->body
        ]);

        return response()->json([
            'success'   => true,
            'comment'   => $comment
        ]);  
    }

    /**
     * Remove the specified comment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyComment(Comment $comment)
    {
        if (auth()->user()->cannot('delete', $comment)) {
            return response()->json([
                'success'   => false,
                'message'   => 'Unauthorized.'
            ], 403); 
        }

        $comment->delete();

        return response()->json([
            'success'   => true,
            'comment'      =>  $comment,
        ]);
    }
}
