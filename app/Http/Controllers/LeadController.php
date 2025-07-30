<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadsRequest;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /*
    public function index()
    {
        return Lead::all();
    }
    */
    public function store(StoreLeadsRequest $request)
    {
        Lead::updateOrCreate(
            ['email' => $request->email, 'complete' => false],
            $request->validated()
            );
        return response()->noContent();
    }
    public function show(Lead $lead)
    {
        // TODO: Create code that gets lead by UUID for returning users who are sent email/message with link with said UUID to continue with form
    }
    public function update(StoreLeadsRequest $request, Lead $lead)
    {
        $data = $request->validated();
        $lead->update($data);

        return response()->noContent();
    }
    /*
    public function destroy(Lead $lead)
    {
        $lead->delete();
    }
    */
}
