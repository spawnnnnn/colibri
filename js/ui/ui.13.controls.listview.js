UI.Controls.ListView = UI.Controls.Control.extend({
    
    _multiple: false,
    _levels: false,
    _dragManager: false,
    _sortField: false,
    _sortOrder: false,
    _columnsSortable: false,

    constructor: function(name, container) {
        this.base(name, container);
        this._selected = [];
        
        this.addHandler('itemClicked', function(sender, args) {
            
            if(args.domEvent.shiftKey && this.multiple()) {
                
                var startIndex = 9999999999;
                this.selected().forEach(function(item) {
                    if(item.index() < startIndex)
                        startIndex = item.index();
                });
                
                $('*').disableSelection();
                this.clearSelection();
                var index = args.item.index();
                for(var i=Math.min(index, startIndex);i<=Math.max(index,startIndex);i++) {
                    this.items().Item(i).selected(true);
                }
                $('*').enableSelection();
                
            }
            else {
                if(!this.multiple() || !args.domEvent.ctrlKey)
                    this.clearSelection();
                args.item.selected(!args.item.selected());
            }
            this.raiseEvent('selectionChanged', {});
            
        });
    },                              
    
    Render: function() {
        this.base('div', 'ui-listview');
        this.tabIndex(true);
        
        new UI.Controls.ListViewHeaders(this.name() + '_header', this);
        new UI.Controls.ListViewItems(this.name() + '_items', this);
        
        this.controls(this.name() + '_header').Render();
        this.controls(this.name() + '_items').Render();
        
        this.bindHtmlEvents();
        
        this._element.trigger('resize');
        
        return this;
    },
    
    bindHtmlEvents: function() {
        var self = this;
        $(window).bind('resizeend', function(e) {
            self.items()._element.hide();
            self.items()._element.outerHeight(self.height() - self.headers().height() - 5);
            self.items()._element.show();                                              
            self.raiseEvent('resize', {domEvent: e});
        });
        
        this._element.click(function(e) {
            if($(e.target).is('.ui-listview-items')) {
                self.clearSelection();
                self.raiseEvent('selectionChanged', {});
            }
        });
        this._element.keydown(function(e) {
            if(self.selected().length === 0) {
                self.selected(self.items().controls('firstChild'));
                return false;
            }

            var selected = self.selected()[0];
            switch(e.keyCode) {
                case 38: // вверx
                    self.clearSelection();
                    if(selected.prev()) {
                        self.selected(selected.prev());
                    }
                    return false;
                case 40: // вниз   
                    if(selected.next()) {
                        self.selected(selected.next());
                    }
                    return false;
                case 36: // home
                    self.clearSelection();
                    self.selected(self.items().controls('firstChild'));
                    return false;
                case 35: // end
                    self.clearSelection();
                    self.selected(self.items().controls('lastChild'));
                    return false;
                case 13: // end
                    if(self.selected().length > 0)
                        self.raiseEvent('itemDoubleClicked', {domEvent: e, item: self.selected()[0], subitem: null});
                    return false;
            }       
            return true;           
        });
        
        this._element.keyup(function(e) {
            self._element.enableSelection();
        });
        
        this._element.blur(function(е) {
            if(self._element.has($(е.relatedTarget)).length == 0) {
                self.removeClass('focus');
                self.raiseEvent('blur', {});
            }
        });
        this._element.focus(function(е) {
            self.addClass('focus');
            self.raiseEvent('focus', {});
        });

        this
            .addHandler('scrolledToBottom', function(sender, args) {
                if(self._pager) {
                    if(self._pager.val() < self._pager.maxpages()) {
                        self._pager.val(self._pager.val() + 1);
                        self._pager.raiseEvent('changed');
                    }
                }
            })
            .addHandler('headerClick', function(sender, args) {
                if(self._pager) {
                    self._pager.val(1);
                    self._pager.raiseEvent('changed');
                }
            });

    },
                                                               
    headers: function(headers) {
        if(headers !== undefined) {
            this.controls(this.name() + '_header').Add(headers);
            return this;
        }
        else
            return this.controls(this.name() + '_header');
    },
    
    items: function() {
        return this.controls(this.name() + '_items');
    },
    
    selected: function(value) {
        if(value !== undefined) {
            if(value.length === undefined)
                value = [value];
            
            this.clearSelection();
            value.forEach(function(v) {
                v.selected(true);
            });
            this.selected()[0].ensureVisible();
            return this;
        }

        var ret = [];
        this.items().forEach(function(key, item) {
            if(item.selected()) {
                ret.push(item);
            }
        });
        return ret;
    },
    
    clearSelection: function() {
        this.items().forEach(function(key, item) {
            item.selected(false);
        });
        return this;
    },
    
    multiple: function(val) {
        if(val === undefined)
            return this._multiple;
        else {
            this._multiple = val;
            return this;
        }
    },
    
    levels: function(val) {
        if(val === undefined)
            return this._levels;
        else {
            this._levels = val;
            return this;
        }
    },

    columnsSortable: function(value) {
        if(value === undefined)
            return this._columnsSortable;
        else {
            this._columnsSortable = value;
            if(this._columnsSortable)
                this.headers().initSortable();
        }
    },

    dragManager: function(value) {
        if(value == undefined)
            return this._dragManager;
        else {
            this._dragManager = value;
            return this;
        }
    },
    
    getDragElement: function(el) {
        
        return '<div class="ui-listview-drag-container" style="width: ' + this.width() + 'px;"><div class="ui-listview-item">' + el.html() + '</div></div>';
        /*if(el.find('.ui-listview-items-table').length > 0) {
            return el.find('.ui-listview-items-table .ui-listview-item-subitem:first').html();
        }
        else
            return el.find('.ui-listview-subitem:first').html();*/
    },
    
    sortField: function(field, order) {
        if(field !== undefined && order !== undefined) {
            this._sortField = field;
            this._sortOrder = order;
        }
        else
            return {field: this._sortField, order: this._sortOrder};
    },
    
    sortString: function() {
        return this._sortField ? this._sortField + ' ' + this._sortOrder : false;
    },

    InitContinous: function(params) {
        var self = this;
        this.items().Clear();
        if(this._pager) this._pager.Dispose();
        this._pager = (new UI.Controls.Pager('pager', this)).Render().Hide().pagesize(50);    
        this._pager.addHandler('changed', function(sender, args) {
            self._getNextPage(this.val());
        });
        this.items()._element.scrollTop(0);
        this.items().Clear();
        this
            .raiseEvent('continousInit', {params: params, complete: function(data) {
                self._pager.Init(self._pager.pagesize(), data.affected).val(1).raiseEvent('changed');
            }});

    },
    
    _getNextPage: function(page) {
        var self = this;
        if(page == 1) {
            self.items().Clear();
        }
        this.raiseEvent('continousNext', {page: this._pager.val(), pagesize: this._pager.pagesize(), complete: function(data) {
            self.items().Add(data.rows);
        }});
        
    },
    
}, {});

UI.Controls.ListViewHeaders = UI.Controls.Control.extend({
    
    constructor: function(name, container, headersData) {
        this.base(name, container);
        if(headersData) {
            this.Add(headersData);
        }
        
        var self = this;
        this.parent().addHandler(['resize', 'shown'], function(sender, args) {

            if(self.children() > 0) {
                
                var itemsWidth = self.parent().items()._list().width();
                var headersWidth = sender.width();
                
                self.forEach(function(k, header) {
                    header.styles({borderRight: null});
                });
                
                var lastHeader = self.controls('lastChild');
                lastHeader.styles({'border-right': (headersWidth - itemsWidth) + 'px solid ' + self.styles('background-color')});
                
            }
            self.raiseEvent('resize', args);
        });
        
    },
    
    Render: function() {
        this.base('div', 'ui-listview-headers');
        return this;
    },

    initSortable: function() {
        var self = this;
        this._element.sortable({
            placeholder: "ui-listview-header-placeholder",
            start: function(e, helper) {
                var pixelWidth = helper.item.attr('data-width');
                // var percentWidth = helper.item.attr('data-percent-width');

                helper.item.outerWidth(pixelWidth);
                helper.placeholder.outerWidth(pixelWidth);
            }, 
            stop: function(e, helper) {
                // var pixelWidth = helper.item.attr('data-width');
                var percentWidth = helper.item.attr('data-percent-width');

                helper.item.outerWidth(percentWidth);
                var header1 = helper.item.data('control');
                
                self.parent().items().changePropertyPosition(header1.headerData().name, helper.item.index());
                
                
            }
        }).disableSelection();
    },
    
    Add: function(headerData) {
        if(!(headerData instanceof Array)) {
            headerData = [headerData];
        }
        
        var self = this;
        var headers = [];
        headerData.forEach(function(header) {
            headers.push((new UI.Controls.ListViewHeader(header.name, self, header)).Render());
        });
        
        return headers.length == 1 ? headers.pop() : headers;
    },     

    Clear: function() {
        this.forEach(function(k, item) {
            item.Dispose();
        });
        return this;
    },
       
    data: function() {
        var headers = [];
        this.forEach(function(name, control) {
            headers.push(control.headerData());
        });
        return headers;
    }

}, {});

UI.Controls.ListViewItems = UI.Controls.Control.extend({
    
    _table: false,
    
    constructor: function(name, container, itemsData) {
        this.base(name, container);
        if(itemsData) {
            this.Add(itemsData);
        }

        var self = this;
        this.parent().addHandler(['resize', 'shown'], function(sender, args) {
            var width = sender.width();
            self.raiseEvent('resize', args);
        });
    },
    
    bindHtmlEvents: function() {
        var self = this;
        this._element.scroll(function() {
            if(self._element.scrollTop() + self._element.innerHeight() == self._element[0].scrollHeight) {
                self.parent().raiseEvent('scrolledToBottom');
            }
            else if(self._element.scrollTop() == 0) {
                self.parent().raiseEvent('scrolledToTop');
            }
        });
        return this;
    },
    
    Render: function() {
        this.base('div', 'ui-listview-items');
        
        this._table = $('<div class="ui-listview-items-table"></div>');
        this._element.append(this._table);
        
        this.bindHtmlEvents();
        
        return this;
    },
    
    Add: function(itemData) {
        if(!(itemData instanceof Array)) {
            itemData = [itemData];
        }
        
        var self = this;
        var items = [];
        var trs = [];
        itemData.forEach(function(item) {
            
            var idField = Object.keys(item)[0];
            
            var key = self.parent().levels() ? item[idField] : self.children();
            if(item instanceof UI.Controls.ListViewItem) {
                items.push(item.Render());
            }
            else {
                var item = (new UI.Controls.ListViewItem(key, self, item)).Render();
                items.push(item);
            }
            trs.push(item._element);
        });
        
        if(self.parent().dragManager()) {
            self.parent().raiseEvent('elementsAdded', {elements: trs});
        }
        
        return items.length == 1 ? items.pop() : items;
    },
    
    Clear: function() {
        this.forEach(function(k, item) {
            item.Dispose();
        });
        this.parent().clearSelection();
        return this;
    },
    
    count: function() {
        return this.children();
    }, 
    
    _list: function() {
        return this._table;
    },
    
    Item: function(index) {
        if(index.length)
            return this._element.find('#' + index).data('control');
        return this.controls(index);
    },
    
    propertyWidth: function(property, width) {
        this._element.find('.ui-listview-item-subitem[property="' + property + '"]').outerWidth(width);
        return this;
    },

    changePropertyPosition: function(property, position) {
        this._element.find('.ui-listview-item-subitem[property="' + property + '"]').each(function(i, o) {
            var o = $(o);
            var parent = o.parent();
            if(position == 0)
                parent.prepend(o);
            else if(position == parent.find('.ui-listview-item-subitem').length -1)
                parent.append(o);
            else
                parent.find('.ui-listview-item-subitem:eq(' + position + ')').before(o);
        });
    },
    
}, {});

UI.Controls.ListViewHeader = UI.Controls.Control.extend({
    
    _headerData: false,

    constructor: function(name, container, headerData) {
        this.base(name, container);
        this._headerData = headerData;
        
        this.parent().addHandler('resize', function(sender, args) {
            
        });
    },
    
    bindHtmlEvents: function() {

        // TODO: сделать перетаскивание колонок
        
        var self = this;
        this._element.click(function() {
            self.listView().focus();
            var sort = self.listView().sortField();
            if(sort.field == self.headerData().name) {
                if(sort.order == 'asc') {
                    self.listView().sortField(self.headerData().name, 'desc');
                }
                else {
                    self.listView().sortField(false, false);
                }
            }
            else {
                self.listView().sortField(self.headerData().name, 'asc');
            }
            
            self._drawSortArrow();
            self.listView().raiseEvent('headerClick', {header: self});
        });

        this._element.mousedown(function() {
            $(this).attr('data-width', $(this).outerWidth());
            $(this).attr('data-percent-width', this.style.width);
        });
        
    },
    
    _drawSortArrow: function() {
        var order = this.listView().sortField().order;
        this.listView()._element.find('.ui-listview-header span').remove();
        if(order)
            $('<span></span>').addClass(order).prependTo(this._element);
    }, 
    
    Render: function() {
        this.base('div', 'ui-listview-header');
        
        if(this._headerData.sortable)
            this._element.addClass('sortable');
            
        if(this._headerData.title) {
            this._element.attr('title', this._headerData.title);
            this._element.html(this._headerData.title);
        }
        if(this._headerData.resizable) {
            this._element.append('<em></em>');
            
            var self = this;
            this._element.resizable({
                handleSelector: this._element.find('em'),
                resizeHeight: false,
                onStartDragging: function(e) {
                    var td1 = this;
                    var td2 = this.next();
                    td2.width('auto');
                }, 
                onDrag: function(e) {

                    var header1 = self;
                    var header2 = self.next();
                    
                    var td1 = header1._element;
                    var td2 = header2._element;
                    
                    var tr = td1.parent();
                    var oldpercentwidth1 = parseInt(header1.headerData().width);
                    var oldpercentwidth2 = parseInt(header2.headerData().width);
                    var newpercentwidth1 = Math.round(td1.outerWidth() * 100 / tr.outerWidth())
                    if(newpercentwidth1 < 2) newpercentwidth1 = 2;
                    var newpercentwidth2 = oldpercentwidth1 + oldpercentwidth2 - newpercentwidth1;
                    td1.css({width: parseInt(newpercentwidth1) + '%'});
                    td2.css({width: parseInt(newpercentwidth2) + '%'});
                    header1.headerData().width = newpercentwidth1 + '%';
                    header2.headerData().width = newpercentwidth2 + '%';
                    
                    self.listView().items().propertyWidth(header1.headerData().name, newpercentwidth1 + '%');
                    self.listView().items().propertyWidth(header2.headerData().name, newpercentwidth2 + '%');
                    
                },
                onStopDragging: function(e) {
                    
                                                    
                },
            }) 
        }
        
        if(this._headerData.width) {
            this.width(this._headerData.width);
        }
        
        this.bindHtmlEvents();

        return this;
    },
    
    listView: function() {
        return this.parent().parent();
    },
    
    headerData: function(val) {
        if(val === undefined)
            return this._headerData;
        else {
            this._headerData = val;
            return this;
        }
    },
    
    next: function() {
        
        var self = this;
        var controls = this.parent().controls();
        var keys = Object.keys(controls);
        for(var i=0; i<keys.length; i++) {
            if(controls[keys[i]] == self) {
                return controls[keys[i + 1]];
            }    
        }
        
        return false;
    }, 
    
    prev: function() {
        
        var self = this;
        var controls = this.parent().controls();
        var keys = Object.keys(controls);
        for(var i=0; i<keys.length; i++) {
            if(controls[keys[i]] == self) {
                return controls[keys[i - 1]];
            }    
        }
        
        return false;
    },
    
}, {});

UI.Controls.ListViewItem = UI.Controls.Control.extend({
    
    _itemData: false,
    
    constructor: function(name, container, itemData) {
        this.base(name, container);
        this._itemData = itemData;
        
        this.parent().addHandler('resize', function(sender, args) {
            
        });
        
    },
    
    _renderRowContent: function() {
        this._element.html('');
        var self = this;
        var hasTemplate = this._itemData._template !== undefined;
        
        if(hasTemplate) {
            
            var td = $('<div class="ui-listview-item-subitem templateable"></div>');
            this._element.append(td);
            
            var table = $('<div class="ui-listview-items-table"></div>');
            var tr = $('<div class="ui-listview-item"></div>');
            table.append(tr);
            td.append(table);

            this._renderRowCells(tr);
            
            var c = $('<div class="ui-listview-item-content"></div>');
            c.html(this._itemData._template);
            td.append(c);
            
            
        }
        else {
            this._renderRowCells(this._element);
        }
        
    },
    
    _renderRowCells: function(el) {
        el.html('');
        var self = this;
        var hasTemplate = this._itemData._template !== undefined;
        
        
        var i = 0;
        this.listView().headers().forEach(function(k, headerObject) {
            header = headerObject.headerData();
            
            var levelField = header.name.split('_');
            levelField = levelField[0] + '_level';
            
            var data = self._itemData;        
            var property = header.name;

            if(data[property] != undefined && header.view == undefined) {
                var value = data[property];
                
                if(header.type == 'numeric')
                    value = parseFloat(value);
                    
                if(header.values != undefined) {
                    if(header.multiple) {
                        value = value.split(',');
                        var newValues = [];
                        value.forEach(function(v) {
                            newValues.push(value.values[v] == undefined ? 'Ошибка' : value.values[v]);
                        });
                        value = newValues.join(', ');
                    }
                    else
                        value = (header.values[value] == undefined ? 'Ошибка' : header.values[value]);
                }
                else if(header.lookup != undefined) {
                    if(header.multiple) {
                        value = value.split(',');
                        var newValues = [];
                        value.forEach(function(v) {
                            var t = findObject(header.lookup.rows, v);
                            newValues.push(t == undefined ? 'Ошибка' : (t == false ? '-' : t));
                        });
                        value = newValues.join(', ');
                    }
                    else {
                        var t = findObject(header.lookup.rows, value);
                        value = (t == undefined ? 'Ошибка' : (t == false ? '-' : t));
                    }
                }

                if(value instanceof Array)
                    value = value.join(', ');    
                    
                title = value;
                try {
                    value = value.ellipsis(100);
                }
                catch(e) {}
            }
            else {
                if(header.view != undefined) {
                    value = eval(header.view);
                }
                title = value;
            }
            
            var type = header.type;
            if(type instanceof Function) {
                header = type(self, headerObject);
                type = header.type;
            }
            
            var isLevels = i == 0 && self.listView().levels();

            var td = $('<div class="ui-listview-item-subitem' + (isLevels ? ' expandable-container' : '') + '" property="' + property + '" style="' + (header.width ? ' width: ' + header.width : '') + '" title="' + (title + '').stripHtml() + '"></div>');
            el.append(td);
            td.data('subitem', property);
            
            var div = td;
            if(isLevels) {
                var div = $('<div class="expandable"></div>');
                div.prepend('<span class="expander leaf" style="padding-left: ' + ((parseInt(data[levelField]))*22) + 'px;"></span>');
                td.append(div);
            }
            
            if(!header.editable)
               div.append(isLevels ? '<span>' + value + '</span>' : value); 
            else {
                
                var field = {
                    field: header.name,
                    type: header.type,
                    title: false,
                };
                if(header.lookup) {
                    field.lookup = $.extend({}, header.lookup);
                    field.lookup.noreload = true;
                }
                if(header.values) {
                    field.values = $.extend({}, header.values);
                }
                field.note = false;
                field.placeholder = header.placeholder;
                if(header.hasTime) field.hasTime = header.hasTime;
                field.width = '100%';
                field.height = '100%';
                field.readonly = false;
                
                if(isLevels) {
                    var span = $('<span></span>');          
                    div.append(span);   
                }
                else {
                    span = td;
                }
                
                var editor = new UI.Controls.Form.FormField.Create(field, span, false)
                    .addHandler('change', function() {        
                        if(this.container().data('checkwith') != JSON.stringify(this.val())) {
                            this.parent().listView().raiseEvent('editableChanged', {row: self, 'property': property, value: this.val()});
                            this.container().attr('data-checkwith', JSON.stringify(this.val()));
                        }
                        return false;
                    })
                    .addHandler('dblclick', function(event, args) {
                        args.domEvent.preventDefault();
                        args.domEvent.stopPropagation();
                        return false;
                    })
                    .addHandler('keydown', function(event, args) {
                        switch(args.domEvent.keyCode) {
                            case 38: // вверx
                            case 40: // вниз   
                            case 36: // home  
                            case 35: // end
                                args.domEvent.preventDefault();
                                args.domEvent.stopPropagation();
                                return false;
                            case 27:
                                this.val(data[property]);
                                row.click();
                                args.domEvent.preventDefault();
                                args.domEvent.stopPropagation();
                                return false;
                            case 13: // enter
                                row.click();
                                args.domEvent.preventDefault();
                                args.domEvent.stopPropagation();
                                return false;
                        }    
                    })
                    .addHandler('blur', function(sender, args) {
                        this.parent().listView().raiseEvent('blur', args);
                    })
                    .parent(self)
                    .Render()
                    .val(data[property]);
                td.attr('data-checkwith', JSON.stringify(data[property]));
                
            }
            
            i++;
        });
    },
    
    Render: function() {
        if(this.listView().levels()) {
            
            var parentField = this.listView().headers().controls('firstChild').headerData().name.split('_');
            parentField = parentField[0] + '_parent';
            
            var parent = this.itemData()[parentField];
            var parentRow = this.listView().items().Item(parent);
            
            if(parentRow)
                this.base('div', 'ui-listview-item', parentRow._element, 'after');
            else
                this.base('div', 'ui-listview-item', this.parent()._list());
            
        }
        else
            this.base('div', 'ui-listview-item', this.parent()._list());
        
        this._renderRowContent();
        this.elementID(this._name);
        this.bindHtmlEvents();
        
        return this;
    },
    
    bindHtmlEvents: function() {
        var self = this;
        this._element.click(function(e) {
            self.listView().focus();
            self.listView().raiseEvent('itemClicked', {domEvent: e, item: self, subitem: $(e.target).data('subitem')});
        });
        this._element.blur(function(е) {
            if(self.listView()._element.has($(е.relatedTarget)).length == 0) {
                self.listView().removeClass('focus');
                self.listView().raiseEvent('blur', {});
            }
        });
        this._element.dblclick(function(e) {
            self.listView().raiseEvent('itemDoubleClicked', {domEvent: e, item: self, subitem: $(e.target).data('subitem')});
        })
    },
    
    listView: function() {
        return this.parent().parent();
    },
    
    itemData: function(val) {
        if(val === undefined)
            return this._itemData;
        else {
            this._itemData = val;
            this._renderRowContent();
            return this;
        }
    },
    
    selected: function(val) {
        if(val == undefined)
            return this._element.hasClass('selected');
        else {
            if(val && !this.selected()) {
                this.addClass('selected');
            }
            else if(!val && this.selected()) {
                this.removeClass('selected');
            }
            return this;
        }
    },
    
    index: function() {
        return this._element.index();
    },     
    
    key: function(val) {
        if(val === undefined)
            return this._element.attr('id');
        else {
            this._element.attr('id', val);
            return this;
        }
    },
    
    block: function(val) {
        
        if(val === undefined) {
            return this._element.hasClass('blocked');
        }
        else {
            val ? this.addClass('blocked') : this.removeClass('blocked');
            return this;
        }
        
    },

    next: function() {
        
        var self = this;
        var controls = this.parent().controls();
        var keys = Object.keys(controls);
        for(var i=0; i<keys.length; i++) {
            if(controls[keys[i]] == self) {
                return controls[keys[i + 1]];
            }    
        }
        
        return false;
    }, 
    
    prev: function() {
        
        var self = this;
        var controls = this.parent().controls();
        var keys = Object.keys(controls);
        for(var i=0; i<keys.length; i++) {
            if(controls[keys[i]] == self) {
                return controls[keys[i - 1]];
            }    
        }
        
        return false;
    },

    
}, {});