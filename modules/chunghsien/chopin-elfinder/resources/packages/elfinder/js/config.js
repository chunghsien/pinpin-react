define(["dojo/cookie"], function(cookie){
    return {
        // elFinder options (REQUIRED)
        // Documentation for client options:
        // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
        defaultOpts : {
            cssAutoLoad : [ '/packages/elfinder/themes/windows-10/css/theme.css' ],
            /*themes : {
                'dark-slim'     : 'https://johnfort.github.io/elFinder.themes/dark-slim/manifest.json',
                'material'      : 'https://nao-pon.github.io/elfinder-theme-manifests/material-default.json',
                'material-gray' : 'https://nao-pon.github.io/elfinder-theme-manifests/material-gray.json',
                'material-light': 'https://nao-pon.github.io/elfinder-theme-manifests/material-light.json',
                'bootstrap'     : 'https://nao-pon.github.io/elfinder-theme-manifests/bootstrap.json',
                'moono'         : 'https://nao-pon.github.io/elfinder-theme-manifests/moono.json',
                'win10'         : 'https://nao-pon.github.io/elfinder-theme-manifests/win10.json'
            },*/
            //width:'96%',
            //height:'90%',
            // connector URL (REQUIRED)
            url : '/elfinder-connector',
            resizable: false,
            tmb:true,
            handlers :{
                dblclick: function(e, fm){
                    e.preventDefault();
                    var hash = e.data.file;
                    fm.path(hash, false, true).done(function(path){
                        var basepath = '/storage/';
                        //$('#elfinderModalDialogBox').modal('hide');
                        //console.log(basepath+path);
                        return basepath+path;
                    }).fail(function(error){
                        
                    });
                    
                }
            },
            contextmenu : {
                // navbarfolder menu
                navbar : ['open', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', /*'|', 'info'*/],

                // current directory menu
                //cwd    : ['reload', 'back', '|', 'upload', 'mkdir', 'mkfile', 'paste', '|', 'info'],

                // current directory file menu
                files  : [
                    'getfile', '|','open', 'quicklook', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|',
                    'rm', '|', /*'edit',*/ 'rename', /*'resize',*/ '|', 'archive', 'extract'/*, '|', 'info'*/
                ]
            },
            uiOptions : {
                toolbar : [
                    [ 'back', 'forward' ],
                    // ['reload'],
                    // ['home', 'up'],
                    [ 'mkdir', 'mkfile', 'upload' ], 
                    [ 'open', 'download', 'getfile' ], 
                    [ 'info' ], [ 'quicklook' ], 
                    [ 'copy', 'cut', 'paste' ], 
                    //[ 'rm' ], 
                    [ 'duplicate', 'rename'/*, 'edit', 'resize'*/ ],
                    [ 'extract', 'archive' ], [ 'search' ], [ 'view' ] ],
            },
            commandsOptions : {
                edit : {
                    extraOptions : {
                        // set API key to enable Creative Cloud image editor
                        // see https://console.adobe.io/
                        creativeCloudApiKey : '',
                        // browsing manager URL for CKEditor, TinyMCE
                        // uses self location with the empty value
                        managerUrl : ''
                    }
                },
                quicklook : {
                    // to enable CAD-Files and 3D-Models preview with sharecad.org
                    sharecadMimes : [ 'image/vnd.dwg', 'image/vnd.dxf', 'model/vnd.dwf', 'application/vnd.hp-hpgl', 'application/plt', 'application/step', 'model/iges', 'application/vnd.ms-pki.stl',
                            'application/sat', 'image/cgm', 'application/x-msmetafile' ],
                    // to enable preview with Google Docs Viewer
                    googleDocsMimes : [ 'application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel',
                            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/postscript',
                            'application/rtf' ],
                    // to enable preview with Microsoft Office Online Viewer
                    // these MIME types override "googleDocsMimes"
                    officeOnlineMimes : [ 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet',
                            'application/vnd.oasis.opendocument.presentation' ]
                }
            }
            // bootCalback calls at before elFinder boot up
            ,
            bootCallback : function (fm, extraObj) {
                /* any bind functions etc. */
                fm.bind('init', function () {
                    // any your code
                });
                // for example set document.title dynamically.
                var title = document.title;
                fm.bind('open', function () {
                    var path = '', cwd = fm.cwd();
                    if (cwd) {
                        path = fm.path(cwd.hash) || null;
                    }
                    //document.title = path ? path + ':' + title : title;
                }).bind('destroy', function () {
                    document.title = title;
                });
            }
        },
        managers : {
            // 'DOM Element ID': { /* elFinder options of this DOM Element */ }
            'elfinder' : {}
        }
    }
});