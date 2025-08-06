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
        file_put_contents($relativePath, $image);

        return $relativePath;
    }
}
