<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscriber;
use App\Models\UserInquiry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InquiryController extends Controller
{
    public function index(Request $request)
    {

        $searchKey = $request->searchKey;

        $inquiries = UserInquiry::getInquiriesForFilters($searchKey);

        return view('admin.inquiry.all_inquiries', compact('inquiries', 'searchKey'));
    }


    public function subscribersList()
    {
        $hasPermission = Auth::user()->hasPermission('view_inquiries');

        if ($hasPermission) {
            $subscribersList =  Subscriber::loadAllSubscribers();
            return view('admin.subscribers.subscriber_list', compact('subscribersList'));
        }
    }
}
