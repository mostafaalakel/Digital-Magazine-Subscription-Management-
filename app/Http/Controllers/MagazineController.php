<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Magazine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MagazineController extends Controller
{
    use ApiResponseTrait;

    public function showMagazines()
    {
        $magazines = Magazine::all(['id', 'name', 'description', 'monthly_price' , 'yearly_price' ,'release_date']);
        if($magazines->isEmpty()){
            return $this->notFoundResponse('we dont have any magazines');
        }
        return $this->retrievedResponse($magazines , 'magazines retrieved successfully');
    }

    public function storeMagazine(Request $request)
    {
        $this->authorize('create', Magazine::class);

        $rules=  [
            'name' => 'required|string',
            'description' => 'required|string|max:500',
            'monthly_price' => 'required|numeric',
            'yearly_price' => 'required|numeric',
            'release_date' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return $this->validationErrorResponse($validator->errors());
        }
        Magazine::create([
            'name' => $request->name ,
            'description' => $request->description,
            'monthly_price' => $request->monthly_price,
            'yearly_price' => $request->yearly_price,
            'release_date' => $request->release_date
        ]);
        return $this->createdResponse(null,'Magazine successfully created');
    }


    public function updateMagazine(Request $request  , $magazine_id)
    {
        $this->authorize('update', Magazine::class);

        $rules=  [
            'name' => 'required|string',
            'description' => 'required|string|max:500',
            'monthly_price' => 'required|numeric',
            'yearly_price' => 'required|numeric',
            'release_date' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return $this->validationErrorResponse($validator->errors());
        }
        $magazine = Magazine::findOrFail($magazine_id);

       $magazine->update([
           'name' => $request->name ,
           'description' => $request->description,
           'monthly_price' => $request->monthly_price,
           'yearly_price' => $request->yearly_price,
           'release_date' => $request->release_date
       ]);
        return $this->updatedResponse(null,'Magazine successfully updated');
    }

    public function deleteMagazine($magazine_id)
    {
        $this->authorize('delete', Magazine::class);

        $magazine = Magazine::findOrFail($magazine_id);
        $magazine->delete();
        return $this->deletedResponse('Magazine successfully deleted');
    }
}
