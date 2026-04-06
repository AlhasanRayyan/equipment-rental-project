{{-- @extends('layouts.master')

@section('content')
    @php
        $trackingData = $trackings
            ->map(function ($tracking) {
                return [
                    'id' => $tracking->id,
                    'equipment_id' => $tracking->equipment_id,
                    'equipment_name' => $tracking->equipment->name ?? 'معدات',
                    'latitude' => (float) $tracking->latitude,
                    'longitude' => (float) $tracking->longitude,
                    'speed' => (float) $tracking->speed,
                    'battery_level' => $tracking->battery_level !== null ? (float) $tracking->battery_level : null,
                    'status' => $tracking->status,
                    'updated_at' => $tracking->updated_at ? $tracking->updated_at->format('Y-m-d H:i:s') : null,
                ];
            })
            ->values();
    @endphp

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h2 class="mb-0">تتبع معداتي</h2>

                    <div class="d-flex flex-wrap gap-3 small">
                        <span><i class="fas fa-circle text-success"></i> متصل</span>
                        <span><i class="fas fa-circle text-danger"></i> قيد الحركة</span>
                        <span><i class="fas fa-circle text-warning"></i> متوقف</span>
                        <span><i class="fas fa-circle text-secondary"></i> غير متصل</span>
                    </div>



                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-3">

                        @if ($ownerEquipmentsCount == 0)
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-box-open fa-3x text-muted"></i>
                                </div>
                                <h4 class="mb-2">لا تملك أي معدات حتى الآن</h4>
                                <p class="text-muted mb-4">
                                    أضف معدة أولاً حتى تتمكن من تتبعها على الخريطة.
                                </p>
                                <a href="{{ route('equipments.create') }}" class="btn btn-primary px-4">
                                    <i class="fas fa-plus-circle me-1"></i>
                                    إضافة معدة
                                </a>
                            </div>
                        @elseif ($trackings->isEmpty())
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-map-marker-alt fa-3x text-muted"></i>
                                </div>
                                <h4 class="mb-2">لا توجد بيانات تتبع لمعداتك حالياً</h4>
                                <p class="text-muted mb-0">
                                    تم العثور على معدات مملوكة لك، لكن لا توجد لها سجلات تتبع ضمن الخريطة حالياً.
                                </p>
                            </div>
                        @else
                            <div id="map" style="width: 100%; height: 700px; border-radius: 16px;"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($trackings->isNotEmpty())
        <script>
            window.trackingData = @json($trackingData);
        </script>

        <script>
            async function initMap() {
                const gazaCenter = {
                    lat: 31.4200,
                    lng: 34.3600
                };

                const gazaBounds = {
                    north: 31.7000,
                    south: 31.2000,
                    west: 34.2000,
                    east: 34.6000,
                };

                const {
                    Map,
                    InfoWindow
                } = await google.maps.importLibrary("maps");

                const {
                    AdvancedMarkerElement,
                    PinElement
                } = await google.maps.importLibrary("marker");

                const map = new Map(document.getElementById("map"), {
                    center: gazaCenter,
                    zoom: 10,
                    minZoom: 9,
                    maxZoom: 17,
                    restriction: {
                        latLngBounds: gazaBounds,
                        strictBounds: false,
                    },
                    mapId: "DEMO_MAP_ID"
                });

                const infoWindow = new InfoWindow();

                const statusConfig = {
                    moving: {
                        label: 'قيد الحركة',
                        color: '#dc3545',
                        badge: 'bg-danger'
                    },
                    online: {
                        label: 'متصل',
                        color: '#198754',
                        badge: 'bg-success'
                    },
                    idle: {
                        label: 'متوقف',
                        color: '#ffc107',
                        badge: 'bg-warning text-dark'
                    },
                    offline: {
                        label: 'غير متصل',
                        color: '#6c757d',
                        badge: 'bg-secondary'
                    }
                };

                window.trackingData.forEach(item => {
                    const config = statusConfig[item.status] || {
                        label: item.status,
                        color: '#0d6efd',
                        badge: 'bg-primary'
                    };

                    const pin = new PinElement({
                        background: config.color,
                        borderColor: config.color,
                        glyphColor: '#ffffff',
                        scale: 1.1
                    });

                    const marker = new AdvancedMarkerElement({
                        map,
                        position: {
                            lat: Number(item.latitude),
                            lng: Number(item.longitude)
                        },
                        title: item.equipment_name,
                        content: pin.element
                    });

                    const battery = item.battery_level !== null ? item.battery_level + '%' : '-';

                    const content = `
                        <div class="card border-0 shadow rounded-4 overflow-hidden" style="min-width: 280px;">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 px-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">${item.equipment_name}</h6>
                                    <small class="text-muted">رقم السجل: ${item.id}</small>
                                </div>
                                <span class="badge ${config.badge} px-3 py-2">${config.label}</span>
                            </div>

                            <div class="card-body px-3 py-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="border rounded-3 p-2 bg-light">
                                            <small class="text-muted d-block">رقم المعدة</small>
                                            <strong>${item.equipment_id}</strong>
                                        </div>
                                    </div>



                                    <div class="col-6">
                                        <div class="border rounded-3 p-2 bg-light">
                                            <small class="text-muted d-block">آخر تحديث</small>
                                            <strong style="font-size: 12px;">${item.updated_at ?? '-'}</strong>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="border rounded-3 p-2 bg-light">
                                            <small class="text-muted d-block">الإحداثيات</small>
                                            <strong style="font-size: 12px;">
                                                ${item.latitude}, ${item.longitude}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    marker.addListener("click", () => {
                        infoWindow.setContent(content);
                        infoWindow.open({
                            anchor: marker,
                            map,
                        });
                    });
                });
            }
        </script>

        <script>
            (function(g) {
                var h, a, k, p = "The Google Maps JavaScript API",
                    c = "google",
                    l = "importLibrary",
                    q = "__ib__",
                    m = document,
                    b = window;
                b = b[c] || (b[c] = {});
                var d = b.maps || (b.maps = {}),
                    r = new Set,
                    e = new URLSearchParams,
                    u = () => h || (h = new Promise(async (f, n) => {
                        await (a = m.createElement("script"));
                        e.set("key", "{{ config('services.google_maps.key') }}");
                        e.set("v", "weekly");
                        e.set("callback", c + ".maps." + q);
                        e.set("libraries", [...r] + "");
                        a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                        d[q] = f;
                        a.onerror = () => h = n(Error(p + " could not load."));
                        a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                        m.head.append(a)
                    }));
                d[l] ? console.warn(p + " only loads once. Ignoring:", g) :
                    d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n));
            })();

            initMap();
        </script>
    @endif
@endsection --}}


@extends('layouts.master')

@section('content')
    @php
        $trackingData = $trackings
            ->map(function ($tracking) {
                return [
                    'id' => $tracking->id,
                    'equipment_id' => $tracking->equipment_id,
                    'equipment_name' => $tracking->equipment->name ?? 'معدات',
                    'latitude' => (float) $tracking->latitude,
                    'longitude' => (float) $tracking->longitude,
                    'status' => $tracking->status,
                    'updated_at' => $tracking->updated_at ? $tracking->updated_at->format('Y-m-d H:i:s') : null,
                ];
            })
            ->values();
    @endphp

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h2 class="mb-0">تتبع معداتي</h2>

                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        {{-- <label class="filter-chip">
                            <input type="checkbox" class="status-filter" value="online" checked>
                            <span><i class="fas fa-circle text-success"></i> متصل</span>
                        </label> --}}

                        <label class="filter-chip">
                            <input type="checkbox" class="status-filter" value="moving" checked>
                            <span><i class="fas fa-circle text-danger"></i> قيد الحركة</span>
                        </label>

                        <label class="filter-chip">
                            <input type="checkbox" class="status-filter" value="idle" checked>
                            <span><i class="fas fa-circle text-warning"></i> متوقف</span>
                        </label>

                        {{-- <label class="filter-chip">
                            <input type="checkbox" class="status-filter" value="offline" checked>
                            <span><i class="fas fa-circle text-secondary"></i> غير متصل</span>
                        </label> --}}

                        <button type="button" class="btn btn-sm btn-dark rounded-pill px-3" id="showAllStatuses">
                            الكل
                        </button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-3">
                        @if ($ownerEquipmentsCount == 0)
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-box-open fa-3x text-muted"></i>
                                </div>
                                <h4 class="mb-2">لا تملك أي معدات حتى الآن</h4>
                                <p class="text-muted mb-4">
                                    أضف معدة أولاً حتى تتمكن من تتبعها على الخريطة.
                                </p>
                                <a href="{{ route('equipments.create') }}" class="btn btn-primary px-4">
                                    <i class="fas fa-plus-circle me-1"></i>
                                    إضافة معدة
                                </a>
                            </div>
                        @elseif ($trackings->isEmpty())
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-map-marker-alt fa-3x text-muted"></i>
                                </div>
                                <h4 class="mb-2">لا توجد بيانات تتبع لمعداتك حالياً</h4>
                                <p class="text-muted mb-0">
                                    تم العثور على معدات مملوكة لك، لكن لا توجد لها سجلات تتبع ضمن الخريطة حالياً.
                                </p>
                            </div>
                        @else
                            <div id="map" style="width: 100%; height: 700px; border-radius: 16px;"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($trackings->isNotEmpty())
        <style>
            .filter-chip {
                position: relative;
                cursor: pointer;
                margin: 0;
            }

            .filter-chip input {
                position: absolute;
                opacity: 0;
                pointer-events: none;
            }

            .filter-chip span {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 14px;
                border: 1px solid #dee2e6;
                border-radius: 999px;
                background: #fff;
                font-size: 14px;
                transition: all .2s ease;
                user-select: none;
            }

            .filter-chip input:checked+span {
                border-width: 2px;
                font-weight: 600;
            }

            /* .filter-chip input[value="online"]:checked + span {
                    background: rgba(25, 135, 84, 0.1);
                    border-color: #198754;
                } */

            .filter-chip input[value="moving"]:checked+span {
                background: rgba(220, 53, 69, 0.1);
                border-color: #dc3545;
            }

            .filter-chip input[value="idle"]:checked+span {
                background: rgba(255, 193, 7, 0.15);
                border-color: #ffc107;
            }

            /* .filter-chip input[value="offline"]:checked + span {
                    background: rgba(108, 117, 125, 0.1);
                    border-color: #6c757d;
                } */
        </style>

        <script>
            window.trackingData = @json($trackingData);
        </script>

        <script>
            async function initMap() {
                const gazaCenter = {
                    lat: 31.4200,
                    lng: 34.3600
                };

                const gazaBounds = {
                    north: 31.7000,
                    south: 31.2000,
                    west: 34.2000,
                    east: 34.6000,
                };

                const {
                    Map,
                    InfoWindow
                } = await google.maps.importLibrary("maps");

                const {
                    AdvancedMarkerElement,
                    PinElement
                } = await google.maps.importLibrary("marker");

                const map = new Map(document.getElementById("map"), {
                    center: gazaCenter,
                    zoom: 10,
                    minZoom: 9,
                    maxZoom: 17,
                    restriction: {
                        latLngBounds: gazaBounds,
                        strictBounds: false,
                    },
                    mapId: "DEMO_MAP_ID"
                });

                const infoWindow = new InfoWindow();

                const statusConfig = {
                    moving: {
                        label: 'قيد الحركة',
                        color: '#dc3545',
                        badge: 'bg-danger'
                    },

                    idle: {
                        label: 'متوقف',
                        color: '#ffc107',
                        badge: 'bg-warning text-dark'
                    },
                   
                };

                const markers = [];

                window.trackingData.forEach(item => {
                    const config = statusConfig[item.status] || {
                        label: item.status,
                        color: '#0d6efd',
                        badge: 'bg-primary'
                    };

                    const pin = new PinElement({
                        background: config.color,
                        borderColor: config.color,
                        glyphColor: '#ffffff',
                        scale: 1.1
                    });

                    const marker = new AdvancedMarkerElement({
                        map,
                        position: {
                            lat: Number(item.latitude),
                            lng: Number(item.longitude)
                        },
                        title: item.equipment_name,
                        content: pin.element
                    });

                    const content = `
                        <div class="card border-0 shadow rounded-4 overflow-hidden" style="min-width: 280px;">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 px-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">${item.equipment_name}</h6>
                                    <small class="text-muted">رقم السجل: ${item.id}</small>
                                </div>
                                <span class="badge ${config.badge} px-3 py-2">${config.label}</span>
                            </div>

                            <div class="card-body px-3 py-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="border rounded-3 p-2 bg-light">
                                            <small class="text-muted d-block">رقم المعدة</small>
                                            <strong>${item.equipment_id}</strong>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="border rounded-3 p-2 bg-light">
                                            <small class="text-muted d-block">آخر تحديث</small>
                                            <strong style="font-size: 12px;">${item.updated_at ?? '-'}</strong>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="border rounded-3 p-2 bg-light">
                                            <small class="text-muted d-block">الإحداثيات</small>
                                            <strong style="font-size: 12px;">
                                                ${item.latitude}, ${item.longitude}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    marker.addListener("click", () => {
                        infoWindow.setContent(content);
                        infoWindow.open({
                            anchor: marker,
                            map,
                        });
                    });

                    markers.push({
                        item,
                        marker
                    });
                });

                function getSelectedStatuses() {
                    return Array.from(document.querySelectorAll('.status-filter:checked')).map(input => input.value);
                }

                function applyFilters() {
                    const selectedStatuses = getSelectedStatuses();

                    markers.forEach(entry => {
                        const shouldShow = selectedStatuses.includes(entry.item.status);
                        entry.marker.map = shouldShow ? map : null;
                    });
                }

                document.querySelectorAll('.status-filter').forEach(input => {
                    input.addEventListener('change', applyFilters);
                });

                document.getElementById('showAllStatuses').addEventListener('click', function() {
                    document.querySelectorAll('.status-filter').forEach(input => {
                        input.checked = true;
                    });

                    applyFilters();
                });

                applyFilters();
            }
        </script>

        <script>
            (function(g) {
                var h, a, k, p = "The Google Maps JavaScript API",
                    c = "google",
                    l = "importLibrary",
                    q = "__ib__",
                    m = document,
                    b = window;
                b = b[c] || (b[c] = {});
                var d = b.maps || (b.maps = {}),
                    r = new Set,
                    e = new URLSearchParams,
                    u = () => h || (h = new Promise(async (f, n) => {
                        await (a = m.createElement("script"));
                        e.set("key", "{{ config('services.google_maps.key') }}");
                        e.set("v", "weekly");
                        e.set("callback", c + ".maps." + q);
                        e.set("libraries", [...r] + "");
                        a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                        d[q] = f;
                        a.onerror = () => h = n(Error(p + " could not load."));
                        a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                        m.head.append(a)
                    }));
                d[l] ? console.warn(p + " only loads once. Ignoring:", g) :
                    d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n));
            })();

            initMap();
        </script>
    @endif
@endsection
