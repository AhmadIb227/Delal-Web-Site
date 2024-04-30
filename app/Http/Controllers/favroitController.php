<?php

namespace App\Http\Controllers;

use App\Models\favroit;
use App\Models\houses;
use Illuminate\Http\Request;

class favroitController extends Controller
{
    //

    public function store($id)
    {
        try {
            $house = houses::find($id);
    
            if (!$house) {
                return response()->json([
                    'status' => false,
                    'message' => 'No item for this ID',
                ], 404);
            }
    
            $user = auth()->user();
    
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'No User!',
                ], 404);
            }
    
            // Check if the house is already favorited by the user
            $existingFavorite = favroit::where('user_id', $user->id)
                ->where('house_id', $house->id)
                ->first();
    
            if ($existingFavorite) {
                return response()->json([
                    'status' => false,
                    'message' => 'The House is already favorited',
                ], 400);
            }
    
            favroit::create([
                'user_id' => $user->id,
                'house_id' => $house->id,
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'The House is inserted to favorites'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    
    public function index($id){
        try {
            $favroit = favroit::find($id);

            if (!$favroit) {
                # code...
                return response()->json([
                    'status' => false,
                    'message' => 'No item for this ID'
                ], 404);
            }

            $house = houses::find($favroit->house_id);

            if (!$house) {
                # code...
                return response()->json([
                    'status' => false,
                    'message' => 'The house is not aviable'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'You have successfully retrieved data',
                'data' => $favroit,
                'house' => $house,
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function delete($id){
        try {
            $favroit = favroit::find($id);

            if (!$favroit) {
                # code...
                return response()->json([
                    'status' => false,
                    'message' => 'No item for this ID'
                ], 404);
            }

            $favroit->delete();

            return response()->json([
                'status' => false,
                'message' => 'The item is successfully deleted'
            ], 200);
            
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function show(){
        try {
            $user = auth()->user();
    
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }
    
            $favroit = favroit::where('user_id', $user->id)->pluck('house_id');
    
            $houses = houses::whereIn('id', $favroit)->get();
    
            return response()->json([
                'status' => true,
                'message' => 'You have successfully retrieved data',
                'data' => $houses,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
    
    public function showAll(){
        try {

            $favroit = favroit::get()->pluck('house_id');

            $houses = houses::whereIn('id',$favroit);

            return response()->json([
                'status' => true,
                'message' => 'You have successfully retrieved data',
                'data' => $houses,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
