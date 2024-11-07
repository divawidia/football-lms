<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
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

        $text = $subscription->product->productName.' subscription of '.$subscription->user->firstName.' '.$subscription->user->lastName.' status successfully mark as unsubscribed';
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
        $data = $this->subscriptionService->getAvailablePlayerSubscriptionProduct($userId);

        return response()->json([
            'data' => $data,
            'message' => 'Successfully retrieve data'
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
