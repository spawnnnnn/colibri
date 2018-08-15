Application.Services.AuthApi = Application.Services.ServiceApi.extend({  
    
    _unscoredClientId: false,
    _sessionID: false,                                                                   
    _apitoken: false,                                                                    
    
    _eventListenerStarted: false,
    
    constructor: function() {
        this.base(this._serviceUrl('auth'), null, false);
        
        this._apitoken = false;
        this._eventListenerStarted = false;
        
        if(typeof ga != 'undefined') {
            var self = this;
            ga(function(tracker) { 
                self._unscoredClientId = tracker.get("clientId");
            });
        }
        
    },
    
    Token: function() {   
        var self = this;
        
        var options = {
            session: this._sessionID
        };
        
        if($.cookie('TKN' + this._sessionID)) {
            this._apitoken = JSON.parse(Base64.decode($.cookie('TKN' + this._sessionID)));

            /* raising cache accessed event */
            this.raiseEvent('auth.token', {});
        }
        else {
                
            this._request('AuthAjaxHandler.Token', options, function(data) {
                
                self._apitoken = data.token;
                
                /* соxраняем токен в куки */
                $.cookie('TKN' + self._sessionID, Base64.encode(JSON.stringify(data.token)), {expires: data.token.lifetime / 24 / 60 / 60 / 1000, path: '/'});
                
                /* raising cache accessed event */
                this.raiseEvent('auth.token', {});
                
            });
            
        }
        
        return this;
        
    },
    
    Check: function(handler) {

        var options = { hash: this._apitoken.token, session: this._sessionID };
        
        this._request('AuthAjaxHandler.Check', options, function(data) {
            
            if(data.error && data.message == 'session is not valid') {
                this._apitoken = false;
                $.cookie('TKN' + this._sessionID, '', {expires: 0});
                this.Token();
            }
            else {
                this.current = data.member;
            }
            
            /* raising cache accessed event */
            this.raiseEvent('auth.check', {options: options, returnData: data});
            
            /* raising handler */
            if(handler) handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    Login: function(login, password, handler) {

        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            login: login,
            password: password,
            clientid: this._unscoredClientId,
        };
        
        var geo = this._getGeoInfo();
        if (typeof geo.id !== 'undefined')
            options.city = geo.id;
        if (typeof geo.timeoffset !== 'undefined')
            options.timeoffset = geo.timeoffset;
        
        // options = $.extend(options, request);
        
        this._request('AuthAjaxHandler.Login', options, function(data) {
            
            this.current = !data.error ? data.member : false;
            
            /* raising cache accessed event */
            this.raiseEvent('auth.login', {options: options, returnData: data});
            
            /* raising handler */
            if(handler) handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    Logout: function(handler) {

        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
        };
        
        // options = $.extend(options, request);
        
        this._request('AuthAjaxHandler.Logout', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.logout', {options: options, returnData: data});
            
            /* raising handler */
            if(handler) handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    Reset: function(email, handler) {

        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            email: email
        };
        
        // options = $.extend(options, request);
        
        this._request('AuthAjaxHandler.Reset', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.reset', {options: options, returnData: data});
            
            /* raising handler */
            if(handler) handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    SaveProfile: function(data, handler) {
        
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            profile: data,
        };
        
        this._request('AuthAjaxHandler.SaveProfile', options, function(data) {
            
            /* changing profile data */
            if(this.current) {
                this.current = $.extend(this.current, data.profile);
            }
            
            /* raising cache accessed event */
            this.raiseEvent('auth.saveprofile', {options: options, returnData: data.events});    
            
            /* raising handler */
            if(handler) handler.apply(this, [data]);
            
        });
        
        return this;
        
    },
    
    CheckPhone: function(phone, code, handler) {
        
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            phone: phone,
            code: code,
        };
        
        this._request('AuthAjaxHandler.CheckPhone', options, function(data) {
            
            /* changing profile data */
            if(this.current) {
                this.current = $.extend(this.current, data.profile);
            }
            
            /* raising cache accessed event */
            this.raiseEvent('auth.checkphonne', {options: options, returnData: data.events});    
            
            /* raising handler */
            if(handler) handler.apply(this, [data]);
            
        });
        
        return this;
        
    }, 
    
    LoginWithSocial: function(method, isFloat) { 
    
        var self = this;
        if(isFloat === true) {
            
            this.raiseEvent('auth.social.float.start', {});
            
            var domain = this._domain;
            var peakMessage = function(ev) {
                if(ev && ev.data) {
                    if(ev.data.message === "relocate") {
                        self.raiseEvent('auth.social.float.complete', {});
                        
                        if(ev.data.href == 'reload')        
                            window.location.reload();
                        else                        
                            window.location.href = ev.data.href;
                    }
                    else if(ev.data.message == 'cancel') {
                        self.raiseEvent('auth.social.float.cancel', {});
                    }
                    else if(ev.data.message == 'reset') {
                        self.Reset(ev.data.email, {});
                    }
                }   
            };

            if(window.addEventListener)
                window.addEventListener("message", peakMessage);
            else
                window.attachEvent("onmessage", peakMessage);

            var w = window.open(this._domain + "/.social/?method=" + method + "&clientid=" + this._unscoredClientId + "&session=" + this._sessionID + "&step=start&return=close", "sociallog", "width=800,height=600,resize=no,address=no,toolbar=no");
            w.opener = window;
            w.unloadTimer = setInterval(function() {
                if(w.closed) {
                    peakMessage({data: {message: 'cancel'}});
                    clearInterval(w.unloadTimer);
                }
            }, 100);
        }
        else {
            location = this._domain + "/.social/?method=" + method + "&clientid=" + this._unscoredClientId + "&session=" + this._sessionID + "&step=start&return=" + location.href;
            return;
        }
    },
    
    ServiceIds: function(rev) {
        if(rev === undefined)
            return {
                'test': 0,
                'casco': 1,
                'mobile': 2,
                'consumer': 3,
                'deposits': 4,
                'autocredits': 5,
                'hypothec': 6,
                'greencard': 7,
                'osago': 8,
                'cards': 9,
                'microcredits': 10,
            };
        else 
            return [
                'test',
                'casco',
                'mobile',
                'consumer',
                'deposits',
                'autocredits',
                'hypothec',
                'greencard',
                'osago',
                'cards',
                'microcredits',
            ];
    }, 
    
    Events: function(date, handler) {
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            date: date
        };
        
        this._request('AuthAjaxHandler.Events', options, function(data) {
            
            /* raising cache accessed event */
            if(!data.error) {
                this.raiseEvent('auth.events', {options: options, returnData: data.events});    
            }
            else {
                console.log('error in events sniffer', data.message);
            }
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    StartEventListener: function(timeout) {
        var self = this;
        self._eventListenerStarted = true;
        
        var time = (new Date()).toDbDate();
        var timeout = (timeout || 15) * 1000;
        var timeoutFunction = function(data) {
            if(data.events && data.events.length > 0) {
                time = data.events[0].date;                              
            }
            if(self._eventListenerStarted) {
                setTimeout(function() {
                    self.Events(time, timeoutFunction);
                    time = (new Date()).toDbDate();
                }, timeout);
            }
        };
        self.Events(false, timeoutFunction);
    }, 
    
    StopEventListener: function() {
        self._eventListenerStarted = false;
    }, 
    
    
    Scores: function(handler) {
        
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            clientid: this._unscoredClientId,
        };
        
        console.log(options);
        
        this._request('AuthAjaxHandler.Scores', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.scores', {options: options, returnData: data.events});    
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
        
        
    },
    
    GetPostScore: function(handler) {
        
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            clientid: this._unscoredClientId,
        };                       
        
        this._request('AuthAjaxHandler.GetPostScoreProbabilityAll', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.pers-score', {options: options, returnData: data.events});    
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
        
        
    },
    
    NBKIScores: function(handler) {
        
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
        };
        
        this._request('AuthAjaxHandler.NBKIScores', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.scores', {options: options, returnData: data.events});    
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
        
        
    },
    
    GetAccounts: function(handler) {
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
        };
        
        this._request('AuthAjaxHandler.GetAccounts', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.accounts', {options: options, returnData: data.events});    
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    }, 
    
    AddAccount: function(bid, login, password, handler) {
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            bid: bid,
            login: login,
            password: password,
        };
        
        this._request('AuthAjaxHandler.AddAccount', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.addaccount', {options: options, returnData: data.events});    
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    AddCalculation: function(cid, service, request, results, cleanData, handler) {
        
        var services = this.ServiceIds();

        var desc = ''; 
        if(services[service] == 6)
            desc = request.price.toMoney() + '&nbsp;<span class="icon-rur"></span><br />на ' + request.years + ' лет';
        else if(services[service] == 5)
            desc = request.price.toMoney() + '&nbsp;<span class="icon-rur"></span><br />на ' + request.months + ' мес';
        else if(services[service] == 4)
            desc = request.price.toMoney() + '&nbsp;<span class="icon-rur"></span><br />на ' + request.months + ' мес';
        else if(services[service] == 3)
            desc = request.price.toMoney() + '&nbsp;<span class="icon-rur"></span><br />на ' + request.months + ' мес';
        else if(services[service] == 9) {
            cleanData.targets['all'] = {'name': 'Низкая стоимость обслуживания'};
            desc = cleanData.targets[request.target].name;            
        } else if(services[service] == 10)
            desc = request.price.toMoney() + '&nbsp;<span class="icon-rur"></span><br />на ' + request.weeks + ' недель';
        
        // Вычисление наилучшего показателя расчета
        var bestrate = 0; 
        if(results !== undefined) {
            if(!(results instanceof Array) ) {
                Object.keys(results).map(function(key) {
                    return results[key];
                });
            }                              
            $(results).each(function(i, currentValue) {
                if(!bestrate) {
                    bestrate = currentValue.effective_rate;
                    return;
                }
                if(services[service] == 4) {
                    if(currentValue.effective_rate > bestrate) {
                        bestrate = currentValue.effective_rate;
                    }
                    return;
                }
                if(currentValue.effective_rate < bestrate) {
                    bestrate = currentValue.effective_rate;
                }                            
            });            
        } 
        
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            cid: cid,
            desc: desc,
            service: services[service],
            bestrate: bestrate
        };
        
        this._request('AuthAjaxHandler.AddCalculation', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.addcalculation', {options: options, returnData: data.events});    
            
            /* raising handler */
            if(handler)
                handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    RemoveCalculation: function(cid, handler) {
        
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            cid: cid,
        };
        
        this._request('AuthAjaxHandler.RemoveCalculation', options, function(data) {
            /* raising cache accessed event */
            this.raiseEvent('auth.removecalculation', {options: options, returnData: data.events});    
            
            /* raising handler */
            if(handler)
                handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    GetCalculations: function(page, pagesize, handler) {
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            page: page, 
            pagesize: pagesize,
        };
        
        this._request('AuthAjaxHandler.GetCalculations', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.calculations', {options: options, returnData: data.calculations});    
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    }, 
    
    GetApplications: function(page, pagesize, handler) {
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            page: page, 
            pagesize: pagesize,
        };
        
        this._request('AuthAjaxHandler.GetApplications', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.applications', {options: options, returnData: data.applications});    
            
            /* raising handler */
            handler.apply(this, [data]);
            
        });
        
        return this;
    }, 
    
    UpdateApplication: function(update, id, handler) {
        
        var options = {
            hash: this._apitoken.token,
            session: this._sessionID,
            id: id,
        };
        for (var i in update) {
            options[i] = update[i];
        }
        if (this.current) {
            options.member = this.current.id;
        }
        
        this._request('AuthAjaxHandler.UpdateApplication', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.updateapplication', {options: options, returnData: data.events});    
            
            /* raising handler */
            if(handler)
                handler.apply(this, [data]);
            
        });
        
        return this;
    }, 
    
    AddPostback: function(id, name, data, handler) {
        
        var services = this.ServiceIds();

        var options = {
            id: id,
            name: name,
            data: data,
        };
        
        this._request('AuthAjaxHandler.AddPostback', options, function(data) {
            
            /* raising cache accessed event */
            this.raiseEvent('auth.addpostback', {options: options, returnData: data.events});    
            
            /* raising handler */
            if(handler)
                handler.apply(this, [data]);
            
        });
        
        return this;
    },
    
    _getGeoInfo: function() {
        var geo = {};
        if (typeof JSON.parse == 'function' && typeof $.cookie == 'function') {
            geo = JSON.parse($.cookie('city'));
        }
        return geo;
    },
});
