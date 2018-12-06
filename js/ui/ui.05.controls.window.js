UI.Controls.Window = Base.extend({
    
    _container: false,
    _title: false,
    _draggable: false,
    
    _state: 'normal',
    _normalSize: false,
    
    _element: false,
    _shadow: false,
    
    _parent: false, 
    _keysEnabled: true,
   
    _controls: false,
    
    constructor: function(container, title, draggable) {
        this._container = container.container ? container.container() : container;
        this._title = title;
        this._draggable = draggable == undefined ? false : draggable;
        this._controls = {};
    }, 
    
    Render: function() {
        
        var self = this;
                       
        this._windowContainer = $('<div class="ui-window-container" tabIndex="' + (UI.tabIndex++) + '"></div>').css({
            left: 0,
            top: 0,
            height: '100%',
            width: '100%',
            position: 'fixed',
            zIndex: UI.zIndex() + 1,
            overflow: 'auto',
            display: 'none'
        }).appendTo(this._container);

        this._shadow = $('<div class="ui-window-shadow"></div>').appendTo(this._windowContainer);
        
        this._element = $('<div class="ui-window"></div>').appendTo(this._windowContainer);
        this._element.data('control', this);
        this._element.append( '<div class="ui-window-title"><div class="ui-window-close"></div><span>' + this._title + '</span></div>' );
        this._element.append( '<div class="ui-window-content"></div>' );
        this._element.append( '<div class="ui-window-buttons"></div>' );
        this._element.append( '<input type="text" class="ui-window-tabfix" tabIndex="0" style="visibility:hidden; position: absolute; width: 1px; height: 1px;"  />' );
        
        if(this._draggable) {
            this._element.draggable({handle: '.ui-window-title', drag: function() {
                //$(window).resize();
            }});
            this._element.addClass('ui-window-draggable');
        }
        
        this._element.find('.ui-window-close').click(function() {
            self.Hide();
        });
        
        this._shadow.click(function(e) {
            self.raiseEvent('click', {domEvent: e});
        });
        
        this._element.click(function(e) {
            if($(e.target).is('.ui-window-title') || $(e.target).is('.ui-window-content') || $(e.target).is('.ui-window-buttons'))
                self.controls('firstChild').focus();
            self.raiseEvent('click', {domEvent: e});
        });

        this._element.keydown(function(e) {
            if(self._keysEnabled) {
                switch(e.keyCode) {
                    case 13:
                        if(e.shiftKey || e.ctrlKey)
                            return true;
                        self.raiseEvent('dialogResult', {result: 'save'});
                        break;
                    case 27:
                        self.raiseEvent('dialogResult', {result: 'cancel'});
                        break;
                }
            }
        });

        this._element.find('.ui-window-title').dblclick(function() {
            if(self._state == 'maximized') {
                self._state = 'normal';
                self._element.find('.ui-window-content').css({overflow: 'visible'});
                self.Resize(self._normalSize.width, 'auto');
            }
            else {
                self._state = 'maximized';
                self._element.find('.ui-window-content').css({overflow: 'auto'});
                self.Resize($(window).width(), $(window).height());
            }
        });

        this._element.find('.ui-window-tabfix').focus(function() {
            self.controls('firstChild').focus();
        });

        this.addHandler('controlCollectionChanged', function(sender, args) {
            if(args.command == 'add') {
                args.control.addHandler('blur', function(sender, args) { self.__lastElementBlur(sender, args); });
            }
        });

        this.setDefaultLocation();
        
        this.raiseEvent('ready', []);
        
        return this;
        
    },
    
    __lastElementBlur: function(sender, args) {
        if(this.controls('lastChild') == sender)
            this.controls('firstChild').focus();
    },

    Show: function(data) {
        var self = this;
        self._windowContainer.css({zIndex: UI.zIndex() + 1})
        self._windowContainer.show();
        self._shadow.show().animate({opacity: 0.5}, 'fast', function() {
            self._element.css({display: 'flex'}).animate({opacity: 1}, 'fast', function() {
                self.raiseEvent('shown', data);
                self.controls('firstChild').focus();
            });
        });
    }, 
    
    Hide: function() {
        var self = this;
        self._element.css({opacity: 0});
        self._element.hide();
        self._shadow.css({opacity: 0});
        self._shadow.hide();
        self._shadow.parent().hide();
        self.raiseEvent('hiden', []);
    }, 
    
    setDefaultLocation: function() {
        this._element.css({ top: ($(window).height() - 200) / 2, left: ($(window).width() - 400) / 2, width: 400, minHeight: 200  });
    }, 
    
    Resize: function(width, height) {
        var self = this;
        if(self._state == 'maximized') {
            self._normalSize = {width: this._element.outerWidth(), height: this._element.outerHeight()};
        }
        this._element.animate({top: height == 0 || ($(window).height() - height) / 2 < 0 ? 0 : ($(window).height() - height) / 2, left: ($(window).width() - width) / 2, width: width, height: height == 0 ? 'auto' : height}, 'fast', function() {
            //$(window).resize();
            //SPAWN ?!?!? self.raiseEvent('resize');
        });
    }, 
    
    enable: function(val) {
        if(val == undefined)
            this._element.hasClass('ui-disabled');
        else {
            if(!val) 
                this._element.addClass('ui-disabled').attr('disabled', 'disabled');
            else
                this._element.removeClass('ui-disabled').removeAttr('disabled');
            return this;
        }
    },
    
    title: function(value) {
        if(value == undefined)
            return this._title;
        else {
            
            this._title = value;
            this._element.find('.ui-window-title>span').html(this._title);
            
            return this;
        }
    },
    
    container: function(type) {
        if(type == undefined)
            return this._element.find('>div.ui-window-content');
        return this._element.find('>div.ui-window-' + type);
    },
    
    controls: function(name, val) {
        if(name == undefined)
            return this._controls;      
        if(name == 'firstChild') {
            for(first in this._controls) {
                return this._controls[first];    
            }
        }
        else if(name == 'lastChild') {
            for(last in this._controls);
            return this._controls[last];    
        }
        if(val === undefined)
            return this._controls[name];
        if(val === null) {
            delete this._controls[name];
            this.raiseEvent('controlCollectionChanged', {command: 'delete'});
        }
        else { 
            this._controls[name] = val;
            this.raiseEvent('controlCollectionChanged', {command: 'add', control: val});
        }

        return val;
    },
    
    addControl: function(o) {
        var self = this;
        
        this.controls(o.field, UI.Controls.Form.FormField.Create(o, this.container('content'), o.title));
        
        if(o.group && o.group != 'window') {
            if(!this.controls('groups'))
                this.controls('groups', new UI.Controls.Form.Tabs('groups', this.container('content'))).parent(self).Render();
            this.controls('groups').addToTab(o.group, this.controls(o.field));
        }
        
        this.controls(o.field)
            .Render()
            .parent(self);
        
        
    }, 
    
    parent: function(value) {
        if(value == undefined)
            return this._parent;
        else {
            this._parent = value;
            return this;
        }
    },
    
    fadeColor: function(value) {
        this._shadow.css({backgroundColor: value});
    },
    
    size: function() {
        return {width: this._element.outerWidth(), height: this._element.outerHeight()};
    },
    
    elementID: function(val) {
        if(val == undefined)
            return this._element.attr('id');
        else {
            this._element.attr('id', val);
            return this;
        }
    },
    
    keysEnabled: function(val) {
        if(val == undefined)
            return this._keysEnabled;
        else {
            this._keysEnabled = val;
            return this;
        }
    },
    
}, {});

