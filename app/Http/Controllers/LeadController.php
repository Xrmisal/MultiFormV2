<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadsRequest;
use App\Http\Resources\LeadResource;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Models\Lead;
use Illuminate\Http\Client\Request;
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
            return response('Email already exists', 409);
        }

        $lead = Lead::updateOrCreate(
            ['email' => $email, 'complete' => false],
            $request->validated()
        );

        return new LeadResource($lead);
    }

    public function show(Lead $lead)
    {
        return new LeadResource($lead);
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
