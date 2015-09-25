/*=========================================================================================
 *@ListModules: Listado de todos los Modulos asociados al portal
 **//*===================================================================================*/
yOSON.AppSchema.modules = {
    'admin': {
        controllers: {
             'acl': {
                actions: {
                    'index': function () {
                        yOSON.AppCore.runModule('dataTable', {"url": "/admin/acl/list", "table": "tdAction3"});
                        yOSON.AppCore.runModule('actionDel');
                    },
                    'byDefault': function () {
                    }
                },
                allActions: function () {
                }
            },'role': {
                actions: {
                    'index': function () {
                        yOSON.AppCore.runModule('dataTable', {"url": "/admin/role/list", "table": "tdAction3"});
                        yOSON.AppCore.runModule('actionDel');
                    },
                    'byDefault': function () {
                    }
                },
                allActions: function () {
                }
            }, 'usuarios': {
                actions: {
                    'index': function () {
                        yOSON.AppCore.runModule('dataTable', {"url": "/admin/usuarios/list", "table": "tdAction4"});
                        yOSON.AppCore.runModule('actionDel');
                    },
                    'byDefault': function () {
                    }
                },
                allActions: function () {
                }
            }, 'banner': {
                actions: {
                    'index': function () {
                        yOSON.AppCore.runModule('dataTable', {"url": "/admin/banner/list", "table": "#tblBanner"});
                        yOSON.AppCore.runModule('actionDel');
                    },
                    'byDefault': function () {
                    }
                },
                allActions: function () {
                }
            }, 'banner2': {
                actions: {
                    'index': function () {
                        yOSON.AppCore.runModule('addBanner');
                        yOSON.AppCore.runModule('selBanner');
                    },
                    'byDefault': function () {
                    }
                },
                allActions: function () {
                }
            },
            'musica': {
                actions: {
                    'index': function () {
                        yOSON.AppCore.runModule('dataTable', {"url": "/admin/musica/list", "table": "#tblMusica"});
                        yOSON.AppCore.runModule('actionDel');
                    },
                    'byDefault': function () {
                    }
                },
                allActions: function () {
                }
            },
            'juego': {
                actions: {
                    'index': function () {
                        yOSON.AppCore.runModule('dataTable', {"url": "/admin/juego/list", "table": "#tblJuego"});
                        yOSON.AppCore.runModule('actionDel');
                    },
                    'byDefault': function () {
                    }
                },
                allActions: function () {
                }
            },
            'text': {
                actions: {
                    'index': function () {
                        yOSON.AppCore.runModule('dataTable', {"url": "/admin/text/list", "table": "#tdAction4"});
                        yOSON.AppCore.runModule('actionDel');
                    },
                    'byDefault': function () {
                    }
                },
                allActions: function () {
                }
            },
            'servicio': {
                actions: {
                    'index': function () {
                        yOSON.AppCore.runModule('dataTable', {"url": "/admin/servicio/list", "table": "#tblServicio"});
                        yOSON.AppCore.runModule('actionDel');
                    },
                    'byDefault': function () {
                    }
                },
                allActions: function () {
                }
            },
            byDefault: function () {
            },
            allActions: function () {
            }
        },
        byDefault: function () {
        },
        allControllers: function () {
        }
    },
    byDefault: function () {
    },
    allModules: function (oMCA) {
    }
};