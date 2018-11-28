
JsApi = Base.extend({
    
    _key: false,
    _hash: false,
    
    _domain: null,
    _token: 'token_' + Base64.encode((new Date()).Ticks + ''),
    
    _userToken: false,
    
    _requests: false,
    
    
    constructor: function(domain, token, sync) {
        this._domain = domain ? domain : location.protocol + '//' + document.domain;
        this._token = token ? token : this._token;
        this._requests = {};
        this._sync = sync == undefined ? false : sync; 
        this.base();
    },
    
    _request: function(cmd, data, handler) {
        var self = this;
        
        var dataStr = '';
        var callback = cmd.replace(/\./, '_');

        data.requestTime = parseInt(((new Date()).getTime())/1000);
        data.token = this._token;
        dataStr = Base64.encode(JSON.stringify(data));
        
        var reqKey = cmd;
        if(!this._sync)
            reqKey += '.' + ((new Date()).getTime()) + String.GUID();
        
        if(this._requests[reqKey] != undefined)
            this._requests[reqKey].abort();
            
        this._requests[reqKey] = $.ajax({
            method: 'post',
            url: this._domain + '/.service/?cmd=' + cmd,
            data: {__j: dataStr},
            dataType: 'text',
            /*callbackParameter: 'callback',
            callback: callback,*/
            success: function(data) {
                data = JSON.parse(data);
                
                delete self._requests[this.reqKey];
                handler.apply(self, [data]); 
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if(errorThrown == 'Unauthorized') {
                    location.reload();
                    return;
                }
                delete self._requests[this.reqKey];
                handler.apply(self, [{error: true, message: textStatus}]);
            }
        });
        
        this._requests[reqKey].reqKey = reqKey;
    },

    _requestFileUpload: function(cmd, file, data, progressHandler, handler) {
        var self = this;
        
        var dataStr = '';
        var callback = cmd.replace(/\./, '_');

        data.requestTime = parseInt(((new Date()).getTime())/1000);
        data.token = this._token;
        dataStr = Base64.encode(JSON.stringify(data));

        var fd = new FormData();
        fd.append("file", file);        
        fd.append("__j", dataStr);
        
        
        var reqKey = cmd;
        if(!this._sync)
            reqKey += '.' + ((new Date()).getTime()) + String.GUID();
        
        if(this._requests[reqKey] != undefined)
            this._requests[reqKey].abort();
            
        this._requests[reqKey] = $.ajax({
            xhr: function() {
                var xhrobj = $.ajaxSettings.xhr();
                if (xhrobj.upload) {
                        xhrobj.upload.addEventListener('progress', function(event) {      
                            
                            if(progressHandler) {
                                progressHandler.apply(self, [event, data])
                            }
                            
                            
                        }, false);
                    }
                return xhrobj;
            },
            method: 'post',
            url: this._domain + '/.service/?cmd=' + cmd,
            data: fd,
            dataType: 'text',
            contentType: false,
            processData: false,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                delete self._requests[this.reqKey];
                handler.apply(self, [data]); 
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if(errorThrown == 'Unauthorized') {
                    location.reload();
                    return;
                }
                delete self._requests[this.reqKey];
                handler.apply(self, [{error: true, message: textStatus}]);
            }
        });
        
        this._requests[reqKey].reqKey = reqKey;
    },
    
    _tokenize: function() {
        
    }
    

}, {
    
});