    let map;
    let marker = null;
    let circle = null;

    function initMap() {
        // Local padrão (Brasil)
        const defaultLocation = { lat: -14.2350, lng: -51.9253 };

        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 4,
            center: defaultLocation,
        });

        // Quando o usuário clicar no mapa
        map.addListener("click", function (event) {
            const latitude = event.latLng.lat();
            const longitude = event.latLng.lng();

            // Atualiza inputs
            document.getElementById("latitude").value = latitude;
            document.getElementById("longitude").value = longitude;

            // Remove o marcador anterior
            if (marker) marker.setMap(null);

            // Adiciona novo marcador
            marker = new google.maps.Marker({
                position: event.latLng,
                map: map,
            });

            // Atualiza o círculo de raio
            drawCircle(event.latLng);
        });

        // Atualiza o raio quando o campo for alterado
        document.getElementById("raio").addEventListener("input", function () {
            if (marker) {
                drawCircle(marker.getPosition());
            }
        });
    }

    function drawCircle(location) {
        const raioKm = parseFloat(document.getElementById("raio").value || 0);

        // Remove o círculo anterior
        if (circle) circle.setMap(null);

        // Cria o novo círculo com base no raio informado
        circle = new google.maps.Circle({
            strokeColor: "#007BFF",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#007BFF",
            fillOpacity: 0.2,
            map: map,
            center: location,
            radius: raioKm * 1000, // km → metros
        });

        // Centraliza o mapa no local clicado
        map.panTo(location);
    }
