<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $post=Post::all();

      return view('index',compact('post'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('post');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    
    {
       $Post=new Post;
       $this->validate($request,['title'=>'required','body'=>'required']);
       $Post->title=$request->title;
       var_dump($request->title);
       $Post->body=$request->body;
       $Post->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post=Post::find($id);
        return view('edit',compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


 



 // $Post=new Post;
 //       $this->validate($request,['title'=>'required','body'=>'required']);
 //       $Post->title=$request->title;
 //       var_dump($request->title);
 //       $Post->body=$request->body;
 //       $Post->save();

        
       $Post=Post::find($id);
       // dd($Post);die;
       // $this->validate($request,['title'=>'required','body'=>'required']);
       $Post->title=$request->title;
       
       $Post->body=$request->body;
       $Post->save();
       return redirect('/post');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        $Post=Post::find($id);
        $Post->delete();
         return redirect('/post');
        // return redirect()->route('post.index');
    }
}
