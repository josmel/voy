yOSON.AppSchema.modules = {
    'admin': {
        controllers:{
            'consulta-previa':{
                actions : {
                    'index' : function(){
                        yOSON.AppCore.runModule('dataTable',{"url":"/admin/consulta-previa/list","table":"article"});
                    },
                    'byDefault':function(){}
                },
                allActions:function(){}
            },
            byDefault : function(){},
            allActions: function(){}
        },
        byDefault : function(){},
        allControllers : function(){}
    },
    byDefault : function(){},
    allModules : function(oMCA){}
};