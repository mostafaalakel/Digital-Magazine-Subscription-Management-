<?php

namespace App\Http\Controllers;

use App\Http\Resources\AllSubscriptionsResource;
use App\Http\Resources\UserSubscriptionsResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Magazine;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    use ApiResponseTrait;

    public function createSubscription(Request $request)
    {
        $this->authorize('create', Subscription::class);
        $rules = [
            'magazine_id' => 'required|exists:magazines,id',
            'type' => 'required|in:monthly,yearly',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }
        if (Subscription::where('user_id', auth()->id())->where('magazine_id', $request->magazine_id)->exists()) {
            return $this->forbiddenResponse('you already subscribed to this magazine');
        }

        $magazine = Magazine::findOrFail($request->magazine_id);

        $startDate = now();
        $endDate = $request->type === 'monthly'
            ? $startDate->copy()->addMonth()
            : $startDate->copy()->addYear();

        $subscription = Subscription::create([
            'user_id' => auth()->id(),
            'magazine_id' => $magazine->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'type' => $request->type,
        ]);

        return $this->createdResponse([
            'subscription_id' => $subscription->id,
            'type' => $subscription->type,
            'price' => $this->getSubscriptionPrice($magazine, $request->type),
        ], 'Subscription created successfully. Proceed to payment.');
    }


    //Update subscription (Admin Only)
    public function updateSubscription(Request $request, $subscription_id)
    {
        $this->authorize('manage', Subscription::class);

        $rules = [
            'type' => 'in:monthly,yearly',
            'status' => 'required|in:pending,active,expired',
        ];

        $validator = Validator::make($request->only(['type', 'status']), $rules);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $subscription = Subscription::findOrFail($subscription_id);

        $data = $request->only(['status']);


        if (isset($request->type)) {
            $data['type'] = $request->type;
            $end_date = Carbon::parse($subscription->start_date)->addMonths($request->type === 'monthly' ? 1 : 12);
            $data['end_date'] = $end_date;
        }

        $subscription->update($data);

        return $this->updatedResponse(null, 'Subscription updated successfully.');
    }

    //Delete subscription (Admin Only)
    public function deleteSubscription($subscription_id)
    {
        $this->authorize('manage', Subscription::class);

        $subscription = Subscription::findOrFail($subscription_id);
        $subscription->delete();

        return $this->deletedResponse('Subscription deleted successfully.');
    }

    // filter results of Subscription in dynamic way (admin only)
    public function filterSubscription(Request $request)
    {
        $this->authorize('manage', Subscription::class);

        $query = Subscription::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $subscriptions = $query->with(['user', 'magazine'])->paginate(10);
        return AllSubscriptionsResource::collection($subscriptions);

    }


//      List user subscriptions (User Only)
    public function listUserSubscriptions()
    {
        $subscriptions = Subscription::where('user_id', auth()->id())
            ->with('magazine')
            ->get();

        if ($subscriptions->isEmpty()) {
            return $this->notFoundResponse('You have not any subscriptions');
        }

        return $this->retrievedResponse(
            UserSubscriptionsResource::collection($subscriptions),
            'User subscriptions retrieved successfully.'
        );
    }


    // Calculate subscription price
    private function getSubscriptionPrice($magazine, $type)
    {
        return $type === 'monthly'
            ? $magazine->monthly_price
            : $magazine->yearly_price;
    }
}
