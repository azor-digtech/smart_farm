<?php
session_start();
require_once "includes/eng_sw_lang.php";
$lang = $_SESSION['lang'] ?? "en";
$L = $LANGUAGES[$lang];

$user = $_SESSION['user'] ?? null;
$isGuest = isset($_GET['guest']);
if (!$user && !$isGuest) header("Location: login.php");

$tanzania_regions = [
    "Arusha", "Dar es Salaam", "Dodoma", "Geita", "Iringa", "Kagera", "Katavi", "Kigoma",
    "Kilimanjaro", "Lindi", "Manyara", "Mara", "Mbeya", "Morogoro", "Mtwara", "Mwanza",
    "Njombe", "Pemba North", "Pemba South", "Pwani", "Rukwa", "Ruvuma", "Shinyanga", "Simiyu",
    "Singida", "Songwe", "Tabora", "Tanga", "Zanzibar North", "Zanzibar South", "Zanzibar West", "Unguja North", "Unguja South", "Unguja Urban West"
];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $L['weather_alerts'] ?? "Weather Alerts" ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        .main-btn { background: #059669; color: white; border-radius: 1rem;}
        .main-btn:hover { background: #047857;}
        .card {border-radius:1.5rem;}
        .back-btn { border-radius: 50%; }
        .forecast-table th, .forecast-table td { text-align:center; }
        .ai-advice { background:#e6f3ea; border-radius:0.7em; padding:0.7em 1em; margin-top:1.2em; font-size:1.1em;}
    </style>
</head>
<body>
<?php include "includes/nav.php"; ?>
<div class="container py-4">
    <button onclick="window.history.back()" class="btn btn-light mb-3 back-btn"><i class="bi bi-arrow-left"></i></button>
    <div class="bg-white card p-4 animate__animated animate__fadeIn">
        <h4 class="text-green-700 mb-2"><?= $L['weather_alerts'] ?? "Weather Alerts" ?></h4>
        <form class="row g-2 mb-3" id="weatherForm">
            <div class="col-8">
                <select name="region" class="form-control" required>
                    <option value="">Select Region...</option>
                    <?php foreach($tanzania_regions as $r): ?>
                    <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars($r) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-4">
                <button class="btn main-btn w-full" type="submit"><?= $L['get_weather'] ?? "Get Weather" ?></button>
            </div>
        </form>
        <div id="weatherResult"></div>
    </div>
</div>
<script>
document.getElementById('weatherForm').onsubmit = async function(e){
    e.preventDefault();
    let region = this.region.value.trim();
    if(!region) return;
    document.getElementById('weatherResult').innerHTML = '<div class="text-center"><div class="spinner-border text-success"></div></div>';
    let res = await fetch('api/weather.php?region='+encodeURIComponent(region));
    let data = await res.json();
    if(data.temp_C){
        let html = `<div class='animate__animated animate__fadeIn'>
            <h5 class='text-success'>${region} <img src="${data.weatherIconUrl}" style="height:28px;vertical-align:middle"></h5>
            <b>Temperature:</b> ${data.temp_C}&#8451; <b>Feels Like:</b> ${data.feelsLikeC}&#8451;<br>
            <b>Weather:</b> ${data.weatherDesc}<br>
            <b>Wind:</b> ${data.wind_kph} km/h
        </div>`;

        // Forecast table
        if(data.forecast && Array.isArray(data.forecast)){
            html += `<div class="mt-3"><b>7 Day Forecast:</b>
            <table class="table table-bordered forecast-table mt-2">
                <thead><tr>
                <th>Date</th>
                <th>Min (&deg;C)</th>
                <th>Max (&deg;C)</th>
                <th>Rain (mm)</th>
                <th>Weather</th>
                </tr></thead><tbody>`;
            for(let d of data.forecast){
                let wdesc = "";
                switch(d.wcode){
                    case 0: wdesc="Clear"; break;
                    case 1: wdesc="Mainly clear"; break;
                    case 2: wdesc="Partly cloudy"; break;
                    case 3: wdesc="Overcast"; break;
                    case 45: case 48: wdesc="Fog"; break;
                    case 51: case 53: case 55: wdesc="Drizzle"; break;
                    case 61: case 63: case 65: wdesc="Rain"; break;
                    case 80: case 81: case 82: wdesc="Showers"; break;
                    // you may add more wx codes...
                    default: wdesc="Unknown";
                }
                // ---- FIX: unknown + rain = "Raining" ----
                if(wdesc === "Unknown" && d.precip && parseFloat(d.precip) > 0){
                    wdesc = "Raining";
                }
                html += `<tr>
                    <td>${d.date}</td>
                    <td>${d.min}</td>
                    <td>${d.max}</td>
                    <td>${d.precip}</td>
                    <td>${wdesc}</td>
                </tr>`;
            }
            html += `</tbody></table></div>`;
        }

        // AI forecast advice
        if(data.advice){
            html += `<div class="ai-advice"><b>Farming Advice:</b><br>${data.advice}</div>`;
        }
        document.getElementById('weatherResult').innerHTML = html;
    }else if(data.error){
        document.getElementById('weatherResult').innerHTML = '<div class="text-danger">'+data.error+'</div>';
    }else{
        document.getElementById('weatherResult').innerHTML = '<div class="text-danger">Data unavailable for this region.</div>';
    }
};
</script>
</body>
</html>