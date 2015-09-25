/*-----------------------------------------------------------------------------------------------
 * @Module: Datable
 * @Description: Js que datable
 *//*-------------------------------------------------------------------------------------------*/
yOSON.AppCore.addModule('dataTable', function(Sb) {
    return {
        init: function(oParams) {
            var dataUrl=oParams.url,
            opts={
                "bJQueryUI": false,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
                "oLanguage":{
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
                //"bProcessing": true,
                "sAjaxSource": dataUrl
            },
            json=$.extend(opts,yOSON.datable[oParams.table]);
            window.instDataTable=$('#datatable').dataTable(json);
        }
    };
}, ['libs/plugins/jqDataTable.js','data/datatable.js']);
/*-----------------------------------------------------------------------------------------------
 * @Module: Action Del
 * @Description: Js para eliminar
 *//*-------------------------------------------------------------------------------------------*/
yOSON.AppCore.addModule('actionDel', function(Sb) {
    var st={
        "del":".ico-delete",
        "table":"#datatable"
    },dom={},
    catchdom=function(){
        dom.table=$(st.table);
    };
    return {
        init: function(oParams) {
            var _this,parent,url="",id="",hash="";
            catchdom();
            dom.table.on("click",st.del,function(e){
                e.preventDefault();
                _this=$(this);
                if(confirm("¿Esta seguro que desea eliminar el item seleccionado?")){
                   url=_this.attr("href");
                    id=_this.attr("data-id");
                    parent=_this.parents("tr");
                    hash=utils.loader(parent,true,1);
                    $.ajax({
                        "url":url,
                        "data":{
                            "id":id
                        },
                        dataType:"JSON",
                        success:function(json){
                            utils.loader($("#"+hash),false,1);
                            if(json.msj=="ok"){
                                instDataTable.fnDraw();
                            }
                        }
                    })
                }
            });
        }
    };
}, ['libs/plugins/jqDataTable.js']);
/*--------------------------------------------------------------------------------------------------------
* @Module : Validate Form
* @Description: Validacion de formularios
*//*----------------------------------------------------------------------------------------------------*/
yOSON.AppCore.addModule('validation', function(Sb){
    return {
        init: function(oParams){
            var forms = oParams.form.split(",");
            $.each(forms,function(index,value){
                var settings = {}, value=$.trim(value);
                for(var prop in yOSON.require[value]) settings[prop]=yOSON.require[value][prop];
                $(value).validate(settings);
            });
        }
    };
}, ['libs/plugins/jqValidate.js','data/require.js']);
/*--------------------------------------------------------------------------------------------------------
* @Module : Popup
* @Description: Validacion de formularios
*//*----------------------------------------------------------------------------------------------------*/
yOSON.AppCore.addModule('popup', function(Sb){
    return {
        init: function(oParams){
            $(".popup").fancybox();
        }
    };
}, ['libs/plugins/jqFancybox.js']);