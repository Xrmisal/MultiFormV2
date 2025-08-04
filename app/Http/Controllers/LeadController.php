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
        return response('Not implemented', 501);
    }
    public function store(StoreLeadsRequest $request)
    {
        $email = $request->input('email');

        if (Lead::where('email', $email)->where('complete', true)->exists()) {
            return response('Email already submitted', 409);
        }

        Lead::updateOrCreate(
            ['email' => $email, 'complete' => false],
            $request->validated()
        );

        return response()->noContent();
    }
    public function show(Lead $lead)
    {
        // TODO: Create code that gets lead by UUID for returning users who are sent email/message with link with said UUID to continue with form
        return response('Not implemented', 501);
    }
    public function update(StoreLeadsRequest $request, Lead $lead)
    {
        if($lead->complete) {
            return response('Cannot update completed submission', 403);
        }
        $data = $request->validated();
        $lead->update($data);

        return response()->noContent();
    }
    public function destroy(Lead $lead)
    {
        return response('Not implemented', 501);
    }
}
