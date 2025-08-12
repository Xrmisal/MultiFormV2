<?php

namespace App\Http\Controllers;

use Propaganistas\LaravelPhone\PhoneNumber;
use Propaganistas\LaravelPhone\Rules\Phone;
use App\Http\Requests\StoreLeadsRequest;
use App\Http\Requests\UpdateLeadRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\LeadResource;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use App\Models\Lead;
use Exception;
use Str;

class LeadController extends Controller
{
    public function index()
    {
        return response('Not implemented', 501);
    }
    public function store(StoreLeadsRequest $request)
    {
        $data = $request->validated();

        if (Lead::where('email', $data['email'])->where('complete', true)->exists() || Lead::where('email', $data['email'])->where('failed', true)->exists()) {
            return response('Email already exists', 409);
        }

        if ($request->hasFile('proof_of_id')) {
            $path = $this->storeImageForLead($data, $request->file('proof_of_id'), 'proof_of_id');

            if($data->proof_of_id) Storage::delete($data->proof_of_id);

            $data['proof_of_id'] = $path;
        } else unset($data['proof_of_id']);

        if ($request->hasFile('proof_of_address')) {
            $path = $this->storeImageForLead($data, $request->file('proof_of_address'), 'proof_of_address');

            if($data->proof_of_address) Storage::delete($data->proof_of_address);

            $data['proof_of_address'] = $path;
        } else unset($data['proof_of_address']);
        

        if ($lead = Lead::where('email', $data['email'])->first()) {
            $lead->fill($data)->save();
            return new LeadResource($lead);
        }
        $lead = Lead::create($data);

        return new LeadResource($lead);
    }

    public function show(Lead $lead)
    {
        if ($lead->complete && $lead->failed) {
            $lead->complete = false;
            File::delete(storage_path($lead->proof_of_id));
            File::delete(storage_path($lead->proof_of_address));
            $lead->proof_of_id = null;
            $lead->proof_of_address = null;
            $lead->save();
            return new LeadResource($lead);
        } else {
            return response('Cannot get submission details', 403);
        }        
    }
    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $data = $request->validated();
        if($lead->complete && !$lead->failed) {
            return response('Cannot update completed submission', 403);
        }

        if($data['complete'] == true) {
            Log::info('Complete if hit');
            $lead->complete = true;
            $lead->save();
            return response()->noContent();
        }

        if ($request->hasFile('proof_of_id')) {
            $path = $this->storeImageForLead($lead, $request->file('proof_of_id'), 'proof_of_id');

            if($lead->proof_of_id) Storage::delete($lead->proof_of_id);

            $data['proof_of_id'] = $path;
        } else unset($data['proof_of_id']);

        if ($request->hasFile('proof_of_address')) {
            $path = $this->storeImageForLead($lead, $request->file('proof_of_address'), 'proof_of_address');

            if($lead->proof_of_address) Storage::delete($lead->proof_of_address);

            $data['proof_of_address'] = $path;
        } else unset($data['proof_of_address']);

        $lead->fill($data)->save();

        return response()->noContent();
    }
    public function destroy(Lead $lead)
    {
        return response('Not implemented', 501);
    }


    private function storeImageForLead(Lead $lead, UploadedFile $file, string $kind) {
        $dir = "images/leads/{$lead->id}";
        $ext = strtolower($file->guessExtension() ? : 'bin');
        $name = "{$kind}_" . ".{$ext}";

        return $file->storeAs($dir, $name);
    }
}
