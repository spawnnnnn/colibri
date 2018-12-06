UI.Controls.CustomTable = UI.Controls.Control.extend({
    
    _container: false,
    _manager: false,
    _emptyMessage: 'Таблица пуста',
    
    _element: false,
    _elementHeader: false,
    _elementData: false,
    
    _selected: [],
    
    _data: false,
    _header: false,
    
    _sortField: false,
    _sortOrder: false,
    
    _sortable: false,
    _multiple: false,
    _istree: false,
    _treeparentfield: false,
    
    _parent: false, 
    _hasTemplate: false,
    
    _expressions: {},
    
    _dragManager: false,
    
    constructor: function(name, container, multiple, emptyMessage, istree, parentfield) {
        this.base(name, container);
        
        this._multiple = multiple;
        this._istree = istree == undefined ? false : istree;
        this._treeparentfield = parentfield == undefined ? false : parentfield;
        this._emptyMessage = emptyMessage == undefined ? this._emptyMessage : emptyMessage;
        this._data = [];

        this.addHandler('resize', this.__resizeHandler);
        var self = this;
        /*this._tick = window.setInterval(function() {
            self.raiseEvent('resize');
        }, 1000);*/
        // $(window).bind('resizeend', function() { self.raiseEvent('resize'); self.raiseEvent('scrollPositionChanged'); });
        // $(window).bind('resize', function() { self.raiseEvent('resize'); self.raiseEvent('scrollPositionChanged'); });

    },
    
    header: function(value) {
        if(value == undefined)
            return this._header;
        else {
            this._header = value;
            this._sortField = false;
            this._sortOrder = false;
            this.Clear();
            this.renderHeader(this._header, this._elementHeader);
        }
    },
    renderHeader: function(header, container) {
        var self = this;
        container.html('');
        var headerRow = $('<div rel="th"></div>').appendTo(container);     
        $(header).each(function(i, h) {
            if(h.field != 'template') {
                var td = $('<div rel="td" class="' + (h.sortable ? ' sortable' : '') + ' ' + h.type + (h.lookup ? ' lookup' : '') + (h.values ? ' values' : '') + '" field="' + h.field + '" title="' + h.title + '" style="' + (h.headerWidth ? 'width: ' + h.headerWidth + ';' : '') + (h.headerAlign ? ' text-align: ' + h.headerAlign + ';' : '') + '">' + 
                    (h.header != undefined && h.header != true ? h.header : h.title) + (h.resizable ? '<em></em>' : '') + 
                '</div>');
                headerRow.append(td);
                td.data('header', h);
            }
        });
        
        headerRow.find('div[rel=td]').mousedown(function(e) {
            if($(this).data('disablesort')) {
                return false;
            }
            var header = self._findHeader($(this).attr('field'));
            if(!header.sortable)
                return false;
            
            if(self._sortField) {
                if(self._sortField.field == header.field) {
                    self._sortOrder = self._sortOrder == 'asc' ? 'desc' : 'asc';
                }
                else {
                    self._sortOrder = 'asc';
                }
                self._sortField = header;
            }
            else {
                self._sortField = header;
                self._sortOrder = 'asc';
            }
            
            self._drawSortArrow($(this), self._sortOrder);
            
            self.raiseEvent('headerClick');
            return false;
        });
        
        headerRow.find('div[rel=td] em').mousedown(function(e) { e.stopPropagation(); return false;  })
        
        headerRow.find('div[rel=td]').each(function(i, o) {    
            var header = $(o).data('header');
            $(o).resizable({
                handleSelector: $(o).find('em'),
                resizeHeight: false,
                onStartDragging: function(e) {
                    var td1 = this;
                    var td2 = this.next();
                    td2.width('auto');
                }, 
                onDrag: function(e) {
                    var td1 = this;
                    var td2 = this.next();
                    var header1 = td1.data('header');
                    var header2 = td2.data('header');
                    var tr = td1.parent();
                    var oldpercentwidth1 = parseInt(header1.headerWidth);
                    var oldpercentwidth2 = parseInt(header2.headerWidth);
                    var newpercentwidth1 = Math.round(td1.outerWidth() * 100 / tr.outerWidth())
                    if(newpercentwidth1 < 2) newpercentwidth1 = 2;
                    var newpercentwidth2 = oldpercentwidth1 + oldpercentwidth2 - newpercentwidth1;
                    td1.css({width: parseInt(newpercentwidth1) + '%'});
                    td2.css({width: parseInt(newpercentwidth2) + '%'});
                    self._elementData.find('[rel=td][field=' + header1.field + ']').css({width: parseInt(newpercentwidth1) + '%'});
                    self._elementData.find('[rel=td][field=' + header2.field + ']').css({width: parseInt(newpercentwidth2) + '%'});
                    header1.headerWidth = newpercentwidth1 + '%';
                    header2.headerWidth = newpercentwidth2 + '%';
                },
                onStopDragging: function(e) { },
            });    
        });
        
        return headerRow;
        
    }, 
    
    _drawSortArrow: function(headerTd, order) {
        
        this._elementHeader.find('div[rel=th] span').remove();
        $('<span></span>').addClass(order).prependTo(headerTd);
        
    }, 
    
    _findHeader: function(field) {
        var ret = false;
        $(this._header).each(function(i, o) {
            if(o.field == field) {
                ret = o;
                return false;
            }
        });
        return ret;
    },
    
    __resizeHandler: function(event, args) {
        this._elementData.css({marginTop: this._elementHeader.outerHeight() + 'px'});
        this._elementHeader.outerWidth(this._elementData.outerWidth());
        this._elementData.css({marginTop: this._elementHeader.outerHeight() + 'px'});
        // this._element.height(0);
        // this._element.outerHeight(this._element.parent().height() < 200 ? 200 : this._element.parent().height());
        // this._element.show();
    }, 
    
    __scrollPositionChanged: function() {     
        if(!this._elementHeader || !this._element)
            return;
        try {          
            if(this._elementHeader.offset().top != this._element.offset().top) {
                this._elementHeader.css({top: this._element.offset().top + 1});
            }
        }
        catch(e) {}
    },
    
    renderFields: function(row, d, treeState) {
        var self = this;
        $(self._header).each(function(i, h) {
            var v = '';
            var title = '';
            var headers = self._header;
            
            if(h.field != 'template') {
            
                if(d[h.field] != undefined && h.view == undefined) {
                    v = d[h.field];            
                    
                    if(h.type == 'numeric')
                        v = parseFloat(v);
                        
                    if(h.values != undefined) {
                        if(h.multiple) {
                            v = v.split(',');
                            var vv = '';
                            $(v).each(function() {
                                vv += (', ' + (h.values[this] == undefined ? 'Ошибка' : h.values[this]));
                            });
                            v = vv.substr(2);
                        }
                        else
                            v = (h.values[v] == undefined ? 'Ошибка' : h.values[v]);
                    }
                    else if(h.lookup != undefined) {
                        if(h.multiple) {
                            v = v.split(',');
                            var vv = '';
                            $(v).each(function() {
                                var t = findObject(h.lookup.rows, this);
                                vv += (', ' + (t == undefined ? 'Ошибка' : (t == false ? '-' : t)));
                            });
                            v = vv.substr(2);
                        }
                        else {
                            var t = findObject(h.lookup.rows, v);
                            v = (t == undefined ? 'Ошибка' : (t == false ? '-' : t));
                        }
                    }

                    if(v instanceof Array)
                        v = v.join(', ');    
                        
                    title = v;
                    try {
                        v = v.ellipsis(100);
                    }
                    catch(e) {}
                }
                else {
                    if(h.view != undefined) {
                        v = eval(h.view);
                    }
                    title = v;
                }
                
                var type = h.type;
                if(type instanceof Function) {
                    h = type(d, $.extend({}, h));
                    type = h.type;
                }
                
                if(h.editable !== undefined && h.editable) { //  && (h.type == 'numeric' || h.type == 'text') && h.lookup == undefined && h.values == undefined
                    // row.append('<div rel="td" class="' + h.type + '" field="' + h.field + '" style="width: ' + (h.headerWidth !== undefined ? h.headerWidth : '100%') + '"><input type="text" class="' + h.type + '" id="custom_editor_' + h.field + '_' + row.index() + '" checkwith="' + v + '" value="' + v + '"' + (h.size ? ' size="' + h.size + '"' : '') + ' style="width: ' + (h.width !== undefined ? h.width + 'px' : '100%') + '" /></div>'); 
                    var cell = $('<div rel="td" class="' + type + (h.lookup ? ' lookup' : '') + (h.values ? ' values' : '') + '" field="' + h.field + '" style="width: ' + (h.headerWidth !== undefined ? h.headerWidth : '100%') + ';' + (h.bgcolor ? 'background-color: ' + h.bgcolor + ';' : '') + '"></div>').appendTo(row);
                    
                    var hh = $.extend({}, h); 
                    hh.readonly = false; 
                    hh.width = hh.post ? 'calc(100% - 20px)' : '100%'; 
                    hh.height = '100%'; 
                    hh.title = false; 
                    if(hh.lookup) hh.lookup.noreload = true;
                    hh.field = 'ct_' + hh.field + '_' + Date.Now().getTime();
                    hh.type = type;
                    hh.note = false;
                    var editor = new UI.Controls.Form.FormField.Create(hh, cell, false).addHandler('ready', function() {
                        this.val(d[h.field]);
                        this.container().attr('data-checkwith', JSON.stringify(d[h.field]));
                    });
                    editor.parent(d).Render().addHandler('change', function() {        
                        if(this.container().data('checkwith') != JSON.stringify(this.val())) {
                            self.raiseEvent('editableChanged', {row: row.index(), 'field': cell.attr('field'), value: this.val()});
                            this.container().attr('data-checkwith', JSON.stringify(this.val()));
                            // row.click();
                        }
                        return false;
                    }).addHandler('dblclick', function(event, args) {
                        args.event.preventDefault();
                        args.event.stopPropagation();
                        return false;
                    }).addHandler('keydown', function(event, args) {
                        switch(args.event.keyCode) {
                            case 38: // вверx
                            case 40: // вниз   
                            case 36: // home  
                            case 35: // end
                                args.event.preventDefault();
                                args.event.stopPropagation();
                                return false;
                            case 27:
                                this.val(d[h.field]);
                                row.click();
                                args.event.preventDefault();
                                args.event.stopPropagation();
                                return false;
                            case 13: // enter
                                row.click();
                                args.event.preventDefault();
                                args.event.stopPropagation();
                                return false;
                        }    
                    });
                    
                }
                else {
                    
                    row.append('<div rel="td" class="' + type + (h.lookup ? ' lookup' : '') + (h.values ? ' values' : '') + '" field="' + h.field + '" style="width: ' + (h.headerWidth !== undefined ? h.headerWidth : '100%') + ';' + (h.bgcolor ? 'background-color: ' + h.bgcolor + ';' : '') + '" title="' + (title + '').stripHtml() + '">' + 
                        (self._istree && i == 0 ? '<a href="#!" class="tree-pointer ' + (d['_childs'].length > 0 ? (treeState ? 'minus' : 'plus') : 'leaf') + '" style="margin-left: ' + (d['_level']*20) + 'px;"></a>' : '') + 
                        (self._istree && i == 0 ? '<span class="tree-info">' + v + '</span>' : v) + 
                    '</div>');
                }
                
            }

            
        });
    },
    
    viewExpressions: function() {
        var self = this;
        $.map(this._expressions, function(expression, key) {
            
            $(self._data).each(function(i, dta) {    
                
                var key = dta[expression.key];
                var val = '-';
                $(expression.results).each(function(i, r) {
                    if(r[expression.key] == dta[expression.key]) {
                        val = r.value;
                        return false;
                    }
                });
                
                self._elementData.find('>[rel=tr]:eq(' + i + ') [rel=td][field=' + expression.field + ']').html(val);
                
            });
            
        });
    }, 
    
    clickOnRow: function(row, e) {
        
        var self = this;
        
        if(e != undefined && e.target && ($(e.target).is('input') || $(e.target).is('select'))) {
            this._element.addClass('focus');
        }
        else
            this._element.focus();
        
        if(row.length == undefined)
            row = $(row);
        
        if(row.hasClass('blocked'))
            return false;

        if(this._multiple) {
            
            if(this._selected.length <= 1 && this._selected[0] == $(row).index())
                return;
            
            if(this._selected.length == 0) {
                row.addClass('selected');
                this._selected.push(row.index());
            }
            else {
                
                if(e && e.ctrlKey) {                      
                    row.addClass('selected');
                    this._selected.push(row.index());
                }
                else if(e && e.shiftKey) {
                    
                    var endIndex = row.index();
                    var startIndex = this._selected[0];
                    
                    var i1 = Math.min(startIndex, endIndex);
                    var i2 = Math.max(startIndex, endIndex);

                    var parent = row.parent();
                    this.clearSelection();
                    for(var i=i1;i<=i2;i++) {
                        parent.find('>[rel=tr]:eq(' + i + ')').addClass('selected');
                        this._selected.push(i);
                    }
                    
                }
                else {
                    
                    this.clearSelection();
                    
                    row.addClass('selected');
                    this._selected.push(row.index());
                    
                }
                
            }
                    
            this.raiseEvent('selectionChanged', {});
            
            
            // out(e.shiftKey, e.ctrlKey);
            
        }
        else {

            /*if(this._selected[0] != $(row).index())
                return;*/
            
            if(this._selected.length > 0)
                self._elementData.find('>div[rel=tr]:eq(' + this._selected[0] + ')').removeClass('selected');
            $(row).addClass('selected');
            
            this._selected = [];
            this._selected.push($(row).index());
            
            this.raiseEvent('selectionChanged', {});
            
        }
        
        
    }, 
    
    Render: function(className, header) {   
        var self = this;
        
        // this._data = data;  
        this._header = header;              
                                        
        this.base('div', 'ui-table-container ' + className);
        this.tabIndex(true);
        
        this._elementHeader = $('<div rel="table" class="ui-table ui-table-header ui-fixed ' + className + '"></div>').appendTo(this._element);
        this._elementData = $('<div rel="table" class="ui-table ui-table-data ' + className + '" style="margin-top: ' + this._elementHeader.height() + 'px;"></div>').appendTo(this._element);

        if(this._header) {
            this.renderHeader(this._header, this._elementHeader);
        }
        
        this.addHandler('scrollPositionChanged', this.__scrollPositionChanged);
        if(this._container.closest('.ui-window-container').length > 0) {
            this._container.closest('.ui-window-container').scroll(function() { self.raiseEvent('scrollPositionChanged') });
        }
        else {
            // тут нужно исправить, непонятно при скроле чего нужно событие вызывать
            $('#content').scroll(function() { self.raiseEvent('scrollPositionChanged'); });
        }
        
        // self._elementData.append('<div rel="td-empty">' + self._emptyMessage + '</div>');
        
        /*var dta = $(this._data);
        if(dta.length == 0) {
            this._container.append('<div rel="td-empty">' + this._emptyMessage + '</div>');
        }
        else 
            $(this._data).each(function(i, d) {

                var row = $('<div rel="tr"></div>').appendTo(this._element);
                $(this._header).each(function(i, h) {
                    row.append('<div rel="td" field="' + h.field + '">' + (d[h.field] == undefined ? d[h.field] : '') + '</div>');
                });  
                
                row.click(function() {
                    self.clickOnRow(this);
                });
                
            }); */
            
        this.raiseEvent('ready', []);
        
        this._element.click(function() { 
            self.clearSelection();
        });
        
        this._element.attr('tabIndex', UI.tabIndex++);
        
        this._element.keydown(function(e) {
            //out(e.keyCode);
            switch(e.keyCode) {
                case 38: // вверx
                    var index = self.selected()[0];
                    self.clearSelection();
                    self.selectRow(index - 1);
                    self.bringToView(self.selected()[0]);
                    return false;
                case 40: // вниз   
                    var index = self.selected()[0];
                    self.clearSelection();
                    self.selectRow(index + 1);
                    self.bringToView(self.selected()[0]);
                    return false;
                case 36: // home
                    self.clearSelection();
                    self.selectRow(0);
                    self.bringToView(0);
                    return false;
                case 35: // end
                    self.clearSelection();
                    self.selectRow(self.count() - 1);
                    self.bringToView(self.count() - 1);
                    return false;
                case 13: // end
                    if(self.selected().length > 0)
                        self.raiseEvent('rowDoubleClicked',{});
                    return false;
            }
            
            if(e.shiftKey || e.ctrlKey) {
                self._element.disableSelection();
            }
        });
        
        this._element.keyup(function(e) {
            self._element.enableSelection();
        });
        
        this._element.blur(function() {
            $(this).removeClass('focus');
        })
        
        //$(window).resize();
        
        return this;
        
    }, 
    
    Clear: function() {
        this.clearSelection();
        this._data = [];
        //this._element.find('div[rel=td-empty]').remove();
        this._elementData.find('>div[rel=tr]').remove();
        /*if(this._element.find('[rel=td-empty]').length == 0) {
            this._element.after('<div rel="td-empty">' + this._emptyMessage + '</div>');
        }*/
    }, 
    
    selectRow: function(i) {
        if(i > this.count() - 1)
            i = this.count() - 1;
        if(i < 0)
            i = 0;
            
        this.clickOnRow(this._elementData.find('>div[rel=tr]:eq(' + i + ')'));
    }, 
    
    bringToView: function(i) {
        if(i > this.count() - 1)
            i = this.count() - 1;
        
        if( isScrolledElementVisible(this._elementData.find('>div[rel=tr]:eq(' + i + ')'), this._container, 60) )
            return false;                                                                        
        
        this._elementData.find('>div[rel=tr]:eq(' + i + ')')[0].scrollIntoView(false);
        
        /*this._container.scrollTop(this._elementData.find('>div[rel=tr]:eq(' + i + ')').offset().top - this._container.find('div[rel=th]').offset().top - this._container.find('div[rel=th]').height());*/
        
    },
    
    clearSelection: function() {
        var self = this;
        $(this._selected).each(function(i, index) {      
            self._elementData.find('>div[rel=tr]:eq(' + this + ')').removeClass('selected');
        });
        this._selected = [];
        this.raiseEvent('selectionChanged', {});
    }, 
    
    _generateTreeLevel: function(rows, parent, level) {
        var self = this;
        parent['_childs'] = [];
        $(rows).each(function(i, d) {
            if(d[self._treeparentfield] == parent['id']) {
                d['_level'] = level;
                parent['_childs'].push(d);
                self._generateTreeLevel(rows, d, level+1);
            }
        });
    }, 
    
    _findById: function(childs, id) {
        
        var index = false;
        $(childs).each(function(i, o) {
            if(o['id'] == id) {
                index = o;
                return false;
            }
        });
        return index;
        
    }, 
    
    _findRowByKey: function(tr) {
        var self = this;
        var key = tr.data('key') + '';
        var indexes = key.split(':');
        var rrows = this._data;
        var rrow = false;
        $(indexes).each(function(i, id) {
            rrow = self._findById(rrows, id);
            rrows = rrow['_childs'];
        });
        return rrow;
    },
    
    _toggleTreeLevel: function(tr) {
        var self = this;

        var dataRow = this._findRowByKey(tr);
        if(dataRow['_childs'].length > 0) {
            
            var isClosed = tr.find('a.tree-pointer').hasClass('plus');
            if(isClosed) {
                tr.find('a.tree-pointer').removeClass('plus').addClass('minus');
                if(tr.parent().find('>[rel=tr][data-parent=' + dataRow['id'] + ']').length > 0) {
                    tr.parent().find('>[rel=tr][data-parent=' + dataRow['id'] + ']').show();
                }
                else {
                    var lastRow = tr;
                    var parentKey = tr.data('key');
                    $(dataRow['_childs']).each(function(i, d) {
                        var row = $('<div rel="tr"' + (d['error'] ? ' class="error"' : '') + (self._istree ? ' data-id="' + d['id'] + '" data-parent="' + d[self._treeparentfield] + '" data-key="' + parentKey + ':' + d['id'] + '"' : '') + '></div>');
                        lastRow.after(row);
                        self.renderFields(row, d);
                        row.find('.tree-pointer').click(function(e) { var row = $(e.target).parent().parent(); self._toggleTreeLevel(row); return false; });
                        row.click(function(e) { self.clickOnRow(this, e); return false; });
                        row.dblclick(function(e) { self.clickOnRow(this, e); self.raiseEvent('rowDoubleClicked', {}); return false; });
                        // self._toggleTreeLevel(row);
                        lastRow = row;
                    });
                }
                self.raiseEvent('expanded', {row: tr.index(), rowData: dataRow});
            }
            else {
                tr.find('a.tree-pointer').addClass('plus').removeClass('minus');
                tr.parent().find('>[rel=tr][data-parent=' + dataRow['id'] + ']').each(function(i, t) {
                    if($(t).find('a.tree-pointer').hasClass('minus'))
                        self._toggleTreeLevel($(t));
                });
                tr.parent().find('>[rel=tr][data-parent=' + dataRow['id'] + ']').hide();
                self.raiseEvent('collapsed', {row: tr.index(), rowData: dataRow});
            }
        }
        
    },
    
    addRows: function(rows) {
        
        var self = this;
        var trs = [];
        // var jrows = [];
        
        if(self._istree) {

            if(self._data.length == 0) {

                $(rows).each(function(i, d) {
                    if(d[self._treeparentfield] == 0) {
                        d['_level'] = 0;
                        self._data.push(d);
                    }
                });
                
                $(self._data).each(function(i, d) {
                    self._generateTreeLevel(rows, d, 1);
                });
                
                $(self._data).each(function(i, d) {
                    
                    
                    var row = $('<div rel="tr"' + (d['error'] ? ' class="error"' : '') + (self._istree ? ' data-id="' + d['id'] + '" data-parent="' + d[self._treeparentfield] + '" data-key="' + d['id'] + '"' : '') + '></div>').appendTo(self._elementData);
                    if(self._hasTemplate) {
                        var td = $('<div rel="td" style="width: 100%; padding: 0px; border: 0px;"></div>').appendTo(row);
                        var templateTable = $('<div rel="table" class="ui-table"></div>').appendTo(td);
                        var rowt = $('<div rel="tr"' + (d['error'] ? ' class="error"' : '') + (self._istree ? ' data-id="' + d['id'] + '" data-parent="' + d[self._treeparentfield] + '" data-key="' + d['id'] + '"' : '') + '></div>').appendTo(templateTable);
                        self.renderFields(rowt, d);
                    }
                    else 
                        self.renderFields(row, d);
                    
                    
                    row.find('.tree-pointer').click(function(e) { var row = $(e.target).parent().parent(); self._toggleTreeLevel(row); return false; });
                    row.click(function(e) { self.clickOnRow(this, e); return false; });
                    row.dblclick(function(e) { self.clickOnRow(this, e); self.raiseEvent('rowDoubleClicked', {}); return false; });
                    // self._toggleTreeLevel(row);
                    trs.push(row);
                    
                });
            
            }
            else {
                
                $(rows).each(function(i, d) {
                    
                    if(d[self._treeparentfield] == 0) {
                        d['_level'] = 0;
                        d['_childs'] = [];
                        self._data.push(d);

                        var row = $('<div rel="tr"' + (d['error'] ? ' class="error"' : '') + (self._istree ? ' data-id="' + d['id'] + '" data-parent="' + d[self._treeparentfield] + '" data-key="' + d['id'] + '"' : '') + '></div>').appendTo(self._elementData);
                        if(self._hasTemplate) {
                            var td = $('<div rel="td" style="width: 100%; padding: 0px; border: 0px;"></div>').appendTo(row);
                            var templateTable = $('<div rel="table" class="ui-table"></div>').appendTo(td);
                            var rowt = $('<div rel="tr"' + (d['error'] ? ' class="error"' : '') + (self._istree ? ' data-id="' + d['id'] + '" data-parent="' + d[self._treeparentfield] + '" data-key="' + d['id'] + '"' : '') + '></div>').appendTo(templateTable);
                            self.renderFields(rowt, d);
                        }
                        else
                            self.renderFields(row, d);
                            
                        row.find('.tree-pointer').click(function(e) { var row = $(e.target).parent().parent(); self._toggleTreeLevel(row); return false; });
                        row.click(function(e) { self.clickOnRow(this, e); return false; });
                        row.dblclick(function(e) { self.clickOnRow(this, e); self.raiseEvent('rowDoubleClicked', {}); return false; });
                        trs.push(row);

                    }
                    else {
                        
                        var tr = self._elementData.find('>[rel=tr][data-id=' + d[self._treeparentfield] + ']');
                        var rrow = self._findRowByKey(tr);
                        d['_level'] = rrow['_level'] + 1;
                        d['_childs'] = [];
                        rrow['_childs'].push(d);
                        
                        if(tr.find('a.tree-pointer').hasClass('leaf')) {
                            tr.find('a.tree-pointer').removeClass('leaf').addClass('plus');
                        }
                        else {
                            
                            var branch = tr.parent().find('>[rel=tr][data-key^="' + tr.data('key') + '"]');
                            var lastRow = tr.parent().find('>[rel=tr][data-key^="' + tr.data('key') + '"]:last');
                            if(branch.length > 1) {
                                var row = $('<div rel="tr"' + (d['error'] ? ' class="error"' : '') + (self._istree ? ' data-id="' + d['id'] + '" data-parent="' + d[self._treeparentfield] + '" data-key="' + tr.data('key') + ':' + d['id'] + '"' : '') + '></div>');
                                lastRow.after(row);
                                if(self._hasTemplate) {
                                    var td = $('<div rel="td" style="width: 100%; padding: 0px; border: 0px;"></div>').appendTo(row);
                                    var templateTable = $('<div rel="table" class="ui-table"></div>').appendTo(td);
                                    var rowt = $('<div rel="tr"' + (d['error'] ? ' class="error"' : '') + (self._istree ? ' data-id="' + d['id'] + '" data-parent="' + d[self._treeparentfield] + '" data-key="' + d['id'] + '"' : '') + '></div>').appendTo(templateTable);
                                    self.renderFields(rowt, d);
                                }
                                else
                                    self.renderFields(row, d);
                                row.find('.tree-pointer').click(function(e) { var row = $(e.target).parent().parent(); self._toggleTreeLevel(row); return false; });
                                row.click(function(e) { self.clickOnRow(this, e); return false; });
                                row.dblclick(function(e) { self.clickOnRow(this, e); self.raiseEvent('rowDoubleClicked', {}); return false; });
                                if(!lastRow.is(':visible')) row.hide();
                                trs.push(row);
                            }
                            
                        }
                        
                    }
                    
                });
                
            }
            
        }
        else {
        
            $(rows).each(function(i, d) {
                
                var key = 'row' + Number.Rnd4();
                if(d['id']) {
                    key = d['id'];
                }
                
                self._data.push(d);

                var row = $('<div rel="tr"' + (d['_bgcolor'] ? ' style="background-color: ' + d['_bgcolor'] + '"' : '') + (d['error'] ? ' class="error"' : '') + ' data-id="' + key + '"' + '></div>').appendTo(self._elementData);
                if(self._hasTemplate) {
                    var td = $('<div rel="td" style="width: 100%; padding: 0px; border: 0px;"></div>').appendTo(row);
                    var templateTable = $('<div rel="table" class="ui-table"></div>').appendTo(td);
                    var rowt = $('<div rel="tr"' + (d['error'] ? ' class="error"' : '') + (self._istree ? ' data-id="' + d['id'] + '" data-parent="' + d[self._treeparentfield] + '" data-key="' + d['id'] + '"' : '') + '></div>').appendTo(templateTable);
                    self.renderFields(rowt, d);
                        
                    var h = self._findHeader('template');
                    if(h) {
                        var content = eval(h.view);
                        var templateContent = $('<div class="template-content"></div>').appendTo(td);
                        templateContent.html(content);
                    }
                }
                else
                    self.renderFields(row, d);
                
                row.click(function(e) { self.clickOnRow(this, e); return false; });
                row.dblclick(function(e) { self.clickOnRow(this, e); self.raiseEvent('rowDoubleClicked', {}); return false; });
                trs.push(row);
                // jrows.push(row);
                
                
            });
            
        }
        
        /*if(self._data.length == 0 && self._container.find('[rel=td-empty]').length == 0) {
            self._elementData.append('<div rel="td-empty">' + self._emptyMessage + '</div>');
        }*/
        
        if(self._sortable) {
            this._elementData.sortable({ 
                items: '>div[rel=tr]',
                stop: function(event, ui) {
                    self.raiseEvent('rowPositionChanged', {newIndex: ui.item.index(), originalIndex: parseInt(ui.item.attr('originalIndex')), row: ui.item});
                    ui.item.removeAttr('originalIndex');
                },
                start: function(event, ui) {
                    if(ui.item.hasClass('selected'))
                        self.clearSelection();
                    ui.item.attr('originalIndex', ui.item.index());
                }
            });
        }
        
        if(self.dragManager()) {
            self.raiseEvent('elementsAdded', {elements: trs});
        }
        
        self.viewExpressions();
        
        //$(window).resize();
        
    }, 
    
    getRows: function(index) {
        if(this._istree) {
            if(index == undefined)
                return this._data;
                
            return this._findRowByKey(this._elementData.find('>[rel=tr]:eq(' + index + ')'));
        }
        else
            return index == undefined ? this._data : this._data[index];
    }, 
    
    expandAll: function() {
        var self = this;
        while(this._elementData.find('.tree-pointer.plus').length > 0)
            this._elementData.find('.tree-pointer.plus').click();
        
    },
    
    setRowStyles: function(index, styles) {
        this._elementData.find('>[rel=tr]:eq(' + index + ')').css(styles);
    },

    getHeader: function() {
        return this._header;
    }, 
    
    changeRow: function(index, row) {
        var self = this;
        
        if(this._istree) {
            
            var self = this;
            var rrow = this._findRowByKey(this._elementData.find('>[rel=tr]:eq(' + index + ')'));

            rrow = $.extend(rrow, row);
            
            var state = self._elementData.find('>[rel=tr]:eq(' + index + ') >[rel=td] a.tree-pointer').hasClass('minus');
            self._elementData.find('>[rel=tr]:eq(' + index + ')').find('>[rel=td]').remove();
            self.renderFields(self._elementData.find('>[rel=tr]:eq(' + index + ')'), rrow, state);
            self._elementData.find('>[rel=tr]:eq(' + index + ')').find('.tree-pointer').click(function(e) { var row = $(e.target).parent().parent(); self._toggleTreeLevel(row); return false; });
            
        }
        else {
            
            var trow = self._elementData.find('>[rel=tr]:eq(' + index + ')');
            self._data.splice(index, 1, row);                                
            trow.find('>[rel=td]').remove();
            
            var d = self._data[index];
            if(self._hasTemplate) {
                var td = $('<div rel="td" style="width: 100%; padding: 0px; border: 0px;"></div>').appendTo(trow);
                var templateTable = $('<div rel="table" class="ui-table"></div>').appendTo(td);
                var rowt = $('<div rel="tr"' + (d['error'] ? ' class="error"' : '') + (self._istree ? ' data-id="' + d['id'] + '" data-parent="' + d[self._treeparentfield] + '" data-key="' + d['id'] + '"' : '') + '></div>').appendTo(templateTable);
                self.renderFields(rowt, d);
                
                var h = self._findHeader('template');
                if(h) {
                    var content = eval(h.view);
                    var templateContent = $('<div class="template-content"></div>').appendTo(td);
                    templateContent.html(content);
                }
            }
            else {
                self.renderFields(trow, d);
            }            
        }
        self.viewExpressions();
        
    },
    
    deleteRow: function(index) {
        var self = this;   
        if(this._istree) {
                                                                  
            var tr = self._elementData.find('>[rel=tr]:eq(' + index + ')');
            var row = self._findRowByKey(tr);

            var tr2 = self._elementData.find('>[rel=tr][data-id=' + row['parent'] + ']');
            if(tr2.length > 0) {
                var row2 = self._findRowByKey(tr2);
            }
            else  {
                var row2 = {'_childs': self._data};
            }
            
            $(row2['_childs']).each(function(i, d) {
                if(d['id'] == row['id']) {
                    row2['_childs'].splice(i, 1);
                    return false;
                }
            });

            var f = function(row) {
                $(row['_childs']).each(function(i, d) {
                    f(d);
                    self._elementData.find('>[rel=tr][data-id=' + d['id'] + ']').remove();
                });
            }
            f(row);
            tr.remove();
            
            if(row2['_childs'].length == 0) {
                tr2.find('a.tree-pointer').removeClass('plus').removeClass('minus').addClass('leaf');
            }   
        }   
        else {                              
            self._data.splice(index, 1);
            self._elementData.find('>[rel=tr]:eq(' + index + ')').remove();
            
        }
        
        var selIndex = this._selected.indexOf(index);
        if(selIndex != -1) {
            this._selected.splice(selIndex, 1);
        }
        
        /*if(self._data.length == 0 && self._container.find('[rel=td-empty]').length == 0) {
            self._elementData.after('<div rel="td-empty">' + self._emptyMessage + '</div>');
        }*/
        
    }, 
    
    blockRow: function(index) {
        this._elementData.find('>[rel=tr]:eq(' + index + ')').addClass('blocked');
    }, 
    
    unblockRow: function(index) {
        this._elementData.find('>[rel=tr]:eq(' + index + ')').removeClass('blocked');
    }, 
    
    hideRows: function(indices) {
        var self = this;
        if(indices != undefined) {
            $(indices).each(function(i, index) {
                self._elementData.find('>[rel=tr]:eq(' + index + ')').hide();    
            });
        }
        self.clearSelection();
        return self;
    },
    
    showRows: function(indices) {
        var self = this;
        if(indices == undefined)
            self._elementData.find('>[rel=tr]').show();    
        else {
            $(indices).each(function(i, index) {
                self._elementData.find('>[rel=tr]:eq(' + index + ')').show();    
            });
        }
        self.clearSelection();
        self.viewExpressions();
        return self;
    },                                             
    
    selected: function() {
        return this._selected;
    },
    
    sortable: function(val) {
        if(val == undefined)
            return this._sortable;
        else {
            this._sortable = val;
            return this;
        }
    },
    
    container: function() {
        return this._elementData;
    }, 
    
    count: function() {
        return this._data.length;
    },
    Count: function() {
        return this.count();
    },

    sortfield: function(value) {
        if(value == undefined)
            return this._sortField;
        else {
            this._sortField = value;
            return this;
        }
    },
    sortorder: function(value) {
        if(value == undefined)
            return this._sortOrder;
        else {
            this._sortOrder = value;
            return this;
        }
    },

    expressions: function(key, value) {
        if(key == undefined)
            return this._expressions;
        else {
            this._expressions[key] = value;
            this.viewExpressions();
        }
    },
    
    Dispose: function() {
        this._elementHeader.remove();
        this._elementData.remove();
        this.base();
    },
    
    hasTemplate: function(val) {
        if(val == undefined)
            return this._hasTemplate;
        else {
            this._hasTemplate = val;
            return this;
        }
    },
    
    key: function(index) {
        return this._elementData.find('>[rel=tr]:eq(' + index + ')').attr('data-id');
    },
    
    index: function(key) {
        return this._elementData.find('>[rel=tr][data-id=' + key + ']').index();
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
        if(el.find('.ui-table[rel=table]').length > 0) {
            return el.find('.ui-table[rel=table] [rel=td]:first').html();
        }
        else
            return el.find('>[rel=td]:first').html();
    },

}, {});