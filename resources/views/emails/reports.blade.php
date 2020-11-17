@component('mail::message')
Hello {{$user->name}}, <br>
You have new reports near to your locations!

@component('mail::table')
|    Pest   |      Message    | Reported at |
|:----------| :-------------: |  :------: |
@foreach($reports as $report)
| {{$report->name}}| {{$report->message}} | {{$report->created_at}}
@endforeach
@endcomponent

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
