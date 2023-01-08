<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TodoItem;
use Illuminate\Support\Carbon;
use Validator;
use Illuminate\Support\Facades\Auth;

class TodoItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get authenticate user
        $user = Auth::guard('api')->user();
        // check user exist or not
        if ($user) {
            return response()->json([
                "status" => "success",
                "statusCode" => 200,
                "message" => "Todo Item Fetched",
                "items" => TodoItem::where("user_id",$user->id)->orderBy('created_at', 'desc')->paginate(10)
            ],200);
        }else{
            // return response please login first
            return response()->json([
                "status" => "error",
                "statusCode" => 200,
                "message" => "Please Login First"
            ],200);
        }
    }
    /**
     * Display a listing of the resource search by title.
     *
     * @return \Illuminate\Http\Response
     */
    protected function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => ['required']
        ]);
        // when validation fail
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // get authenticate user
        $user = Auth::guard('api')->user();
        // check user exist or not
        if ($user) {
            return response()->json([
                "status" => "success",
                "statusCode" => 200,
                "message" => "Todo Item Fetched",
                "items" => TodoItem::where("user_id",$user->id)
                            ->where('title', 'LIKE', '%' . $request['query']. '%')
                            ->orderBy('created_at', 'desc')->paginate(10)
            ],200);
        }else{
            // return response please login first
            return response()->json([
                "status" => "error",
                "statusCode" => 200,
                "message" => "Please Login First"
            ],200);
        }
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
        $validator = Validator::make($request->all(), [
            'title' => ['required','max:255'],
            'description' => ['required']
        ]);
        // when validation fail
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // get authenticate user
        $user = Auth::guard('api')->user();
        // check user exist or not
        if ($user) {
            // create todo item
            $newTodoItem = TodoItem::create([
                "user_id"=>$user->id,
                "title"=>$request->title,
                "description"=>$request->description
            ]);
            return response()->json([
                "status" => "success",
                "statusCode" => 200,
                "message" => "Todo Item Created Successfully",
                "item" => $newTodoItem
            ],200);
        }else{
            // return response please login first
            return response()->json([
                "status" => "error",
                "statusCode" => 200,
                "message" => "Please Login First"
            ],200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // get authenticate user
        $user = Auth::guard('api')->user();
        // check user exist or not
        if ($user) {
            // find todo item against id
            $todoItem = TodoItem::where("id",$id)->select("id","title","description")->first();
            if ($todoItem) {
                return response()->json([
                    "status" => "success",
                    "statusCode" => 200,
                    "message" => "Todo Item Fetched Successfully",
                    "item" => $todoItem
                ],200);
            }else{
                // return response item not found against id
                return response()->json([
                    "status" => "error",
                    "statusCode" => 200,
                    "message" => "Todo Item not found"
                ],200);
            }
        }else{
            // return response please login first
            return response()->json([
                "status" => "error",
                "statusCode" => 200,
                "message" => "Please Login First"
            ],200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            'description' => ['required']
        ]);
        // when validation fail
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // get authenticate user
        $user = Auth::guard('api')->user();
        // check user exist or not
        if ($user) {
            // find todo item against id
            $todoItem = TodoItem::find($id);
            if ($todoItem) {
                // update todo item
                $todoItem->update($request->all());
                return response()->json([
                    "status" => "success",
                    "statusCode" => 200,
                    "message" => "Todo Item Updated Successfully",
                    "item" => $todoItem
                ],200);
            }else{
                // return response item not found against id
                return response()->json([
                    "status" => "error",
                    "statusCode" => 200,
                    "message" => "Todo Item not found"
                ],200);
            }
        }else{
            // return response please login first
            return response()->json([
                "status" => "error",
                "statusCode" => 200,
                "message" => "Please Login First"
            ],200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // get authenticate user
        $user = Auth::guard('api')->user();
        // check user exist or not
        if ($user) {
            // find todo item against id
            $todoItem = TodoItem::find($id);
            // check todo item exist or not
            if ($todoItem) {
                // delete todo item
                $todoItem->delete();
                return response()->json([
                    "status" => "success",
                    "statusCode" => 200,
                    "message" => "Todo Item Deleted Successfully"
                ],200);
            }else{
                // return response item not found
                return response()->json([
                    "status" => "error",
                    "statusCode" => 404,
                    "message" => "Todo Item Not Found"
                ],200);
            }
        }else{
            // return response please login first
            return response()->json([
                "status" => "error",
                "statusCode" => 200,
                "message" => "Please Login First"
            ],200);
        }
    }
}
