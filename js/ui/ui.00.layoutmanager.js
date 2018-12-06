'use strict';

UI.LayoutManager = Base.extend({

    _container: false,
    _layoutPath: false,
    _layoutIsUrl: true,
    
    _layout: false,
    _name: false,
    
    _parent: false,
    _controls: false,
    
    constructor: function(parent, layout, isUrl, name) {
        if(isUrl == undefined) isUrl = true;
        this._parent = parent;
        this._container = parent.container();
        this._layoutPath = layout;
        this._layoutIsUrl = isUrl;
        this._name = name ? name : 'layout';
        this._controls = {};
    }, 
    
    container: function() {
        return this._container;
    },  
    
    controls: function(name, val) {
        if(name == undefined)
            return this._controls;      
        if(name == 'firstChild') {
            for(first in this._controls) {
                return this._controls[first];    
            }
        }
        if(val == undefined)
            return this._controls[name];
        this._controls[name] = val;     
        return val;
    },
    
    main: function() {
        return this._layout;
    },
    
    Render: function() {
        var self = this;
        this.addHandler('layoutmanager.layout.loaded', this.__layoutLoaded);
        if(this._layoutIsUrl) {
            $.get(this._layoutPath, function(data) {
                self.raiseEvent('layoutmanager.layout.loaded', {layout: data});
            });
        }
        else {
            self.raiseEvent('layoutmanager.layout.loaded', {layout: this._layoutPath});
        }
        
    },
    
    __layoutLoaded: function(event, args) {
        this._layout = args.layout;
        this._renderLayout();
    },
    
    _renderControl: function(name, control, parent, main) {
        var self = this;

        var controlClass = control.control;  

        var container = parent['container'].apply(parent, (control.container ? control.container : []));
        
        var args = [name, container];
        if(control.create) {
            args = args.concat(control.create);
        }
        
        var object = construct(eval(controlClass), args);
        
        object.__control = control;
        object['Render'].apply(object, (control.render ? control.render : []));
        if(control.then) {
            $.map(control.then, function(value, name) {
                object[name].apply(object, value);
            });
        }
        
        if(control.controls) {
            $.map(control.controls, function(value, name) {
                self._renderControl(name, value, object);    
            });
        } 
        
        if(control.tab) {
            var tab = parent['tab'].apply(parent, [control.tab]);
            tab.controls.push(object);
        }
        
        object.parent(parent);
        parent.controls(name, object);
        
        return object;
    },
    
    _renderLayout: function() {       
        this._parent.controls(this._name, this._renderControl(this._name, this._layout, this._parent));    
        this.raiseEvent('layoutmanager.ready', {layout: this._parent.controls(this._name)});
    },
    
});