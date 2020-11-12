var lang = (function () {
    var locq = window.location.search, fullLang, locm, lang;
    if (locq && (locm = locq.match(/lang=([a-zA-Z_-]+)/))) {
        // detection by url query (?lang=xx)
        fullLang = locm[1];
    } else {
        // detection by browser language
        fullLang = (navigator.browserLanguage || navigator.language || navigator.userLanguage);
    }
    lang = fullLang.substr(0, 2);
    if (lang === 'pt')
        lang = 'pt_BR';
    else if (lang === 'ug')
        lang = 'ug_CN';
    else if (lang === 'zh')
        lang = (fullLang.substr(0, 5).toLowerCase() === 'zh-tw') ? 'zh_TW' : 'zh_CN';
    return lang;
})();

require([ 'elfinder', 'elfinder/config', 'elfinder/extras/editors.default.min', 'jquery', 'bootstrap', 'jquery-ui', 'elfinder/i18n/elfinder.'+lang], function (elfinder, config, editors, $) {
    var start = function (elfinder, editors, config) {
        //$(function(){});
        var optEditors = {
            commandsOptions : {
                edit : {
                    editors : Array.isArray(editors) ? editors : []
                }
            }
        }, opts = {};
        if (config && config.managers) {
            $.each(config.managers, function (id, mOpts) {
                opts = Object.assign(opts, config.defaultOpts || {});
                // editors marges to opts.commandOptions.edit
                try {
                    mOpts.commandsOptions.edit.editors = mOpts.commandsOptions.edit.editors.concat(editors || []);
                } catch (e) {
                    Object.assign(mOpts, optEditors);
                }
                // Make elFinder
                $('#' + id).elfinder(
                // 1st Arg - options
                $.extend(true, {lang : lang}, opts, mOpts || {}),
                // 2nd Arg - before boot up function
                function (fm, extraObj) {
                    // `init` event callback function
                    fm.bind('init', function () {
                        // Optional for Japanese decoder "encoding-japanese"
                        if (fm.lang === 'ja') {
                            require([ 'encoding-japanese' ], function (Encoding) {
                                if (Encoding && Encoding.convert) {
                                    fm.registRawStringDecoder(function (s) {
                                        return Encoding.convert(s, {
                                            to : 'UNICODE',
                                            type : 'string'
                                        });
                                    });
                                }
                            });
                        }
                    });
                });
            });
        } else {
            alert('"elFinderConfig" object is wrong.');
        }
    }

    start(elfinder, editors, config);
    $('#elfinderModalDialogBox').modal('show');
});