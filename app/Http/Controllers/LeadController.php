<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadsRequest;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Models\Lead;
use Propaganistas\LaravelPhone\Rules\Phone;

class LeadController extends Controller
{
    public function index()
    {
        return response()->json('Get Hit');
    }
    public function store(StoreLeadsRequest $request)
    {

        if(Lead::where('email', $request->email)->where('complete', true)->exists()) {
            return response('Email already submitted', 409);
        }

        Lead::updateOrCreate(
            ['email' => $request->email, 
            'complete' => false],
            $request->validated()
        );
        
        return response()->noContent();
    }
    public function show(Lead $lead)
    {
        // TODO: Create code that gets lead by UUID for returning users who are sent email/message with link with said UUID to continue with form
        return response()->json('Get individual Hit');
    }
    public function update(StoreLeadsRequest $request, Lead $lead)
    {
        if($lead->complete) {
            return response('Cannot update completed submission', 403);
        }
        $data = $request->validated();
        $lead->updateOrCreate($data);

        return response()->noContent();
    }
    public function destroy(Lead $lead)
    {
        return response()->json('Delete Hit');
    }
}
