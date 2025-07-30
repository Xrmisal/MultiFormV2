<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        
    }
    public function show(Lead $lead)
    {
        // TODO: Create code that gets lead by UUID for returning users who are sent email/message with link with said UUID to continue with form
    }
    public function update(Request $request, Lead $lead)
    {
        
    }
    /*
    public function destroy(Lead $lead)
    {
        $lead->delete();
    }
    */
}
