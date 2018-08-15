Application.Services.AdvApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('app'), null);
    },
    
    SearchFor: function(request, handler) {

        var options = {
        };
        
        options = $.extend(options, request);
        
        this._request('AdvAjaxHandler.Process', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('adv.search.results', {options: options, returnData: data});
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    SetDevice: function(request, handler) {

        var options = {
        };
        
        options = $.extend(options, request);
        
        this._request('AdvAjaxHandler.SetDevice', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('adv.search.results', {options: options, returnData: data});
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
});

Application.Services.GeoApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('app'), null);
    },
    
    Cities: function(request, handler) {

        var options = {
        };
        
        options = $.extend(options, request);
        
        this._request('GeoAjaxHandler.GetCities', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('geo.cities.results', {options: options, returnData: data});
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
});

