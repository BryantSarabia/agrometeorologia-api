https://agroambiente.regione.abruzzo.it/api/aedita_meteo/get_data?id_station={{$id_station}}&date_from=@if(isset($from)){{$from}}@else{{date('Y-m-d', strtotime(date('Y-m-d') . ' - 1 month'))}}@endif&date_to=@if(isset($to)){{$to}}@else{{date('Y-m-d')}}@endif