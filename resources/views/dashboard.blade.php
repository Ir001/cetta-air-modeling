<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
    integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
    crossorigin=""/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cetta Air Modeling</h3>
                    </div>
                    <div class="card-body">
                        <form id="form" action="" method="post">
                            <div class="form-group">
                                <label>Project Name</label>
                                <input type="text" name="title" class="form-control">
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <label>Pilih Titik Koordinat Sumber Emisi</label>
                                <button type="btton" onclick="return resetMarker()" class="btn btn-sm btn-danger">Reset Titik</button>
                            </div>
                            <div id="map" class="my-3 rounded" style="min-height: 20rem">Loading...</div>
                            <div id="maps"></div>
                            <button  type="submit" class="btn w-full btn-primary">Submit</button>
                        </form>
                        <label>Response</label>
                        <textarea name="response" placeholder="Response" id="response" rows="10" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
    integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
    crossorigin=""></script>
    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                alert('Pastikan telah menyalakan GPS!')
            }
        }
        var marker = [];
        var map;
        function showPosition(position){
            let lat = position?.coords?.latitude;
            let lon = position?.coords?.longitude;
            map = L.map('map').setView([lat, lon], 13);
            // marker.push(L.marker([lat, lon]).addTo(map));
            // marker[marker.length-1].bindPopup(`${lat}, ${lon}`).openPopup();

            // Personal Access Token
            // pk.eyJ1IjoiaXJ3YW5hbnRvbmlvMjcwOCIsImEiOiJja3FtNzB2d3IxNjlqMndwcW9sdmxlZXJmIn0.o2FH-_r7OfnSGXvETn1LWQ
            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiaXJ3YW5hbnRvbmlvMjcwOCIsImEiOiJja3FtNzB2d3IxNjlqMndwcW9sdmxlZXJmIn0.o2FH-_r7OfnSGXvETn1LWQ', {
            // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                attribute : 'Cetta Air'
                // accessToken: 'pk.eyJ1IjoiaXJ3YW5hbnRvbmlvMjcwOCIsImEiOiJja3FtNzB2d3IxNjlqMndwcW9sdmxlZXJmIn0.o2FH-_r7OfnSGXvETn1LWQ'
            }).addTo(map);
            map.on("click", function(e){
                lat = e.latlng.lat
                lon = e.latlng.lng
                marker.push(L.marker([lat, lon]).addTo(map));
                marker[marker.length-1].bindPopup(`SRC${marker.length}`).openPopup();
            })
        }
        function resetMarker(){
            marker.map((marker) => {
                map.removeLayer(marker)
            })
        }
        getLocation()
    </script>
    <script>
        $(document).ready(function(){
            $('#form').submit(function(e){
                e.preventDefault();
                marker.map((map, index)=>{
                    let html = `
                    <input type="hidden" name="lat[${index}]" value="${map._latlng.lat}"/>
                    <input type="hidden" name="lon[${index}]" value="${map._latlng.lng}"/>
                    <input type="hidden" name="label[${index}]" value="${map._popup._content}"/>
                    `
                    $('#maps').append(html)
                })
                // return;
                $.ajax({
                    url : `{{ url('store') }}`,
                    type : 'POST',
                    dataType : 'json',
                    data : $(this).serialize(),
                    success : function(data){
                        if(data.success){
                            $('#response').val(data.data)
                        }
                    }
                })
            })
        })
    </script>
</body>
</html>