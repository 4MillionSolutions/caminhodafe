    let map;
    let marker = null;
    let circle = null;
    let geocoder;

    function initMap() {
        const defaultLocation = { lat: -22.1576, lng: -48.9814 };

        // inicializa mapa
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 8,
            center: defaultLocation,
        });

        // inicializa o geocoder
        geocoder = new google.maps.Geocoder();

        // clique no mapa
        map.addListener("click", function (event) {
            const latitude = event.latLng.lat();
            const longitude = event.latLng.lng();

            // Atualiza inputs
            document.getElementById("latitude").value = latitude;
            document.getElementById("longitude").value = longitude;

            // Remove marcador anterior
            if (marker) marker.setMap(null);

            // Adiciona novo marcador
            marker = new google.maps.Marker({
                position: event.latLng,
                map: map,
            });

            // Atualiza círculo
            drawCircle(event.latLng);

            // Busca cidade
            buscarCidade(latitude, longitude);
        });

        // Atualiza o raio quando mudar o campo
        document.getElementById("raio").addEventListener("input", function () {
            if (marker) {
                drawCircle(marker.getPosition());
            }
        });
    }

    function drawCircle(location) {
        const raioKm = parseFloat(document.getElementById("raio").value || 0);

        if (circle) circle.setMap(null);

        circle = new google.maps.Circle({
            strokeColor: "#007BFF",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#007BFF",
            fillOpacity: 0.2,
            map: map,
            center: location,
            radius: raioKm * 1000,
        });

        map.panTo(location);
    }

    // 🔍 Função para buscar cidade a partir da coordenada
    function buscarCidade(lat, lng) {
        const latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };

        geocoder.geocode({ location: latlng }, function (results, status) {
            if (status === "OK") {
                if (results[0]) {
                    let cidade = "";
                    // percorre os componentes do endereço
                    for (const componente of results[0].address_components) {
                        if (componente.types.includes("administrative_area_level_2")) {
                            cidade = componente.long_name;
                            break;
                        }
                        if (componente.types.includes("locality")) {
                            cidade = componente.long_name;
                            break;
                        }
                    }

                    document.getElementById("cidade_regiao").value = cidade || "Cidade não encontrada";
                } else {
                    document.getElementById("cidade_regiao").value = "Nenhum resultado encontrado";
                }
            } else {
                console.error("Erro ao buscar cidade:", status);
                document.getElementById("cidade_regiao").value = "Erro ao buscar cidade";
            }
        });
    }

    // torna global para o callback do script
    window.initMap = initMap;
