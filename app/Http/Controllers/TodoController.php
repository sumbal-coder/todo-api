<?php
namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use JWTAuth;
// use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TodoController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        // $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            return $this->user->todo()->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate data
        $data = $request->only('title', 'description',);
        $validator = Validator::make($data, [
            'title' => 'required|string',
            'description' => 'required',

        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new todo
        $todo = $this->user->todo()->create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        //Todo created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Todo List created successfully',
            'data' => $todo
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $todo = $this->user->todo()->find($id);
    
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, todo list not found.'
            ], 400);
        }
    
        return $todo;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function edit(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validate data
        $data = $request->only('title', 'description');
        $validator = Validator::make($data, [
            'title' => 'required|string',
            'description' => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, update todo by finding user's relevant todolist id
        $todo = $this->user->todo()->find($id);
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, todo list not found or does not belong to you.'
            ], 400);
        }
        else{
            $todo = $todo->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            //Todo updated, return success response
            return response()->json([
                'success' => true,
                'message' => 'Todo updated successfully',
                'data' => $todo
            ], Response::HTTP_OK);
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Request is valid, delete todo by finding user's relevant todolist id
        $todo = $this->user->todo()->find($id);
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, todo list not found or does not belong to you.'
            ], 400);
        }

        else{
            $todo->delete();
            return response()->json([
                'success' => true,
                'message' => 'Todo List deleted successfully'
            ], Response::HTTP_OK);
            }
    }
}