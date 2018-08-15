Application.Services.CascoApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('insurance'), null);
    },
    
    SearchFor: function(searchOptions, handler) {
        
        var options = {
            vehbrand:'acura',
            vehmodel:'mdx',
            vehyear:2009,
            vehpower:150,
            vehregion:'moskva',
            vehcost:1500000,
            vehwheel:0,
            vehcredit:0,
            vehbank:'',
            juridical:0,
            drvcount:1,
            drvgender:'M',
            drvage:'30',
            drvexp:'10',
            drvfamily:'0',
            drvchild:'0',
            pubrand:-1,
            pumodel:-1,
            psbrand:-1,
            psmodel:-1,
            franch:0,
            riskcover:'full',
            avarkom:0,
            refund:1,
            evacuation:0,
            techhelp:0,
            norefpay:0,
            dagosum:0,
            nssum:0,
            docost:0,
            paymenttype:1
        };
        
        options = $.extend(options, searchOptions);
        this._request('CascoAjaxHandler.Calculate', options, function(data) {

            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    GetVehBrands: function(handler) {
        this._request('CascoAjaxHandler.GetVehBrands', {}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    GetVehModels: function(vehbrand, handler) {
        this._request('CascoAjaxHandler.GetVehModels', {vehbrand: vehbrand}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    GetVehCost: function(vehbrand, vehmodel, vehpower, vehyear, handler) {
        this._request('CascoAjaxHandler.GetVehCost', {vehbrand:vehbrand,vehmodel:vehmodel,vehpower:vehpower,vehyear:vehyear}, function(data) {
            
            data.e_min_cost = data.min_cost - (data.max_cost * 25) / 100;
            data.e_max_cost = data.max_cost + (data.max_cost * 50) / 100;
            
            if (data.e_min_cost < 10000) {
                data.e_min_cost = 10000;
            }
            
            data.e_min_cost = data.e_min_cost - (data.e_min_cost % 10000);
            data.e_max_cost = data.e_max_cost - (data.e_max_cost % 10000);

            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    GetVehPowers: function(vehbrand, vehmodel, handler) {
        this._request('CascoAjaxHandler.GetVehPowers', {vehbrand:vehbrand,vehmodel:vehmodel}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    GetPuModels: function(pubrand, handler) {
        this._request('CascoAjaxHandler.GetPuModels', {pubrand: pubrand}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    GetPsModels: function(psbrand, handler) {
        this._request('CascoAjaxHandler.GetPsModels', {psbrand: psbrand}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    }

});

Application.Services.OsagoApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('insurance'), null);
    },
    
    GetVehModels: function(vehbrand, handler) {
        this._request('OsagoAjaxHandler.GetVehModels', {vehbrand: vehbrand}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    GetVehCost: function(vehbrand, vehmodel, vehpower, vehyear, handler) {
        this._request('OsagoAjaxHandler.GetVehCost', {vehbrand:vehbrand,vehmodel:vehmodel,vehpower:vehpower,vehyear:vehyear}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    GetVehPowers: function(vehbrand, vehmodel, handler) {
        this._request('OsagoAjaxHandler.GetVehPowers', {vehbrand:vehbrand,vehmodel:vehmodel}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    SearchFor: function(searchOptions, handler) {
        
        var options = {
            "vehtype":"1",
            "vehpower":"100",
            "vehregion":"moskva",
            "juridical":"0",
            "drvcount":"1",
            "drvage":"18",
            "drvexp":"0",
            "bonusmalus":"0",
            "term":"10"
        };
        
        options = $.extend(options, searchOptions);
        this._request('OsagoAjaxHandler.Calculate', options, function(data) {

            handler.apply(this, [data]);
            
        });
        
        return this;
    }
    
});

Application.Services.VzrApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('insurance'), null);
    },
    
    GetCountryInfo: function(countries, handler) {
        this._request('VzrAjaxHandler.GetCountryInfo', {countries: countries}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    }
    
});

Application.Services.InsuranceApi = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('insurance'), null);
    },
    
    DoTask: function(executor, settings, handler) {
        this._request('TasksAjaxHandler.DoTask', {executor: executor, settings: settings}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    }
    
});

Application.Services.Casco2Api = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('insurance2'), null);
    },
    
    SearchFor: function(searchOptions, handler) {
        
        var options = {
        };
        
        options = $.extend(options, searchOptions);
        this._request('CascoAjaxHandler.Process', options, function(data) {

            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    GetVehBrands: function(handler) {
        this._request('CascoAjaxHandler.GetVehBrands', {}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    GetVehModels: function(vehbrand, handler) {
        this._request('CascoAjaxHandler.GetVehModels', {vehbrand: vehbrand}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    GetVehCost: function(vehbrand, vehmodel, vehpower, vehyear, handler) {
        this._request('CascoAjaxHandler.GetVehCost', {vehbrand:vehbrand,vehmodel:vehmodel,vehpower:vehpower,vehyear:vehyear}, function(data) {
            
            data.e_min_cost = data.min_cost - (data.max_cost * 25) / 100;
            data.e_max_cost = data.max_cost + (data.max_cost * 50) / 100;
            
            if (data.e_min_cost < 10000) {
                data.e_min_cost = 10000;
            }
            
            data.e_min_cost = data.e_min_cost - (data.e_min_cost % 10000);
            data.e_max_cost = data.e_max_cost - (data.e_max_cost % 10000);

            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    GetVehPowers: function(vehbrand, vehmodel, handler) {
        this._request('CascoAjaxHandler.GetVehPowers', {vehbrand:vehbrand,vehmodel:vehmodel}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    }

});

Application.Services.Osago2Api = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('insurance2'), null);
    },
    
    SearchFor: function(searchOptions, handler) {
        
        var options = {
        };
        
        options = $.extend(options, searchOptions);
        this._request('OsagoAjaxHandler.Process', options, function(data) {

            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    GetVehBrands: function(handler) {
        this._request('OsagoAjaxHandler.GetVehBrands', {}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    },
    
    GetVehModels: function(vehbrand, handler) {
        this._request('OsagoAjaxHandler.GetVehModels', {vehbrand: vehbrand}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    }
});

Application.Services.Insurance2Api = Application.Services.ServiceApi.extend({
    
    constructor: function() {
        this.base(this._serviceUrl('insurance2'), null);
    },
    
    DoTask: function(executor, settings, handler) {
        this._request('TasksAjaxHandler.DoTask', {executor: executor, settings: settings}, function(data) {
            handler.apply(this, [data]);
        });
        
        return this;
    }
    
});