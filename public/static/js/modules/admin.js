yOSON.AppCore.addModule("dataTable", (function(Sb) {

    return {
        init: function(oParams) {
            var dataUrl, json, opts;
            dataUrl = oParams.url;
            opts = {                                                                                    
                "bJQueryUI": false,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                "oLanguage": {
                    "sSearch": "Busqueda",
                    "sLengthMenu": "<span>Mostrar registros por pagina</span> _MENU_",
                    "sZeroRecords": "No hay resultados",
                    "sInfo": "Mostrar _START_ a _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrar 0 a 0 de 0 records",
                    "sInfoFiltered": "( _MAX_ registros en total)",
                    "oPaginate": {
                        "sLast": "Última",
                        "sFirst": "Primera",
                        "sNext": ">",
                        "sPrevious": "<"
                    }
                },
                "bServerSide": true,
                "sAjaxSource": dataUrl
            };
            json = $.extend(opts, yOSON.datable[oParams.table]);
            return window.instDataTable = $('#datatable').dataTable(json);
        }
    };
}), ["libs/plugins/jqDataTable.js", "data/datatable.js"]);

yOSON.AppCore.addModule("actionDel", (function(Sb) {
    var bindEvents, catchDom, dom, st;
    st = {
        del: ".ico-delete",
        table: "#datatable"
    };
    dom = {};
    catchDom = function() {
        return dom.table = $(st.table);
    };
    bindEvents = function() {
        var $this, id, url;
        $this = null;
        url = "";
        id = "";
        return dom.table.on("click", st.del, function(e) {
            var hash, parent;
            e.preventDefault();
            $this = $(this);
            if (confirm("¿Esta seguro que desea eliminar el item seleccionado?")) {
                url = $this.attr("href");
                id = $this.attr("data-id");
                parent = $this.parents("tr");
                hash = utils.loader(parent, true, 1);
                return $.ajax({
                    "url": url,
                    "data": {
                        "id": id
                    },
                    "dataType": "JSON",
                    "success": function(json) {
                        utils.loader($("#" + hash), false, 1);
                        if (json.msj === "ok") {
                            return instDataTable.fnDraw();
                        }
                    }
                });
            }
        });
    };
    return {
        init: function(oParams) {
            catchDom();
            return bindEvents();
        }
    };
}), ["libs/plugins/jqDataTable.js"]);

yOSON.AppCore.addModule("addBanner", (function(Sb) {
    var bindEvents, catchDom, dom, evtClose, st, successFile, tmpl;
    st = {
        sort: "#sortBanner",
        btnClose: ".ic-close",
        notBanner: ".nd-banner",
        ctnBanner: ".row-well"
    };
    dom = {};
    tmpl = null;
    catchDom = function() {
        dom.sort = $(st.sort);
        dom.notBanner = $(st.notBanner);
        return dom.ctnBanner = $(st.ctnBanner);
    };
    bindEvents = function() {
        $.jqFile({
            "nameFile": "imagen",
            "routeFile": "/admin/banner2/banner-image",
            "btnFile": ".ctn-file .btn-file",
            "beforeCharge": function() {
                return utils.loader(dom.ctnBanner, true);
            },
            "success": successFile,
            "error": function(state, msg) {
                utils.loader(dom.ctnBanner, false);
                return echo(msg);
            }
        });
        dom.sort.sortable({
            "axis": "y",
            "containment": "parent",
            "tolerance": "pointer",
            "delay": 200
        });
        return dom.sort.find("li").each(function(index, value) {
            return $(value).find(st.btnClose).on("click", evtClose);
        });
    };
    successFile = function(json) {
        var element;
        if (json.state) {
            if (dom.notBanner.is(":visible")) {
                dom.notBanner.slideUp(600);
            }
            element = tmpl(json).replace(/[\n\r]/g, "");
            element = $(element);
            element.css("display", "none");
            dom.sort.append(element);
            element.find(st.btnClose).on("click", evtClose);
            return element.slideDown(600, function() {
                return utils.loader(dom.ctnBanner, false);
            });
        } else {
            utils.loader(dom.ctnBanner, false);
            return echo(json.msg);
        }
    };
    evtClose = function(e) {
        var parent;
        e.preventDefault();
        parent = $(this).parent();
        parent.css("border", "none");
        if (dom.sort.find("li").length === 1) {
            dom.notBanner.slideDown(600);
        }
        return parent.slideUp(600, function() {
            return $(this).remove();
        });
    };
    return {
        init: function(oParams) {
            tmpl = _.template($("#tplBanner").html());
            catchDom();
            return bindEvents();
        }
    };
}), ["libs/plugins/jqFile.js", "libs/plugins/jqUI.js", "libs/plugins/jqSortable.js", "libs/plugins/jqUnderscore.js"]);


yOSON.AppCore.addModule("selBanner", (function(Sb) { 
    var bindEvents, catchDom, dom, st;
    st = {
        slct: "#type"
    };
    dom = {};
    catchDom = function() {
        return dom.slct = $(st.slct);
    };
    bindEvents = function() {
        var $this;
        $this = null;
        return dom.slct.on("change", function(e) {
            $this = $(this);
            return location.href = yOSON.bannerType + $this.find("option:selected").val();
        });
    };
    return {
        init: function(oParams) {
            catchDom();
            return bindEvents();
        }
    };
}));


yOSON.AppCore.addModule("crudTips", (function(Sb) {
    var st = {
        "btnCreate": "#btnCreateTip",
        "btnClose": ".ctn-tips .lnk-close",
        "ctnCampo": "#campos"
    },
    dom = {},
            catchDom = function() {
                dom.btnCreate = $(st.btnCreate);
                dom.btnClose = $(st.btnClose);
                dom.ctnCampo = $(st.ctnCampo);
            },
            bindEvents = function() {
                dom.btnCreate.on("click", function(e) {
                    e.preventDefault();
                    var campo = $('<div class="control-group"><input name="idtip[]" type="hidden" /><td class="controls"> Nombre en EspaÃ±ol:<input name="tip_spanish[]" type="text" /></td><td class="controls">Nombre en Ingles:<input name="tip_english[]" type="text" /><button class="lnk-close" type="button">Â¡ELiminar!</button></td>');
                    campo.find(".lnk-close").on("click", function(e) {
                        campo.remove();
                    });
                    dom.ctnCampo.append(campo);
                });
                dom.btnClose.on("click", function(e) {
                    e.preventDefault();
                    var $this = $(this),
                            parent = $this.parents(".ctn-tips"),
                            idTips = parent.find("input[name='idtip[]']").val();
                    $.ajax({
                        url: "/admin/reservation/delete-tip",
                        "method": "get",
                        data: {
                            "id": idTips
                        },
                        "success": function() {
                            parent.remove();
                        }
                    });
                });
            };
    return {
        init: function(oParams) {
            catchDom();
            bindEvents();
        }
    };
}));


yOSON.AppCore.addModule("crudEmpresa", (function(Sb) {
    var st = {
        "btnCreate": "#btnCreateEmpresa",
        "btnClose": ".ctn-empresa .lnk-close",
        "ctnCampo": "#campos2"
    },
    dom = {},
            catchDom = function() {
                dom.btnCreate = $(st.btnCreate);
                dom.btnClose = $(st.btnClose);
                dom.ctnCampo = $(st.ctnCampo);
            },
            bindEvents = function() {
                dom.btnCreate.on("click", function(e) {
                    e.preventDefault();
                    var campo = $('<div class="control-group"><tr><input name="idEmpresaTurismo[]" type="hidden" /><td class="controls">Nombre:<input name="nombre[]" type="text" /></td><td class="controls">Email:<input name="email[]" type="text" /></td><td class="controls">TelÃ©fono:<input name="telefono[]" type="text" /></td></tr>\n\<td class="controls">Web:<input name="web[]" type="text" /></td></tr>\n\<td class="controls">Otorgamiento de derecho:<select name="derecho[]"><option value="0">NO</option><option value="1">SI</option></select><br><button class="lnk-close" type="button">Â¡ELiminar!</button></td>');
                    campo.find(".lnk-close").on("click", function(e) {
                        campo.remove();
                    });
                    dom.ctnCampo.append(campo);
                });
                dom.btnClose.on("click", function(e) {
                    e.preventDefault();
                    var $this = $(this),
                            parent = $this.parents(".ctn-empresa"),
                            idEmpresa = parent.find("input[name='idEmpresaTurismo[]']").val();
                    $.ajax({
                        url: "/admin/reservation/delete-empresa",
                        "method": "get",
                        data: {
                            "id": idEmpresa
                        },
                        "success": function() {
                            parent.remove();
                        }
                    });
                });
            };
    return {
        init: function(oParams) {
            catchDom();
            bindEvents();
        }
    };
}));



yOSON.AppCore.addModule("crudActivityes", (function(Sb) {
    var st = {
        "btnCreate": "#btnCreateActivityes",
        "btnClose": ".ctn-activityes .lnk-close",
        "ctnCampo": "#campos"
    },
    dom = {},
            catchDom = function() {
                dom.btnCreate = $(st.btnCreate);
                dom.btnClose = $(st.btnClose);
                dom.ctnCampo = $(st.ctnCampo);
            },
            bindEvents = function() {
                dom.btnCreate.on("click", function(e) {
                    e.preventDefault();
                    var campo = $('<div class="control-group"><tr><input name="idactivity[]" type="hidden" /><td class="controls">Nombre en EspaÃ±ol:<input name="activity_spanish[]" type="text" /></td><td class="controls">Nombre en Ingles:<input name="activity_english[]" type="text" /><button class="lnk-close" type="button">Â¡ELiminar!</button></td>');
                    campo.find(".lnk-close").on("click", function(e) {
                        campo.remove();
                    });
                    dom.ctnCampo.append(campo);
                });
                dom.btnClose.on("click", function(e) {
                    e.preventDefault();
                    var $this = $(this),
                            parent = $this.parents(".ctn-activityes"),
                            idActivituyes = parent.find("input[name='idactivity[]']").val();
                    $.ajax({
                        url: "/admin/reservation/delete-activiyes",
                        "method": "get",
                        data: {
                            "id": idActivituyes
                        },
                        "success": function() {
                            parent.remove();
                        }
                    });
                });
            };
    return {
        init: function(oParams) {
            catchDom();
            bindEvents();
        }
    };
}));



yOSON.AppCore.addModule("crudServices", (function(Sb) {
    var st = {
        "btnCreate": "#btnCreateServices",
        "btnClose": ".ctn-services .lnk-close",
        "ctnCampo": "#campos"
    },
    dom = {},
            catchDom = function() {
                dom.btnCreate = $(st.btnCreate);
                dom.btnClose = $(st.btnClose);
                dom.ctnCampo = $(st.ctnCampo);
            },
            bindEvents = function() {
                dom.btnCreate.on("click", function(e) {
                    e.preventDefault();
                    var campo = $('<div class="control-group"><tr><input name="idservice[]" type="hidden" /><td class="controls">Nombre en EspaÃ±ol:<input name="service_spanish[]" type="text" /></td><td class="controls">Nombre en Ingles:<input name="service_english[]" type="text" /><button class="lnk-close" type="button">Â¡ELiminar!</button></td>');
                    campo.find(".lnk-close").on("click", function(e) {
                        campo.remove();
                    });
                    dom.ctnCampo.append(campo);
                });
                dom.btnClose.on("click", function(e) {
                    e.preventDefault();
                    var $this = $(this),
                            parent = $this.parents(".ctn-services"),
                            idActivituyes = parent.find("input[name='idservice[]']").val();
                    $.ajax({
                        url: "/admin/reservation/delete-services",
                        "method": "get",
                        data: {
                            "id": idActivituyes
                        },
                        "success": function() {
                            parent.remove();
                        }
                    });
                });
            };
    return {
        init: function(oParams) {
            catchDom();
            bindEvents();
        }
    };
}));


yOSON.AppCore.addModule("crudAcces", (function(Sb) {
    var st = {
        "btnCreate": "#btnCreateAcces",
        "btnClose": ".ctn-acces .lnk-close",
        "ctnCampo": "#campos"
    },
    dom = {},
            catchDom = function() {
                dom.btnCreate = $(st.btnCreate);
                dom.btnClose = $(st.btnClose);
                dom.ctnCampo = $(st.ctnCampo);
            },
            bindEvents = function() {
                dom.btnCreate.on("click", function(e) {
                    e.preventDefault();
                    var campo = $('<div class="control-group"><tr><input name="idaccess[]" type="hidden" /><td class="controls">Nombre en EspaÃ±ol:<input name="access_spanish[]" type="text" /></td><td class="controls">Nombre en Ingles:<input name="access_english[]" type="text" /></td><td class="controls">Descripcion en EspaÃ±ol:<input name="description_spanish[]" type="text" /></td><td class="controls">Descripcion en Ingles:<input name="description_english[]" type="text" /></td><button class="lnk-close" type="button">Â¡ELiminar!</button></div>');
                    campo.find(".lnk-close").on("click", function(e) {
                        campo.remove();
                    });
                    dom.ctnCampo.append(campo);
                });
                dom.btnClose.on("click", function(e) {
                    e.preventDefault();
                    var $this = $(this),
                            parent = $this.parents(".ctn-acces"),
                            idAccess = parent.find("input[name='idaccess[]']").val();
                    $.ajax({
                        url: "/admin/reservation/delete-acces",
                        "method": "get",
                        data: {
                            "id": idAccess
                        },
                        "success": function() {
                            parent.remove();
                        }
                    });
                });
            };
    return {
        init: function(oParams) {
            catchDom();
            bindEvents();
        }
    };
}));