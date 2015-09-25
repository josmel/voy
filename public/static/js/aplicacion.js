function replaceAll(f, d, e) {
    while (f.toString().indexOf(d) != -1) {
        f = f.toString().replace(d, e);
    }
    return f;
}
var puntaje = function(d, c) {
    $(d).raty({readOnly: true,score: c,starOff: "/img/t2.png",starOn: "/img/t1.png"});
};
function regresar() {
    $("#mapa-buscador").hide();
    $("#esconder").css("display", "block");
    $("#esconder2").css("display", "block");
}
function initSucursales() {
    var x;
    var u = document.getElementById("mapCont");
    var y = $("#mapCont").data("lat");
    var w = $("#mapCont").data("lng");
    var C = Array();
    var A = new google.maps.MarkerImage("/img/point.png", new google.maps.Size(26, 32), new google.maps.Point(0, 0));
    var E = new google.maps.MarkerImage("/img/point.png", new google.maps.Size(26, 32), new google.maps.Point(0, 0));
    var G = new google.maps.LatLng(y, w);
    var s = {center: G,zoom: 17,mapTypeId: google.maps.MapTypeId.ROADMAP,backgroundColor: "#ffffff",disableDefaultUI: true,navigationControl: true,navigationControlOptions: {position: google.maps.ControlPosition.TOP_RIGHT,style: google.maps.NavigationControlStyle.SMALL}};
    var H = new google.maps.Map(u, s);
    var t = Array();
    function v(a, b) {
        place = new google.maps.LatLng(a.lat, a.lng);
        var c = new google.maps.Marker({icon: b,position: place,map: H,title: a.title,zIndex: 100});
        i = a.index;
        (function(d, e) {
            google.maps.event.addListener(e, "click", function() {
                I(e, d);
            });
        })(i, c);
        return c;
    }
    function x(b) {
        var a;
        for (var c = 0; c < b.length; c++) {
            if (b[c].lat) {
                var d = v(b[c], A);
                C[c] = d;
            }
        }
    }
    $(".list-suc .ul-suc li a").each(function(c) {
        var b = $(this).data("lat");
        var d = $(this).data("lng");
        var a = $(this).find("h6").text();
        t[c] = new Object();
        t[c].lat = b;
        t[c].lng = d;
        t[c].title = a;
        t[c].index = c;
    });
    x(t);
    var z = {lat: y,lng: w,index: C.length};
    C.push(v(z, E));
    function D() {
        $(C).each(function() {
            this.setIcon(A);
        });
    }
    function I(b, a) {
        if (a == null) {
            D();
            b.setIcon(E);
            H.panTo(b.getPosition());
        } else {
            if (a == (C.length - 1)) {
                B();
            } else {
                $(".list-suc .ul-suc li a").eq(a).trigger("click");
            }
        }
    }
    function B() {
        $("h4.ubi-map span").html("");
        index = C.length - 1;
        I(C[index], null);
        $(".ubicancion").effect("highlight", {}, 1000);
        $(".list-suc .ul-suc li a.activo").removeClass("activo");
    }
    function F(c) {
        var b = $(this).data("lat");
        var d = $(this).data("lng");
        var e = $(".list-suc .ul-suc li a").index(this);
        var a = $(this).find(".phone span").text();
        var f = $(c.target);
        $(".list-suc .ul-suc li a.activo").removeClass("activo");
        if (f.is("span.close-banch")) {
            B();
        } else {
            $(this).addClass("activo");
            $("h4.ubi-map span").html(" : <span class='co_l'>" + a + "</span>");
            I(C[e], null);
        }
        return false;
    }
    $(".list-suc .ul-suc li a").on("click", F);
}

$(document).ready(function() {
    $("input, textarea").placeholder();
    if ($.browser.mozilla) {
        $(".verlistado").css("padding-top", "10px");
    }
    $(".agregar-coment-1").click(function(b) {
        b.preventDefault();
        if ($(".agregar-comentario-desc").is(":hidden")) {
            $(".agregar-comentario-desc").show("slow");
        } else {
            $(".agregar-comentario-desc").slideUp();
        }
    });
    //$(".cover").mosaic({animation: "slide",anchor_y: "top",hover_y: "300px"});
    $(".subir").hide();
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $(".subir").fadeIn();
            $("#ver").fadeOut();
        } else {
            $(".subir").fadeOut();
            $("#ver").fadeIn();
        }
    });
    $(".subir a").click(function() {
        $("body,html").animate({scrollTop: 0}, 500);
        return false;
    });
    $("#ver").on("click", function(c) {
        c.preventDefault();
        var d = $(this).attr("href");
        switch (d) {
            case "#cate-home":
                offset = $(d).offset().top - 20;
                break;
            default:
                offset = $(d).offset().top;
                break;
        }
        $("html, body").animate({scrollTop: offset}, "slow");
    });
    $("#bubi #q").keyup(function() {
        if (($(this).val() != "") && ($("#bubi #fq").val() != "seleccione distrito")) {
            $("#buscarmap").removeClass("disabled").addClass("map");
            $("#buscarmap").attr("href", "#");
            $("#buscarmap").removeAttr("disabled");
            $("#buscarmap").fadeIn();
        }
        if ($(this).val() == "") {
            $("#buscarmap").hide();
        }
    }).keyup();
    $("#buscarmap").on("click", function() {
        var h = $("#bubi #q").val();
        var condi = h.substring(0, 12);        
        var cot = h.substring(0, 4);
        var cotName = h.substring(0, 5);
        if(condi === 'restaurante:'){
            var numer = h.length;
            var hvalor = h.substring(12, numer);
        }else if(cot === 'tag:'){
            var numer = h.length;
            var hvalor = h.substring(4, numer);
        }else if(cotName === 'name:'){
            var numer = h.length;
            var hvalor = h.substring(5, numer);
        }else{
            var hvalor = h;
        }
        var f = $("#bubi #fq").val();
        if(f === ''){
            var e = urlJson + "/jsonmapasa?q=" + h;    
        }else{
            var e = urlJson + "/jsonmapasa?distrito=" + f + "&q=" + h;
        }
        var e = urlJson + "/jsonmapasa?distrito=" + f + "&q=" + h;
        $("#map").remove();
        $("#subir-home").remove();
        $(".mensaje").remove();
        $(".mensaje2").remove();
        $("#mapa-buscador").append("<div id='map' style='height:800px;'></div>");
        $("#esconder").css("display", "none");
        $("#esconder2").css("display", "none");
        if (($("#bubi #q").val() != "") && ($("#bubi #fq").val() != "seleccione")) {
            $("#mapa-buscador").fadeIn();
            $("#search #q").attr("value", hvalor);
            var g = $.getJSON(e, function(a) {
                if (a.response.numFound >= 1) {
                    map = new GMaps({el: "#map",zoom: 12,lat: -12.043333,lng: -77.028333,scrollwheel:false});
                    $.each(a.response.docs, function(d, j) {
                        map.setCenter(j.latitud, j.longitud);
                        var b = j.tx_descripcion;
                        var c = b.substring(0, 50);
                        var r = replaceAll(j.restaurante, " ", "-");
                        var l = replaceAll(j.name, " ", "-");
                        var k = replaceAll(j.distrito, " ", "-");
                        var z = j.va_imagen;
                        if (z=="platos-default.png"){
                            map.addMarker({lat: j.latitud,lng: j.longitud,icon: {size: new google.maps.Size(32, 37),url: "/img/icomap.png"},title: j.restaurante,infoWindow: {content: "<img src=" + urlJson + "/imagenes/defecto/" + j.va_imagen + " class='img-mapa'><p class='restaurante-map'><a href=/plato/"+r+"/" + l + "-" + j.id + ">" + j.restaurante + "</a></p><p class='plato-map'>" + j.name + "</p><p class='txt-map'>" + c + "...</p><a class='a-map' href=/plato/"+ r +"/" + l + "-" + j.id + "> ver mas </a>"}});
                        }else{
                            map.addMarker({lat: j.latitud,lng: j.longitud,icon: {size: new google.maps.Size(32, 37),url: "/img/icomap.png"},title: j.restaurante,infoWindow: {content: "<img src=" + urlJson + "/imagenes/plato/general/" + j.va_imagen + " class='img-mapa'><p class='restaurante-map'><a href=/plato/"+r+"/" + l + "-" + j.id + ">" + j.restaurante + "</a></p><p class='plato-map'>" + j.name + "</p><p class='txt-map'>" + c + "...</p><a class='a-map' href=/plato/"+ r +"/" + l + "-" + j.id + "> ver mas </a>"}});
                        }
                    
                    });
                } else {
                    $("#mapa-buscador").hide();
                    $(".descrip-product").remove();
                    $("#subir-home").remove();
                    $(".content-left").css("height", "100px");
                    $(".mensaje").remove();
                    $(".mensaje2").remove();
                    $(".contenido-plato").css("background", "url(/img/back-detalle.png) repeat");
                    $(".contenido-plato").append('<p class="mensaje">"Lamentamos no haber encontrado lo que estabas buscando pero tenemos muchas mas opciones para ti.</p><p class="mensaje2">También tenemos una opción para que nos escribas si deseas registrar un nuevo plato.</p>');
                    $(".contenido-plato").append('<div class="recomendados-platos primer-home" id="subir-home" style="padding-bottom: 90px;"></div>');
                    $("#subir-home").append('<div class="sub" style="margin-top: 10px;margin-bottom: 15px;background: url(/img/img-resultados.png);width: 41%;padding: 0.9em 0px;"><span  style="padding-left: 10px;color:white;font-weight: bold;">Platos Destacados</span></div>');
                    $("#subir-home").append('<ul id="listajson"></ul>');
                    $.getJSON(urlJson + "/jsondesta", function(b) {
                        $.each(b, function(j, d) {
                            var c = replaceAll(d.va_nombre, " ", "-");
                            var z = d.va_imagen;
                            if (z=="platos-default.png"){
                                $("#listajson").append('<li><div class="plato_r"><div class="mosaic-block cover2"><div class="mosaic-overlay"><span>' + d.va_nombre + '</span><img src=' + urlJson + '/imagenes/defecto/' + d.va_imagen + ' class="img-plato"><img src="/img/mas.png" alt="" class="mas"></div><a href="/plato/'+r+'/' + c + "-" + d.in_id + '" class="mosaic-backdrop"><div class="details"><h4>' + d.va_nombre + '</h4><p class="title-details" style="font-weight: bold;">Descripción</p><p class="desc-plato" style="font-size:0.9em;">' + d.tx_descripcion + '</p></div></a></div><div class="foo"><p class="nom_res">' + d.restaurant_nombre + '</p><div class="pt"><p class="com">' + d.NumeroComentarios + ' <i class="icon-comment"></i></p><div class="punt"><div class="puntuaciones c' + d.Ta_puntaje_in_id + '"></div></div></div></div></div></li>');
                            }else {
                                $("#listajson").append('<li><div class="plato_r"><div class="mosaic-block cover2"><div class="mosaic-overlay"><span>' + d.va_nombre + '</span><img src=' + urlJson + '/imagenes/plato/destacado/' + d.va_imagen + ' class="img-plato"><img src="/img/mas.png" alt="" class="mas"></div><a href="/plato/'+r+'/' + c + "-" + d.in_id + '" class="mosaic-backdrop"><div class="details"><h4>' + d.va_nombre + '</h4><p class="title-details" style="font-weight: bold;">Descripción</p><p class="desc-plato" style="font-size:0.9em;">' + d.tx_descripcion + '</p></div></a></div><div class="foo"><p class="nom_res">' + d.restaurant_nombre + '</p><div class="pt"><p class="com">' + d.NumeroComentarios + ' <i class="icon-comment"></i></p><div class="punt"><div class="puntuaciones c' + d.Ta_puntaje_in_id + '"></div></div></div></div></div></li>');
                            }
                        });
                        $(".cover2").mosaic({animation: "slide",anchor_y: "top",hover_y: "300px"});
                    });
                }
            });
            g.fail(function(b, a, d) {
                var c = a + ", " + d;
                console.log(c);
                $("#mapa-buscador").hide();
                $(".descrip-product").remove();
                $("#subir-home").remove();
                $(".content-left").css("height", "100px");
                $(".mensaje").remove();
                $(".mensaje2").remove();
                $(".contenido-plato").css("background", "url(/img/back-detalle.png) repeat");
                $(".contenido-plato").append('<p class="mensaje">"Lamentamos no haber encontrado lo que estabas buscando pero tenemos muchas mas opciones para ti.</p><p class="mensaje2">También tenemos una opción para que nos escribas si deseas registrar un nuevo plato.</p>');
                $(".contenido-plato").append('<div class="recomendados-platos primer-home" id="subir-home" style="padding-bottom: 90px;"></div>');
                $("#subir-home").append('<div class="sub" style="margin-top: 10px;margin-bottom: 15px;background: url(/img/img-resultados.png);width: 41%;padding: 0.9em 0px;"><span  style="padding-left: 10px;color:white;font-weight: bold;">Platos Destacados</span></div>');
                $("#subir-home").append('<ul id="listajson"></ul>');
                $.getJSON(urlJson + "/jsondesta", function(j) {
                    $.each(j, function(m, l) {
                        var k = replaceAll(l.va_nombre, " ", "-");
                        var z = l.va_imagen;
                        if (z=="platos-default.png"){
                            $("#listajson").append('<li><div class="plato_r"><div class="mosaic-block cover2"><div class="mosaic-overlay"><span>' + l.va_nombre + '</span><img src=' + urlJson + 'magenes/defecto/' + l.va_imagen + 'class="img-plato"><img src="/img/mas.png" alt="" class="mas"></div><a href="/plato/restaurante/' + k + "-" + l.in_id + '" class="mosaic-backdrop"><div class="details"><h4>' + l.va_nombre + '</h4><p class="title-details" style="font-weight: bold;">Descripción</p><p class="desc-plato" style="font-size:0.9em;">' + l.tx_descripcion + '</p></div></a></div><div class="foo"><p class="nom_res">' + l.restaurant_nombre + '</p><div class="pt"><p class="com">' + l.NumeroComentarios + ' <i class="icon-comment"></i></p><div class="punt"><div class="puntuaciones c' + l.Ta_puntaje_in_id + '"></div></div></div></div></div></li>');
                        }else {
                            $("#listajson").append('<li><div class="plato_r"><div class="mosaic-block cover2"><div class="mosaic-overlay"><span>' + d.va_nombre + '</span><img src=' + urlJson + '/imagenes/plato/destacado/' + d.va_imagen + ' class="img-plato"><img src="/img/mas.png" alt="" class="mas"></div><a href="/plato/restaurante/' + c + "-" + d.in_id + '" class="mosaic-backdrop"><div class="details"><h4>' + d.va_nombre + '</h4><p class="title-details" style="font-weight: bold;">Descripción</p><p class="desc-plato" style="font-size:0.9em;">' + d.tx_descripcion + '</p></div></a></div><div class="foo"><p class="nom_res">' + d.restaurant_nombre + '</p><div class="pt"><p class="com">' + d.NumeroComentarios + ' <i class="icon-comment"></i></p><div class="punt"><div class="puntuaciones c' + d.Ta_puntaje_in_id + '"></div></div></div></div></div></li>');
                        }
                    });
                    $(".cover2").mosaic({animation: "slide",anchor_y: "top",hover_y: "300px"});
                });
            });
        }else{
            $("#mapa-buscador").hide();
            alert("debe ingresar el plato");
        }
    });
});

