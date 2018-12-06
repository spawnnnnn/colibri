Data.Recordset = Base.extend({
    
    _source: '',
    _ajaxClientClass: '',
    
    _affected: 0,
    _pagesize: 0,
    _paged: true,
    _tag: null,
        
    _filters: false,
    _sort: false,

    _requests: false,
    _userToken: false,
    _domain: null,
    _token: 'token_' + Base64.encode((new Date()).getTime() + ''),
    _sync: false,
    
    constructor: function(dataSourceName, ajaxClient, filters) {
        
        this._ajaxClientClass = ajaxClient;
        if(!this._ajaxClientClass) 
            this._ajaxClientClass = 'DataSourceAjaxHandler';
        
        this._filters = filters;
        this._source = dataSourceName;
        
        this._domain = location.protocol + '//' + document.domain;
        this._token = this._token;
        this._requests = {};
        this._sync = false; 
        this.base();

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
            reqKey += '.' + ((new Date()).getTime());
        
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
                data = $.parseJSON(data);
                
                delete self._requests[this.reqKey];
                handler.apply(self, [data]); 
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if(errorThrown == 'Unauthorized' && location.hash != '') {
                    location.reload();
                    return;
                }
                delete self._requests[this.reqKey];
                handler.apply(self, [{error: true, message: textStatus}]);
            }
        });
        
        this._requests[reqKey].reqKey = reqKey;
                
    },
    
    _requestJsonp: function(cmd, data, handler) {
        var self = this;
        
        var dataStr = '';
        var callback = cmd.replace(/\./, '_');

        data.requestTime = parseInt(((new Date()).getTime())/1000);
        data.token = this._token;
        dataStr = Base64.encode(JSON.stringify(data));
        
        var reqKey = cmd;
        if(!this._sync)
            reqKey += '.' + ((new Date()).getTime());
        
        if(this._requests[reqKey] != undefined)
            this._requests[reqKey].abort();

        this._requests[reqKey] = $.ajax({
            url: this._domain + '/.service/?cmd=' + cmd + '&t=' + dataStr,
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

            
        /*this._requests[reqKey] = $.jsonp({
            url: this._domain + '/.service/?cmd=' + cmd + '&t=' + dataStr,
            callbackParameter: 'callback',
            callback: callback,
            success: function(data) {
                delete self._requests[this.reqKey];
                handler.apply(self, [data]); 
            },
            error: function(jqXHR, textStatus, errorThrown) {
                
                delete self._requests[this.reqKey];
                handler.apply(self, [{error: true, message: textStatus}]);
            }
        }); */
        
        this._requests[reqKey].reqKey = reqKey;
                
    },
    
    pagesize: function(value) {
        if(value == undefined) 
            return this._pagesize;
        else {
            this._pagesize = value;
            return this;
        }
    }, 
    
    affected: function(value) {
        if(value == undefined) 
            return this._affected;
        else {
            this._affected = value;
            return this;
        }
    }, 
    
    paged: function(value) {
        if(value == undefined) 
            return this._paged;
        else {
            this._paged = value;
            return this;
        }
    }, 
    
    tag: function(value) {
        if(value == undefined) 
            return this._tag;
        else {
            this._tag = value;
            return this;
        }
    }, 
    
    _getFilters: function() {
        var f = [];
        $(this._filters).each(function(i, o) {
            if(o.replaceAll)
                f.push(o.replaceAll('&lt;', '<').replaceAll('&gt;', '>'));
        });
        return f;
    },

    Init: function(filters, sort, okCallback, errorCallback) {
        var self = this;
        
        this._filters = filters;
        this._sort = sort;                                                                                            
        this._request(this._ajaxClientClass + '.Init', {source: this._source, filter: this._filters, sort: this._sort}, function(data) {
            
            if(data.error) {
                /*if(window.Alert) {
                    window.Alert.Show({
                        message: data.message,
                        title: 'Ошибка запроса',
                        removeCancelButton: true,
                        okButtonTitle: 'Хорошо'
                    });
                }
                else
                    alert(data.message);*/
                
                if(errorCallback)
                    errorCallback.apply(self, [data]);
                
                return;
            }
            
            self._pagesize = data.pagesize;
            self._affected = data.affected;
            
            if(okCallback)
                okCallback.apply(self, []);
            
        });
        
        
    }, 
    
    Load: function(page, okCallback, errorCallback) {
        
        this._request(this._ajaxClientClass + '.Load', {source: this._source, filter: this._filters, sort: this._sort, page: page}, function(data) {
            
            if(data.error) {
                /*if(window.Alert) {
                    window.Alert.Show({
                        message: data.message,
                        title: 'Ошибка запроса',
                        removeCancelButton: true,
                        okButtonTitle: 'Хорошо'
                    });
                }
                else
                    alert(data.message);*/
                    
                if(errorCallback)
                    errorCallback.apply(self, []);
                    
                return;
            }

            if(okCallback)
                okCallback.apply(self, [data]);
            
        });
            
    },
    
    Save: function(row, okCallback, errorCallback) {
        
        this._request(this._ajaxClientClass + '.Save', {source: this._source, row: row}, function(data) {
            
            if(data.error) {
                /*if(window.Alert) {
                    window.Alert.Show({
                        message: data.message,
                        title: 'Ошибка запроса',
                        removeCancelButton: true,
                        okButtonTitle: 'Хорошо'
                    });
                }
                else
                    alert(data.message);*/
                    
                if(errorCallback)
                    errorCallback.apply(self, []);
                    
                return;
            }
            
            if(okCallback)
                okCallback.apply(self, [data]);
        });
            
    },

    Delete: function(row, okCallback, errorCallback) {
        
        this._request(this._ajaxClientClass + '.Delete', {source: this._source, row: row}, function(data) {
            
            if(data.error) {
                /*if(window.Alert) {
                    window.Alert.Show({
                        message: data.message,
                        title: 'Ошибка запроса',
                        removeCancelButton: true,
                        okButtonTitle: 'Хорошо'
                    });
                }
                else
                    alert(data.message);*/
                    
                if(errorCallback)
                    errorCallback.apply(self, []);
                    
                return;
            }

            if(okCallback)
                okCallback.apply(self, [data]);
            
        });
            
    },
    
    Export: function(fields, filters, okCallback, errorCallback) {
        
        var self = this;
        
        var flds = [];
        $(fields).each(function(i, f) {
            flds.push(JSON.parse(JSON.stringify(f)));
        });
        $(flds).each(function(i, ff) {
            if(ff.lookup) 
                delete ff.lookup.rows;
        });
        
        this._filters = filters;
        this._request(this._ajaxClientClass + '.Export', {source: this._source, fields: flds, filter: this._filters, mail: false}, function(data) {
            
            if(data.error) {
                /*if(window.Alert) {
                    window.Alert.Show({
                        message: data.message,
                        title: 'Ошибка запроса',
                        removeCancelButton: true,
                        okButtonTitle: 'Хорошо'
                    });
                }
                else
                    alert(data.message);*/
                    
                if(errorCallback)
                    errorCallback.apply(self, []);
                    
                return;
            }
            
            if(okCallback)
                okCallback.apply(self, [data]);
            
        });
        
    },
    
    ExportToMail: function(fields, filters, okCallback, errorCallback) {
        
        var self = this;
        
        var flds = [];
        $(fields).each(function(i, f) {
            flds.push(JSON.parse(JSON.stringify(f)));
        });
        $(flds).each(function(i, ff) {
            if(ff.lookup) 
                delete ff.lookup.rows;
        });
        
        this._filters = filters;
        this._request(this._ajaxClientClass + '.Export', {source: this._source, fields: flds, filter: this._filters, mail: true}, function(data) {
            
            if(data.error) {
                /*if(window.Alert) {
                    window.Alert.Show({
                        message: data.message,
                        title: 'Ошибка запроса',
                        removeCancelButton: true,
                        okButtonTitle: 'Хорошо'
                    });
                }
                else
                    alert(data.message);*/
                    
                if(errorCallback)
                    errorCallback.apply(self, []);
                    
                return;
            }
            
            if(okCallback)
                okCallback.apply(self, [data]);
            
        });
        
    },
    
    Import: function(fields, data, okCallback, errorCallback) {
        
        var self = this;
        
        var flds = [];
        $(fields).each(function(i, f) {
            flds.push(JSON.parse(JSON.stringify(f)));
        });
        $(flds).each(function(i, ff) {
            if(ff.lookup) 
                delete ff.lookup.rows;
        });
        
        
       
        this._request(this._ajaxClientClass + '.Import', {source: this._source, fields: flds, file: data.file, date: data.date}, function(data) {
            
            if(data.error) {
                /*if(window.Alert) {
                    window.Alert.Show({
                        message: data.message,
                        title: 'Ошибка запроса',
                        removeCancelButton: true,
                        okButtonTitle: 'Хорошо'
                    });
                }
                else
                    alert(data.message);*/
                    
                if(errorCallback)
                    errorCallback.apply(self, []);
                    
                return;
            }
            
            if(okCallback)
                okCallback.apply(self, [data]);
            
        });
        
    }
    
    
}, {});