UI = {tabIndex: 0};
UI.zIndex = function() { return Math.max.apply(null, $.map($('body *'), function(e,n) {if ($(e).css('position') != 'static')return parseInt($(e).css('z-index')) || 1;})); };
UI.require = function(css, js) { css.forEach(function(c) { var res = hex_md5(c); if($('#res' + res).length == 0) { $('head').append('<link id="res' + res + '" rel="stylesheet" href="' + _ROOTPATH + c + '" type="text/css" />'); }; }); js.forEach(function(j) { var res = hex_md5(j); if($('#res' + res).length == 0) { $('head').append('<script type="text/javascript" id="res' + res + '" src="' + _ROOTPATH + j + '"></script>'); }; }); };
UI.Controls = {}; 