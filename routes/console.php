<?php

use App\Mail\LeadFailed;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fail:lead {id}', function (string $id) {
    $lead = Lead::find($id);
    if (!$lead) {
        return $this->error('Lead with ID ' . $id . ' does not exist.');
    } else if($lead->failed) {
        return $this->error('Lead with ID ' . $id . ' has already been failed.');       
    } else if(!$lead->complete) {
        return $this->error('Lead with ID ' . $id . ' has not been completed and therefore cannot be failed');
    } else {
        $lead->failed = true;
        $lead->save();

        $this->comment('Lead with ID ' . $id . ' has been failed.');

        Mail::to($lead->email)->send(new LeadFailed($lead));
        $this->comment('Email sent to ' . $lead->email);
    }
    
})->purpose('Fail an ID');
