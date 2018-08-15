Application.Services.MobileApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('mobile'), null);
    }, 
    
    FromCache: function(cacheId, handler) {
        this._request('MobileAjaxHandler.FromCache', {
            cid: cacheId
        }, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('mobile.cache.results', {cacheId: cacheId, returnData: data});
            
            handler.apply(this, data);
            
        });
        
        return this;
    },
    
    SearchFor: function(searchOptions, handler) {
        
        var options = {
            reload: 0,
            zone: 1,
            numbertype: 0,
            paymenttype: 0,
            calls: 3,
            callsavg: 5,
            callsgoals: Json.Encode({0: 25, 2: 25, 1: 25, 3: 25}), 
            callsfrom: Json.Encode({2: 90, 4: 10}),
            callswhen: Json.Encode({1: 90, 2: 10, 0: 0}),
            callswhere: Json.Encode({4: 40, 8: 40, 128: 20}),
            smscount: 5,
            smsperiod: 1,
            mmscount: 0,
            mmsperiod: 0,
            traffic: 'false',
            trafficcount: 0,
            trafficwhen: Json.Encode({0: 50, 1: 40, 2: 10})
        };
        
        options = $.extend(options, searchOptions);
        
        this._request('MobileAjaxHandler.Process', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('mobile.search.results', {options: options, returnData: data});
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    Info: function(operator, tariff, service, handler) {
        
        this._request('MobileAjaxHandler.Info', {
            operator: operator,
            tariff: tariff,
            service: service
        }, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('mobile.info.results', {operator:operator, tariff:tariff, service:service, returnData: data});
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
        
    },
    
    PrecompiledTariffInfo: function(tariff) {
        
        this._request('MobileAjaxHandler.TariffInfo', {
            tariff: tariff
        }, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('mobile.precompiledtariff.info.results', {tariff:tariff, returnData: data});
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
        
    }
    
    
});