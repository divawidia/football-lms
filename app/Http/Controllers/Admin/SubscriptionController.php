<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionTaxRequest;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class SubscriptionController extends Controller
{
    private SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()){
            return $this->subscriptionService->index();
        }

        return view('pages.admins.payments.subscriptions.index');
    }

    public function playerIndex()
    {
        $user = $this->getLoggedUser();
        return $this->subscriptionService->playerIndex($user);
    }


    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        $data = $this->subscriptionService->show($subscription);
        if (\request()->ajax()){
            return response()->json(['data' => $data, 'message' => 'Successfully retrieve players subscription data']);
        }
        return view('pages.admins.payments.subscriptions.detail', [
            'data' => $data
        ]);
    }

    public function invoices(Subscription $subscription)
    {
        return $this->subscriptionService->invoices($subscription);
    }

    public function setScheduled(Subscription $subscription){
        $this->subscriptionService->scheduled($subscription, $this->getLoggedUserId(), $this->getAcademyId());

        $text = $subscription->product->productName.' subscription of '.$subscription->user->firstName.' '.$subscription->user->lastName.' status successfully mark as scheduled';
        Alert::success($text);
        return redirect()->route('subscriptions.show', $subscription->id);
    }

    public function setUnsubscribed(Subscription $subscription){
        $this->subscriptionService->unsubscribed($subscription);

        $text = $subscription->product->productName.' subscription of '.$subscription->user->firstName.' '.$subscription->user->lastName.' status successfully mark as unsubscribed';
        Alert::success($text);
        return redirect()->route('subscriptions.show', $subscription->id);
    }

    public function createNewInvoice(Subscription $subscription){
        $this->subscriptionService->createNewInvoice($subscription, $this->getLoggedUserId(), $this->getAcademyId());

        $text = $subscription->product->productName.' invoice subscription of '.$subscription->user->firstName.' '.$subscription->user->lastName.' successfully renewed';
        Alert::success($text);
        return redirect()->route('subscriptions.show', $subscription->id);
    }

    public function create()
    {
        $data = $this->subscriptionService->create();
        return view('pages.admins.payments.subscriptions.create', [
            'taxes' => $data['taxes'],
            'contacts' => $data['players'],
        ]);
    }
    public function store(SubscriptionRequest $request)
    {
        $data = $request->validated();
        $loggedUserId = $this->getLoggedUserId();
        $academyId = $this->getAcademyId();
        $result = $this->subscriptionService->store($data, $loggedUserId, $academyId);

        $text = 'Subscription of  '.$result->invoiceNumber.' successfully created';
        Alert::success($text);
        return redirect()->route('invoices.show', $result->id);
    }

    public function getAvailablePlayerSubscriptionProduct(Request $request){
        $userId = $request->query('userId');
        try {
            $data = $this->subscriptionService->getAvailablePlayerSubscriptionProduct($userId);

            return response()->json([
                'data' => $data,
                'message' => 'Successfully retrieve data'
            ]);
        }catch (Exception $e) {
            Log::error('Error retrieving available player subscription product data : ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while retrieving available player subscription product data : '. $e->getMessage()], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionTaxRequest $request, Subscription $subscription)
    {
        $data = $request->validated();
        try {
            $data = $this->subscriptionService->updateTax($data, $subscription);

            return response()->json([
                'data' => $data,
                'message' => 'Successfully updating subscriptions tax data'
            ]);
        }catch (Exception $e) {
            Log::error('Error updating subscriptions tax data : ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating subscriptions tax data : '. $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        try {
            $data = $this->subscriptionService->destroy($subscription);

            return response()->json([
                'data' => $data,
                'message' => 'Successfully deleted subscription data'
            ]);
        }catch (Exception $e) {
            Log::error('Error deleting subscription data : ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while deleting subscription data : '. $e->getMessage()], 500);
        }
    }
}
