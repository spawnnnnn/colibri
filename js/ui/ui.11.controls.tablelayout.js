UI.Controls.TableLayout = UI.Controls.Control.extend({
    
    _rowsCount: 0,
    
    constructor: function(name, container) {
        this.base(name, container);
    }, 
    
    Render: function() {
        var self = this;
        this.base('table', 'ui-tablelayout');
        $(window).resize(function() { self.Resize(); });
        self.raiseEvent('ready', []);
        return self;
    },
    
    Resize: function() { this.raiseEvent('resize', []); },
    
    addRow: function() {
        return new UI.Controls.TableLayout.Row('i' + (this._rowsCount++), this);
    },
    
    generate: function(layout) {
        var self = this;
        $(layout).each(function(i, orow) {
            // rows
            var row = self.addRow().Render();
            $(orow).each(function(i, ocell) {
                // cells
                var cell = row.addCell().Render().elementID(ocell.name);
                $.map(ocell, function(value, name) {
                    if(name == 'name') return true;
                    cell[name].apply(cell, value);
                });
                
                
            })
            
        });
    },
    
    container: function(path) {
        if(path == undefined)
            return this._element;
        else {
            var o = this;
            path = path.split('.');
            $(path).each(function(i, item) {
                o = o.controls('i' + item);
            });
            return o;
        }
    },
    
}, {});


UI.Controls.TableLayout.Row = UI.Controls.Control.extend({
    
    _cellsCount: 0,
    
    constructor: function(name, container) {
        this.base(name, container);    
    }, 
    
    Render: function() {
        var self = this;
        this.base('tr', 'ui-tablelayout-row');
        $(window).resize(function() { self.Resize(); });
        self.raiseEvent('ready', []);
        return self;
    },
    
    Resize: function() {
        this.raiseEvent('resize', []);
    },
    
    addCell: function() {
        return new UI.Controls.TableLayout.Cell('i' + (this._cellsCount++), this);
    }
    
}, {});

UI.Controls.TableLayout.Cell = UI.Controls.Control.extend({
    
    _align: false,
    _controlsCount: 0,
    
    constructor: function(name, container) {
        this.base(name, container);
    }, 
    
    Render: function() {
        var self = this;
        this.base('td', 'ui-tablelayout-cell');
        $(window).resize(function() { self.Resize(); });
        self.raiseEvent('ready', []);
        return self;
    },
    
    Resize: function() {
        this.raiseEvent('resize', []);
    },

    colSpan: function(val) {
        if(val == undefined)
            return this._element.attr('colspan');
        else {
            this._element.attr('colspan', val);
            return this;
        }
    },
    
    rowSpan: function(val) {
        if(val == undefined)
            return this._element.attr('rowspan');
        else {
            this._element.attr('rowspan', val);
            return this;
        }
    },
    
    verticalAlign: function(val) {
        if(val == undefined)
            return this._element.css('vertical-align');
        else {
            this._element.css('vertical-align', val);
            return this;
        }
    },   
    
    
}, {});
