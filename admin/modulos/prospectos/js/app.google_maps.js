var placeSearch, autocomplete,autocomplete_lab;
      var map, bermudaTriangle;
      var lastMarker;
      var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
      };

      function initAutocomplete() {
    	  $("#autocomplete").attr("autocomplete","nope");
    	  console.log($("#autocomplete").attr("autocomplete"))
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete 		= new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
            {types: ['geocode']});

        autocomplete_lab 	= new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById('autocomplete_lab')),
                {types: ['geocode']});
        
        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
        autocomplete_lab.addListener('place_changed', fillInAddressLab);
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 18
          });
        
        
        
      }

      function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        for (var component in componentForm) {
        	if ($("#"+component).length>0) {
        		$("#"+component).val("");
        		$("#"+component)[0].disabled = false;
        	}
        }
        
     // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
          var addressType = place.address_components[i].types[0];
          if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
            $("#"+addressType).val(val);
          }
        }
        
        
        var lat = place.geometry.location.lat(),
        lng = place.geometry.location.lng();

    // Then do whatever you want with them

    console.log(lat);
    console.log(lng);
    load(lat,lng);
    addMarker(lat,lng,true);
      }
      
      function fillInAddressLab() {
          // Get the place details from the autocomplete object.
          var place = autocomplete_lab.getPlace();

          /*for (var component in componentForm) {
          	if ($("#"+component).length>0) {
          		$("#"+component).val("");
          		$("#"+component)[0].disabled = false;
          	}
          }*/
          
       // Get each component of the address from the place details
          // and fill the corresponding field on the form.
          for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
              var val = place.address_components[i][componentForm[addressType]];
              $("#"+addressType+"_lab").val(val);
            }
          }
          
          
          var lat = place.geometry.location.lat(),
          lng = place.geometry.location.lng();
          load(lat,lng,"_lab");

        }
      // Bias the autocomplete object to the user's geographical location,
      // as supplied by the browser's 'navigator.geolocation' object.
      function geolocate() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
            autocomplete_lab.setBounds(circle.getBounds());
          });
        }
      }
      
      function load(lat,lng,str) {
    	  str = (str==undefined ? "" : str);
    	  $.get( "https://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+lng+"&sensor=true_or_false%27&key=AIzaSyC4dzOsaVl_IRxGsqBGvWoq-YHrOqQKUGg", function( data ) {
    		  for (var u = 0 ; u < data.results.length ; u++) {
    			  //console.log(data.results[u])
	    		  for (var i = 0; i < data.results[u].address_components.length; i++) {
	    			  var addressType = data.results[u].address_components[i].types[0];
	    			  if (addressType == 'postal_code') {
	    				  $("#postal_code"+str).val(data.results[u].address_components[i].short_name);
	    				  }
	    			  }
    			  }
    		  }, "json" );
    	  
      }
      
      function addMarker(lat,lng,isFirst) {
    	  var myLatlng	= {lat: lat, lng: lng};
    	  
    	  var location 	= new google.maps.LatLng(lat, lng);
    	  var bool		= checkInside(location,bermudaTriangle);
    	  if (bool) {
	   		  map.panTo(myLatlng);
	
	   		  if (lastMarker) {
	   			lastMarker.setMap(null);
	   		  }
	          var marker = new google.maps.Marker({
	        	  position: myLatlng,
	        	  map: map,
	        	  label: (isFirst==undefined || isFirst==true ? "A" : "B")
	          });
	          if (isFirst==false) {
	        	  lastMarker = marker;
	          } else {
	        	  $("#direccion_lat").val(lat);
	        	  $("#direccion_lng").val(lng);
	          }
	          /*map.addListener('center_changed', function() {
	            // 3 seconds after the center of the map has changed, pan back to the
	            // marker.
	            window.setTimeout(function() {
	              map.panTo(marker.getPosition());
	            }, 3000);
	          });*/
	
	          marker.addListener('click', function() {
	            //map.setZoom(8);
	
	            map.setCenter(marker.getPosition());
	          });
	          attachMessage(marker, (isFirst==undefined || isFirst==true ? "Dirección Según Google" : "Dirección Seteada Por Operador"));
    	  } else {
    		  console.log("fuera de area")
    	  }
      }
      
      function attachMessage(marker, message) {
          var infowindow = new google.maps.InfoWindow({
            content: message
          });

          marker.addListener('click', function() {
            infowindow.open(marker.get('map'), marker);
          });
        }
      
      function addPoligon(poligon) {
    	  var triangleCoords = JSON.parse(poligon);//'[{"lat": -34.59554643409643, "lng": -58.44352732033508},{"lat": -34.59861242626411, "lng": -58.430137732932735},{"lat": -34.608573696196636, "lng": -58.44056616158264},{"lat": -34.602957382414736, "lng": -58.448848823020626},{"lat": -34.59554643409643, "lng": -58.44352732033508}]'

            // Construct the polygon.
            var bermudaTriangle = new google.maps.Polygon({
              paths: triangleCoords,
              strokeColor: '#FF0000',
              strokeOpacity: 0.8,
              strokeWeight: 2,
              fillColor: '#FF0000',
              fillOpacity: 0.35
            });
            bermudaTriangle.setMap(map);
            return bermudaTriangle;
      }
      
      function checkInside(latLng,ibermudaTriangle) {
    	  var res =
              google.maps.geometry.poly.containsLocation(latLng, ibermudaTriangle) ?
              true :
              false;
    	  return res;
      }