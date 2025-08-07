<x-mail::message>
Hello, due to an error with your submission, we have failed to process your details. Please click the button below to look over details and resubmit proof documents.

<x-mail::button :url="config('app.frontend_url') . '/form/' . $lead->id">
Resubmit
</x-mail::button>

Thanks,<br>
- H
</x-mail::message>
