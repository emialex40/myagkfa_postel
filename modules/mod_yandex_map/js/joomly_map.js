ymaps.ready(function() { init(yandex_module_ids); } );
function init(y_ids) {
	y_ids.forEach(function(ymap_m_id, i, arr) {	
		myMap = new ymaps.Map("map" + ymap_m_id, {
          center: [y_cords[ymap_m_id].lat[0],y_cords[ymap_m_id].lng[0]],
            zoom: y_zoom[ymap_m_id],
			type: y_mapType[ymap_m_id]
        });
		var length = y_cords[ymap_m_id].lat.length;
		for (var i = 0; i < length; i++) {
			myPlacemark = new ymaps.Placemark([y_cords[ymap_m_id].lat[i],y_cords[ymap_m_id].lng[i]], { 
				balloonContent: y_cords[ymap_m_id].info[i] },{
				preset: y_preset[ymap_m_id],
				iconColor: y_cords[ymap_m_id].color[i]
			});
			myMap.geoObjects.add(myPlacemark);
		}
		if (length > 1)
		{	
			myMap.setBounds(myMap.geoObjects.getBounds());
		}	
		myMap.behaviors.disable('scrollZoom')
	});
	yandex_module_ids = [];
}	