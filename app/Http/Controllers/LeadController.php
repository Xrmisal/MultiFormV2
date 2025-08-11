<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadsRequest;
use App\Http\Resources\LeadResource;
use Exception;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Models\Lead;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\File;
use Propaganistas\LaravelPhone\Rules\Phone;
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


        if ($lead = Lead::where('email', $data['email'])->first()) {
            $this->update($request, $lead);
            return new LeadResource($lead);
        }

        if (isset($data['proof_of_id'])) {
            try {
                $relativePath = $this->storeImage($data['proof_of_id']);
                $data['proof_of_id'] = $relativePath;
            } catch(Exception $e) {
                return response($e ,400);
            }
        }

        if (isset($data['proof_of_address'])) {
            try {
                $relativePath = $this->storeImage($data['proof_of_address']);
                $data['proof_of_address'] = $relativePath;
            } catch(Exception $e) {
                return response($e ,400);
            }
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
    public function update(StoreLeadsRequest $request, Lead $lead)
    {
        $data = $request->validated();
        if($lead->complete && !$lead->failed) {
            return response('Cannot update completed submission', 403);
        }
        if(!isset($data['complete'])) {
            if (isset($data['proof_of_id'])) {
                try {
                    $relativePath = $this->storeImage($data['proof_of_id']);
                    $data['proof_of_id'] = $relativePath;
                } catch(Exception $e) {
                    return response($e ,400);
                }
                if($lead->proof_of_id) {
                    $absolutePath = storage_path($lead->proof_of_id);
                    File::delete($absolutePath);
                }
            }

            if (isset($data['proof_of_address'])) {
                try {
                    $relativePath = $this->storeImage($data['proof_of_address']);
                    $data['proof_of_address'] = $relativePath;
                } catch(Exception $e) {
                    return response($e ,400);
                }
                if($lead->proof_of_address) {
                    $absolutePath = storage_path($lead->proof_of_address);
                    File::delete($absolutePath);
                }
            }
        }



        $lead->update($data);

        return response()->noContent();
    }
    public function destroy(Lead $lead)
    {
        return response('Not implemented', 501);
    }

    private function storeImage($image) {
        // base64 check
        if(preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {

            // Take out encoded text without MIME type
            $image = substr($image, strpos($image, ',') + 1);

            // Get file extension
            $type = strtolower($type[1]);

            if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                throw new Exception('invalid image type');
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new Exception('base64_decode failed');
            }
        } else {
            throw new Exception('Image data did not base64 pattern');
        }

        $dir = 'images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = storage_path($dir);
        $relativePath = $dir . $file;
        if(!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($absolutePath . $file, $image);

        return $relativePath;
    }
}
