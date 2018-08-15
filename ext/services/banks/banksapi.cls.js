Application.Services.HypothecApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('banks'), null);
    },
    
    SearchFor: function(searchOptions, handler) {
        
        var options = {
            'region': 1,
            'currency': 'RUR',
            'price': 30000000,
            'years': 15,
            'first': Json.Encode([40, 100]),
            'verification': '2-НДФЛ',
            'collateral': null,
            'insurance': 'complex',
            'annuitet': true,
            'pricing_method': '',
            'citizenship': 1,
            'recording': 1,
            'housing': Json.Encode(['Квартира в новостройке'])
        };
        
        options = $.extend(options, searchOptions);
        
        this._request('HypothecBanksAjaxHandler.Process', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('hypothec.search.results', {options: options, returnData: data});
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    Info: function(creditId, handler) {
        
        this._request('HypothecBanksAjaxHandler.Info', {
            credit: creditId
        }, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('hypothec.info.results', {credit:creditId, returnData: data});
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
        
    }
    
});

Application.Services.AutocreditsApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('banks'), null);
    },
    
    SearchFor: function(searchOptions, handler) {
        
        var options = {
            'months': 36,
            'annuitet': true,
            'kasko': 0,
            'currency': 'RUR',
            'price': 500000,
            'region': 1,
            'verification': '2-НДФЛ',
            'pricing_method': 'fix',
            'collateral': null,
            'insurance': 'complex',
            'recording': 1,
            'first': Json.Encode([20,49]),
            'makeup': 'иностранная марка новая', 
            'brand_origin': 'car_for',
            'is_new': 'car_new',
            'subsidy': 0 
        };
        
        options = $.extend(options, searchOptions);
        
        this._request('AutocreditsBanksAjaxHandler.Process', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('autocredits.search.results', {options: options, returnData: data});
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    Info: function(creditId, handler) {
        
        this._request('AutocreditsBanksAjaxHandler.Info', {
            credit: creditId
        }, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('autocredits.info.results', {credit:creditId, returnData: data});
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
        
    }
    
});

Application.Services.DepositsApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('banks'), null);
    },
    
    SearchFor: function(searchOptions, handler) {
        
        var options = {
            'months': 10,
            'price': 5000000,
            'type': 0,
            'enter_method': '',
            'pricing_method': '',
            'may_update':  '',
            'may_take_part': '',
            'pay_percent_period': '',
            'currency': 'RUR' 
        };
        
        options = $.extend(options, searchOptions);
        
        this._request('DepositsBanksAjaxHandler.Process', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('deposits.search.results', {options: options, returnData: data});
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    Info: function(creditId, handler) {
        
        this._request('DepositsBanksAjaxHandler.Info', {
            credit: creditId
        }, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('deposits.info.results', {credit:creditId, returnData: data});
            
            /* raising handler */
            if(handler)
                handler.apply(this, [data]);
            
        });
        
        return this;
        
    }
    
});

Application.Services.BankApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('banks'), null);
    },
    
    BankUnits: function(options, handler) {
                            
        this._request('InfoBanksAjaxHandler.BankUnits', options, function(data) {     
            
            /* raising cache accessed event */
            this.raiseEvent('banks.units.success', {options: options, returnData: data});
            
            /* raising handler */
            if(handler)
                handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    RawInfo: function(options, handler) {
                            
        this._request('InfoBanksAjaxHandler.PartnersRawInfo', options, function(data) {     
            
            /* raising cache accessed event */
            this.raiseEvent('banks.units.success', {options: options, returnData: data});
            
            /* raising handler */
            if(handler)
                handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
});