UI.Controls.Control = Base.extend({
    
    _container: false,
    _element: false,
    _parent: false, 
    _controls: false,
    _tag: false,
    _name: false,        
                     
    constructor: function(name, container) {
        this.base();
        
        this._name = name;
        this._controls = {};
        this._tag = {};
        
        this._container = container.container ? container.container() : container;
        if(container.container)
            this._parent = container;
        if(container.controls)
            container.controls(name, this);
        
    },
    
    container: function() {
        return this._element;
    },
    
    parent: function(value) {
        if(value == undefined)
            return this._parent;
        else
            this._parent = value;
        return this;
    },
    Parent: function(value) {
        return this.parent(value);
    },

    controls: function(name, val) {
        if(name == undefined)
            return this._controls;      
        if(name == 'firstChild') {
            for(first in this._controls)
                return this._controls[first];    
        }
        else if(name == 'lastChild') {
            for(last in this._controls);
            return this._controls[last];    
        }
        if(val === undefined)
            return this._controls[name];
        
        if(val === null)
            delete this._controls[name];
        else
            this._controls[name] = val;     
        return val;
    },
    
    find: function(path) {
        var p = this;
        var splitedPath = path.split('/');
        splitedPath.forEach(function(v) {
            p = p.controls(v);
            if(!p)
                return false;
        });
        return p;        
    },
    
    children: function() {
        return Object.countKeys(this.controls())
    }, 
    
    width: function(val) {
        if(val == undefined)
            return this._element.outerWidth();
        else {
            this._element.outerWidth(val);
            return this;
        }
    }, 
    
    height: function(val) {
        if(val == undefined)
            return this._element.outerHeight();
        else {
            this._element.outerHeight(val);
            return this;
        }
    },
    
    styles: function(css) {
        if(css instanceof Object)
            this._element.css(css);
        else if(css.length) {
            return this._element.css(css);
        }
        return this;
    },
    
    name: function() {
        return this._name;
    }, 
    
    className: function(val) {
        if(val == undefined)
            return this._element.attr('class');
        else {
            this._element.attr('class', val);
            return this;
        }
    },

    addClass: function(val) {
        this._element.addClass(val);
        return this;
    },

    removeClass: function(val) {
        this._element.removeClass(val);
        return this;
    },

    elementID: function(val) {
        if(val == undefined)
            return this._element.attr('id');
        else {
            this._element.attr('id', val);
            return this;
        }
    },
    
    html: function(value) {
        if(value == undefined)
            return this._element.html();
        else {
            this._element.html(value);
            return this;
        }
    },
    
    tag: function(data) {
        if(data == undefined) {
            return this._tag;
        }
        else {
            this._tag = data;
            return this;
        }
    },       
    
    enable: function(val) {
        if(val == undefined)
            this._element ? this._element.hasClass('ui-disabled') : false;
        else {
            if(!val) 
                this._element ? this._element.addClass('ui-disabled').attr('disabled', 'disabled') : false;
            else
                this._element ? this._element.removeClass('ui-disabled').removeAttr('disabled') : false;
            
            Object.forEach(this.controls(), function(name, control) {
                control.enable(val);
            });
            
            return this;
        }
    },
    
    readonly: function(val) {
        if(val == undefined)
            return this._element.prop('readonly');
        else {
            this._element.prop('readonly', val);
            return this;
        }
    },
    
    path: function() {
        return (this.parent().path ? this.parent().path() : '') + '/' + this.name();
    }, 
    
    Hide: function() {
        this._element.hide();
        this.raiseEvent('hidden', []);
        return this;
    }, 

    Show: function() {
        if(this._element.css('display') == 'none')
            this._element.css('display', '');
        else
            this._element.show();
        this.raiseEvent('shown', []);
        return this;
    }, 

    hide: function() { return this.Hide(); },
    
    show: function() { return this.Show(); },
    
    visible: function() {
        return this._element.is(':visible');
    },
    
    position: function(position) {
        if(position == undefined)
            return this._element.offset();
        else {
            this._element.css({
                position: 'absolute',
                zIndex: UI.zIndex() + 1,
                left: position.left + 'px',
                top: position.top + 'px'
            });
            return this;
        }
    },

    focus: function() {
        if(canFocus(this._element))
            this._element.focus();
        else {
            this._element.focus();
            this._element.addClass('focus');
        }
        return this;
    },

    blur: function() {
        if(canFocus(this._element))
            this._element.blur();
        else {
            this._element.blur();
            this._element.removeClass('focus');
        }
        return this;
    },
    
    forEach: function(handler) {
        var self = this;
        Object.forEach(this.controls(), function(name, o) {
            return handler.apply(self, [name, o]);
        });
        return this;
    },
                    
    Render: function(element, className, parentElement, mode) {
        var self = this;
        if(mode == undefined)
            mode = 'append';
        this._element = $('<' + element + ' class="ui-control ' + className + '"></' + element + '>');
        if(mode == 'append')
            this._element.appendTo(parentElement !== undefined ? parentElement : this._container);
        else if(mode == 'prepend')
            this._element.prependTo(parentElement !== undefined ? parentElement : this._container);
        else if(mode == 'after')
            (parentElement !== undefined ? parentElement : this._container).after(this._element);
        else if(mode == 'before')
            (parentElement !== undefined ? parentElement : this._container).before(this._element);
            
        // this._element.attr('tabIndex', UI.tabIndex++);
        this._element.data('control', this);
        this._element.focus(function() {
            self._element.addClass('focus');
        }).blur(function() {
            self._element.removeClass('focus');
        });
        return self;
    },
    
    Dispose: function() {
        this._element.remove();
        if(this.parent()) {
            this.parent().controls(this._name, null);
        }
        this.raiseEvent('elementDisposed');
        return this;
    },

    tabIndex: function(value) {
        if(value === true) {
            this._element.attr('tabIndex', UI.tabIndex++);
        }
        else 
            return this._element.attr('tabIndex');
    },

    ensureVisible: function() {
        this._element[0].scrollIntoView(false);
    },
    
});