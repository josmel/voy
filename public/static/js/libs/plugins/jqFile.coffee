(($) ->
	getBrowser = ->
		a = uaMatch(navigator.userAgent)
		b = {}
		if a.browser
			b[a.browser] = true
			b.version = a.version
		if b.chrome
			b.webkit = true
		else
			b.safari = true  if b.webkit
		b
	uaMatch = (b) ->
		b = b.toLowerCase()
		a = /(chrome)[ \/]([\w.]+)/.exec(b) or /(webkit)[ \/]([\w.]+)/.exec(b) or /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(b) or /(msie) ([\w.]+)/.exec(b) or b.indexOf("compatible") < 0 and /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(b) or []
		browser: a[1] or ""
		version: a[2] or "0"

	browser= getBrowser()						#Parámetro que devuelve la descripción del browser
	body= $("body")								#Variable global que estoriza el body
	jqFileUtils=
		hash: ()->
			Math.random().toString(36).substr(2)
		valExt: (ext,eReg)->
			return /^(jpg|gif|png|jpeg|bmp)$/gi.test($.trim(ext))
		validSize: (file,maxSize)->
			if browser.msie then return true
			sz= file[0].files[0].size
			if parseInt(sz) <= maxSize
				true
			else
				false
		messages:
			"0": "No se cargo un archivo"
			"1": "El archivo a cargar no esta permitido"
			"2": "El archivo excede su peso"
	class jqFile
		constructor: (options)->
			opt=
				btnFile: ".btn-jqFile"					#Parámetro que referencia al boton a submitear
				html5: false							#Parámetro que activa soporte para html5
				routeFile: "jqFile"						#Parámetro donde se envia el submit del formulario
				routeHtml5: null						#Ruta donde se envía la imagen usando html5, si se deja en blanco enviará a la misma ruta que routeFile
				createFile: true						#Crea el input file
				nameFile: "inputFile"					#Nombre del input file
				methodForm: "POST"						#Method del Formulario al crear el file
				eReg: /^(jpg|gif|png|jpeg|bmp)$/gi		#Expresion regular para validar la extensión
				maxSize: 2097152						#Cantidad de bytes que se pueden subir por imágenes
				success: null							#Callback de respuestas
				error: null								#Callback de error
				beforeCharge: null						#Callback antes de cargar el archivo
				afterCharge: null						#Callback despues de cargar el archivo
			@settings= $.extend opt,options
			@settings.routeHtml5= @settings.routeFile if @settings.routeHtml5 is null
			@arquitect= {}
			@_init()
		_init: ()->
			@_arquitect()
			@_bindEvents()
		_arquitect: ()->
			settings= @settings
			@arquitect.btnFile= $(settings.btnFile)
			idIframe= jqFileUtils.hash()
			@arquitect.idIframe= idIframe
			#Creando Iframe
			@arquitect.iframe= $("<iframe />",
				"name": idIframe
				"id": idIframe
				"src": "javascript:false;"
				"style": "display:none;"
			)
			body.append @arquitect.iframe
			#Seteando InputFile
			if settings.createFile is true
				@_createFile(idIframe)
			else
				@arquitect.file= $("input[name='"+settings.nameFile+"']")
				@arquitect.form= @arquitect.file.parents "form"
				@arquitect.form.attr "target", idIframe
				@arquitect.form.attr "action", settings.routeFile
		_bindEvents: ()->
			_this= @
			settings= @settings
			arquitect= @arquitect
			validFile= null
			#Eventos Input File
			arquitect.file.bind "change",()->
				validFile= _this._validFile.call this,settings
				if validFile
					settings.beforeCharge and settings.beforeCharge()
					arquitect.form.hide().submit()
			#Eventos Iframe	
			arquitect.iframe.bind "load",()->
				response= if browser.msie and parseInt(browser.version.substr(0,1)) <= 8 then window.frames[arquitect.idIframe].document.body.innerHTML else arquitect.iframe[0].contentDocument.body.innerHTML
				if response isnt "false"
					json= (new Function("return " + response))()
					settings.afterCharge and settings.afterCharge()
					settings.success and settings.success(json)
					arquitect.form.show()
		_createFile: (idIframe)->
			settings= @settings
			optFile= @_settingsFile()
			#Creando Form
			@arquitect.form= $("<form />",
				"action": settings.routeFile
				"target": idIframe
				"method": settings.methodForm
				"enctype": "multipart/form-data"
			)
			@arquitect.form.css optFile.form
			#Creando File
			file=
				"type": "file"
				"name": settings.nameFile
			@arquitect.file= $("<input />",file)
			@arquitect.file.css optFile.file
			@arquitect.form.append @arquitect.file
			#Agregandolo al Body
			body.append @arquitect.form
		_settingsFile: (optFile)->
			btnFile= @arquitect.btnFile
			dimentions=
				"width": btnFile.outerWidth(true)
				"height": btnFile.outerHeight(true)
			positions= btnFile.offset()
			cssForm=
				"position": "absolute"
				"overflow":"hidden"
				"-ms-filter": "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"
				"filter": "alpha(opacity=0)"
				"opacity": 0
				"z-index": "99"
			cssFile=
				"display": "block"
				"font-size": "999px"
				"cursor": "pointer"
			cssForm= $.extend cssForm,dimentions,positions
			cssFile= $.extend cssFile,dimentions
			return {
				"form": cssForm
				"file": cssFile
			}
		_validFile: (settings)->
			file= $(this)
			srcFile= file.val()
			ext= ""
			eReg= settings.eReg
			if srcFile isnt ""
				srcFile= srcFile.split("\\");
				srcFile= srcFile[srcFile.length-1];
				ext= srcFile.split(".")
				ext= ext[ext.length-1]
				valExt= jqFileUtils.valExt ext,eReg
				valSize= jqFileUtils.validSize file,settings.maxSize
				if valExt and valSize
					return true
				else
					if !valExt
						settings.error and settings.error(1,jqFileUtils.messages["1"])
					else
						settings.error and settings.error(2,jqFileUtils.messages["2"])
					return false
			else
				settings.error and settings.error(0,jqFileUtils.messages["0"])
				return false
	$.extend
		jqFile: (json) ->
			new jqFile(json)
			return
	return
) jQuery