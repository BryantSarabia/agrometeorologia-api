@component('mail::message')
Hello {{$user->name}}, <br>
You have new reports near to your locations!

@component('mail::table')
|    Pest   |      Message    | Distance |
|:----------| :------------- |  ------: |
| col 1 is  |  left-aligned   | $1600    |
| col 2 is  |    centered     |   $12    |
| col 3 is  | right-aligned   |    $1    |
@foreach($reports as $report)
| {{$report->name}}| {{$report->message}} | {{$report->id}} | {{$report->created_at}}
@endforeach
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
