<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function createPayment(Request $request, $subscriptionId)
    {
        $this->authorize('create', Payment::class);
        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $subscription = Subscription::where('id', $subscriptionId)
                ->where('user_id', $userId)
                ->first();

            if ($subscription->status === 'active') {
                return response()->json(['error' => 'Subscription is already active.'], 400);
            }

            $amount = $subscription->type === 'monthly'
                ? $subscription->magazine->monthly_price
                : $subscription->magazine->yearly_price;

            $provider = new PayPalClient();
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();
            $provider->setAccessToken($paypalToken);

            $response = $this->createPayPalOrder($provider, $amount, $subscription->id, $userId);

            if (isset($response['id']) && $response['status'] === 'CREATED') {
                DB::commit();
                return response()->json([
                    'paypal_order_id' => $response['id'],
                    'approve_url' => $response['links'][1]['href'],
                ], 201);
            } else {
                throw new \Exception('Error in creating PayPal order');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function paymentSuccess(Request $request, $subscriptionId, $userId)
    {
        DB::beginTransaction();
        try {
            $provider = new PayPalClient();
            $provider->setApiCredentials(config('paypal'));
            $token = $provider->getAccessToken();
            $provider->setAccessToken($token);

            $response = $provider->capturePaymentOrder($request->query('token'));

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                if (isset($response['purchase_units'][0]['payments']['captures'][0]['amount']['value'])) {
                    $amount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];

                    $subscription = Subscription::where('id', $subscriptionId)
                        ->where('user_id', $userId)
                        ->first();

                    if (!$subscription) {
                        return response()->json(['error' => 'Subscription not found for the given user and ID.'], 404);
                    }
                    // update subscriptions status and (start_date , end_date ) when pay
                    $subscription->status = 'active';
                    $subscription->start_date = now();
                    $subscription->end_date = $subscription->type === 'monthly'
                        ? now()->addMonth()
                        : now()->addYear();
                    $subscription->save();


                    Payment::create([
                        'user_id' => $userId,
                        'subscription_id' => $subscription->id,
                        'amount' => $amount,
                        'payment_method' => 'PayPal',
                        'payment_date' => now(),
                    ]);

                    DB::commit();
                    return response()->json(['message' => 'Payment successful and subscription activated.'], 200);
                } else {
                    throw new \Exception('Amount not found in PayPal response');
                }
            } else {
                throw new \Exception('Payment not completed');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paymentCancel(Request $request, $subscriptionId)
    {
        return response()->json(['message' => 'Payment canceled.', 'subscription_id' => $subscriptionId], 200);
    }


    private function createPayPalOrder($provider, $amount, $subscriptionId, $userId)
    {
        return $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $amount,
                    ]
                ]
            ],
            "application_context" => [
                "return_url" => route('payment.success', ['subscriptionId' => $subscriptionId, 'userId' => $userId]),
                "cancel_url" => route('payment.cancel', ['subscriptionId' => $subscriptionId]),
            ]
        ]);
    }

    public function showUserPayments() // this is for user
    {
        $this->authorize('view', Payment::class);
        $payments = Payment::where('user_id', Auth::id())->with('subscription')->get();
        return $this->retrievedResponse(PaymentResource::collection($payments), 'payments retrieved successfully.');
    }

    public function showAllPayments() // this is for admin
    {
        $this->authorize('manage', Payment::class);
        $payments = Payment::with('subscription')->paginate(10);
        return PaymentResource::collection($payments);
    }


}
