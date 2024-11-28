<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ManageUserController extends Controller
{
    use ApiResponseTrait;
    public function addAdmin(Request $request)
    {
        $this->authorize('manage-users');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return $this->validationErrorResponse($validate->errors());
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin'
        ]);

        return $this->createdResponse(null,'Admin Added successfully');

    }

    public function addPublisher(Request $request)
    {
        $this->authorize('manage-users');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return $this->validationErrorResponse($validate->errors());
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'publisher'
        ]);

        return $this->createdResponse(null,'publisher Added successfully');
    }

    public function listSubscriber(Request $request)
    {
        $this->authorize('manage-users');
        $users = User::where('role', 'subscriber')->paginate(10);
        return UserResource::collection($users);
    }

    public function listPublishers(Request $request)
    {
        $this->authorize('manage-users');
        $publishers = User::where('role', 'publisher')->paginate(10);
        return UserResource::collection($publishers);
    }

    public function deleteSubscriber($subscriberId)
    {
        $this->authorize('manage-users');
        $user = User::findOrFail($subscriberId);
        $user->delete();
        return $this->deletedResponse('subscriber deleted successfully');
    }

    public function deletePublisher($publisherId)
    {
        $this->authorize('manage-users');
        $publisher = User::findOrFail($publisherId);
        $publisher->delete();
        return $this->deletedResponse('Publisher deleted successfully');
    }

    public function updateSubscriberPassword(Request $request, $subscriberId)
    {
        $this->authorize('manage-users');

        $rules = [
            'password' => 'required|string|min:6|confirmed',
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return $this->validationErrorResponse($validate->errors());
        }

        $user = User::findOrFail($subscriberId);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->updatedResponse('subscriber password updated successfully');
    }

    public function updatePublisherPassword(Request $request, $publisherId)
    {
        $this->authorize('manage-users');

        $rules = [
            'password' => 'required|string|min:6|confirmed',
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return $this->validationErrorResponse($validate->errors());
        }

        $publisher = User::findOrFail($publisherId);

        $publisher->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->updatedResponse('Publisher password updated successfully');
    }

}
