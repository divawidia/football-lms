<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSubscriptionTaxRequest;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Services\TaxService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class SubscriptionController extends Controller
{
    private SubscriptionService $subscriptionService;
    private TaxService $taxService;

    public function __construct(SubscriptionService $subscriptionService, TaxService $taxService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->taxService = $taxService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()){
            return $this->subscriptionService->index();
        }

        return view('pages.payments.subscriptions.index', [
            'taxes' => $this->taxService->getAllTaxes(status: '1')
        ]);
    }

    public function playerIndex(): JsonResponse
    {
        return $this->subscriptionService->playerIndex($this->getLoggedUser());
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        return view('pages.payments.subscriptions.detail', [
            'data' => $subscription,
            'taxes' => $this->taxService->getAllTaxes(status: '1')
        ]);
    }

    public function edit(Subscription $subscription): JsonResponse
    {
        return ApiResponse::success($subscription);
    }

    public function invoices(Subscription $subscription): JsonResponse
    {
        return $this->subscriptionService->invoices($subscription);
    }

    public function setScheduled(Subscription $subscription): JsonResponse
    {
        $this->subscriptionService->scheduled($subscription, $this->getLoggedUserId(), $this->getAcademyId());
        return ApiResponse::success(message: $subscription->product->productName.' subscription of '.$this->getUserFullName($subscription->user).' status successfully continued');
    }

    public function setUnsubscribed(Subscription $subscription): JsonResponse
    {
        $this->subscriptionService->unsubscribed($subscription);
        return ApiResponse::success(message: $subscription->product->productName.' subscription of '.$this->getUserFullName($subscription->user).' status successfully mark as unsubscribed');
    }

    public function renewSubscription(Subscription $subscription): JsonResponse
    {
        $this->subscriptionService->renewSubscription($subscription);
        return ApiResponse::success(message: $subscription->product->productName.' subscription of '.$this->getUserFullName($subscription->user).' successfully renewed');
    }

    public function createNewInvoice(Subscription $subscription): RedirectResponse
    {
        $this->subscriptionService->renewSubscription($subscription);
        Alert::success($subscription->product->productName.' invoice subscription of '.$subscription->user->firstName.' '.$subscription->user->lastName.' successfully renewed');
        return redirect()->route('subscriptions.show', $subscription->hash);
    }

    public function getAvailablePlayerSubscriptionProduct(Request $request): JsonResponse
    {
        $userId = $request->query('userId');
        try {
            return ApiResponse::success($this->subscriptionService->getAvailablePlayerSubscriptionProduct($userId));
        }catch (Exception $e) {
            Log::error('Error retrieving available player subscription product data : ' . $e->getMessage());
            return ApiResponse::error('An error occurred while retrieving available player subscription product data : '. $e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionTaxRequest $request, Subscription $subscription): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->subscriptionService->updateTax($data, $subscription);
            return ApiResponse::success(message: 'Successfully updating subscriptions tax data');
        }catch (Exception $e) {
            Log::error('Error updating subscriptions tax data : ' . $e->getMessage());
            return ApiResponse::error('An error occurred while updating subscriptions tax data : '. $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription): JsonResponse
    {
        try {
            $this->subscriptionService->destroy($subscription);
            return ApiResponse::success(message: 'Successfully deleted subscription data');
        }catch (Exception $e) {
            Log::error('Error deleting subscription data : ' . $e->getMessage());
            return ApiResponse::error('An error occurred while deleting subscription data : '. $e->getMessage());
        }
    }
}
