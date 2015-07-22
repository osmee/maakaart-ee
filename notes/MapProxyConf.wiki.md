
```
services:
  demo:
  kml:
  tms:
    # needs no arguments
  wms:
    srs: ['EPSG:4326', 'EPSG:900913', 'EPSG:3301']
    # image_formats: ['image/jpeg', 'image/png']
    md:
      # metadata used in capabilities documents
      title: OpenStreetMap WMS by maakaart.ee
      abstract: Use with care, powered by MapProxy
      online_resource: http://www.maakaart.ee/
      contact:
        person: Jaak Laineste
        position: contact
        organization: MTÜ Avatud Maakaardi Selts
        address: 
        city: Tartu
        postcode: 
        country: Estonia
        phone: 
        fax: 
        email: talk-ee@openstreetmap.com
      access_constraints:
        Use it as you like. For mass-usage please inform maintainers.
        The data is licensed as Creative Commons Attribution-Share Alike 2.0
        (http://creativecommons.org/licenses/by-sa/2.0/)
      fees: 'None'

layers:
  - name: osm
    title: Maakaart.ee OSM WMS kaart.maakaart.ee/service
    sources: [osm_cache]
  - name: osm_noname
    title: Maakaart.ee OSM WMS kaart.maakaart.ee/service
    sources: [osm_noname_cache]
  - name: mapnik
    title: Maakaart.ee OSM WMS Mapnik tile-dest
    sources: [mapnik_cache]
  - name: maaamet_orto
    title: Maaameti ortofoto cache
    sources: [maaamet_orto_cache]
  - name: maaamet_reljeef
    title: Maaameti reljeefi cache
    sources: [maaamet_reljeef_cache]
  - name: topo50
    title: NL Topokaart
    sources: [topo_cache]
  # - name: osm_full_example
  #   title: Omniscale OSM WMS - osm.omniscale.net
  #   sources: [osm_cache_full_example]
    
caches:
  osm_cache:
#    grids: [GLOBAL_MERCATOR, global_geodetic_sqrt2]
    grids: [global_mercator_osm]
    sources: [osm_wms]
    meta_buffer: 100
    meta_size: [8, 8]
    format: image/png
    request_format: image/png
    link_single_color_images: true
  osm_noname_cache:
    grids: [global_mercator_osm]
    sources: [osm_noname_mapserv]
    meta_buffer: 100
    meta_size: [8, 8]
    format: image/png
    request_format: image/png
    link_single_color_images: true
  topo_cache:
    grids: [global_mercator_osm]
    sources: [topo_mapserv]
    meta_buffer: 10
    meta_size: [2, 2]
    format: image/png
    request_format: image/png
    link_single_color_images: true
    cache:
      type: mbtiles
      filename: /suur/mapproxy_cache/topocache.mbtiles
  mapnik_cache:
#    grids: [GLOBAL_MERCATOR, global_geodetic_sqrt2]
    grids: [global_mercator_osm]
    sources: [osm_mapnik]
    meta_buffer: 20
    meta_size: [4, 4]
    format: image/png
    request_format: image/png
    link_single_color_images: true
  maaamet_orto_cache:
#    grids: [global_geodetic_sqrt2]
    grids: [global_mercator_osm]
    sources: [maaamet_ortofoto_wms]
    meta_buffer: 0
    meta_size: [4, 4]
    format: image/jpeg
    request_format: image/jpeg
  maaamet_reljeef_cache:
#    grids: [global_geodetic_sqrt2]
    grids: [global_mercator_osm,ee_grid]
    sources: [maaamet_reljeef_wms]
    meta_buffer: 0
    meta_size: [4, 4]
    format: image/jpeg
    request_format: image/jpeg

  # osm_cache_full_example:
  #   meta_buffer: 20
  #   meta_size: [5, 5]
  #   format: image/png
  #   request_format: image/tiff
  #   link_single_color_images: true
  #   use_direct_from_level: 5
  #   grids: [grid_full_example]
  #   sources: [osm_wms, overlay_full_example]


sources:
  osm_mapnik:
    type: tile
    grid: global_mercator_osm
    url: http://localhost/modtile/est/%(z)s/%(x)s/%(y)s.png
    origin: nw
  osm_wms:
    type: mapserver
    req:
      layers: default
      map: /opt/mapserver/osm-google-p5432.map
    concurrent_requests: 4
    mapserver:
      binary: /usr/lib/cgi-bin/mapserv
      working_dir: /opt/mapserver
#    supported_srs: ['EPSG:4326', 'EPSG:31467', 'EPSG:900913', 'EPSG:3301']
  osm_noname_mapserv:
    type: mapserver
    req:
      layers: default
      map: /home/ats/osm-google-noname.map
    concurrent_requests: 4
    mapserver:
      binary: /usr/lib/cgi-bin/mapserv
      working_dir: /opt/mapserver
#    supported_srs: ['EPSG:4326', 'EPSG:31467', 'EPSG:900913', 'EPSG:3301']
  topo_mapserv:
    type: mapserver
    req:
      layers: ee_topo
      map: /suur/mapdata/eetopod/topo50wms.map
    concurrent_requests: 4
    mapserver:
      binary: /usr/lib/cgi-bin/mapserv
      working_dir: /opt/mapserver
#    supported_srs: ['EPSG:4326', 'EPSG:900913']
#  osm_wms:
#    type: wms
#    req:
#      url: http://localhost/cgi-bin/mapserv?
#      layers: default
#      map: /opt/mapserver/osm-google-livewms.map
#    concurrent_requests: 4
#    http:
#      client_timeout: 60000
#    supported_srs: ['EPSG:4326', 'EPSG:31467', 'EPSG:900913', 'EPSG:3301']

  maaamet_ortofoto_wms:
    type: wms
    req:
      url: http://kaart.maaamet.ee/wms/alus-geo?
      layers: of10000
    concurrent_requests: 4
    supported_srs: ['EPSG:4326']
  maaamet_reljeef_wms:
    type: wms
    req:
      url: http://kaart.maaamet.ee/wms/fotokaart?
      layers: reljeef
    concurrent_requests: 4
    supported_srs: ['EPSG:3301']
    coverage:
      bbox: [500000, 6400000, 800000, 6800000]
      bbox_srs: 'EPSG:3301'


  # overlay_full_example:
  #   type: wms
  #   concurrent_requests: 4
  #   wms_opts:
  #     version: 1.3.0
  #     featureinfo: true
  #   supported_srs: ['EPSG:4326', 'EPSG:31467', 'EPSG:900913', 'EPSG:3301']
  #   supported_formats: ['image/tiff', 'image/jpeg']
  #   http:
  #     ssl_no_cert_checks: true
  #   req:
  #     url: https://user:password@example.org:81/service?
  #     layers: roads,rails
  #     styles: base,base
  #     transparent: true
  #     # # always request in this format
  #     # format: image/png
  #     map: /home/map/mapserver.map
    

grids:
  global_mercator_osm:
    base: GLOBAL_MERCATOR
    num_levels: 19
  global_geodetic_sqrt2:
    base: GLOBAL_GEODETIC
    res_factor: 'sqrt2'
  # grid_full_example:
  #   tile_size: [512, 512]
  #   srs: 'EPSG:900913'
  #   bbox: [5, 45, 15, 55]
  #   bbox_srs: 'EPSG:4326'
  #   min_res: 2000 #m/px
  #   max_res: 50 #m/px
  #   align_resolutions_with: GLOBAL_MERCATOR
  # another_grid_full_example:
  #   srs: 'EPSG:900913'
  #   bbox: [5, 45, 15, 55]
  #   bbox_srs: 'EPSG:4326'
  #   res_factor: 1.5
  #   num_levels: 25
  ee_grid:
    srs: 'EPSG:3301'
    bbox: [20, 50, 30, 60]
    bbox_srs: 'EPSG:4326'
    res_factor: 1.5
    num_levels: 25

globals:
  # # coordinate transformation options
  # srs:
  #   # WMS 1.3.0 requires all coordiates in the correct axis order,
  #   # i.e. lon/lat or lat/lon. Use the following settings to
  #   # explicitly set a CRS to either North/East or East/North
  #   # ordering.
  #   axis_order_ne: ['EPSG:9999', 'EPSG:9998']
  #   axis_order_en: ['EPSG:0000', 'EPSG:0001']
  #   # you can set the proj4 data dir here, if you need custom
  #   # epsg definitions. the path must contain a file named 'epsg'
  #   # the format of the file is:
  #   # <4326> +proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs  <>
  #   proj_data_dir: '/path to dir that contains epsg file'

  # # cache options
  cache:
    # where to store the cached images
    base_dir: '/suur/mapproxy_cache'
    # where to store lockfiles
    lock_dir: '/suur/mapproxy_cache/locks'
  #   # request x*y tiles in one step
  #   meta_size: [4, 4]
  #   # add a buffer on all sides (in pixel) when requesting
  #   # new images
  #   meta_buffer: 80
  http:
    client_timeout: 60000

  tiles:
    expires_hours: 72

  # image/transformation options
  image:
      # resampling_method: nearest
      # resampling_method: bilinear
      resampling_method: bicubic
  #     jpeg_quality: 90
  #     # stretch cached images by this factor before
  #     # using the next level
  #     stretch_factor: 1.15
  #     # shrink cached images up to this factor before
  #     # returning an empty image (for the first level)
  #     max_shrink_factor: 4.0

```