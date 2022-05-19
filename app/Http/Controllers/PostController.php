<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('posts.blog', [
            'posts' => Post::latest()->filter(request(['search', 'category', 'author']))->paginate(6)->withQueryString(),
        ]);
    }


    public function show($id)
    {
        return view('posts.post', [
            'post' => Post::findOrFail($id),
        ]);
    }

    public function create()
    {
        return view('posts.create', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $attributes =  $request->validate([
            'title' => 'required|unique:posts',
            'note' => 'required',
            'body' => 'required',
            'category_id' => ['required', Rule::exists('categories', 'id')]
        ]);

        $user = auth()->user();
        $user->posts()->create($attributes);

        return redirect('/');
    }

    public function edit($id){
        return view('posts.update',[
            'post' => Post::find($id)
        ]);
    }

    public function update($id){

        $attributes =  request()->validate([
            'title' => 'required',
            'note' => 'required',
            'body' => 'required',
        ]);

        $post = Post::findOrFail($id);

        $post->title = $attributes['title'];
        $post->note = $attributes['note'];
        $post->body = $attributes['body'];

        $post->save();

        return view('posts.post', [
            'post' => $post,
        ]);
    }

    public function destroy($id){

        $post = Post::findOrFail($id);

        $post->delete();

        return redirect('/');
    }
}
