<?php

namespace App\Http\Controllers;

use App\Models\AcceptedHouse;
use App\Models\houses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AcceptedHouseController extends Controller
{
    //

    public function index($id){
        try {
            $acceptedHouse = AcceptedHouse::find($id);
            if (!$acceptedHouse) {
                # code...
                return response()->json([
                    'status' => false,
                    'message' => 'No House with this id'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'You have successfully retrieved data',
                'data'=> $acceptedHouse
            ]);
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
            //code...
            $acceptedHouse = AcceptedHouse::find($id);
            if (!$acceptedHouse) {
                # code...
                return response()->json([
                    'status' => false,
                    'message' => 'No House with this id'
                ], 404);
            }

            $acceptedHouse->delete();

            return response()->json([
                'status' => true,
                'message' => 'The House successfully deleted'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function store($id){
        try {
            $house = houses::find($id);

            if (!$house) {
                return response()->json([
                    'status' => false,
                    'message' => 'No house with this ID',
                ], 404);
            }


            AcceptedHouse::create([
                'user_id' => $house->user_id,
                'displayType' => $house->displayType,
                'price' => $house->price,
                'note' => $house->note,
                'neighborhood' => $house->neighborhood,
                'area' => $house->area,
                'width' => $house->width,
                'height' => $house->height,
                'estateType' => $house->estateType,
                'estateStreet' => $house->estateStreet,
                'estateDeed' => $house->estateDeed,
                'images' => $house->images
            ]);


            return response()->json([
                'status' => true,
                'message' => 'You have accepted the house',
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id){
        try {
            $acceptedHouse = AcceptedHouse::where('displayType' , $id)->paginate(10);

            return response()->json([
                'stauts' => false,
                'message' => 'You have successfully retrieved data',
                'data' => $acceptedHouse
            ]);
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function update($id, Request $request){
        try {
            $house = AcceptedHouse::find($id);
    
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
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
