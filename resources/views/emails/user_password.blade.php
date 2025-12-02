@component('mail::message')
# Hello {{ $receiver->fname }},

Your account has been created successfully.

Here are your login details:

**Email:** {{ $receiver->email }}

**Password:** {{ $password }}

Please change your password after logging in for the first time.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
