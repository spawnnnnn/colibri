UI.Controls.Desktop = UI.Controls.Control.extend({
    
    _contextmenu: false,
    
    constructor: function(name, container) {
        this.base(name, container);
        
    },
        
    Render: function() {
        this.base('div', 'ui-desktop');

        this._element.append('<div class="ui-widget-container"></div>');
        this._element.append('<div class="ui-context-menu-stick">•••</div>');

        this._contextmenu = new UI.Controls.ContextMenu('desktopcm', $(document.body), true).orientation('left top');
        this._contextmenu.Render(); 
        this._contextmenu.addHandler('menuItemClicked', function(sender, args) {
            var widget = args.itemData.data;
            var item = args.item;
            
            if(widget.visible()) {
                widget.Hide();
            }
            else {
                widget.Show();
            }
            
            this.updateItem(item, {key: widget.name(), text: widget.title(), 'keyboard': false, icon: widget.visible() ? 'res/img/icons/disable.svg' : 'res/img/icons/enable.svg', data: widget});

        });
        
        this.bindHtmlEvents();
        return this;
    },
    
    RenderWidgets: function(widgets) {
        var self = this;
        
        var newWidgets = $.extend({}, widgets);
        var order = this.getWidgetsPositions();
        var orderedWidgets = {};
        order.forEach(function(position) {              
            orderedWidgets[position.name] = {name: position.name, widget: newWidgets[position.name], visible: position.visible};
            delete newWidgets[position.name];
        });
        Object.keys(newWidgets).forEach(function(name) {
            orderedWidgets[name] = {name: name, widget: newWidgets[name], visible: true};
        });
        
        var menu = [];
        Object.keys(orderedWidgets).forEach(function(name) {
            var position = orderedWidgets[name];
            var args = [position.name, self];
            var widget = construct(eval(position.widget), args);
            widget.Render();
            if(!position.visible) {
                widget.Hide();
            }
            menu.push({key: widget.name(), text: widget.title(), 'keyboard': false, icon: position.visible ? 'res/img/icons/disable.svg' : 'res/img/icons/enable.svg', data: widget});
        });
        
        self._contextmenu.addItems(menu);
        
    },
    
    bindHtmlEvents: function() {
        var self = this;
        
        this._element.find('.ui-widget-container').sortable({
            handle: ".ui-widget-title",
            cancel: ".ui-widget-content",
            placeholder: "ui-widget-placeholder ui-corner-all",
            start: function(e, helper) {
                var pixelWidth = helper.item.attr('data-width');
                var percentWidth = helper.item.attr('data-percent-width');

                helper.item.outerWidth(pixelWidth);
                helper.placeholder.outerWidth(pixelWidth);
                helper.placeholder.outerHeight(helper.item.outerHeight());
            },
            stop: function(e, helper) {
                
                var pixelWidth = helper.item.attr('data-width');
                var percentWidth = helper.item.attr('data-percent-width');

                helper.item.outerWidth(percentWidth);
                
                self.saveWidgetsPositions();
                
            }
        });
        
        this._element.find('.ui-context-menu-stick').click(function() {
            var position = $(this).offset();
            position.left += $(this).outerWidth();
            self._contextmenu.width(350);
            self._contextmenu.Show(position);
        })
    
    },
    
    getWidgetsPositions: function() {
        var positions = localStorage.getItem('colibri-ui-desktopwidgets');
        if(!positions) positions = [];
        else positions = JSON.parse(positions);
        return positions;
    }, 
    
    saveWidgetsPositions: function(el) {
        var keys = [];
        this.container().find('>.ui-widget').each(function(i, o){
            var widget = $(o).data('control');
            keys.push({name: widget.name(), visible: $(o).is(':visible')});
        });
        localStorage.setItem('colibri-ui-desktopwidgets', JSON.stringify(keys));
    },
    
    container: function() {
        return this._element.find('.ui-widget-container');
    },
                                                               
    
}, {});

UI.Controls.Widget = UI.Controls.Control.extend({
        
    Render: function(title) {
        this.base('div', 'ui-widget');
        this.tabIndex(true);

        this._element.append('<div class="ui-widget-title"><div class="ui-widget-close">&#10006;</div><span>' + title + '</span></div>');      
        this._element.append('<div class="ui-widget-content"></div>');
        
        this.RenderContent();               
        
        this.bindHtmlEvents();
        return this;
    },
    
    bindHtmlEvents: function() {
        var self = this;
        
        this._element.mousedown(function() {
            $(this).attr('data-width', $(this).outerWidth());
            $(this).attr('data-percent-width', this.style.width);
        });
        
        this._element.find('.ui-widget-close').click(function() {
            self.Hide();
            self.parent().saveWidgetsPositions();    
        });
        
        return this;
    },
    
    RenderContent: function() {
        return this;
    },
    
    title: function(val) {
        if(val == undefined) {
            return this._element.find('.ui-widget-title>span').html();
        }
        else {
            this._element.find('.ui-widget-title>span').html(val);
            return this;
        }
    },
    
    container: function() {
        return this._element.find('.ui-widget-content');
    },
                                                               
    
    
});