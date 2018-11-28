
Module = JsApi.extend({
    
    _loadConfig: function(module, callback) {
        
        var self = this;
        this._request(module + 'AjaxHandler.GetConfig', {}, function(data) {
            if(callback) callback(data);
        });
        
    },                       
    
    _storeConfig: function(module, data, callback) {
        this._request(module + 'AjaxHandler.SaveConfig', {config: data}, function(data) {
            if(callback) callback(data);
        });
    },
    

}, {

});