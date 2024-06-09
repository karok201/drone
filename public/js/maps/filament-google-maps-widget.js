// import { MarkerClusterer } from "@googlemaps/markerclusterer";
// import debounce from "underscore/modules/debounce.js";


var selectedMarker = null;
var infoBoxContainer = document.getElementById('infoBoxContainer');

export default function googleMapsWidget({
  cachedData,
  config,
  mapEl,
}) {
  return {
    map: null,
    bounds: null,
    infoWindow: null,
    mapEl: null,
    data: null,
    markers: [],
    layers: [],
    modelIds: [],
    mapIsFilter: false,
    clusterer: null,
    center: null,
    isMapDragging: false,
    isIdleSkipped: false,
    config: {
      center: {
        lat: 0,
        lng: 0,
      },
      clustering: false,
      controls: {
        mapTypeControl: true,
        scaleControl: true,
        streetViewControl: true,
        rotateControl: true,
        fullscreenControl: true,
        searchBoxControl: false,
        zoomControl: false,
      },
      fit: true,
      mapIsFilter: false,
      gmaps: "",
      layers: [],
      zoom: 12,
      markerAction: null,
      mapConfig: [],
    },

    loadGMaps: function () {
      if (!document.getElementById("filament-google-maps-google-maps-js")) {
        const script = document.createElement("script");
        script.id = "filament-google-maps-google-maps-js";
        window.filamentGoogleMapsAsyncLoad = this.createMap.bind(this);
        script.src =
          this.config.gmaps + "&callback=filamentGoogleMapsAsyncLoad";
        document.head.appendChild(script);
      } else {
        const waitForGlobal = function (key, callback) {
          if (window[key]) {
            callback();
          } else {
            setTimeout(function () {
              waitForGlobal(key, callback);
            }, 100);
          }
        };

        waitForGlobal(
          "filamentGoogleMapsAPILoaded",
          function () {
            this.createMap();
          }.bind(this)
        );
      }
    },

    init: function () {
      this.mapEl = document.getElementById(mapEl) || mapEl;
      this.data = cachedData;
      this.config = { ...this.config, ...config };
      this.loadGMaps();
    },

    callWire: function (thing) {},


    createMap: function () {
      window.filamentGoogleMapsAPILoaded = true;
      this.infoWindow = new google.maps.InfoWindow({
        content: "",
        disableAutoPan: true,
      });

    this.map = new google.maps.Map(this.mapEl, {
        center: this.config.center,
        zoom: this.config.zoom,
        ...this.config.controls,
        ...this.config.mapConfig,
        streetViewControl: false,
    });

        const restrictedZones = [
        [
            //Бериева
            { lat: 47.199500, lng: 38.813998 },
            { lat: 47.206617, lng: 38.819294 },
            { lat: 47.203368, lng: 38.845963 },
            { lat: 47.215413, lng: 38.879534 },
            { lat: 47.202294, lng: 38.889590 },
            { lat: 47.197914, lng: 38.881566 },
            { lat: 47.194994, lng: 38.883636 },
            { lat: 47.187447, lng: 38.878343 },
            { lat: 47.190358, lng: 38.851391 }
        ],

        //Порт
        [
            { lat: 47.195870, lng: 38.933033 },
            { lat: 47.210356, lng: 38.949729 },
            { lat: 47.206145, lng: 38.960286 }, //  <- Уточните эту координату 47.194438, 38.949473
            { lat: 47.194438, lng: 38.949473 }
        ],

        //Металлург ТАГМЕТ
        [
            { lat: 47.241865, lng: 38.940859 },
            { lat: 47.236524, lng: 38.938958 },
            { lat: 47.239339, lng: 38.914900 },//47.239339, 38.914900
            { lat: 47.246904, lng: 38.916994 },//47.246904, 38.916994
            { lat: 47.247887, lng: 38.921105 },//47.247887, 38.921105
            { lat: 47.259129, lng: 38.923129 },//47.259129, 38.923129
            { lat: 47.261428, lng: 38.938661 },
            { lat: 47.254925, lng: 38.938195 },
            { lat: 47.243819, lng: 38.932677 }
        ],

        //КРАСНЫЙ КОТ
        [
            { lat: 47.240939, lng: 38.909756 },
            { lat: 47.236454, lng: 38.897945 },
            { lat: 47.254272, lng: 38.892126 },
            { lat: 47.260487, lng: 38.911115 },
            { lat: 47.255601, lng: 38.913310 },
            { lat: 47.251663, lng: 38.905644 }
        ],

        //ЛЕМАКС
        [
            { lat: 47.256032, lng: 38.877388 },
            { lat: 47.258456, lng: 38.875089 },
            { lat: 47.261231, lng: 38.881521 },
            { lat: 47.257713, lng: 38.884152 }
        ],

        //АЭРОДРОМ
        [
            { lat: 47.231248, lng: 38.860312 },
            { lat: 47.238334, lng: 38.867162 },
            { lat: 47.246311, lng: 38.868363 },
            { lat: 47.256087, lng: 38.857184 },
            { lat: 47.249834, lng: 38.834074 },
            { lat: 47.243535, lng: 38.829184 },
            { lat: 47.234833, lng: 38.815659 },
            { lat: 47.236210, lng: 38.831612 }
        ],

        //КРИСТАЛЛ
        [
            { lat: 47.216430, lng: 38.938019 },
            { lat: 47.215741, lng: 38.934852 },
            { lat: 47.220377, lng: 38.931759 },
            { lat: 47.221234, lng: 38.935404 }
        ],

        //Гидропресс
        [
            { lat: 47.228588, lng: 38.901843 },
            { lat: 47.227685, lng: 38.895939 },//47.227685, 38.895939
            { lat: 47.238033, lng: 38.884947 },//47.238033, 38.884947
            { lat: 47.240235, lng: 38.888924 },//47.240235, 38.888924
            { lat: 47.236888, lng: 38.889272 },//47.236888, 38.889272
            { lat: 47.238423, lng: 38.898589 }
        ]
    ];

    function isPointInPolygon(point, polygon) {
        let isInside = false;
        for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
            const xi = polygon[i].lng, yi = polygon[i].lat;
            const xj = polygon[j].lng, yj = polygon[j].lat;

            const intersect = ((yi > point.lat) !== (yj > point.lat)) &&
                (point.lng < (xj - xi) * (point.lat - yi) / (yj - yi) + xi);
            if (intersect) isInside = !isInside;
        }
        return isInside;
    }

    // --- Функция для генерации координат шестиугольника ---
    google.maps.event.addListener(this.map, 'click', function(event) {
        if (selectedMarker) {
            transition(selectedMarker, [event.latLng.lat(), event.latLng.lng()]);
        }
        infoBoxContainer.style.display = "none";
    });
    function generateHexagonCoordinates(centerLat, centerLon, radius) {
        const points = [];
        for (let i = 0; i < 6; i++) {
            const angleDeg = 60 * i - 30;
            const angleRad = Math.PI / 180 * angleDeg;
            const lat = centerLat + radius * Math.cos(angleRad) * 0.7;
            const lon = centerLon + radius * Math.sin(angleRad);
            points.push(new google.maps.LatLng(lat, lon));
        }
        points.push(points[0]); // Замыкаем шестиугольник
        return points;
    }

    // --- Радиус шестиугольника ---
    const hexagonRadius = 0.003;

    // --- Смещение по вертикали для четных/нечетных рядов ---
    const verticalOffset = hexagonRadius * Math.sqrt(3) / 2 * 0.7;

    // ---  Задаем границы области, где нужно отображать шестиугольники ---
    const bounds = {
        south: 47.04966614389952,
        west: 38.47513875803426,
        north: 47.411946080503235,
        east: 39.13337216704194
    };

        // --- Генерируем сетку шестиугольников ---
    for (let lat = bounds.south; lat <= bounds.north; lat += hexagonRadius * Math.sqrt(3) * 0.7) {
        for (let lon = bounds.west; lon <= bounds.east; lon += hexagonRadius * 1.5) {
            // --- Чередуем смещение для создания структуры сот ---
            const offset = (Math.round((lon - bounds.west) / (hexagonRadius * 1.5)) % 2 === 0) ? 0 : verticalOffset;

            // --- Создаем координаты шестиугольника ---
            const coordinates = generateHexagonCoordinates(lat + offset, lon, hexagonRadius);

            // --- Проверяем, находится ли центр соты в запретной зоне ---
            let isRestricted = false;
            for (const zone of restrictedZones) {
                if (isPointInPolygon({ lat: lat + offset, lng: lon }, zone)) {
                    isRestricted = true;
                    break;
                }
            }

            // --- Устанавливаем цвет соты в зависимости от того, запрещена ли она ---
            const hexagonFillColor = isRestricted ? '#FF0000' : '#00FF00';

            // --- Создаем полигон ---
            const hexagon = new google.maps.Polygon({
                paths: coordinates,
                fillColor: hexagonFillColor, // Зеленый цвет заливки
                fillOpacity: 0.25, // Прозрачность заливки
                strokeWeight: 0.2, // Толщина линии
                strokeColor: '#000000', // Черный цвет линии
                strokeOpacity: 1,
                strokeStyle: 'shortdash'
            });

            // --- Добавляем шестиугольник на карту ---
            hexagon.setMap(this.map);
        }
    }

      this.center = this.config.center;

      this.createMarkers();

      this.createLayers();

      this.idle();

      window.addEventListener(
        "filament-google-maps::widget/setMapCenter",
        (event) => {
          this.recenter(event.detail);
        }
      );

      this.show(true);
    },
    show: function (force = false) {
      if (this.markers.length > 0 && this.config.fit) {
        this.fitToBounds(force);
      } else {
        if (this.markers.length > 0) {
          this.map.setCenter(this.markers[0].getPosition());
        } else {
          this.map.setCenter(this.config.center);
        }
      }
    },
    createLayers: function () {
      this.layers = this.config.layers.map((layerUrl) => {
        return new google.maps.KmlLayer({
          url: layerUrl,
          map: this.map,
        });
      });
    },
    createMarker: function (location) {
      let markerIcon;

      if (location.icon && typeof location.icon === "object") {
        if (location.icon.hasOwnProperty("url")) {
          markerIcon = {
            url: location.icon.url,
          };

          if (
            location.icon.hasOwnProperty("type") &&
            location.icon.type === "svg" &&
            location.icon.hasOwnProperty("scale")
          ) {
            markerIcon.scaledSize = new google.maps.Size(
              location.icon.scale[0],
              location.icon.scale[1]
            );
          }
        }
      }

      const point = location.location;
      const label = location.label;

      const marker = new google.maps.Marker({
        position: point,
        title: label,
        model_id: location.id,
        ...(markerIcon && { icon: markerIcon }),
      });

      if (this.modelIds.indexOf(location.id) === -1) {
        this.modelIds.push(location.id);
      }

        const flightPlanCoordinates = [
            { lat: 47.20255508094605, lng: 38.92590165138245 },
            { lat: 47.201635614931895, lng: 38.93604935255944 },
            { lat: 47.201714659191914, lng: 38.939921288910504 }
        ];

        let plan = []
        for (let i = 0; i < location.path.length; i++) {
            plan[i] = {lat: parseFloat(location.path[i].lng), lng: parseFloat(location.path[i].lat) }
        }


        const flightPath = new google.maps.Polyline({
            path: plan,
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 2,
        });

        flightPath.setVisible(false);


        let startMarker = new google.maps.Marker({
            position:flightPath.getPath().getAt(0),
            map:this.map,
            label: {
                text: 'Начало',
                color: "white",
                fontSize: "12px",
            }
        });

        startMarker.setVisible(false);

        let endMarker =  new google.maps.Marker({
            position:flightPath.getPath().getAt(flightPath.getPath().getLength()-1),
            map:this.map,
            label: {
                text: 'Конец',
                color: "white",
                fontSize: "12px",
            },
        });

        endMarker.setVisible(false);

        flightPath.setMap(this.map);

        marker.addListener("click", function () {
            flightPath.setVisible(!flightPath.getVisible());
            startMarker.setVisible(!startMarker.getVisible());
            endMarker.setVisible(!endMarker.getVisible());
        })
      return marker;
    },

    createMarkers: function () {
      this.markers = this.data.map((location) => {
        const marker = this.createMarker(location);
        marker.setMap(this.map);

          setInterval(updateMarker,2500);

          function updateMarker() {
              console.log(1);
              fetch("/api/drones/getCurrentPosition", {
                  method: "POST",
                  headers: {
                      "Content-type": "application/json; charset=UTF-8"
                  }
              })
                  .then((response) => response.json())
                  .then((json) => {
                      for (let i = 0; i < json.length; i++) {
                          if (json[i].id == marker.model_id) {
                              var result = {latitude: json[i].lat, longitude: json[i].lgt };
                              transition(result);
                          }
                      }
                  });
          }

          var position = [marker.position.lat(), marker.position.lng()];

          var numDeltas = 100;
          var delay = 10; //milliseconds
          var i = 0;
          var deltaLat;
          var deltaLng;
          function transition(result){
              i = 0;
              deltaLat = (result.latitude - position[0])/numDeltas;
              deltaLng = (result.longitude - position[1])/numDeltas;
              moveMarker();
          }

          function moveMarker(){
              position[0] += deltaLat;
              position[1] += deltaLng;
              var latlng = new google.maps.LatLng(position[0], position[1]);
              // console.log(position[0]);
              marker.setPosition(latlng);
              if(i!=numDeltas){
                  i++;
                  setTimeout(moveMarker, delay);
              }
          }


          if (this.config.markerAction) {
          google.maps.event.addListener(marker, "click", (event) => {
            this.$wire.mountAction(this.config.markerAction, {
              model_id: marker.model_id,
            });
          });
        }

        return marker;
      });
    },
    removeMarker: function (marker) {
      marker.setMap(null);
    },
    removeMarkers: function () {
      for (let i = 0; i < this.markers.length; i++) {
        this.markers[i].setMap(null);
      }

      this.markers = [];
    },
    mergeMarkers: function () {
      const operation = (list1, list2, isUnion = false) =>
        list1.filter(
          (a) =>
            isUnion ===
            list2.some(
              (b) =>
                a.getPosition().lat() === b.getPosition().lat() &&
                a.getPosition().lng() === b.getPosition().lng()
            )
        );

      const inBoth = (list1, list2) => operation(list1, list2, true),
        inFirstOnly = operation,
        inSecondOnly = (list1, list2) => inFirstOnly(list2, list1);

      const newMarkers = this.data.map((location) => {
        let marker = this.createMarker(location);
        marker.addListener("click", () => {
          this.infoWindow.setContent(location.label);
          this.infoWindow.open(this.map, marker);
        });

        return marker;
      });

      if (!this.config.mapIsFilter) {
        const oldMarkersRemove = inSecondOnly(newMarkers, this.markers);

        for (let i = oldMarkersRemove.length - 1; i >= 0; i--) {
          oldMarkersRemove[i].setMap(null);
          const index = this.markers.findIndex(
            (marker) =>
              marker.getPosition().lat() ===
                oldMarkersRemove[i].getPosition().lat() &&
              marker.getPosition().lng() ===
                oldMarkersRemove[i].getPosition().lng()
          );
          this.markers.splice(index, 1);
        }
      }

      const newMarkersCreate = inSecondOnly(this.markers, newMarkers);

      for (let i = 0; i < newMarkersCreate.length; i++) {
        newMarkersCreate[i].setMap(this.map);
        this.markers.push(newMarkersCreate[i]);
      }

      this.fitToBounds();
    },
    fitToBounds: function (force = false) {
      if (
        this.markers.length > 0 &&
        this.config.fit &&
        (force || !this.config.mapIsFilter)
      ) {
        this.bounds = new google.maps.LatLngBounds();

        for (const marker of this.markers) {
          this.bounds.extend(marker.getPosition());
        }

        this.map.fitBounds(this.bounds);
      }
    },
    createClustering: function () {
      if (this.markers.length > 0 && this.config.clustering) {
        // use default algorithm and renderer
        this.clusterer = new MarkerClusterer({
          map: this.map,
          markers: this.markers,
        });
      }
    },
    updateClustering: function () {
      if (this.config.clustering) {
        this.clusterer.clearMarkers();
        this.clusterer.addMarkers(this.markers);
      }
    },
    moved: function () {
      function areEqual(array1, array2) {
        if (array1.length === array2.length) {
          return array1.every((element, index) => {
            if (element === array2[index]) {
              return true;
            }

            return false;
          });
        }

        return false;
      }

      console.log("moved");

      const bounds = this.map.getBounds();
      const visible = this.markers.filter((marker) => {
        return bounds.contains(marker.getPosition());
      });
      const ids = visible.map((marker) => marker.model_id);

      if (!areEqual(this.modelIds, ids)) {
        this.modelIds = ids;
        console.log(ids);
        this.$wire.set("mapFilterIds", ids);
      }
    },
    idle: function () {
      if (this.config.mapIsFilter) {
        let that = self;
        const debouncedMoved = debounce(this.moved, 1000).bind(this);

        google.maps.event.addListener(this.map, "idle", (event) => {
          if (self.isMapDragging) {
            self.idleSkipped = true;
            return;
          }
          self.idleSkipped = false;
          debouncedMoved();
        });
        google.maps.event.addListener(this.map, "dragstart", (event) => {
          self.isMapDragging = true;
        });
        google.maps.event.addListener(this.map, "dragend", (event) => {
          self.isMapDragging = false;
          if (self.idleSkipped === true) {
            debouncedMoved();
            self.idleSkipped = false;
          }
        });
        google.maps.event.addListener(this.map, "bounds_changed", (event) => {
          self.idleSkipped = false;
        });
      }
    },
    update: function (data) {
      this.data = data;
      this.mergeMarkers();
      this.updateClustering();
      this.show();
    },
    recenter: function (data) {
      this.map.panTo({ lat: data.lat, lng: data.lng });
      this.map.setZoom(data.zoom);
    },
  };
}
