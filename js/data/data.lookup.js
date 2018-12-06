Data.Lookup = JsApi.extend({

        
    LookupData: function(obj, lookup, callback) {
        if(lookup.rows !== undefined) {
            if(callback != undefined)
                callback.apply(obj, [lookup]);
            return ;
        }
        this._request('ApplicationAjaxHandler.LookupData', {lookup: lookup}, function(data) {
            
            if(data.error) {
                //alert(data.message);
            }
            
            if(callback != undefined)
                callback.apply(obj, [data]);
                
        });
    },

    GetLookups: function(fields, callback) {
        var self = this;
        self.lookupsComplete = 0;
        $(fields).each(function(i, f) {
            if(f.lookup == undefined) return true;
            
            self.lookupsComplete++;
            self.LookupData(f.lookup, f.lookup, function(data) {
                this.rows = toArray(data.rows);
                self.lookupsComplete--;
            });
        });
        
        if(self.lookupsComplete == 0) 
            callback();
        else {
            var interval1Sec = -1;
            var interval = -1;
            var t = function() {
                if(self.lookupsComplete == 0) {
                    clearTimeout(interval1Sec);
                    callback();
                }
                else {
                    interval = setTimeout(t, 100);
                }
            };
            interval = setTimeout(t, 100);
            interval1Sec = setTimeout(function() {
                clearTimeout(interval);
                callback();
            }, 10000);
        }
    },
    
    AddLookupData: function(obj, lookup, value, callback) {
        
        this._request('ManagementSystemAjaxHandler.AddLookupData', {lookup: lookup, value: value}, function(data) {
            
            if(data.error) {
                //alert(data.message);
            }
            
            if(callback != undefined)
                callback.apply(obj, [data]);
                
        });
        
    },
    
    

});