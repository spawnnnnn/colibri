UI.Controls.Button = UI.Controls.Control.extend({
    
    _container: false,
    _title: false,
    _icon: false,
    
    _element: false,
    
    _data: {},
    
    constructor: function(name, container, title, icon, className, tooltip) {
        this.base(name, container);

        this._title = title;
        this._tooltip = tooltip;
        this._icon = icon == undefined ? false : icon;
        this._className = className == undefined ? false : className;
        
    }, 
    
    Render: function() {
        
        var self = this;
        this.base('button', 'ui2-button ' + this._className);
        
        if(this._tooltip)
            this._element.attr('title', this._tooltip);
        this._element.append( (this._icon ? '<img src="' + _ROOTPATH + this._icon + '" />' : '') + (this._title ? '<span>' + this._title + '</span>' : '') );
        
        this._element.click(function() {
            if(!$(this).is(':disabled'))
                self.raiseEvent('click', []);
        }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}) }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) });
        
        this.tabIndex(true);
        this.raiseEvent('ready', []);
        
        return this;
        
    },

    title: function(value) {
        if(value == undefined)
            return this._element.find('span').html();
        else {
            if(this._element.find('span').length == 0)
                this._element.append('<span></span>');
            this._element.find('span').html(value);
            return this;
        }
    },
    
    icon: function(value) {
        if(value == undefined)
            return this._icon;
        else {
            
            this._icon = value;
            
            if(this._element.find('img').length == 0) {
                this._element.prepend('<img />');
            }
            this._element.find('img').attr('src', this._icon)
            
            return this;
        }
    },
    
    iconClass: function(value) {
        if(value == undefined)
            return this._icon;
        else {
            
            this._icon = value;
            
            if(this._element.find('img').length == 0) {
                this._element.prepend('<img src="' + _ROOTPATH + '/res/img/1x1.gif"  />');
            }
            this._element.find('img').attr('class', this._icon)
            
            return this;
        }
    },
    
    buttonGroup: function(value) {
        if(value == undefined)
            return this._element.hasClass('ui-button-group');
        else {
            if(value)
                this._element.addClass('ui-button-group');
            else
                this._element.removeClass('ui-button-group');
            return this;
        }
    }, 
    
    
}, {});