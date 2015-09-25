#-----------------------------------------------------------------------------------------------
 # @Module: Validate Form
 # @Description: Validacion de formularios
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "validation", ((Sb) ->
	init: (oParams) ->
		forms= oParams.form.split(",")
		$.each forms,(index,value)->
			settings= {}
			value= $.trim value
			for prop of yOSON.require[value]
				settings[prop]= yOSON.require[value][prop]
			$(value).validate settings
), ["data/require.js","libs/plugins/jqValidate.js"]
#-----------------------------------------------------------------------------------------------
# @Module: Calendar
# @Description: Modulo calendario
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "calendar", ((Sb) ->
	st=
		calendar: "#calendar"
		ctnLoad: ".frm-panel"
		ctnMap: "#ctnMap"
		btnSearch: "#btnSearchMap"
		inptSearch: "#inptSearchMap"
		inptAddress: "#location"
		inptLat: "#latitude"
		inptLog: "#longitude"
		inptPicture: "#picture"
		ctnFile: ".ctn-eventImg"
		imgFile: "#eventImg"
		btnFile: "#btnFile"
		frmEvent: "#frmEvent"
		editEvent: ".editEvent"
		tmplEvent: "#tplEvent"
		tmplEventEdit: "#tplEventEdit"
	dom= {}
	coordsDefault=
		lat: -12.0777778
		lng: -76.91111109999997
	imgDefault= "/img/elements/default.png"
	arquitectFile= null
	data=
		"es":
			"dayName": ['dom', 'lun', 'mar', 'mié', 'jue', 'vie', 'sab']
			"monthNames": ['enero', 'febrero', 'marzo', 'abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre']
	catchDom= ()->
		dom.calendar= $(st.calendar)
		dom.ctnLoad= $(st.ctnLoad)
		dom.tmplEvent= _.template $(st.tmplEvent).html()
		dom.tmplEventEdit= _.template $(st.tmplEventEdit).html()
	bindEvents= ()->
		$('#calendar').fullCalendar
			events:
				url: "/json-Event-Date"
			header:
				left: 'prev,next'
				center: 'title'
				right: 'month,agendaWeek,agendaDay'
			editable: true
			selectable: true
			select: (start, end, allDay)->
				flagDate= if allDay then 1 else 0
				dateStart= $.fullCalendar.formatDate(start,"yyyy-MM-dd HH:mm:ss")
				dateEnd= $.fullCalendar.formatDate(end,"yyyy-MM-dd HH:mm:ss")
				json=
					"event": "Crear Evento"
					"img": imgDefault
					"lat": coordsDefault.lat
					"lng": coordsDefault.lng
					"flagDate": allDay
					"dateStart": dateStart
					"dateEnd": dateEnd
					"flagAct": true
				element= $(dom.tmplEventEdit(json).replace(/[\n\r]/g, ""))
				openFancyBox element,()->
					validateFrm(element)
					map= renderMap()
					marker= renderMarker map,coordsDefault.lat,coordsDefault.lng,true,evtDragend
					evtFile(element)
					searchMap element,map,marker
			eventClick: (calEvent, jsEvent, view)->
				start= $.fullCalendar.formatDate(calEvent.start,"yyyy-MM-dd HH:mm:ss")
				end= $.fullCalendar.formatDate(calEvent.end,"yyyy-MM-dd HH:mm:ss")
				calEvent["fullDate"]= getDate calEvent.start,calEvent.end,calEvent.allDay
				calEvent["event"]= "Modificar Evento"
				calEvent["address"]= calEvent["location"]
				calEvent["lat"]= calEvent.coords[0]
				calEvent["lng"]= calEvent.coords[1]
				calEvent["idevent"]= calEvent.id
				calEvent["flagDate"]= if calEvent["allDay"] then 1 else 0
				calEvent["dateStart"]= start
				calEvent["dateEnd"]= end
				calEvent["flagAct"]= if calEvent["flagAct"] is "1" then true else false
				element= $(dom.tmplEvent(calEvent).replace(/[\n\r]/g, ""))
				openFancyBox element,()->
					map= renderMap()
					map.setCenter calEvent["lat"], calEvent["lng"]
					renderMarker map,calEvent["lat"],calEvent["lng"],false
					editEvent element,calEvent
			eventDrop: (event)->
				allDay= if event.allDay then "1" else "0"
				json=
					"name": event.title
					"idevent": event.id
					"start": $.fullCalendar.formatDate(event.start,"yyyy-MM-dd HH:mm:ss")
					"end": $.fullCalendar.formatDate(event.end,"yyyy-MM-dd HH:mm:ss")
					"allDay": allDay
					"description": event.description
					"latitude": event.coords[0]
					"longitude": event.coords[1]
					"location": event.location
					"picture": event.nameImg
					"flagAct": event.flagAct
				$.ajax
					"url": "/agregar-event"
					"type": "POST"
					"dataType": "JSON"
					"data": json
					"success": (json)->
						if json.state is 1
							location.reload()
						else
							echo json.msg
					"error": ()->
						echo "Ocurrió un error en la creación del evento. Intente nuevamente."
			eventResize: (event)->
				allDay= if event.allDay then "1" else "0"
				json=
					"name": event.title
					"idevent": event.id
					"start": $.fullCalendar.formatDate(event.start,"yyyy-MM-dd HH:mm:ss")
					"end": $.fullCalendar.formatDate(event.end,"yyyy-MM-dd HH:mm:ss")
					"allDay": allDay
					"description": event.description
					"latitude": event.coords[0]
					"longitude": event.coords[1]
					"location": event.location
					"picture": event.nameImg
					"flagAct": event.flagAct
				$.ajax
					"url": "/agregar-event"
					"type": "POST"
					"dataType": "JSON"
					"data": json
					"success": (json)->
						if json.state is 1
							location.reload()
						else
							echo json.msg
					"error": ()->
						echo "Ocurrió un error en la creación del evento. Intente nuevamente."
			loading: (bool)->
				if bool
					utils.loader dom.ctnLoad,true
				else
					utils.loader dom.ctnLoad,false
	openFancyBox= (element,callback)->
		$.fancybox
			content: element
			autoResize: false
			fitToView: false
			afterShow: callback
			beforeClose: ()->
				if arquitectFile isnt null
					removeFile(arquitectFile)
					arquitectFile= null
	renderMap= ()->
		map= new GMaps
			div: '#mapa'
			lat: coordsDefault.lat
			lng: coordsDefault.lng
			zoom: 14
			panControl: false
			mapTypeControl: false
	renderMarker= (map,lat,lng,drag,evtDrag)->
		json=
			"lat": lat
			"lng": lng
		if drag
			json= $.extend json,
				draggable: true
				dragend: (json)->
					evtDrag map,json
		map.addMarker json
	searchMap= (el,map,marker)->
		dom.btnSearch= $(st.btnSearch,el)
		dom.inptSearch= $(st.inptSearch,el)
		dom.ctnMap= $(st.ctnMap,el)
		inptVal= ""
		dom.btnSearch.on "click",()->
			inptVal= dom.inptSearch.val()
			if inptVal isnt ""
				inptVal= inptVal+",Perú"
				utils.loader dom.ctnMap,true
				GMaps.geocode
					address: inptVal
					callback: (results, status)->
						if status is "OK"
							latLng= results[0].geometry.location
							map.setCenter latLng.lat(), latLng.lng()
							setLatLng latLng
							marker.setPosition latLng
							map.setZoom 14
						else
							echo "No se encontró resultados para la búsqueda"
						utils.loader dom.ctnMap,false
		dom.inptSearch.on "keypress", (e)->
			if e.which is 13
				dom.btnSearch.trigger "click"
				return false
	evtDragend= (map,json)->
		map.panTo json.latLng
		setLatLng json.latLng
		GMaps.geocode
			lat: json.latLng.lat()
			lng: json.latLng.lng()
			callback: (results, status)->
				if status is "OK"
					$(st.inptAddress).val results[0]['formatted_address']
				else
					echo "No se encontró la dirección que referencia el marker"
	evtFile= (el)->
		dom.ctnFile= $(st.ctnFile)
		dom.btnFile= $(st.btnFile)
		dom.imgFile= $(st.imgFile)
		dom.inptPicture= $(st.inptPicture)
		arquitectFile= createFile(el,dom.btnFile)
		$.jqFile
			"nameFile": "imagen"
			"routeFile": "/agregar-picture"
			"createFile": false
			"beforeCharge": ()->
				utils.loader dom.ctnFile,true
			"success": (json)->
				if json.state is 1
					dom.imgFile.attr "src",json.urlImagen
					dom.inptPicture.val json.nombre
				else
					echo json.msg
				utils.loader dom.ctnFile,false
			"error": (state,msg)->
				utils.loader dom.ctnFile,false
				echo msg
	createFile= (el,btn)->
		form= $("<form />",
			"method": "POST"
			"enctype": "multipart/form-data"
		)
		file= $("<input />",
			"type": "file"
			"name": "imagen"
		)
		optFile= styleFile(btn)
		form.css optFile.form
		file.css optFile.file
		form.append file
		el.append form
		return {
			"form": form
			"file": file
		}
	removeFile= (json)->
		json.file.off()
		json.form.off()
		json.form.remove()
	styleFile= (btn)->
		dimentions=
			"width": btn.outerWidth(true)
			"height": btn.outerHeight(true)
		cssForm=
			"position": "absolute"
			"overflow":"hidden"
			"-ms-filter": "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"
			"filter": "alpha(opacity=0)"
			"opacity": 0
			"z-index": "99"
			"left": "268px"
			"top": "637px"
		cssFile=
			"display": "block"
			"font-size": "999px"
			"cursor": "pointer"
		cssForm= $.extend cssForm,dimentions
		cssFile= $.extend cssFile,dimentions
		return {
			"form": cssForm
			"file": cssFile
		}
	editEvent= (el,calEvent)->
		$(st.editEvent,el).on "click",(e)->
			parentEl= el.parent()
			utils.loader parentEl,true
			parentEl.css "height",el.height()
			el.remove()
			element= $(dom.tmplEventEdit(calEvent).replace(/[\n\r]/g, ""))
			parentEl.append element
			parentEl.css "height","auto"

			validateFrm(element)
			map= renderMap()
			map.setCenter calEvent.lat,calEvent.lng
			marker= renderMarker map,calEvent.lat,calEvent.lng,true,evtDragend
			evtFile(element)
			searchMap element,map,marker

			utils.loader parentEl,false
	setLatLng= (latLng)->
		$(st.inptLat).val latLng.lat()
		$(st.inptLog).val latLng.lng()
	validateFrm= (el)->
		json= yOSON.require[st.frmEvent]
		handler=
			"submitHandler":(frm)->
				if typeof dom.inptPicture isnt "undefined" and dom.inptPicture.val() isnt ""
					utils.loader el,true
					data= $(frm).serializeArray()
					$.ajax
						"url": "/agregar-event"
						"type": "POST"
						"dataType": "JSON"
						"data": data
						"success": (json)->
							if json.state is 1
								location.reload()
							else
								echo json.msg
						"error": ()->
							echo "Ocurrió un error en la creación del evento. Intente nuevamente."
				else
					echo "Suba la imagen del evento"
				return false
		$(st.frmEvent,el).validate $.extend(json,handler)
	getDate= (dateStart,dateEnd,allDay)->
		dateTrad= data.es
		dayStart= dateTrad.dayName[dateStart.getDay()]
		numStart= dateStart.getDate()
		monthStart= dateTrad.monthNames[dateStart.getMonth()]
		hourStart= dateStart.getHours()
		minStart= if dateStart.getMinutes() is 0 then "00" else dateStart.getMinutes()
		yearStart= dateStart.getFullYear()
		if dateEnd isnt null
			dayEnd= dateTrad.dayName[dateEnd.getDay()]
			numEnd= dateEnd.getDate()
			monthEnd= dateTrad.monthNames[dateEnd.getMonth()]
			hourEnd= dateEnd.getHours()
			minEnd= if dateEnd.getMinutes() is 0 then "00" else dateEnd.getMinutes()
			yearEnd= dateEnd.getFullYear()
			if equalDates(dateStart,dateEnd)
				if allDay
					return dayStart+", "+numStart+" "+monthStart+" "+yearStart
				else
					return dayStart+", "+numStart+" "+monthStart+" "+yearStart+", "+hourStart+":"+minStart+" - "+hourEnd+":"+minEnd
			else
				if allDay
					return dayStart+", "+numStart+" "+monthStart+" "+yearStart+" - "+dayEnd+", "+numEnd+" "+monthEnd+" "+yearEnd
				else
					return dayStart+", "+numStart+" "+monthStart+" "+yearStart+", "+hourStart+":"+minStart+" - "+dayEnd+", "+numEnd+" "+monthEnd+" "+yearEnd+", "+hourEnd+":"+minEnd
		else
			return dayStart+", "+numStart+" "+monthStart+" "+yearStart
	equalDates= (date1,date2)->
		date1.getDate() is date2.getDate() and  date1.getMonth() is date2.getMonth() and date1.getFullYear() is date2.getFullYear()
	init: (oParams) ->
		catchDom()
		bindEvents()
),["libs/plugins/jqFullCalendar.js","libs/plugins/jqUI.js","libs/plugins/jqUnderscore.js","libs/plugins/gmaps.js","data/require.js","libs/plugins/jqValidate.js","libs/plugins/jqFile.js","libs/plugins/jqFancybox.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Denounce
 # @Description: Modulo para realizar denuncias
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "denounce", ((Sb) ->
	st=
		map: "#mapDenounce"
	gMap= null
	bindEvents= ()->
		gMap = new GMaps
			el: st.map
			zoom: 9
			lat: -12.0777778
			lng: -76.91111109999997
			markerClusterer: (map)->
				new MarkerClusterer(map)
		$.getJSON "/json-denuncias-molina",(data)->
			$.each data, (i,marker)->
				mdesc= if marker.description is null then "-" else marker.description
				mdate= if marker.fecha is null then "-" else marker.fecha
				gMap.addMarker
					lat: marker.latitude
					lng: marker.longitude
					icon:
						size: new google.maps.Size(32,37)
						url: "/img/pin.png"
					title: marker.description
					infoWindow:
						content: '<div class="denc-ctn"><img class="denc-img" src=' + marker.img + '><div class="denc-descp"><h3>'+ mdesc + '</h3><p> Fecha:  '+ mdate + ' </p></div></div>'
	init: (oParams) ->
		bindEvents()
), ["libs/plugins/gmaps.js","libs/plugins/jqMarkerclusterer.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: delRow
 # @Description: Eliminar Fila
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "delRow", ((Sb) ->
	st=
		del: ".ico-delete"
	dom= {}
	catchDom= ()->
		dom.del= $(st.del)
	bindEvents= ()->
		$this= null
		url= ""
		id= ""
		answer= "¿Esta seguro que desea eliminar el item seleccionado?"
		dom.del.on "click",(e)->
			e.preventDefault()
			$this= $(this)
			if confirm(answer)
				url= $this.attr "href"
				id= $this.attr "data-id"
				parent= $this.parents "tr"
				hash= utils.loader parent,true,1
				$.ajax
					"url": url
					"data":
						"id": id
					"dataType": "JSON"
					"success": (json)->
						utils.loader $("#"+hash),false,1
						if json.state is 1
							parent.fadeOut 600,()->
								this.remove()
						else
							echo json.msg
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Alerts
 # @Description: Modulo para remover alerts
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "alerts", ((Sb) ->
	st=
		alert: ".alert"
	dom= {}
	catchDom= ()->
		dom.alert= $(st.alert)
	bindEvents= ()->
		setTimeout ()->
			dom.alert.slideUp 600
		,5000
	init: (oParams) ->
		catchDom()
		bindEvents()
)