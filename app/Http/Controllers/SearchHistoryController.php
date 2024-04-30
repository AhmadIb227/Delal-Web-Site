<?php

namespace App\Http\Controllers;

use App\Models\AcceptedHouse;
use App\Models\houses;
use App\Models\SearchHistory;
use Illuminate\Http\Request;

class SearchHistoryController extends Controller
{
    //

    public function search(Request $request){

        try {
            //code...
            $neighborhood = $request->input('neighborhood');

            $user = auth()->user();
    
            $houses = AcceptedHouse::where('neighborhood', 'LIKE', '%' . $neighborhood . '%')->paginate(15);
            SearchHistory::create([
                'query' => $neighborhood,
                'user_id' => $user->id
            ]);
        
            return response()->json([
                'status' => true,
                'message' => 'You have successfully retrieved data',
                'data' => $houses
            ], 200 );
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status'=>false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show(){
        try {
            $user = auth()->user();
            $history = SearchHistory::where('user_id',$user->id)->paginate(15);

            return response()->json([
                'status' => false,
                'message' => 'You have successfully retrieved data',
                'data' => $history,
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message'=> $th->getMessage()
            ], 500);
        }
    }

    public function delete($id){
        try {
            $search = SearchHistory::find($id);
            $user = auth()->user();

            $userId = $user->id;

            if (!$search) {
                # code...
                return response()->json([
                    'status' => false,
                    'message' => 'No Iteam for this ID'
                ], 404);
            }

            if ($userId != $search->user_id) {
                # code...
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete!',
                ], 202);
            }


            $search->delete();

            return response()->json([
                'status' => true,
                'message' => 'You have delete the Iteam',
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
