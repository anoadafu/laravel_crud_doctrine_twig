<?php

namespace App\Http\Controllers;

use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Add pagination if render more than 10 images
        $images = Image::orderBy('created_at','desc')->paginate(12);

        return view('index')->with(compact('images'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'string|required|max:60',
            'description' => 'string|required|max:280',
            'category' => 'string|required|max:25',
            'image' => 'image|required|max:4096', // 4MB Max
        ]);

        // Save uploaded file to local storage
        $file = $request->file('image');
        $storage_path = Storage::disk('public')->put('images', $file);

        // Store image data to DB
        $image = new Image([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'category' => $request->input('category'),
            'storage_path' => $storage_path
        ]);
        $image->save();

        $image->create_thumb();

        return redirect('images')->with('status', 'Image Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $image = Image::findOrFail($id);

        return view('show')->with(compact('image'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $image = Image::findOrFail($id);

        return view('edit')->with(compact('image'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Check image exist
        $image = Image::findOrFail($id);

        $request->validate([
            'title' => 'string|required|max:60',
            'description' => 'string|required|max:280',
            'category' => 'string|required|max:25',
        ]);

        // Update Image data
        $image->title = $request->input('title');
        $image->description = $request->input('description');
        $image->category = $request->input('category');

        $image->save();

        return redirect('images')->with('status', 'Image Edited');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = Image::find($id);
        
        // Check if image exists before deleting
        if (!isset($image)){
            return redirect('/')->with('error', 'No Such Image');
        }

        // Delete Image inctance, image file  and image thumb
        $image->delete();

        return redirect('images')->with('status', 'Image Removed');
    }
    /**
     * Perform search query by title.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $images = Image::where('title', 'LIKE', '%'. $request->q .'%' )->paginate (12);

        return view('search')->with(['images' => $images, 'search_query' => $request->q]);
    }
}
