Application.Services.StatsApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('stats'), null);
    }, 
    
    SaveStats: function(service, cid, date, request, results, handler) {
        
        this._request('StatsAjaxHandler.SaveStats', {
            service: service,
            cid: cid, 
            date: date.toDbDate(), 
            request: Json.Encode(request), 
            results: Json.Encode(results)
        }, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('stats.saved', {cacheId: cid, returnData: data});
            
            handler.apply(this, [data]);
            
        });
                                                                           
        return this;
    }, 
    
    SectionData: function(service, bank, section, handler) {
        
        this._request('StatsAjaxHandler.GetSectionData', {
            service: service,
            bank: bank, 
            section: section,
        }, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('sectiondata.loaded', {returnData: data});
            
            handler.apply(this, [data]);
            
        });
        
        return this;
    }
    
    
});