;(function () {
    //already loaded
    if(window.cadesplugin)
        return;

    var pluginObject;
    var plugin_resolved = 0;
    var plugin_reject;
    var plugin_resolve;
    var isOpera = 0;
    var isFireFox = 0;
    var isEdge = 0;
    var failed_extensions = 0;

    var canPromise = !!window.Promise;
    var cadesplugin;

    if(canPromise)
    {
        cadesplugin = new Promise(function(resolve, reject)
        {
            plugin_resolve = resolve;
            plugin_reject = reject;
        });
    } else
    {
        cadesplugin = {};
    }
    
    function check_browser() {
        var ua= navigator.userAgent, tem, M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
        if(/trident/i.test(M[1])){
            tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
            return {name:'IE',version:(tem[1] || '')};
        }
        if(M[1]=== 'Chrome'){
            tem= ua.match(/\b(OPR|Edge)\/(\d+)/);
            if(tem!= null) return {name:tem[1].replace('OPR', 'Opera'),version:tem[2]};
        }
        M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
        if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
        return {name:M[0],version:M[1]};
    }
    var browserSpecs = check_browser();

    function cpcsp_console_log(level, msg){
        //IE9 РЅРµ РјРѕР¶РµС‚ РїРёСЃР°С‚СЊ РІ РєРѕРЅСЃРѕР»СЊ РµСЃР»Рё РЅРµ РѕС‚РєСЂС‹С‚Р° РІРєР»Р°РґРєР° developer tools
        if(typeof(console) == 'undefined')
            return;
        if (level <= cadesplugin.current_log_level ){
            if (level == cadesplugin.LOG_LEVEL_DEBUG)
                console.log("DEBUG: %s", msg);
            if (level == cadesplugin.LOG_LEVEL_INFO)
                console.info("INFO: %s", msg);
            if (level == cadesplugin.LOG_LEVEL_ERROR)
                console.error("ERROR: %s", msg);
            return;
        }
    }

    function set_log_level(level){
        if (!((level == cadesplugin.LOG_LEVEL_DEBUG) ||
              (level == cadesplugin.LOG_LEVEL_INFO) ||
              (level == cadesplugin.LOG_LEVEL_ERROR))){
            cpcsp_console_log(cadesplugin.LOG_LEVEL_ERROR, "cadesplugin_api.js: Incorrect log_level: " + level);
            return;
        }
        cadesplugin.current_log_level = level;
        if (cadesplugin.current_log_level == cadesplugin.LOG_LEVEL_DEBUG)
            cpcsp_console_log(cadesplugin.LOG_LEVEL_INFO, "cadesplugin_api.js: log_level = DEBUG");
        if (cadesplugin.current_log_level == cadesplugin.LOG_LEVEL_INFO)
            cpcsp_console_log(cadesplugin.LOG_LEVEL_INFO, "cadesplugin_api.js: log_level = INFO");
        if (cadesplugin.current_log_level == cadesplugin.LOG_LEVEL_ERROR)
            cpcsp_console_log(cadesplugin.LOG_LEVEL_INFO, "cadesplugin_api.js: log_level = ERROR");
        if(isNativeMessageSupported())
        {
            if (cadesplugin.current_log_level == cadesplugin.LOG_LEVEL_DEBUG)
                window.postMessage("set_log_level=debug", "*");
            if (cadesplugin.current_log_level == cadesplugin.LOG_LEVEL_INFO)
                window.postMessage("set_log_level=info", "*");
            if (cadesplugin.current_log_level == cadesplugin.LOG_LEVEL_ERROR)
                window.postMessage("set_log_level=error", "*");
        }
    }

    function set_constantValues()
    {
        cadesplugin.CAPICOM_LOCAL_MACHINE_STORE = 1;
        cadesplugin.CAPICOM_CURRENT_USER_STORE = 2;
        cadesplugin.CADESCOM_LOCAL_MACHINE_STORE = 1;
        cadesplugin.CADESCOM_CURRENT_USER_STORE = 2;
        cadesplugin.CADESCOM_CONTAINER_STORE = 100;
        
        cadesplugin.CAPICOM_MY_STORE = "My";

        cadesplugin.CAPICOM_STORE_OPEN_MAXIMUM_ALLOWED = 2;

        cadesplugin.CAPICOM_CERTIFICATE_FIND_SUBJECT_NAME = 1;

        cadesplugin.CADESCOM_XML_SIGNATURE_TYPE_ENVELOPED = 0;
        cadesplugin.CADESCOM_XML_SIGNATURE_TYPE_ENVELOPING = 1;
        cadesplugin.CADESCOM_XML_SIGNATURE_TYPE_TEMPLATE = 2;

        cadesplugin.XmlDsigGost3410UrlObsolete = "http://www.w3.org/2001/04/xmldsig-more#gostr34102001-gostr3411";
        cadesplugin.XmlDsigGost3411UrlObsolete = "http://www.w3.org/2001/04/xmldsig-more#gostr3411";
        cadesplugin.XmlDsigGost3410Url = "urn:ietf:params:xml:ns:cpxmlsec:algorithms:gostr34102001-gostr3411";
        cadesplugin.XmlDsigGost3411Url = "urn:ietf:params:xml:ns:cpxmlsec:algorithms:gostr3411";

        cadesplugin.CADESCOM_CADES_DEFAULT = 0;
        cadesplugin.CADESCOM_CADES_BES = 1;
        cadesplugin.CADESCOM_CADES_T = 0x5;
        cadesplugin.CADESCOM_CADES_X_LONG_TYPE_1 = 0x5d;

        cadesplugin.CADESCOM_ENCODE_BASE64 = 0;
        cadesplugin.CADESCOM_ENCODE_BINARY = 1;
        cadesplugin.CADESCOM_ENCODE_ANY = -1;

        cadesplugin.CAPICOM_CERTIFICATE_INCLUDE_CHAIN_EXCEPT_ROOT = 0;
        cadesplugin.CAPICOM_CERTIFICATE_INCLUDE_WHOLE_CHAIN = 1;
        cadesplugin.CAPICOM_CERTIFICATE_INCLUDE_END_ENTITY_ONLY = 2;

        cadesplugin.CAPICOM_CERT_INFO_SUBJECT_SIMPLE_NAME = 0;
        cadesplugin.CAPICOM_CERT_INFO_ISSUER_SIMPLE_NAME = 1;

        cadesplugin.CAPICOM_CERTIFICATE_FIND_SHA1_HASH = 0;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_SUBJECT_NAME = 1;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_ISSUER_NAME = 2;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_ROOT_NAME = 3;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_TEMPLATE_NAME = 4;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_EXTENSION = 5;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_EXTENDED_PROPERTY = 6;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_APPLICATION_POLICY = 7;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_CERTIFICATE_POLICY = 8;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_TIME_VALID = 9;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_TIME_NOT_YET_VALID = 10;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_TIME_EXPIRED = 11;
        cadesplugin.CAPICOM_CERTIFICATE_FIND_KEY_USAGE = 12;

        cadesplugin.CAPICOM_DIGITAL_SIGNATURE_KEY_USAGE = 128;

        cadesplugin.CAPICOM_PROPID_ENHKEY_USAGE = 9;

        cadesplugin.CAPICOM_OID_OTHER = 0;
        cadesplugin.CAPICOM_OID_KEY_USAGE_EXTENSION = 10;

        cadesplugin.CAPICOM_EKU_CLIENT_AUTH = 2;
        cadesplugin.CAPICOM_EKU_SMARTCARD_LOGON = 5;
        cadesplugin.CAPICOM_EKU_OTHER = 0;

        cadesplugin.CAPICOM_AUTHENTICATED_ATTRIBUTE_SIGNING_TIME = 0;
        cadesplugin.CADESCOM_AUTHENTICATED_ATTRIBUTE_DOCUMENT_NAME = 1;
        cadesplugin.CADESCOM_AUTHENTICATED_ATTRIBUTE_DOCUMENT_DESCRIPTION = 2;
        cadesplugin.CADESCOM_ATTRIBUTE_OTHER = -1;

        cadesplugin.CADESCOM_STRING_TO_UCS2LE = 0;
        cadesplugin.CADESCOM_BASE64_TO_BINARY = 1;

        cadesplugin.CADESCOM_DISPLAY_DATA_NONE = 0;
        cadesplugin.CADESCOM_DISPLAY_DATA_CONTENT = 1;
        cadesplugin.CADESCOM_DISPLAY_DATA_ATTRIBUTE = 2;

        cadesplugin.CADESCOM_ENCRYPTION_ALGORITHM_RC2 = 0;
        cadesplugin.CADESCOM_ENCRYPTION_ALGORITHM_RC4 = 1;
        cadesplugin.CADESCOM_ENCRYPTION_ALGORITHM_DES = 2;
        cadesplugin.CADESCOM_ENCRYPTION_ALGORITHM_3DES = 3;
        cadesplugin.CADESCOM_ENCRYPTION_ALGORITHM_AES = 4;
        cadesplugin.CADESCOM_ENCRYPTION_ALGORITHM_GOST_28147_89 = 25;

        cadesplugin.CADESCOM_HASH_ALGORITHM_SHA1 = 0;
        cadesplugin.CADESCOM_HASH_ALGORITHM_MD2 = 1;
        cadesplugin.CADESCOM_HASH_ALGORITHM_MD4 = 2;
        cadesplugin.CADESCOM_HASH_ALGORITHM_MD5 = 3;
        cadesplugin.CADESCOM_HASH_ALGORITHM_SHA_256 = 4;
        cadesplugin.CADESCOM_HASH_ALGORITHM_SHA_384 = 5;
        cadesplugin.CADESCOM_HASH_ALGORITHM_SHA_512 = 6;
        cadesplugin.CADESCOM_HASH_ALGORITHM_CP_GOST_3411 = 100;
        cadesplugin.CADESCOM_HASH_ALGORITHM_CP_GOST_3411_2012_256 = 101;
        cadesplugin.CADESCOM_HASH_ALGORITHM_CP_GOST_3411_2012_512 = 102;

        cadesplugin.LOG_LEVEL_DEBUG = 4;
        cadesplugin.LOG_LEVEL_INFO = 2;
        cadesplugin.LOG_LEVEL_ERROR = 1;

        cadesplugin.CADESCOM_AllowNone = 0;
        cadesplugin.CADESCOM_AllowNoOutstandingRequest = 0x1;
        cadesplugin.CADESCOM_AllowUntrustedCertificate = 0x2;
        cadesplugin.CADESCOM_AllowUntrustedRoot = 0x4;
        cadesplugin.CADESCOM_SkipInstallToStore = 0x10000000;
    }

    function async_spawn(generatorFunc) {
      function continuer(verb, arg) {
        var result;
        try {
              result = generator[verb](arg);
        } catch (err) {
              return Promise.reject(err);
        }
        if (result.done) {
              return result.value;
        } else {
              return Promise.resolve(result.value).then(onFulfilled, onRejected);
        }
      }
      var generator = generatorFunc(Array.prototype.slice.call(arguments, 1));
      var onFulfilled = continuer.bind(continuer, "next");
      var onRejected = continuer.bind(continuer, "throw");
      return onFulfilled();
    }

    function isIE() {
        // var retVal = (("Microsoft Internet Explorer" == navigator.appName) || // IE < 11
        //     navigator.userAgent.match(/Trident\/./i)); // IE 11
        return (browserSpecs.name == 'IE' || browserSpecs.name == 'MSIE');
    }

    function isIOS() {
        var retVal = (navigator.userAgent.match(/ipod/i) ||
          navigator.userAgent.match(/ipad/i) ||
          navigator.userAgent.match(/iphone/i));
        return retVal;
    }

    function isNativeMessageSupported()
    {
        // Р’ IE СЂР°Р±РѕС‚Р°РµРј С‡РµСЂРµР· NPAPI
        if(isIE())
            return false;
        // Р’ Edge СЂР°Р±РѕС‚Р°РµРј С‡РµСЂРµР· NativeMessage
        if(browserSpecs.name == 'Edge') {
            isEdge = true;
            return true;
        }
        // Р’ Chrome, Firefox Рё Opera СЂР°Р±РѕС‚Р°РµРј С‡РµСЂРµР· Р°СЃРёРЅС…СЂРѕРЅРЅСѓСЋ РІРµСЂСЃРёСЋ РІ Р·Р°РІРёСЃРёРјРѕСЃС‚Рё РѕС‚ РІРµСЂСЃРёРё
        if(browserSpecs.name == 'Opera') {
            isOpera = true;
            if(browserSpecs.version >= 33){
                return true;
            }
            else{
                return false;
            }
        }
        if(browserSpecs.name == 'Firefox') {
            isFireFox = true;
            if(browserSpecs.version >= 52){
                return true;
            }
            else{
                return false;
            }
        }
        if(browserSpecs.name == 'Chrome') {
            if(browserSpecs.version >= 42){
                return true;
            }
            else{
                return false;
            }
        }
    }

    // Р¤СѓРЅРєС†РёСЏ Р°РєС‚РёРІР°С†РёРё РѕР±СЉРµРєС‚РѕРІ РљСЂРёРїС‚РѕРџСЂРѕ Р­Р¦Рџ Browser plug-in
    function CreateObject(name) {
        if (isIOS()) {
            // РќР° iOS РґР»СЏ СЃРѕР·РґР°РЅРёСЏ РѕР±СЉРµРєС‚РѕРІ РёСЃРїРѕР»СЊР·СѓРµС‚СЃСЏ С„СѓРЅРєС†РёСЏ
            // call_ru_cryptopro_npcades_10_native_bridge, РѕРїСЂРµРґРµР»РµРЅРЅР°СЏ РІ IOS_npcades_supp.js
            return call_ru_cryptopro_npcades_10_native_bridge("CreateObject", [name]);
        }
        if (isIE()) {
             // Р’ Internet Explorer СЃРѕР·РґР°СЋС‚СЃСЏ COM-РѕР±СЉРµРєС‚С‹
             if (name.match(/X509Enrollment/i)) {
                try {
                    // РћР±СЉРµРєС‚С‹ CertEnroll СЃРѕР·РґР°СЋС‚СЃСЏ С‡РµСЂРµР· CX509EnrollmentWebClassFactory
                    var objCertEnrollClassFactory = document.getElementById("certEnrollClassFactory");
                    return objCertEnrollClassFactory.CreateObject(name);
                }
                catch (e) {
                    throw("Р”Р»СЏ СЃРѕР·РґР°РЅРёСЏ РѕР±СЊРµРєС‚РѕРІ X509Enrollment СЃР»РµРґСѓРµС‚ РЅР°СЃС‚СЂРѕРёС‚СЊ РІРµР±-СѓР·РµР» РЅР° РёСЃРїРѕР»СЊР·РѕРІР°РЅРёРµ РїСЂРѕРІРµСЂРєРё РїРѕРґР»РёРЅРЅРѕСЃС‚Рё РїРѕ РїСЂРѕС‚РѕРєРѕР»Сѓ HTTPS");
                }
            }
            // РћР±СЉРµРєС‚С‹ CAPICOM Рё CAdESCOM СЃРѕР·РґР°СЋС‚СЃСЏ С‡РµСЂРµР· CAdESCOM.WebClassFactory
            try {
                var objWebClassFactory = document.getElementById("webClassFactory");
                return objWebClassFactory.CreateObject(name);
            }
            catch (e) {
                // Р”Р»СЏ РІРµСЂСЃРёР№ РїР»Р°РіРёРЅР° РЅРёР¶Рµ 2.0.12538
                return new ActiveXObject(name);
            }
        }
        // СЃРѕР·РґР°СЋС‚СЃСЏ РѕР±СЉРµРєС‚С‹ NPAPI
        return pluginObject.CreateObject(name);
    }

    function decimalToHexString(number) {
        if (number < 0) {
            number = 0xFFFFFFFF + number + 1;
        }

        return number.toString(16).toUpperCase();
    }
    
    function GetMessageFromException(e) {
        var err = e.message;
        if (!err) {
            err = e;
        } else if (e.number) {
            err += " (0x" + decimalToHexString(e.number) + ")";
        }
        return err;
    }

    function getLastError(exception) {
        if(isNativeMessageSupported() || isIE() || isIOS() ) {
            return GetMessageFromException(exception);
        }

        try {
            return pluginObject.getLastError();
        } catch(e) {
            return GetMessageFromException(exception);
        }
    }

    // Р¤СѓРЅРєС†РёСЏ РґР»СЏ СѓРґР°Р»РµРЅРёСЏ СЃРѕР·РґР°РЅРЅС‹С… РѕР±СЉРµРєС‚РѕРІ
    function ReleasePluginObjects() {
        return cpcsp_chrome_nmcades.ReleasePluginObjects();
    }

    // Р¤СѓРЅРєС†РёСЏ Р°РєС‚РёРІР°С†РёРё Р°СЃРёРЅС…СЂРѕРЅРЅС‹С… РѕР±СЉРµРєС‚РѕРІ РљСЂРёРїС‚РѕРџСЂРѕ Р­Р¦Рџ Browser plug-in
    function CreateObjectAsync(name) {
        return pluginObject.CreateObjectAsync(name);
    }

    //Р¤СѓРЅРєС†РёРё РґР»СЏ IOS
    var ru_cryptopro_npcades_10_native_bridge = {
      callbacksCount : 1,
      callbacks : {},

      // Automatically called by native layer when a result is available
      resultForCallback : function resultForCallback(callbackId, resultArray) {
            var callback = ru_cryptopro_npcades_10_native_bridge.callbacks[callbackId];
            if (!callback) return;
            callback.apply(null,resultArray);
      },

      // Use this in javascript to request native objective-c code
      // functionName : string (I think the name is explicit :p)
      // args : array of arguments
      // callback : function with n-arguments that is going to be called when the native code returned
      call : function call(functionName, args, callback) {
        var hasCallback = callback && typeof callback == "function";
        var callbackId = hasCallback ? ru_cryptopro_npcades_10_native_bridge.callbacksCount++ : 0;

        if (hasCallback)
          ru_cryptopro_npcades_10_native_bridge.callbacks[callbackId] = callback;

        var iframe = document.createElement("IFRAME");
            var arrObjs = new Array("_CPNP_handle");
            try{
        iframe.setAttribute("src", "cpnp-js-call:" + functionName + ":" + callbackId+ ":" + encodeURIComponent(JSON.stringify(args, arrObjs)));
            } catch(e){
                    alert(e);
            }
              document.documentElement.appendChild(iframe);
        iframe.parentNode.removeChild(iframe);
        iframe = null;
      }
    };

    function call_ru_cryptopro_npcades_10_native_bridge(functionName, array){
        var tmpobj;
        var ex;
        ru_cryptopro_npcades_10_native_bridge.call(functionName, array, function(e, response){
                                          ex = e;
                                          var str='tmpobj='+response;
                                          eval(str);
                                          if (typeof (tmpobj) == "string"){
                                                tmpobj = tmpobj.replace(/\\\n/gm, "\n");
                                            tmpobj = tmpobj.replace(/\\\r/gm, "\r");
                                          }
                                          });
        if(ex)
            throw ex;
        return tmpobj;
    }

    function show_firefox_missing_extension_dialog()
    {
        if (!window.cadesplugin_skip_extension_install)
        {  
            var ovr = document.createElement('div');
            ovr.id = "cadesplugin_ovr";
            ovr.style = "visibility: hidden; position: fixed; left: 0px; top: 0px; width:100%; height:100%; background-color: rgba(0,0,0,0.7)";
            ovr.innerHTML = "<div id='cadesplugin_ovr_item' style='position:relative; width:400px; margin:100px auto; background-color:#fff; border:2px solid #000; padding:10px; text-align:center; opacity: 1; z-index: 1500'>" +
                            "<button id='cadesplugin_close_install' style='float: right; font-size: 10px; background: transparent; border: 1; margin: -5px'>X</button>" +
                            "<p>Р”Р»СЏ СЂР°Р±РѕС‚С‹ РљСЂРёРїС‚РѕРџСЂРѕ Р­Р¦Рџ Browser plugin РЅР° РґР°РЅРЅРѕРј СЃР°Р№С‚Рµ РЅРµРѕР±С…РѕРґРёРјРѕ СЂР°СЃС€РёСЂРµРЅРёРµ РґР»СЏ Р±СЂР°СѓР·РµСЂР°. РЈР±РµРґРёС‚РµСЃСЊ, С‡С‚Рѕ РѕРЅРѕ Сѓ Р’Р°СЃ РІРєР»СЋС‡РµРЅРѕ РёР»Рё СѓСЃС‚Р°РЅРѕРІРёС‚Рµ РµРіРѕ." +
                            "<p><a href='https://www.cryptopro.ru/sites/default/files/products/cades/extensions/firefox_cryptopro_extension_latest.xpi'>РЎРєР°С‡Р°С‚СЊ СЂР°СЃС€РёСЂРµРЅРёРµ</a></p>" +
                            "</div>";
            document.getElementsByTagName("Body")[0].appendChild(ovr);
            document.getElementById("cadesplugin_close_install").addEventListener('click',function()
                                    {
                                        plugin_loaded_error("РџР»Р°РіРёРЅ РЅРµРґРѕСЃС‚СѓРїРµРЅ");
                                        document.getElementById("cadesplugin_ovr").style.visibility = 'hidden';
                                    });

            ovr.addEventListener('click',function()
                                {
                                    plugin_loaded_error("РџР»Р°РіРёРЅ РЅРµРґРѕСЃС‚СѓРїРµРЅ");
                                    document.getElementById("cadesplugin_ovr").style.visibility = 'hidden';
                                });
            ovr.style.visibility="visible";
        }
    }


    //Р’С‹РІРѕРґРёРј РѕРєРЅРѕ РїРѕРІРµСЂС… РґСЂСѓРіРёС… СЃ РїСЂРµРґР»РѕР¶РµРЅРёРµРј СѓСЃС‚Р°РЅРѕРІРёС‚СЊ СЂР°СЃС€РёСЂРµРЅРёРµ РґР»СЏ Opera.
    //Р•СЃР»Рё СѓСЃС‚Р°РЅРѕРІР»РµРЅРЅР° РїРµСЂРµРјРµРЅРЅР°СЏ cadesplugin_skip_extension_install - РЅРµ РїСЂРµРґР»Р°РіР°РµРј СѓСЃС‚Р°РЅРѕРІРёС‚СЊ СЂР°СЃС€РёСЂРµРЅРёРµ
    function install_opera_extension()
    {
        if (!window.cadesplugin_skip_extension_install)
        {
            document.addEventListener('DOMContentLoaded', function() {
                var ovr = document.createElement('div');
                ovr.id = "cadesplugin_ovr";
                ovr.style = "visibility: hidden; position: fixed; left: 0px; top: 0px; width:100%; height:100%; background-color: rgba(0,0,0,0.7)";
                ovr.innerHTML = "<div id='cadesplugin_ovr_item' style='position:relative; width:400px; margin:100px auto; background-color:#fff; border:2px solid #000; padding:10px; text-align:center; opacity: 1; z-index: 1500'>" +
                                "<button id='cadesplugin_close_install' style='float: right; font-size: 10px; background: transparent; border: 1; margin: -5px'>X</button>" +
                                "<p>Р”Р»СЏ СЂР°Р±РѕС‚С‹ РљСЂРёРїС‚РѕРџСЂРѕ Р­Р¦Рџ Browser plugin РЅР° РґР°РЅРЅРѕРј СЃР°Р№С‚Рµ РЅРµРѕР±С…РѕРґРёРјРѕ СѓСЃС‚Р°РЅРѕРІРёС‚СЊ СЂР°СЃС€РёСЂРµРЅРёРµ РёР· РєР°С‚Р°Р»РѕРіР° РґРѕРїРѕР»РЅРµРЅРёР№ Opera." +
                                "<p><button id='cadesplugin_install' style='font:12px Arial'>РЈСЃС‚Р°РЅРѕРІРёС‚СЊ СЂР°СЃС€РёСЂРµРЅРёРµ</button></p>" +
                                "</div>";
                document.getElementsByTagName("Body")[0].appendChild(ovr);
                var btn_install = document.getElementById("cadesplugin_install");
                btn_install.addEventListener('click', function(event) {
                    opr.addons.installExtension("epebfcehmdedogndhlcacafjaacknbcm",
                        function()
                        {
                            document.getElementById("cadesplugin_ovr").style.visibility = 'hidden';
                            location.reload();
                        },
                        function(){})
                });
                document.getElementById("cadesplugin_close_install").addEventListener('click',function()
                        {
                            plugin_loaded_error("РџР»Р°РіРёРЅ РЅРµРґРѕСЃС‚СѓРїРµРЅ");
                            document.getElementById("cadesplugin_ovr").style.visibility = 'hidden';
                        });

                ovr.addEventListener('click',function()
                        {
                            plugin_loaded_error("РџР»Р°РіРёРЅ РЅРµРґРѕСЃС‚СѓРїРµРЅ");
                            document.getElementById("cadesplugin_ovr").style.visibility = 'hidden';
                        });
                ovr.style.visibility="visible";
                document.getElementById("cadesplugin_ovr_item").addEventListener('click',function(e){
                    e.stopPropagation();
                });
            });
        }else
        {
            plugin_loaded_error("РџР»Р°РіРёРЅ РЅРµРґРѕСЃС‚СѓРїРµРЅ");
        }
    }

    function firefox_or_edge_nmcades_onload() {
        cpcsp_chrome_nmcades.check_chrome_plugin(plugin_loaded, plugin_loaded_error);
    }

    function nmcades_api_onload () {
        window.postMessage("cadesplugin_echo_request", "*");
        window.addEventListener("message", function (event){
            if (typeof(event.data) != "string" || !event.data.match("cadesplugin_loaded"))
               return;
            if(isFireFox || isEdge)
            {
                // Р”Р»СЏ Firefox РІРјРµСЃС‚Рµ СЃ СЃРѕРѕР±С‰РµРЅРёРµРј cadesplugin_loaded РїСЂРёР»РµС‚Р°РµС‚ url РґР»СЏ Р·Р°РіСЂСѓР·РєРё nmcades_plugin_api.js
                var url = event.data.substring(event.data.indexOf("url:") + 4);
                var fileref = document.createElement('script');
                fileref.setAttribute("type", "text/javascript");
                fileref.setAttribute("src", url);
                fileref.onerror = plugin_loaded_error;
                fileref.onload = firefox_or_edge_nmcades_onload;
                document.getElementsByTagName("head")[0].appendChild(fileref);
                // Р”Р»СЏ Firefox Рё Edge Сѓ РЅР°СЃ С‚РѕР»СЊРєРѕ РїРѕ РѕРґРЅРѕРјСѓ СЂР°СЃС€РёСЂРµРЅРёСЋ.
                failed_extensions++;
            }else {
                cpcsp_chrome_nmcades.check_chrome_plugin(plugin_loaded, plugin_loaded_error);
            }
        }, false);
    }

    //Р—Р°РіСЂСѓР¶Р°РµРј СЂР°СЃС€РёСЂРµРЅРёСЏ РґР»СЏ Chrome, Opera, YaBrowser, FireFox, Edge
    function load_extension()
    {

        if(isFireFox || isEdge){
            // РІС‹Р·С‹РІР°РµРј callback СЂСѓРєР°РјРё С‚.Рє. РЅР°Рј РЅСѓР¶РЅРѕ СѓР·РЅР°С‚СЊ ID СЂР°СЃС€РёСЂРµРЅРёСЏ. РћРЅ СѓРЅРёРєР°Р»СЊРЅС‹Р№ РґР»СЏ Р±СЂР°СѓР·РµСЂР°.
            nmcades_api_onload();
            return;
        } else {
            // РІ Р°СЃРёРЅС…СЂРѕРЅРЅРѕРј РІР°СЂРёР°РЅС‚Рµ РґР»СЏ chrome Рё opera РїРѕРґРєР»СЋС‡Р°РµРј РѕР±Р° СЂР°СЃС€РёСЂРµРЅРёСЏ
            var fileref = document.createElement('script');
            fileref.setAttribute("type", "text/javascript");
            fileref.setAttribute("src", "chrome-extension://iifchhfnnmpdbibifmljnfjhpififfog/nmcades_plugin_api.js");
            fileref.onerror = plugin_loaded_error;
            fileref.onload = nmcades_api_onload;
            document.getElementsByTagName("head")[0].appendChild(fileref);
            fileref = document.createElement('script');
            fileref.setAttribute("type", "text/javascript");
            fileref.setAttribute("src", "chrome-extension://epebfcehmdedogndhlcacafjaacknbcm/nmcades_plugin_api.js");
            fileref.onerror = plugin_loaded_error;
            fileref.onload = nmcades_api_onload;
            document.getElementsByTagName("head")[0].appendChild(fileref);
        }
    }

    //Р—Р°РіСЂСѓР¶Р°РµРј РїР»Р°РіРёРЅ РґР»СЏ NPAPI
    function load_npapi_plugin()
    {
        var elem = document.createElement('object');
        elem.setAttribute("id", "cadesplugin_object");
        elem.setAttribute("type", "application/x-cades");
        elem.setAttribute("style", "visibility: hidden");
        document.getElementsByTagName("body")[0].appendChild(elem);
        pluginObject = document.getElementById("cadesplugin_object");
        if(isIE())
        {
            var elem1 = document.createElement('object');
            elem1.setAttribute("id", "certEnrollClassFactory");
            elem1.setAttribute("classid", "clsid:884e2049-217d-11da-b2a4-000e7bbb2b09");
            elem1.setAttribute("style", "visibility: hidden");
            document.getElementsByTagName("body")[0].appendChild(elem1);
            var elem2 = document.createElement('object');
            elem2.setAttribute("id", "webClassFactory");
            elem2.setAttribute("classid", "clsid:B04C8637-10BD-484E-B0DA-B8A039F60024");
            elem2.setAttribute("style", "visibility: hidden");
            document.getElementsByTagName("body")[0].appendChild(elem2);
        }
    }

    //РћС‚РїСЂР°РІР»СЏРµРј СЃРѕР±С‹С‚РёРµ С‡С‚Рѕ РІСЃРµ РѕРє.
    function plugin_loaded()
    {
        plugin_resolved = 1;
        if(canPromise)
        {
            plugin_resolve();
        }else {
            window.postMessage("cadesplugin_loaded", "*");
        }
    }

    //РћС‚РїСЂР°РІР»СЏРµРј СЃРѕР±С‹С‚РёРµ С‡С‚Рѕ СЃР»РѕРјР°Р»РёСЃСЊ.
    function plugin_loaded_error(msg)
    {
        if(isNativeMessageSupported())
        {
            //РІ Р°СЃРёРЅС…СЂРѕРЅРЅРѕРј РІР°СЂРёР°РЅС‚Рµ РїРѕРґРєР»СЋС‡Р°РµРј РѕР±Р° СЂР°СЃС€РёСЂРµРЅРёСЏ, РµСЃР»Рё СЃР»РѕРјР°Р»РёСЃСЊ РѕР±Р° РїСЂРѕР±СѓРµРј СѓСЃС‚Р°РЅРѕРІРёС‚СЊ РґР»СЏ Opera
            failed_extensions++;
            if(failed_extensions<2)
                return;
            if(isOpera && (typeof(msg) == 'undefined'|| typeof(msg) == 'object'))
            {
                install_opera_extension();
                return;
            }
        }
        if(typeof(msg) == 'undefined' || typeof(msg) == 'object')
            msg = "РџР»Р°РіРёРЅ РЅРµРґРѕСЃС‚СѓРїРµРЅ";
        plugin_resolved = 1;
        if(canPromise)
        {
            plugin_reject(msg);
        } else {
            window.postMessage("cadesplugin_load_error", "*");
        }
    }

    //РїСЂРѕРІРµСЂСЏРµРј С‡С‚Рѕ Сѓ РЅР°СЃ С…РѕС‚СЊ РєР°РєРѕРµ С‚Рѕ СЃРѕР±С‹С‚РёРµ СѓС€Р»Рѕ, Рё РµСЃР»Рё РЅРµ СѓС…РѕРґРёР»Рѕ РєРёРґР°РµРј РµС‰Рµ СЂР°Р· РѕС€РёР±РєСѓ
    function check_load_timeout()
    {
        if(plugin_resolved == 1)
            return;
        if(isFireFox)
        {
            show_firefox_missing_extension_dialog();
        }
        plugin_resolved = 1;
        if(canPromise)
        {
            plugin_reject("РСЃС‚РµРєР»Рѕ РІСЂРµРјСЏ РѕР¶РёРґР°РЅРёСЏ Р·Р°РіСЂСѓР·РєРё РїР»Р°РіРёРЅР°");
        } else {
            window.postMessage("cadesplugin_load_error", "*");
        }

    }

    //Р’СЃРїРѕРјРѕРіР°С‚РµР»СЊРЅР°СЏ С„СѓРЅРєС†РёСЏ РґР»СЏ NPAPI
    function createPromise(arg)
    {
        return new Promise(arg);
    }

    function check_npapi_plugin (){
        try {
            var oAbout = CreateObject("CAdESCOM.About");
            plugin_loaded();
        }
        catch (err) {
            document.getElementById("cadesplugin_object").style.display = 'none';
            // РћР±СЉРµРєС‚ СЃРѕР·РґР°С‚СЊ РЅРµ СѓРґР°Р»РѕСЃСЊ, РїСЂРѕРІРµСЂРёРј, СѓСЃС‚Р°РЅРѕРІР»РµРЅ Р»Рё
            // РІРѕРѕР±С‰Рµ РїР»Р°РіРёРЅ. РўР°РєР°СЏ РІРѕР·РјРѕР¶РЅРѕСЃС‚СЊ РµСЃС‚СЊ РЅРµ РІРѕ РІСЃРµС… Р±СЂР°СѓР·РµСЂР°С…
            var mimetype = navigator.mimeTypes["application/x-cades"];
            if (mimetype) {
                var plugin = mimetype.enabledPlugin;
                if (plugin) {
                    plugin_loaded_error("РџР»Р°РіРёРЅ Р·Р°РіСЂСѓР¶РµРЅ, РЅРѕ РЅРµ СЃРѕР·РґР°СЋС‚СЃСЏ РѕР±СЊРµРєС‚С‹");
                }else
                {
                    plugin_loaded_error("РћС€РёР±РєР° РїСЂРё Р·Р°РіСЂСѓР·РєРµ РїР»Р°РіРёРЅР°");
                }
            }else
            {
                plugin_loaded_error("РџР»Р°РіРёРЅ РЅРµРґРѕСЃС‚СѓРїРµРЅ");
            }
        }
    }

    //РџСЂРѕРІРµСЂСЏРµРј СЂР°Р±РѕС‚Р°РµС‚ Р»Рё РїР»Р°РіРёРЅ
    function check_plugin_working()
    {
        var div = document.createElement("div");
        div.innerHTML = "<!--[if lt IE 9]><iecheck></iecheck><![endif]-->";
        var isIeLessThan9 = (div.getElementsByTagName("iecheck").length == 1);
        if (isIeLessThan9) {
            plugin_loaded_error("Internet Explorer РІРµСЂСЃРёРё 8 Рё РЅРёР¶Рµ РЅРµ РїРѕРґРґРµСЂР¶РёРІР°РµС‚СЃСЏ");
            return;
        }

        if(isNativeMessageSupported())
        {
            load_extension();
        }else if(!canPromise) {
                window.addEventListener("message", function (event){
                    if (event.data != "cadesplugin_echo_request")
                       return;
                    load_npapi_plugin();
                    check_npapi_plugin();
                    },
                false);
        }else
        {
            if(document.readyState === "complete"){
                load_npapi_plugin();
                check_npapi_plugin();
            } else {
                window.addEventListener("load", function (event) {
                    load_npapi_plugin();
                    check_npapi_plugin();
                }, false);
            }
        }
    }

    function set_pluginObject(obj)
    {
        pluginObject = obj;
    }

    //Export
    cadesplugin.JSModuleVersion = "2.1.1";
    cadesplugin.async_spawn = async_spawn;
    cadesplugin.set = set_pluginObject;
    cadesplugin.set_log_level = set_log_level;
    cadesplugin.getLastError = getLastError;

    if(isNativeMessageSupported())
    {
        cadesplugin.CreateObjectAsync = CreateObjectAsync;
        cadesplugin.ReleasePluginObjects = ReleasePluginObjects;
    }

    if(!isNativeMessageSupported())
    {
        cadesplugin.CreateObject = CreateObject;
    }

    if(window.cadesplugin_load_timeout)
    {
        setTimeout(check_load_timeout, window.cadesplugin_load_timeout);
    }
    else
    {
        setTimeout(check_load_timeout, 20000);
    }

    set_constantValues();

    cadesplugin.current_log_level = cadesplugin.LOG_LEVEL_ERROR;
    window.cadesplugin = cadesplugin;
    check_plugin_working();
}());

RuToken = Base.extend({
    
    _plugin: false,               
    _oldstyle: false,
    
    _certs: false,
    _logLevel: false,
    _securityLevel: false,
    
    constructor: function(logLevel, securityLevel) {
        this._logLevel = logLevel || RuToken.LogLevel.Debug;
        this._securityLevel = securityLevel || RuToken.SecurityLevel.Huge;
    },   
    
    /* properties */
    certs: function() {
        return this._certs;
    },
    
    /* private */
    _getCertificatesList: function() {
        var self = this;
        self._certs = {};
        cadesplugin.async_spawn(function *() {
            var MyStoreExists = true;
            try {
                var oStore = yield cadesplugin.CreateObjectAsync("CAdESCOM.Store");
                if (!oStore) {
                    alert("Create store failed");
                    return;
                }

                yield oStore.Open();
            }
            catch (ex) {
                MyStoreExists = false;
            }


            var certCnt;
            var certs;
            if (MyStoreExists && self._securityLevel == RuToken.SecurityLevel.Low) {
                try {
                    certs = yield oStore.Certificates;
                    certCnt = yield certs.Count;
                }
                catch (ex) {
                    alert("РћС€РёР±РєР° РїСЂРё РїРѕР»СѓС‡РµРЅРёРё Certificates РёР»Рё Count: " + cadesplugin.getLastError(ex));
                    return;
                }
                for (var i = 1; i <= certCnt; i++) {
                    var cert;
                    try {
                        cert = yield certs.Item(i);
                    }
                    catch (ex) {
                        alert("РћС€РёР±РєР° РїСЂРё РїРµСЂРµС‡РёСЃР»РµРЅРёРё СЃРµСЂС‚РёС„РёРєР°С‚РѕРІ: " + cadesplugin.getLastError(ex));
                        return;
                    }
                    
                    let certPublicKey = yield cert.PublicKey();
                    let encodedKey = yield certPublicKey.EncodedKey;
                    let certAlgorithm = yield certPublicKey.Algorithm;
                    let certAlgorithmFriendlyName = yield certAlgorithm.FriendlyName;
                    let certValue = yield cert.Export(0);
                    
                    var name = yield cert.SubjectName;
                    var nameParts = name.split(',');
                    var parts = {};
                    nameParts.forEach(function(part) {
                        part = part.split('=');
                        parts[part[0].trim()] = part[1].trim();
                    });
                    
                    
                    var key = yield cert.Thumbprint;
                    self._certs[key] = {
                        ValidFromDate: yield cert.ValidFromDate,
                        SubjectName: name,
                        SubjectParts: parts,
                        Thumbprint: key,
                        cert: cert,
                        certPublicKey: certPublicKey,
                        certPublicKeyEncoded: yield encodedKey.Value(),
                        certAlgorithm: certAlgorithm,
                        certAlgorithmFriendlyName: certAlgorithmFriendlyName,
                        exportedCert: certValue,
                    };

                }

                yield oStore.Close();
            }

            //Р’ РІРµСЂСЃРёРё РїР»Р°РіРёРЅР° 2.0.13292+ РµСЃС‚СЊ РІРѕР·РјРѕР¶РЅРѕСЃС‚СЊ РїРѕР»СѓС‡РёС‚СЊ СЃРµСЂС‚РёС„РёРєР°С‚С‹ РёР· 
            //Р·Р°РєСЂС‹С‚С‹С… РєР»СЋС‡РµР№ Рё РЅРµ СѓСЃС‚Р°РЅРѕРІР»РµРЅРЅС‹С… РІ С…СЂР°РЅРёР»РёС‰Рµ
            try {
                yield oStore.Open(cadesplugin.CADESCOM_CONTAINER_STORE);
                certs = yield oStore.Certificates;
                certCnt = yield certs.Count;
                for (var i = 1; i <= certCnt; i++) {
                    var cert = yield certs.Item(i);
                    var key = yield cert.Thumbprint;
                    if(self._certs[key])
                        continue;

                    let certPublicKey = yield cert.PublicKey();
                    let encodedKey = yield certPublicKey.EncodedKey;
                    let certAlgorithm = yield certPublicKey.Algorithm;
                    let certAlgorithmFriendlyName = yield certAlgorithm.FriendlyName;
                    let certValue = yield cert.Export(0);
                    
                    var name = yield cert.SubjectName;
                    var nameParts = name.split(',');
                    var parts = {};
                    nameParts.forEach(function(part) {
                        part = part.split('=');
                        parts[part[0].trim()] = part[1].trim();
                    });

                    self._certs[key] = {
                        ValidFromDate: yield cert.ValidFromDate,
                        SubjectName: name,
                        SubjectParts: parts,
                        Thumbprint: key,
                        cert: cert,
                        certPublicKey: certPublicKey,
                        certPublicKeyEncoded: yield encodedKey.Value(),
                        certAlgorithm: certAlgorithm,
                        certAlgorithmFriendlyName: certAlgorithmFriendlyName,
                        exportedCert: certValue,
                    };
                }
                yield oStore.Close();

            }
            catch (ex) {
            }
              
            out(self._certs);
            self.raiseEvent('ready');
            
        });
    },

    Render: function() {
        var self = this;
        if(!cadesplugin) {
            out('Плагин не загружен');
        }
        cadesplugin.then(function () {
            cadesplugin.set_log_level(self._logLevel);
            self._getCertificatesList();
        }, function(error) {
            out(error);
        });
        
        return this;
    },
    
    _hashFileData: function (certIndex, dataInBase64, callback) {
        
        // делаем запрос на сервер для формирование кэша по нужному алгоритму
        
        var self = this;
        var cert = this._certs[certIndex];
        try {
            cadesplugin.async_spawn(function *() {
                
                var oHashedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.HashedData");

                oHashedData.Algorithm = cadesplugin.CADESCOM_HASH_ALGORITHM_CP_GOST_3411_2012_512 
                oHashedData.DataEncoding = cadesplugin.CADESCOM_BASE64_TO_BINARY
                out(dataInBase64);
                yield oHashedData.Hash(dataInBase64);
                
                var sHashValue = yield oHashedData.Value;
                if(callback)
                    callback.apply(self, [sHashValue, oHashedData])

            });
        } catch (e) {
            out(e);
        }
    },
    
    HashFileData: function(action, file, callback) {
        
        var form_data = new FormData();
        form_data.append('file', file);
        $.ajax({
            url: '/.ajax/?cmd=' + action,
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function(d){
                d = JSON.parse(d);
                
                callback.apply(self, [d.hash]);
                
                
            }
        });
        
    },
    
    EncodeFile: function(cert, fileData, callback) {
        var self = this;
        
        cadesplugin.async_spawn(function*(args) {
         
            var certificate = self._certs[args[0]].cert;
            var Signature;
            try
            {
                var CAPICOM_AUTHENTICATED_ATTRIBUTE_SIGNING_TIME = 0;

                var errormes = "";
                try {
                    var oSigner = yield cadesplugin.CreateObjectAsync("CAdESCOM.CPSigner");
                } catch (err) {
                    errormes = "Failed to create CAdESCOM.CPSigner: " + err.number;
                    throw errormes;
                }
                
                
                var oSigningTimeAttr = yield cadesplugin.CreateObjectAsync("CADESCOM.CPAttribute");

                yield oSigningTimeAttr.propset_Name(CAPICOM_AUTHENTICATED_ATTRIBUTE_SIGNING_TIME);
                yield oSigningTimeAttr.propset_Value(new Date());
                
                /*var attr = yield oSigner.AuthenticatedAttributes2;
                yield attr.Add(oSigningTimeAttr);


                var oDocumentNameAttr = yield cadesplugin.CreateObjectAsync("CADESCOM.CPAttribute");
                var CADESCOM_AUTHENTICATED_ATTRIBUTE_DOCUMENT_NAME = 1;
                yield oDocumentNameAttr.propset_Name(CADESCOM_AUTHENTICATED_ATTRIBUTE_DOCUMENT_NAME);
                yield oDocumentNameAttr.propset_Value("Document Name");
                yield attr.Add(oDocumentNameAttr);*/
                              
                if (oSigner) {
                    yield oSigner.propset_Certificate(certificate);
                }
                else {
                    errormes = "Failed to create CAdESCOM.CPSigner";
                    throw errormes;
                }

                var oSignedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
                var CADES_BES = 1;

                var dataToSign = args[1];
                if (dataToSign) {
                    yield oSignedData.propset_ContentEncoding(1); //CADESCOM_BASE64_TO_BINARY
                    yield oSignedData.propset_Content(dataToSign);
                    yield oSigner.propset_Options(1); //CAPICOM_CERTIFICATE_INCLUDE_WHOLE_CHAIN
                    try {
                        var StartTime = Date.now();
                        Signature = yield oSignedData.SignCades(oSigner, CADES_BES);
                    }
                    catch (err) {
                        errormes = "Не удалось создать подпись из-за ошибки: " + cadesplugin.getLastError(err);
                        throw errormes;
                    }
                }
                
                if(callback)
                    callback.apply(self, [Signature, fileData]);

            }
            catch(err) {
                out(err);
            }
        }, cert, fileData); //cadesplugin.async_spawn         
        
        return this;
    },
    
}, {
    
    LogLevel: {
        Debug: cadesplugin.LOG_LEVEL_DEBUG,
        Info: cadesplugin.LOG_LEVEL_INFO,
        Error: cadesplugin.LOG_LEVEL_ERROR,
    },
    
    SecurityLevel: {
        Huge: 1,
        Low: 0,
    },
    
});