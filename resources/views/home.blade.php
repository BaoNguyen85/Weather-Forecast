<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Forecast</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <div>
        <div class="text-white d-flex justify-content-center align-items-center" style="height: 5rem; background: #5372F0">
            <h1 style="font-size: 2rem;font-weight:bold">Weather Dashboard</h1>
        </div>
        <div class="py-5 px-5" style="background: #E3F2FD;min-height: 100vh;">
            <div class="row">
                <div class="col-4 px-5">
                    <div class="w-100">
                        <h5 style="font-weight: 600;">Enter a City Name</h5>
                        <form action="{{ URL::to('/') }}" method="GET">
                            @csrf
                            <input name="city" class="w-100 px-3 py-2 rounded-1 mb-4" style="font-size: large; font-weight:400; border:1px solid #dcdcdc" type="text" placeholder="E.g., New York, London, Tokyo">
                            <button type="submit" class="text-white w-100 py-2 rounded-1 border-0" style="background: #5372F0; font-size:large; font-weight:400">Search</button>
                        </form>
                        <div class="d-flex justify-content-center align-items-center">
                            <hr style="width: 45%;" />
                            <p class="px-3 my-3 text-black-50" style="font-weight: 500; font-size:larger">or</p>
                            <hr style="width: 45%;" />
                        </div>
                        <button class="text-white w-100 py-2 rounded-1 border-0" style="background: #6C757D; font-size:large; font-weight:400">Use Current Location</button>
                    </div>
                    <div class="py-5">
                        <form action="{{ URL::to('/subscribe') }}" method="post">
                            @csrf
                            <h5 style="font-weight: 600;">Sign up to receive by email</h5>
                            <input id="email" name="email" class="w-100 px-3 py-2 rounded-1 mb-4" style="font-size: large; font-weight:400; border:1px solid #dcdcdc" type="email" placeholder="Example@gmail.com" required>
                            <input id="location" name="location" class="w-100 px-3 py-2 rounded-1 mb-4" style="font-size: large; font-weight:400; border:1px solid #dcdcdc" type="text" placeholder="E.g., New York, London, Tokyo" required>
                            <div class="pb-3 d-flex align-items-center">
                                <input type="checkbox" name="daily" value="1" required> 
                                <p class="my-2 mx-2" style="font-weight: bold">Receive Daily</p>
                            </div>
                            <?php
                                
                                $message = Session::get('message');
                                if($message){
                                    echo '<div class="pb-3 d-flex align-items-center">',$message,'</div>';
                                    Session::put('message',null);
                                }
                            ?>
                            <button type="submit" class="text-white w-100 py-2 rounded-1 border-0" style="background: #5372F0; font-size:large; font-weight:400">Xác nhận</button>
                        </form>
                    </div>
                </div>
                <div class="col-8 px-5">
                    @if(isset($currentWeather['location']['name']))
                    <div class="text-white d-flex justify-content-between align-items-center rounded-1" style="background: #5372F0;">
                        <div class="px-4 py-3">
                            <h5 class="py-2" style="font-weight: bold;">{{ $currentWeather['location']['name'] }} ({{ $currentWeather['location']['localtime'] }})</h5>
                            <p>Temperature: {{ $currentWeather['current']['temp_c'] }}°C</p>
                            <p>Wind: {{ $currentWeather['current']['wind_mph'] }} M/S</p>
                            <p>Humidity: {{ $currentWeather['current']['humidity'] }} %</p>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-center" style="padding-right: 10%">
                            <img style="width: 8rem" src="{{ $currentWeather['current']['condition']['icon'] }}">
                            <p>{{ $currentWeather['current']['condition']['text'] }}</p>
                        </div>
                    </div>
                    @else
                    <div class="text-white d-flex justify-content-between align-items-center rounded-1" style="background: #5372F0;">
                        <div class="px-4 py-3">
                            <h5 class="py-2" style="font-weight: bold;">Location not found</h5>
                            <p>Temperature: 0°C</p>
                            <p>Wind: 0 M/S</p>
                            <p>Humidity: 0%</p>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-center" style="padding-right: 10%">
                            <p>No data</p>
                        </div>
                    </div>
                    @endif
                    <h4 class="py-3" style="font-weight: bold;">4-Day Forecast</h4>
                    <div class="row w-100" id="weather-data">
                        @if(isset($forecastWeather['forecast']['forecastday']))
                        @foreach($forecastWeather['forecast']['forecastday'] as $key => $day)
                        <div class="col-3 pb-3 {{ $key >= 4 ? ' d-none' : '' }}">
                            <div class="px-3 pt-3 rounded-1" style="width: 12rem; background: #6C757D; color:#FFFFFF">
                                <h6 style="font-weight: bold;">({{ $day['date'] }})</h6>
                                <img class="w-50" src="{{ $day['day']['condition']['icon'] }}">
                                <div class="py-1" style="color: #E8E9EA;">Temp: {{ $day['day']['avgtemp_c'] }}°C</div>
                                <div class="py-1" style="color: #E8E9EA;">Wind: {{ $day['day']['maxwind_kph'] }} M/S</div>
                                <div class="pt-1 pb-3" style="color: #E8E9EA;">Humidity: {{ $day['day']['avghumidity'] }}%</div>
                            </div>
                        </div> 
                        @endforeach
                        @else
                        <div class="col-3 pb-3">
                            <div class="px-3 pt-3 rounded-1" style="width: 12rem; background: #6C757D; color:#FFFFFF">
                                <h6 style="font-weight: bold;">No data</h6>
                                <img class="w-50" src="" alt="No data">
                                <div class="py-1" style="color: #E8E9EA;">Temp: 0°C</div>
                                <div class="py-1" style="color: #E8E9EA;">Wind: 0 M/S</div>
                                <div class="pt-1 pb-3" style="color: #E8E9EA;">Humidity: 0%</div>
                            </div>
                        </div> 
                        @endif
                    </div>

                    @if(isset($forecastWeather['forecast']['forecastday']) && count($forecastWeather['forecast']['forecastday']) > 4)
                        <button id="load-more" class="btn btn-primary mt-3">Xem thêm</button>
                    @endif

                    <h4 class="py-3" style="font-weight: bold;">History</h4>
                    <div class="row w-100" id="history-data">
                        @if(isset($all_history))
                            @foreach($all_history as $key => $history)
                            <div class="col-3 pb-3 {{ $key >= 4 ? ' d-none' : '' }}">
                                <div class="px-3 pt-3 rounded-1" style="width: 12rem; background: #6C757D; color:#FFFFFF">
                                    <h6 style="font-weight: bold;">{{ $history->name }}</h6>
                                    <div class="py-1" style="color: #E8E9EA;">Time: {{ $history->time_now }}</div>
                                    <div class="py-1" style="color: #E8E9EA;">Temp: {{ $history->temp }} °C</div>
                                    <div class="py-1" style="color: #E8E9EA;">Wind: {{ $history->wind }} M/S</div>
                                    <div class="pt-1 pb-3" style="color: #E8E9EA;">Humidity: {{ $history->wind }} %</div>
                                </div>
                            </div> 
                            @endforeach
                        @else 
                        <div class="col-3 pb-3 ">
                            <div class="px-3 pt-3 rounded-1" style="width: 12rem; background: #6C757D; color:#FFFFFF">
                                <h6 style="font-weight: bold;">No history</h6>
                                <div class="py-1" style="color: #E8E9EA;">Time: 0</div>
                                <div class="py-1" style="color: #E8E9EA;">Temp: 0°C</div>
                                <div class="py-1" style="color: #E8E9EA;">Wind: 0 M/S</div>
                                <div class="pt-1 pb-3" style="color: #E8E9EA;">Humidity: 0%</div>
                            </div>
                        </div> 
                        @endif
                    </div>
                    @if(isset($all_history) && count($all_history) > 4)
                        <button id="load-more-history" class="btn btn-primary mt-3">Xem thêm</button>
                    @endif
                </div>
            </div>
        </div>
        
    </div>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var loadMoreButton = document.getElementById('load-more');
        var hiddenItems = document.querySelectorAll('#weather-data .d-none');
        var itemsToShow = 4;
        var currentItems = 4;

        loadMoreButton.addEventListener('click', function() {
            for (var i = currentItems; i < currentItems + itemsToShow; i++) {
                if (hiddenItems[i]) {
                    hiddenItems[i].classList.remove('d-none');
                } else {
                    loadMoreButton.style.display = 'none'; 
                    break;
                }
            }
            currentItems += itemsToShow;
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var loadMoreButton = document.getElementById('load-more-history');
        var hiddenItems = document.querySelectorAll('#history-data .d-none');
        var itemsToShow = 4;
        var currentItems = 0;

        loadMoreButton.addEventListener('click', function() {
            for (var i = currentItems; i < currentItems + itemsToShow; i++) {
                if (hiddenItems[i]) {
                    hiddenItems[i].classList.remove('d-none');
                } else {
                    loadMoreButton.style.display = 'none';
                    break;
                }
            }
            currentItems += itemsToShow;
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</html>