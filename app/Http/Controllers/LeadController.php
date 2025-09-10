<?php

namespace App\Http\Controllers;

use Propaganistas\LaravelPhone\PhoneNumber;
use Propaganistas\LaravelPhone\Rules\Phone;
use App\Http\Requests\StoreLeadsRequest;
use App\Http\Requests\UpdateLeadRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\LeadResource;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use App\Models\Lead;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Str;

class LeadController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Lead::class, 'lead');
    }
    public function index()
    {
        return response('Not implemented', 501);
    }
    public function store(StoreLeadsRequest $request)
    {
        $data = $request->validated();
        $request->user()->lead
        ? $lead = $request->user()->lead
        : $lead = Lead::where('email', $data['email'])->first();
        $isCompleteLead = $lead ? $lead->status == 'complete' : false;
        
        if ($isCompleteLead) {
            return response('Submission already completed.', 409);
        } else if (!$lead) {
            $lead = new Lead;
            $lead->user_id = $request->user()->id;
        }
        $this->handleImages($data, $lead, $request);
        $lead->fill($data)->save();

        return new LeadResource($lead);
    }

    public function getStatus(Request $request) {
        $user = $request->user();
        return response()->json([
            'leadStatus' => $user->lead->status ?? 'empty'
        ]);
    }

    public function show(Request $request, Lead $lead)
    {
        if ($lead && $lead->status == 'failed') {
            File::delete(storage_path($lead->proof_of_id));
            File::delete(storage_path($lead->proof_of_address));
            $lead->proof_of_id = null;
            $lead->proof_of_address = null;
            $lead->save();
        } else if(!$lead) {
            return response()->json([
                'message' => 'no lead found'
            ], 404);
        }
        return new LeadResource($lead);
    }
    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $data = $request->validated();
        $isFinishedSubmission = $lead->status == 'complete';
        $isFailedSubmission = $lead->status == 'failed';

        if($isFinishedSubmission) {
            return response('Cannot update completed submission', 403);
        }

        if($isFailedSubmission) {
            $lead->status ? $lead->status = false : null;
        }
        $this->handleImages($data, $lead, $request);
        $lead->fill($data)->save();

        return response()->noContent();
    }
    public function destroy(Lead $lead)
    {
        return response('Not implemented', 501);
    }
    private function handleImages(array &$data, Lead $lead, FormRequest $request){
        if ($path = $this->setImageStoreForData('proof_of_id', $lead, $request)) {
            $data['proof_of_id'] = $path;
        }
        if ($path = $this->setImageStoreForData('proof_of_address', $lead, $request)) {
            $data['proof_of_address'] = $path;
        }
    }
    private function setImageStoreForData(string $kind, Lead $lead, FormRequest $request) {
        if ($request->hasFile($kind)) {
            if($lead->{$kind}) Storage::delete($lead->{$kind});
            $path = $this->storeImageForLead($lead, $request->file($kind), $kind);

            return $path;
        } return null;
    }
    private function storeImageForLead(Lead $lead, UploadedFile $file, string $kind) {
        $dir = "images/leads/{$lead->user_id}";
        $ext = strtolower($file->guessExtension() ? : 'bin');
        $name = "{$kind}" . ".{$ext}";

        return $file->storeAs($dir, $name);
    }
}
