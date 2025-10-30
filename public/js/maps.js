let mapIncluir, mapAlterar;
let markerIncluir = null, markerAlterar = null;
let circleIncluir = null, circleAlterar = null;
let geocoder;

// Inicializa todos os mapas
function initMap() {
    geocoder = new google.maps.Geocoder();

    let ExisteIncluirDiv = document.getElementById("div-maps-incluir");
    if (ExisteIncluirDiv) initMapIncluir();

    let ExisteAlterarDiv = document.getElementById("div-maps-alterar");
    if (ExisteAlterarDiv) initMapAlterar();
}

// MAPA DE INCLUIR
function initMapIncluir() {
    const defaultLocation = { lat: -22.1576, lng: -48.9814 };

    mapIncluir = new google.maps.Map(document.getElementById("div-maps-incluir"), {
        zoom: 8,
        center: defaultLocation,
    });

    mapIncluir.addListener("click", function (event) {
        const latitude = event.latLng.lat();
        const longitude = event.latLng.lng();

        document.getElementById("modal_tabela_latitude").value = latitude;
        document.getElementById("modal_tabela_longitude").value = longitude;

        if (markerIncluir) markerIncluir.setMap(null);

        markerIncluir = new google.maps.Marker({
            position: event.latLng,
            map: mapIncluir,
        });

        drawCircle(markerIncluir.getPosition(), "incluir");

        buscarCidade(latitude, longitude, "cidade_regiao_incluir");
    });

    document.getElementById("raio_incluir").addEventListener("input", function () {
        if (markerIncluir) {
            drawCircle(markerIncluir.getPosition(), "incluir");
        }
    });
}

// MAPA DE ALTERAR
function initMapAlterar() {
    const defaultLocation = { lat: -22.1576, lng: -48.9814 };

    mapAlterar = new google.maps.Map(document.getElementById("div-maps-alterar"), {
        zoom: 8,
        center: defaultLocation,
    });

    mapAlterar.addListener("click", function (event) {
        const latitude = event.latLng.lat();
        const longitude = event.latLng.lng();

        document.getElementById("modal_tabela_latitude").value = latitude;
        document.getElementById("modal_tabela_longitude").value = longitude;

        if (markerAlterar) markerAlterar.setMap(null);

        markerAlterar = new google.maps.Marker({
            position: event.latLng,
            map: mapAlterar,
        });

        drawCircle(markerAlterar.getPosition(), "alterar");

        buscarCidade(latitude, longitude, "cidade_regiao_alterar");
    });

    document.getElementById("raio_alterar").addEventListener("input", function () {
        if (markerAlterar) {
            drawCircle(markerAlterar.getPosition(), "alterar");
        }
    });
}

// DESENHA CÍRCULO DEPENDENDO DO MODAL
function drawCircle(position, tipo) {
    let raio = parseFloat(document.getElementById(`raio_${tipo}`).value || 0) * 1000;

    if (tipo === "incluir") {
        if (circleIncluir) circleIncluir.setMap(null);
        circleIncluir = new google.maps.Circle({
            map: mapIncluir,
            center: position,
            radius: raio,
            strokeColor: "#007BFF",
            fillColor: "#007BFF",
            fillOpacity: 0.2,
        });
        mapIncluir.panTo(position);
    } else {
        if (circleAlterar) circleAlterar.setMap(null);
        circleAlterar = new google.maps.Circle({
            map: mapAlterar,
            center: position,
            radius: raio,
            strokeColor: "#28A745",
            fillColor: "#28A745",
            fillOpacity: 0.2,
        });
        mapAlterar.panTo(position);
    }
}

// BUSCA CIDADE DINÂMICA PARA CADA MODAL
function buscarCidade(lat, lng, campoDestino) {
    const latlng = { lat: lat, lng: lng };

    geocoder.geocode({ location: latlng }, function (results, status) {
        if (status === "OK" && results[0]) {
            let cidade = "";
            for (const comp of results[0].address_components) {
                if (comp.types.includes("administrative_area_level_2") ||
                    comp.types.includes("locality")) {
                    cidade = comp.long_name;
                    break;
                }
            }
            document.getElementById(campoDestino).value = cidade || "Cidade não encontrada";
        } else {
            document.getElementById(campoDestino).value = "Erro ao buscar";
        }
    });
}

// ✅ Necessário para o callback da API
window.initMap = initMap;
