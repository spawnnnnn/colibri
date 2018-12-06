
Mods.Modules = Base.extend({
    
    _container: false,
    _manager: false,
    
    _modules: [],
    
    constructor: function(manager) {
        // вызываем инициализатор базового класса
        this.base();
        
        // инициализирует специализированное меню модулей
        this._manager = manager;
        this.Init();
    },
    
    FindModule: function(entry) {
        var ret = false;
        $(this._modules).each(function(i, o) {
            if(o.entry() == entry) {
                ret = o;
                return false;
            }
        });
        return ret;
    },
    
    FindSection: function(module, section) {
        var ret = false;
        $(module.sections()).each(function(i, o) {
            if(o.entry() == section) {
                ret = o;
                return false;
            }
        });
        return ret;
    },
    
    AddModule: function(entry, module) {
        var self = this;
        
        var s = [];
        $(module.sections()).each(function(i, o) {     
            var moduleClass = ManagementSystem.FindModuleClass(module.entry());
            var moduleSectionClass = moduleClass + '.' + (o.class ? o.class :  'ModuleSection');
            var section = false;
            eval('section    = new ' + moduleSectionClass + '(self._manager, module, o);');
            s.push(section);
        });
        module.sections(s);
        
        var s = [];
        $(module.widgets()).each(function(i, o) {     
            var moduleClass = ManagementSystem.FindModuleClass(module.entry());
            var moduleWidgetClass = moduleClass + '.' + (o.class ? o.class :  'ModuleWidget');
            var widget = false;
            eval('widget    = new ' + moduleWidgetClass + '(self._manager, module, o);');
            s.push(widget);
        });
        module.widgets(s);
        
        this._modules.push(module);
    },
    
    ClickOnFirstSection: function(module) {
        this._container.find('a[rel=module][entry=' + module.entry() + ']').parent().find('a[rel=section]:eq(0)').click();
    },
    
    Init: function() {
        
        var self = this;
                     
        this._manager.Modules(function(data) {
            
            if(data.error) {
                alert('Ошибка в модулях: ' + data.message);
                
                if(data.errorno == 'unlogged') {
                    location.reload();
                }
                
                return;
            }
            var modules = data.modules;
            $(modules).each(function(i, module) {
                self.AddModule(module.entry, self._manager.CreateModule(module));    
            });
            
            self.raiseEvent('ready', {});
            
        });
        
    },
    
    Render: function(container) {
        
        var self = this;
        self._container = container;
        
        self._container.append('<div><a href="javascript:void(0);" rel="module" entry="desktop">Рабочий стол</a></div>');
        
        $(this._modules).each(function(i, module) {
            
            self._container.append('<div id="' + module.entry() + '"><a href="javascript:void(0);" rel="module" entry="' + module.entry() + '">' + module.title() + '</a><ul></ul></div>');
            var ul = self._container.find('#' + module.entry() + ' ul');
            $(module.sections()).each(function(j, section) {
                if(section.issection()) {
                    ul.append('<li><hr /></li>');
                }
                ul.append('<li id="' + section.entry() + '_href"><a href="javascript:void(0);" rel="section" entry="' + section.entry() + '">' + section.title() + '</a></li>');
            });
            
        });
        
        self._container.find('a[rel=module]').unbind('click');
        self._container.find('a[rel=module]').click(function() {
            
            self._container.find('div>a').removeClass('selected');
            self._container.find('ul').hide();
            $(this).addClass('selected');
            $(this).next().show();
            
            self.raiseEvent('clickOnModule', {module: self.FindModule($(this).attr('entry'))});
            return false;
        });

        self._container.find('a[rel=section]').unbind('click');
        self._container.find('a[rel=section]').click(function() {

            self._container.find('div ul>li>a').removeClass('selected');
            $(this).addClass('selected');

            var module = self.FindModule($(this).parents('ul').prev().attr('entry'));
            var section = self.FindSection(module, $(this).attr('entry'));
            self.raiseEvent('clickOnSection', {module: module, section: section});
            return false;
        });
        
    },
    
    RenderDesktop: function(widgetsContainer) {
        
        self._widgetsContainer = widgetsContainer;
        self._widgetsContainer.html('');
        self._widgetsContainer.css({overflowY: 'auto'});
        self._widgetsContainer.append('<h1>Рабочий стол</h1>');
        
        $(this._modules).each(function(i, module) {
            $(module.widgets()).each(function(j, widget) {
                var widgetContainer = $('<div class="ui-widget ' + widget.entry() + '"><div class="ui-widget-title">Widget</div><div class="ui-widget-content"></div></div>');
                self._widgetsContainer.append(widgetContainer);
                widget.Render(widgetContainer);
            });
            
        });
    },
    
    
    

}, {
    instance: null,
});

Mods.Module = Base.extend({
    
    _manager: false,
    _module: false,
    _data: false,
    _widgets: false,
    
    constructor: function(manager, module) {
        // вызываем инициализатор базового класса
        this.base();
        
        // инициализирует специализированное меню модулей
        this._manager = manager;
        this._module = module;
        this._data = [];
        
        this.Init();
    },
    
    Init: function() {
        
        var self = this;
                   
        if(this._module instanceof String) {
                     
            // передавали название модуля, а не обьект
            this._manager.Module(this._module, function(data) {
                
                if(data.error) {
                    alert('Ошибка в модулях 2: ' + data.message);
                    
                    if(data.errorno == 'unlogged') {
                        location.reload();
                    }
                    
                    return;
                }
                
                var module = data.module;
                self._module = module;
                
                self.raiseEvent('ready', {});
                
            });
        
        }       
        else {
            // обьект уже инициализирован нужно просто вызвать событие ready
            self.raiseEvent('ready', {});            
        }  
        
    },
    
    LoadData: function(dontFireEvent, reBlock, successCallback, errorCallback) {
        var self = this;
        var dfe = dontFireEvent == undefined ? false : dontFireEvent;
        var rb = reBlock == undefined ? false : reBlock;    

        this._manager._request(this.ajax() + '.Data', {mode: 'load', module: this.entry(), loadmaster: rb}, function(data) {
            if(data.error) {
                if(!dfe) {
                    if(errorCallback)
                        errorCallback.apply(this, [data]);
                    else
                        self.raiseEvent('dataLoadError', {message: data.message});
                }
                return false;
            }
            
            if(!data.result && data.blockData) {
                if(errorCallback)
                    errorCallback.apply(this, [data]);
                else
                    self.raiseEvent('dataLoadError', {message: 'Модуль заблокирован пользователем ' + data.blockData.user + '. Пожалуйста, дождитесь окончания работы пользователя'});
                return false;
            }
            
            self._data = data.result;
            if(!dfe) {
                if(successCallback)
                    successCallback.apply(this, [data]);
                else
                    self.raiseEvent('dataLoadComplete', {});
            }
            
        });
        
    },
    
    StoreData: function(successCallback, errorCallback) {
        var self = this;
        
        this._manager._request(this.ajax() + '.Data', {mode: 'store', module: this.entry(), value: this._data}, function(data) {
            
            if(data.error) {
                // alert(data.message);
                if(errorCallback)
                    errorCallback.apply(this, [data]);
                else
                    self.raiseEvent('dataStoreError', {});
                return false;
            }
            if(successCallback)
                successCallback.apply(this, [data]);
            else
                self.raiseEvent('dataSroreComplete', {});
        });
        
    },
    
    entry: function(val) {
        if(val == undefined) {
            return this._module.entry;
        }
        else
            this._module.entry = val;
    },
    
    title: function(val) {
        if(val == undefined)
            return this._module.title;
        else
            this._module.title = val;
    },
    
    sections: function(val) {
        if(val == undefined)
            return this._module.sections;
        else
            this._module.sections = val;
    },
    
    widgets: function(val) {
        if(val == undefined)
            return this._module.widgets;
        else
            this._module.widgets = val;
    },
    
    section: function(name) {
        var ret = false;
        $(this._module.sections).each(function(i, o) {
            if(o.entry() == name) { 
                ret = o;
                return false;
            }
        })
        return ret;
    },
    
    widget: function(name) {
        var ret = false;
        $(this._module.widgets).each(function(i, o) {
            if(o.entry() == name) { 
                ret = o;
                return false;
            }
        })
        return ret;
    },
    
    module: function() {
        return this._module;
    },
    
    manager: function() {
        return this._manager;
    },
    
    ajax: function() {
        return this._module.ajax;
    },
    
        
    
}, {
    instance: null,
});

Mods.ModuleSection = Base.extend({
    
    _manager: false,
    _module: false,
    _section: false,
    
    __loading: false,
    __loadingTimeout: -1,
    
    _data: false,
    
    constructor: function(manager, module, section) {
        // вызываем инициализатор базового класса
        this.base();
        
        // инициализирует специализированное меню модулей
        this._manager = manager;
        this._module = module;
        this._section = section;
        this._data = [];
        
        this.Init();
    },
    
    Init: function() {
        
        var self = this;
                   
        if(this._module instanceof String) {
                     
            // передавали название модуля, а не обьект
            this._manager.Module(this._module, function(data) {
                
                if(data.error) {
                    alert('Ошибка в модулях 3: ' + data.message);
                    
                    if(data.errorno == 'unlogged') {
                        location.reload();
                    }
                    
                    return;
                }
                
                var module = data.module;
                self._module = module;
                
                if(self._section instanceof String) {
                    
                    $(self._module.sections).each(function(i, o) {
                        if(o.entry == self._section) {
                            self._section = o;
                            return false;
                        }
                    });
                    
                }
                
                self.raiseEvent('ready', {});
                
            });
        
        }       
        else {
            // обьект уже инициализирован нужно просто вызвать событие ready
            self.raiseEvent('ready', {});            
        }  
        
    },
    
    _showLoading: function(val) {
        var self = this;
        if(val) {
            if(this.__loading) {
                this.__loading.remove();
                this.__loading = false;
            }
            
            if(this.__loadingTimeout != -1)
                clearTimeout(this.__loadingTimeout);
            this.__loadingTimeout = setTimeout(function() {
                self.__loading = $('<div></div>').css({
                    backgroundColor: '#fff',
                    zIndex: UI.zIndex() + 1,
                    width: $(window).width(),
                    height: $(window).height(),
                    top: 0,
                    left: 0,
                    position: 'fixed',
                    opacity: 0.3
                }).addClass('loading').appendTo(document.body);
            }, 2000);
            
        }
        else {
            if(this.__loadingTimeout != -1)
                clearTimeout(this.__loadingTimeout);
            if(this.__loading) {
                this.__loading.remove();
                this.__loading = false;
                this.__loadingTimeout = -1;
            }
        }
    }, 
    
    LoadData: function(dontFireEvent, reBlock, successCallback, errorCallback) {
        var self = this;
        var dfe = dontFireEvent == undefined ? false : dontFireEvent;
        var rb = reBlock == undefined ? false : reBlock;
        
        if(!dfe)
            self._showLoading(true);
            
        self._manager._request(this.module().ajax() + '.Data', {mode: 'load', module: self.module().entry(), section: self.entry(), loadmaster: rb}, function(data) {
            if(data.error) {
                if(!dfe) {
                    if(errorCallback)
                        errorCallback.apply(this, [data]);
                    else
                        self.raiseEvent('dataLoadError', {message: data.message});
                }
                return false;
            }
            
            if(!dfe)
                self._showLoading(false);
            
            if(!data.result && data.blockData) {
                if(errorCallback)
                    errorCallback.apply(this, [data]);
                else
                    self.raiseEvent('dataLoadError', {message: 'Модуль заблокирован пользователем ' + data.blockData.user + '. Пожалуйста, дождитесь окончания работы пользователя'});
                return false;
            }
            
            self._data = data.result;
            if(!dfe) {
                if(successCallback)
                    successCallback.apply(this, [data]);
                else
                    self.raiseEvent('dataLoadComplete', {});
            }
            
        });
        
    },
    
    StoreData: function(successCallback, errorCallback) {
        var self = this;
        
        self._showLoading(true);
        self._manager._request(this.module().ajax() + '.Data', {mode: 'store', module: this.module().entry(), section: this.entry(), value: this._data}, function(data) {
            
            if(data.error) {
                // alert(data.message);
                if(errorCallback)
                    errorCallback.apply(this, [data])
                else
                    self.raiseEvent('dataStoreError', {});
                
                self._showLoading(false);
                return false;
            }
                    
            self._showLoading(false);
            if(successCallback)
                successCallback.apply(this, [data]);
            else
                self.raiseEvent('dataSroreComplete', {});
            
        });
        
    },
    
    Sync: function(mode, callback) {
        var self = this;
        
        self._showLoading(true);
        self._manager._request(this.module().ajax() + '.Sync', {mode: mode, module: this.module().entry(), section: this.entry()}, function(data) {
            
            if(data.error) {
                alert('Ошибка в модулях 4: ' + data.message);
                if(callback)
                    callback.apply(self, [data]);
                else
                    self.raiseEvent('dataSyncError', {});
            }
            
            self._showLoading(false);
            if(callback)
                callback.apply(self, [data]);
            else
                self.raiseEvent('dataSyncComplete', {});
            
        });
        
    },
    
    SyncToProduction: function(mode, callback) {
        var self = this;
        
        self._showLoading(true);
        self._manager._request(this.module().ajax() + '.SyncToProduction', {mode: mode, module: this.module().entry(), section: this.entry()}, function(data) {
            
            if(data.error) {
                alert('Ошибка в модулях 6: ' + data.message);
                if(callback)
                    callback.apply(self, [data]);
                else
                    self.raiseEvent('dataSyncError', {});
            }
            
            self._showLoading(false);
            if(callback)
                callback.apply(self, [data]);
            else
                self.raiseEvent('dataSyncComplete', {});
            
        });
        
    },
    
    CopySources: function(callback) {
        var self = this;
        
        self._showLoading(true);
        self._manager._request(this.module().ajax() + '.CopySources', {module: this.module().entry(), section: this.entry()}, function(data) {
            
            if(data.error) {
                alert('Ошибка в модулях 7: ' + data.message);
                if(callback)
                    callback.apply(self, [data]);
                else
                    self.raiseEvent('copySourcesError', {});
            }
            
            self._showLoading(false);
            if(callback)
                callback.apply(self, [data]);
            else
                self.raiseEvent('copySourcesComplete', {});
            
        });
        
    },
    
    CopyFrom: function(file, callback) {
        var self = this;
        
        self._showLoading(true);
        self._manager._request(this.module().ajax() + '.CopyFrom', {file: file, module: this.module().entry(), section: this.entry()}, function(data) {
            
            if(data.error) {
                alert('Ошибка в модулях 9: ' + data.message);
                if(callback)
                    callback.apply(self, [data]);
                else
                    self.raiseEvent('copySourcesError', {});
            }
            
            self._showLoading(false);
            if(callback)
                callback.apply(self, [data]);
            else
                self.raiseEvent('copySourcesComplete', {});
            
        });
        
    },
    
    Undo: function(callback) {
        var self = this;
        
        self._showLoading(true);
        self._manager._request(this.module().ajax() + '.Undo', {module: this.module().entry(), section: this.entry()}, function(data) {
            
            if(data.error) {
                alert('Ошибка в модулях 10: ' + data.message);
                if(callback)
                    callback.apply(self, [data]);
                else
                    self.raiseEvent('UndoError', {});
            }
            
            self._showLoading(false);
            if(callback)
                callback.apply(self, [data]);
            else
                self.raiseEvent('UndoComplete', {});
            
        });
        
    },
    
    entry: function(val) {
        if(val == undefined)
            return this._section.entry;
        else
            this._section.entry = val;
    },
    
    issection: function(val) {
        if(val == undefined)
            return this._section.section;
        else
            this._section.section = val;
    },
    
    title: function(val) {
        if(val == undefined)
            return this._section.title;
        else
            this._section.title = val;
    },
    
    module: function() {
        return this._module;
    },
    
    section: function() {
        return this._section;
    },
    
    manager: function() {
        return this._manager;
    },  
    
    config: function(val, afterLookupsData) {
        if(val == undefined)
            return this._config;
        else {
            var self = this;
            this._config = val;
            
            var __lookupsloading = 0;
            var __lookupdata = function(fields) {
                $.map(fields, function(field, fname) {

                    if(field.lookup != undefined && (field.lookup.ondemand === undefined || field.lookup.ondemand === false)) {
                        __lookupsloading++;
                        self.manager().LookupData(field.lookup, field.lookup, function(data) {
                            __lookupsloading--;
                            this.rows = toArray(data.rows);
                        })
                    };
                    

                    if(field.fields) {
                        __lookupdata(field.fields);
                    }
                    
                });
                
            }
                               
            this._showLoading(true);    
            __lookupdata(this._config.fields);
            
            var conf = this._config;
            $(conf.levels).each(function(i, o) {
                __lookupdata(conf[o].fields);
                conf = conf[o];
            })
            
            if(afterLookupsData !== undefined) {
                var tcount = 0;
                var t = function() {
                    if(__lookupsloading <= 0) {
                        self._showLoading(false);    
                        afterLookupsData();
                    }
                    else if(tcount++ < 100)
                        setTimeout(t, 100); 
                    else {
                        self._showLoading(false);    
                        afterLookupsData();
                    }
                }
                setTimeout(t, 100);
            }
            else {
                this._showLoading(false);                    
            }
            
            /*if(this._config.credits != undefined)
                __lookupdata(this._config.credits.fields);
            if(this._config.credits != undefined && this._config.credits.rates != undefined)
                __lookupdata(this._config.credits.rates.fields); */
            
            
        }
    },
    
    _createListAndButtonLayout: function(container) {
        var table           = new UI.Controls.TableLayout(container).Render();
        var cell            = table.addRow().Render().addCell().Render().height('100%').verticalAlign('top');
        var buttons         = table.addRow().Render().addCell().Render().verticalAlign('bottom').styles({paddingTop: '10px'});
        return [cell, buttons];
    },
    
    _createFiltersRowLayout: function(container) {
        var table = new UI.Controls.TableLayout(container).Render();
        var filtersRow = table.addRow().Render();
        var filtersCell = filtersRow.addCell().Render().elementID('filters').width('100%').styles({paddingBottom: '10px'});
        var filtersButtonCell = filtersRow.addCell().Render().elementID('filtersButton').styles({paddingBottom: '10px', textAlign: 'right'});
        return [filtersCell, filtersButtonCell];
    }, 
    
    
    _getHeader: function(fields) {
        var header = [];
        $(fields).each(function(i, f) {
            if(!f.header)
                return true;
            header.push(f);
        });
        return header;
    },

    _getFilter: function(fields) {
        var self = this;
        var filter = [];
        $(fields).each(function(i, field) {
            if(!field.filter)
                return true;

            var f = self.manager().controls('filter' + field.field).filter();
            if(f)
                filter.push(f);
        });
        return filter;
    },
    
    _getSort: function(list) {
        var sortField = list.sortfield();
        var sortOrder = list.sortorder();
        if(sortField)
            return [sortField.field + ' ' + sortOrder];
        else
            return [];
    }, 
    
    
      
    
}, {
    instance: null,
})

Mods.ModuleWidget = Base.extend({
    
    _manager: false,
    _module: false,
    _widget: false,
    
    constructor: function(manager, module, widget) {
        this.base();

        this._manager = manager;
        this._module = module;
        this._widget = widget;
        
        this.Init();
    }, 
    
    Render: function() {
        this.base();
    },

    entry: function(val) {
        if(val == undefined)
            return this._widget.entry;
        else
            this._widget.entry = val;
    },
    
    module: function() {
        return this._module;
    },

    manager: function() {
        return this._manager;
    },  
    
    
}, {});