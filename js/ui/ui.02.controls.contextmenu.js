UI.Controls.ContextMenu = UI.Controls.Control.extend({
    
    _shadow: false,
    
    _orientation: 'right bottom',
    _permanent: false,

    _selected: false,
    
    constructor: function(name, container, permanent) {
        this.base(name, container);
        this._permanent = permanent;
    }, 
    
    Render: function() {
        this.base('div', 'ui-contextmenu');
        this.tabIndex(true);

        var self = this;
        this._element.click(function(e) {
            var item = $(e.target).closest('div');
            self.raiseEvent('menuItemClicked', {item: item.data('key'), itemData: item.data('item')});
        }).mouseover(function(e) {
            var item = $(e.target).closest('div');
            self.selected(item.index());
            self.raiseEvent('menuItemOver', {item: item.data('key'), itemData: item.data('item')});
        }).mouseout(function(e) {
            var item = $(e.target).closest('div');
            self.selected(item.index());
            self.raiseEvent('menuItemOut', {item: item.data('key'), itemData: item.data('item')});
        });                       
        this._shadow = $('<div></div>').css({position: 'fixed', display: 'none', left: 0, right: 0, width: '100%', height: '100%'}).prependTo(document.body);
        this._shadow.click(function() {
            self.Hide();
            if(!self._permanent)
                self.Dispose();
        });
        self._element.keydown(function(e) {
            var selected = self.selected();
            if(selected === false) {
                self.selected(0);
                return false;
            }
            switch(e.keyCode) {
                case 38: { // вверx
                    if(selected > 0)
                        self.selected(selected - 1);
                    break;
                }
                case 40: { // вниз
                    if(selected < self._element.find('>div').length - 1)
                        self.selected(selected + 1);
                    break;
                }
                case 13: { // enter
                    self._element.find('>div:eq(' + selected + ')').click();
                    break;
                }
                case 27: { // escape
                    self._shadow.click();
                    break;
                }
            }       
            return false;     
        });
        return this;
    },
    
    orientation: function(value) {
        if(value == undefined)
            return this._orientation;
        else {
            this._orientation = value;
            return this;
        }
    },
    
    addItem: function(item, parent) {
        var self = this;
        var parentEl = this._element;
        if(parent != undefined) {
            parentEl = this._element.find('[data-key="' + parent + '"]>div');
            if(parentEl.length == 0) {
                this._element.find('[data-key="' + parent + '"]').append('<div class="ui-contextmenu"></div>');
                parentEl = this._element.find('[data-key="' + parent + '"]>div');
            }
        }
        
        var uiItem = $('<div data-key="' + item.key + '"><span></span></div>').appendTo(parentEl);
        if(item.icon)
            uiItem.find('>span').append('<span class="ui-icon"><img src="' + item.icon + '" /></span>');
        if(item.text)
            uiItem.find('>span').append('<span class="ui-text"' + (item.def ? ' style="font-weight: bold"' : '') + '>' + item.text + '</span>');
        if(item.keyboard && !item.childs)
            uiItem.find('>span').append('<span class="ui-keyboard">' + item.keyboard + '</span>');
        
        if(item.childs) {
            item.childs.forEach(function(c) {
                self.addItem(c, item.key);
            });
            uiItem.find('>span').append('<span class="ui-childs">►</span>');
        }
        
        uiItem.data('item', item);

        return this;
    },
    
    addItems: function(items, parent) {
        var self = this;
        items.forEach(function(item) {
            self.addItem(item, parent);
        });
        return this;
    },
    
    removeItem: function(key) {
        this._element.find('div[data-key="' + key + '"]').remove();
        return this;
    },
    
    updateItem: function(key, item) {
        var self = this;
        var itemEl = this._element.find('div[data-key="' + key + '"]');
        var itemData = itemEl.data('item');
        itemEl.data('item', item);
        itemEl.find('>span').find('.ui-icon').html('<img src="' + item.icon + '" />');
        itemEl.find('>span').find('.ui-text').html(item.text);
        if(item.keyboard && !item.childs)
            itemEl.find('>span').find('.ui-keyboard').html(item.keyboard);
        
        if(item.childs) {
            item.childs.forEach(function(c) {
                self.addItem(c, item.key);
            });
            itemEl.find('.ui-childs').remove();
            itemEl.find('>span').append('<span class="ui-childs">►</span>');
        }
        return this;
    },
    
    removeAll: function() {
        this._element.find('>div').remove();
        return this;
    },  
    
    removeChilds: function(parent) {
        this._element.find('div[data-key="' + parent + '"]').find('.ui-contextmenu').remove();
        return this;
    },  

    Show: function(point) {
        
        this._shadow.show().css({zIndex: UI.zIndex() + 1});
        this._element.show().css({zIndex: UI.zIndex() + 2});
        switch(this._orientation) {
            default:
            case 'right bottom': {
                if(point.top + this._element.outerHeight() > $(window).height())
                    point.top = $(window).height() - this._element.outerHeight();
                if(point.left + this._element.outerWidth() > $(window).width())
                    point.left = $(window).width() - this._element.outerWidth();
                this._element.css({left: point.left, top: point.top, 'box-shadow': '10px 10px 20px rgba(0,0,0,0.4)'});
                break;
            }
            case 'left bottom': {
                if(point.top + this._element.outerHeight() > $(window).height())
                    point.top = $(window).height() - this._element.outerHeight();
                if(point.left - this._element.outerWidth() < 0) point.left = this._element.outerWidth();
                this._element.css({left: point.left - this._element.outerWidth(), top: point.top, 'box-shadow': '-10px 10px 20px rgba(0,0,0,0.4)'});
                break;
            }
            case 'right top': {
                if(point.left + this._element.outerWidth() > $(window).width())
                    point.left = $(window).width() - this._element.outerWidth();
                if(point.top - this._element.outerHeight() < 0) point.top = this._element.outerHeight();
                this._element.css({left: point.left, top: point.top - this._element.outerHeight(), 'box-shadow': '10px -10px 20px rgba(0,0,0,0.4)'});
                break;
            }
            case 'left top': {
                if(point.top - this._element.outerHeight() < 0) point.top = this._element.outerHeight();
                if(point.left - this._element.outerWidth() < 0) point.left = this._element.outerWidth();
                this._element.css({left: point.left - this._element.outerWidth(), top: point.top - this._element.outerHeight(), 'box-shadow': '-10px -10px 20px rgba(0,0,0,0.4)'});
                break;
            }
        }
        this.focus();
        this.raiseEvent('shown');
        return this;
    },
    
    Hide: function() {
        this._element.hide();                                               
        this._shadow.hide();
        this.raiseEvent('hidden');
        return this;
    },
    
    Dispose: function() {
        this._element.remove();
        this._shadow.remove();
    },

    clearSelection: function() {
        this._selected = false;
        this._element.find('>div').removeClass('selected');
    },

    selected: function(value) {
        if(value == undefined)
            return this._selected;
        else {
            this.clearSelection();
            this._selected = value;
            this._element.find('>div:eq(' + value + ')').addClass('selected');
        }
    },

});