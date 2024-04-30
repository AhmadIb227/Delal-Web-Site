<?php

namespace App\Http\Controllers;

use App\Models\favroit;
use App\Models\houses;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class housesController extends Controller
{
    //

    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'images.*' => 'image|mimes:jpeg,png,jpg',
                'neighborhood' => 'required',
                'area' => 'required',
                'width' => 'required',
                'height' => 'required',
                'estateType' => 'required',
                'estateStreet' => 'required',
                'estateDeed' => 'nullable',
                'price' => 'required',
                'displayType' => 'required',
                'note' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 201);
            }
    
            $user = auth()->user();
    
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $imageUrls = [];
    
                foreach ($images as $image) {
                    $path = $image->store('public/images');
                    $url = Storage::url($path);
                    $imageUrls[] = $url;
                }
    
                $imagesString = json_encode($imageUrls, JSON_UNESCAPED_SLASHES);
    
                $data = houses::create([
                    'user_id' => $user->id,
                    'images' => $imagesString,
                    'area' => $request->input('area'),
                    'neighborhood' => $request->input('neighborhood'),
                    'width' => $request->input('width'),
                    'height' => $request->input('height'),
                    'estateType' => $request->input('estateType'),
                    'estateStreet' => $request->input('estateStreet'),
                    'estateDeed' => $request->input('estateDeed'),
                    'displayType' => $request->input('displayType'),
                    'note' => $request->input('note'),
                    'price' => $request->input('price'),
                ]);
    
                return response()->json([
                    'status' => true,
                    'message' => 'The house has been uploaded',
                    'data' => $data
                ], 200);
            }
    
            return response()->json([
                'status' => false,
                'message' => 'Please upload an image',
            ], 202);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    

    public function show($displayType)
    {
        try {
            $user = auth()->user();
    
            $userId = $user ? $user->id : null;
    
            $houses = houses::where('displayType', $displayType)->paginate(5);
    
            // Get the IDs of all favorited houses by the user if authenticated
            $favoritedHouseIds = $user ? favroit::where('user_id', $user->id)->pluck('house_id')->toArray() : [];
    
            // Loop through each house and add a new field 'is_favorited' indicating if it is favorited
            foreach ($houses as $house) {
                $house->is_favorited = $user ? in_array($house->id, $favoritedHouseIds) : false;
            }
    
            return response()->json([
                'status' => true,
                'message' => 'You have successfully retrieved data',
                'user_id' => $userId,
                'data' => $houses,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
    
    

    public function showAll()
    {
        try {
            $data = houses::paginate(15);
    
            return response()->json([
                'status' => true,
                'message' => 'You have successfully retrieved data',
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
    

    public function delete($id){
        try {

            $house = houses::find($id);

            if (!$house) {
                # code...
                return response()->json([
                    'status' => false,
                    'message' => 'No item for this ID',
                ], 404);
            }

            $files = array($house->images);
            
            File::delete($files);

            $house->delete();

            return response()->json([
                'status'=> true,
                'message' => 'the iteam is succsfully deleted'
            ]);
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statue' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function index($id){
        try {

            $house = houses::find($id);

            if (!$house) {
                # code...
                return response()->json([
                    'status' => false,
                    'message' => 'No item for this ID',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'You have successfully retrieved data',
                'data' => $house
            ]);
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statue' => false,
                'message' => $th->getMessage()
            ]);
        }
    }


    
    public function update($id, Request $request){
        try {
            $house = houses::find($id);
    
            if (!$house) {
                return response()->json([
                    'status'=>false,
                    'message'=>'No item for this ID'
                ]);
            }
    
            $validator = Validator::make($request->all(),[
                'images.*' => 'image|mimes:jpeg,png,jpg',
                'neighborhood' => 'required',
                'width' => 'required',
                'height' => 'required',
                'estateType' => 'required',
                'estateStreet' => 'required',
                'estateDeed' => 'nullable',
                'price' => 'required',
                'displayType'=>'required',
                'note' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 201);
            }
    
            // Update house details
            $house->neighborhood = $request->input('neighborhood');
            $house->width = $request->input('width');
            $house->height = $request->input('height');
            $house->estateType = $request->input('estateType');
            $house->estateStreet = $request->input('estateStreet');
            $house->estateDeed = $request->input('estateDeed');
            $house->price = $request->input('price');
            $house->displayType = $request->input('displayType');
            $house->note = $request->input('note');

            // Handle image editing and deletion
            if ($request->has('images')) {
                $images = $request->file('images');
                $imageUrls = [];
    
                foreach ($images as $image) {
                    $path = $image->store('public/images');
                    $url = Storage::url($path);
                    $imageUrls[] = $url;
                }
    
                // Replace existing images with new ones
                $house->images = $imageUrls;
            }
    
            // Handle deleted images
            if ($request->has('deletedImages')) {
                $deletedImages = $request->input('deletedImages');
    
                foreach ($deletedImages as $deletedImageIndex) {
                    if (isset($house->images[$deletedImageIndex])) {
                        // Delete the image from storage
                        Storage::delete(str_replace('storage', 'public', $house->images[$deletedImageIndex]));
    
                        // Remove the image from the images array
                        unset($house->images[$deletedImageIndex]);
                    }
                }
    
                // Re-index the images array
                $house->images = array_values($house->images);
            }
    
            $house->save();
    
            return response()->json([
                'status'=>true,
                'message'=>'The house has been updated',
                'data' => $house
            ], 200);
    
        } catch (\Throwable $th) {
            return response()->json([
                'status' =>false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
