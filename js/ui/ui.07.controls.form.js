UI.Controls.Form = {
    charWidth: 9,
    inputPadding: 13
};

UI.Controls.Form.Pane = UI.Controls.Pane.extend({
    
}, {});

UI.Controls.Form.Tabs = UI.Controls.Control.extend({
    
    _tabs: false,
    _selectedTab: false,
    
    constructor: function(name, container) {
        this.base(name, container);
        this._tabs = [];
        this.addHandler('tabChanged', function() {
            $(this._tabs).each(function(i, v) {
                $(v.controls).each(function(i, c) {
                    c.Hide();
                });
            });
            
            $(this._tabs[this._selectedTab].controls).each(function(i, c) {
                c.Show();
            })
        });
    },
    
    Render: function() {
        var self = this;
        
        this.base('div', 'ui-formfield-tabs');
        this._element.append('<div class="ui-formfield-tabs-arrows"><a href="#" class="left">◄</a><a href="#" class="right">►</a></div>');
        this._element.append('<div class="ui-formfield-tabs-container"><div></div></div>');
        this._element.attr('tabIndex', UI.tabIndex++);
        
        $(window).resize(function() {
            self._element.hide();
            self._element.width(self._element.parent().width());
            self._element.show();
            
            try {
                var tabsContainer = self._element.find('.ui-formfield-tabs-container');
                var slider = tabsContainer.find('>div');
                var lastTab = slider.find('>div:last');      
                var arrows = self._element.find('.ui-formfield-tabs-arrows');
                slider.css('margin-left', '0px');
                if(lastTab.offset().left - tabsContainer.offset().left + lastTab.width() > tabsContainer.width() - arrows.width()) {
                    self._element.find('.ui-formfield-tabs-arrows').show();
                }
                else {
                    self._element.find('.ui-formfield-tabs-arrows').hide();
                }
            }
            catch(e) {
                
            }
                   
        });
        
        this._element.find('.ui-formfield-tabs-arrows a').click(function() {
            var tabsContainer = self._element.find('.ui-formfield-tabs-container');
            var slider = tabsContainer.find('>div');
            var lastTab = slider.find('>div:last');
            var arrows = self._element.find('.ui-formfield-tabs-arrows');
            
            var currentMargin = parseInt(slider.css('margin-left'));
            if($(this).is('.right')) {
                if((lastTab.offset().left + lastTab.width()) - tabsContainer.offset().left > tabsContainer.width() - arrows.width() )
                    slider.animate({'margin-left': currentMargin - 100}, 100);
            }
            else {
                if(currentMargin + 100 <= 0)
                    slider.animate({'margin-left': currentMargin + 100}, 100);
            }
            
            
        });

        this._element.keydown(function(e) {
            if(self.selected().length === 0) {
                self.selectTab(0);
                return false;
            }

            var selected = self.selected();
            switch(e.keyCode) {
                case 39: { // right 
                    if(selected < self._tabs.length - 1)
                        self.selectTab(selected + 1);
                    return false;

                }
                case 37: { // left 
                    if(selected > 0)
                        self.selectTab(selected - 1);
                    return false;
                }
            }       
            return true;           
        });
        
        return this;
    },
    
    tabExists: function(title) {
        var ret = false;
        $(this._tabs).each(function(i, o) {
            if(o.title == title) {
                ret = true;
                return false;
            }
        });
        return ret;
    }, 
    
    addTab: function(title, controls) {
        var self = this;
        if(!this.tabExists(title)) {
            
            this._tabs.push({title: title, controls: controls ? controls : []});
            
            var tab = $('<div class="ui-formfield-tab"><span>' + title + '</span></div>').appendTo(this._element.find('.ui-formfield-tabs-container>div'));
            tab.click(function(e) {
                if(self._selectedTab == false || self._selectedTab != ($(e.target).parent().index())) {
                    self._element.find('.ui-formfield-tab:eq(' + self._selectedTab + ')').removeClass('selected');
                    self._selectedTab = $(e.target).parent().index();
                    $(e.target).parent().addClass('selected');
                    self.raiseEvent('tabChanged', {});
                    $(window).resize();
                }
            });
        }
    }, 
    
    generate: function(tabs) {
        var self = this;
        tabs.forEach(function(tab) {
            self.addTab(tab);
        });
    },
    
    removeTab: function(index) {
        this._tabs.splice(index, 1);
        this._element.find('.ui-formfield-tab:eq(' + index + ')').remove();
    }, 
    
    addToTab: function(title, control) {
        if(!this.tabExists(title))
            this.addTab(title, []);

        this.tab(title).controls.push(control);
        return control;
    }, 
    
    tab: function(title) {
        if(title == undefined)
            return false;
        
        if(!isNaN(parseInt(title))) {
            return this._tabs[title];
        }
        
        var ret = false;
        $(this._tabs).each(function() {
            if(this.title == title) {
                ret = this;
                return false;
            }
        });
        return ret;
    }, 
    
    selectTab: function(index) {
        this._element.find('.ui-formfield-tab:eq(' + index + ')>span').click();
    },
    
    validate: function() {
        return true;
    }, 
    
    selected: function() {
        return this._selectedTab;
    },
    
}, {});

UI.Controls.Form.FormField = UI.Controls.Control.extend({
    
    _title: false,
    _field: false,
    
    _changed: false,
    
    constructor: function(name, container, title, className, field) {
        this.base(name, container);
        this._title = title;
        this._className = className == undefined ? '' : className;
        this._field = field == undefined ? false : field;
    }, 
    
    RenderControl: function(controlContainer) {
        
    }, 
    
    Render: function() {
        
        var self = this;
        
        if(this.field().require)
            UI.require(this.field().require.css, this.field().require.js);
        
        this.base('div', 'ui-formfield' + (this._field.required != undefined && this._field.required == true ? ' ui-required ' : '') + (this._field.className != undefined ? ' ' + this._field.className : '') + this._className);
        this._element.css('width', this._field.width);
        this._element.attr('title', 'Название поля: ' + this._field.field);  
        if(this._field.newline != undefined && (this._field.newline == 1 || this._field.newline == 'true')) {
            if(!this._element.prev().is('br'))
                this._element.before('<br />');
        }
        
        if(this._field.title)
            this._element.append('<div class="ui-formfield-title">' + this._title + (this._field.required != undefined && this._field.required == true ? '<span>*</span>' : '') + '</div>');
        
        this._element.append('<div class="ui-formfield-input">' + ((this._field.checkbox && this._field.checkbox == true) ? '<input type="checkbox" value="" class="enabler" />' : '') + '</div></div>');
        
        if(this._field.placeholder && this._field.note) {
            this._element.append('<div class="ui-formfield-note">' + this._field.placeholder + '</div>');
        }
        
        this.RenderControl(this._element.find('.ui-formfield-input'));

        this._element.find('input,textarea,select').change(function() {
            self._changed = true;
        });
        
        this._element.find('.enabler').click(function() {
            self.enable(this.checked);
        });
        
        if( (this._field.checkbox && this._field.checkbox == true) )
            this.enable(false);
        
        this.raiseEvent('ready', []);
        
        return this;
        
    },
    
    enable: function(val) {
        if(val === undefined)
            return !this._element.hasClass('ui-disabled');
        else {
            if(!val) { 
                this._element.addClass('ui-disabled').attr('disabled', 'disabled');
                this._element.find('.enabler').removeAttr('checked');
                this._element.find('input:not(.enabler),select,textarea').attr('disabled', 'disabled');
            }
            else { 
                this._element.removeClass('ui-disabled').removeAttr('disabled');
                this._element.find('.enabler').attr('checked', 'cheched');
                this._element.find('input:not(.enabler),select,textarea').removeAttr('disabled');
            }
            
            return this;
        }
    },
    
    field: function() {
        return this._field;
    }, 
    
    val: function(val) {
        return this;
    },
    
    validate: function() {
        return true;
    },
    
    changed: function() {
        return this._changed;
    },
    
    readonly: function(val) {
        if(val === undefined) {
            this._element.find('input,select,textarea').prop('readonly');
        }
        else {
            this._element.find('input,select,textarea').prop('readonly', val);
            return this;
        }
    },
    
    Dispose: function() {
        if(this._element.prev().is('br'))
            this._element.prev().remove();
        this.base();
    }

}, {
    
    Create: function(fieldData, container, title) {
        
        if(fieldData.lookup || fieldData.values) {
            return new UI.Controls.Form.Lookup(fieldData.field, container, title, '', fieldData);
        }

        switch(fieldData.type) {                                                                                
            case 'array':
                return new UI.Controls.Form.FieldsArray(fieldData.field, container, fieldData);
            case 'object':
                return new UI.Controls.Form.Fieldset(fieldData.field, container, fieldData);
            case 'text':
                return new UI.Controls.Form.Text(fieldData.field, container, title, '', fieldData);
            case 'href':
                return new UI.Controls.Form.Href(fieldData.field, container, title, '', fieldData);
            case 'json':
                return new UI.Controls.Form.Json(fieldData.field, container, title, '', fieldData);
            case 'numeric':
                return new UI.Controls.Form.Numeric(fieldData.field, container, title, '', fieldData);
            case 'memo':
                return new UI.Controls.Form.Memo(fieldData.field, container, title, '', fieldData);
            case 'date':
                return new UI.Controls.Form.Date(fieldData.field, container, title, '', fieldData);
            case 'image':
                return new UI.Controls.Form.Image(fieldData.field, container, title, '', fieldData);
            case 'images':
                return new UI.Controls.Form.Images(fieldData.field, container, title, '', fieldData);
            case 'label':
                return new UI.Controls.Form.Label(fieldData.field, container, title, '', fieldData);
            case 'file':
                return new UI.Controls.Form.File(fieldData.field, container, title, '', fieldData);
            case 'files':
                return new UI.Controls.Form.Files(fieldData.field, container, title, '', fieldData);
            case 'bool':
                return new UI.Controls.Form.Bool(fieldData.field, container, title, '', fieldData);
            case 'coords':
                return new UI.Controls.Form.Coords(fieldData.field, container, title, '', fieldData);
            case 'fileselect':
                return new UI.Controls.Form.FileSelect(fieldData.field, container, title, '', fieldData);
            case 'choosefile':
                return new UI.Controls.Form.ChooseFile(fieldData.field, container, title, '', fieldData);
        }
        
        return false;        
        
    }
    
});

UI.Controls.Form.Fieldset = UI.Controls.Control.extend({
    
    _field: false,
    _changed: false,
    _title: false,
    
    constructor: function(name, container, field, className) {
        this.base(name, container);
        this._field = field;
        this._title = field.title;
        this._className = className == undefined ? false : className;
    }, 
    
    RenderFields: function() {
        var self = this;
        this._field.fields.forEach(function(field) {
            self.controls(field.field, UI.Controls.Form.FormField.Create(field, self, field.title).Render());
        })
    },
    
    Render: function() {
        
        var self = this;
        
        this.base('fieldset', 'ui-fieldset ' + this._className + (this._field.className != undefined ? ' ' + this._field.className : ''));
        this._element.append('<legend>' + this._title + '</legend>');
        if(this._field.newline != undefined && (this._field.newline == 1 || this._field.newline == 'true')) {
            this._element.before('<br />');
        }        
        
        this.RenderFields();
        
        this.raiseEvent('ready', []);
        
        return this;
        
    },
    
    enable: function(val) {
        if(val === undefined)
            return !this._element.hasClass('ui-disabled');
        else {
            if(!val) { 
                this._element.addClass('ui-disabled').attr('disabled', 'disabled');
                this._element.find('.enabler').removeAttr('checked');
            }
            else {
                this._element.removeClass('ui-disabled').removeAttr('disabled');
                this._element.find('.enabler').attr('checked', 'cheched');
            }
                
            var self = this;
            $.map(this.controls(), function(control, i) {
                control.enable(self.enable());
            });
            
            return this;
        }
    },
    
    focus: function() {
        for(k in this._controls) break;
        this._controls[k].focus();
    },
    
    validate: function() {
        var validated = true;
        $.map(this._controls, function(o, i) {
            if(!o.validate())
                validated = false;
        });
        
        return validated;
    },
    
    
    field: function() {
        return this._field;
    },     

    val: function(value) {
        
        if(value === undefined) {
            
            var self = this;
            var val = {};
            Object.forEach(self.controls(), function(ii, v) {
                if(v.field)
                    val[v.field().field] = v.val();
            });
            return val;
            
        }
        else {                                  
            
            var self = this;                    
            if(!value) {
                Object.keys(value).forEach(function(k) {
                    self.controls(k).val('');
                });
            }
            else {
                if(!(value instanceof Object))
                    value = JSON.parse(value);
                Object.keys(value).forEach(function(k) {
                    self.controls(k).val(value[k]);
                });
            }
        }
        
    },
    
    changed: function() {
        return this._changed;
    },
    
}, {});

UI.Controls.Form.FieldsArray = UI.Controls.Control.extend({
    
    _field: false,
    
    _items: false, 
    
    _emptyMessage: false,
    
    _changed: false,
    
    constructor: function(name, container, field, className) {
        this.base(name, container);
        this._field = field;
        this._className = className == undefined ? false : className;
        this._items = [];
    }, 
    
    addEmptyMessage: function() {        
        this._emptyMessage = new UI.Controls.Pane('empty', this.container()).Render();
        this._emptyMessage.html('<p>Данные не заполнены</p>');
    },
    
    removeEmptyMessage: function() {
        if(!this._emptyMessage)
            return false;
        this._emptyMessage._element.remove();
        this._emptyMessage = false;
    },
    
    addItem: function(value) {
        var self = this;
        
        var lastIndex = this._items.length;
        
        var item = new UI.Controls.Pane('row' + lastIndex, self.container()).Render();
        self._items.push(item);
        
        var del = (new UI.Controls.Button('delete', item, '', false, 'delete'))
            .addHandler('click', function(event, args) {
                self.removeItem(this._element.parent().index());
                if(self._items.length == 0)
                    self.addEmptyMessage();
            })
            .Render()
            .iconClass('icon-delete');
        
        if(this._field.readonly) {
            del.Hide();
        }
        
        $.map(this._field.fields, function(v, i) {
            
            var control = item.controls(v.field, UI.Controls.Form.FormField.Create(v, item.container(), v.title));
            if(control) {
                control.Render();
            }

        });
                
        if(value != undefined) {
            item.tag(value);
            $.map(value, function(o, key) { 
                item.controls(key) ? item.controls(key).val(o) : false;    
            });
        }
        
        self.raiseEvent('itemAdded', {item: item, data: value});
        
        self._changed = true;
        
    },
    
    removeItem: function(index) {
        var item = this._items[index];
        item.container().html('');
        item._element.remove();
        this._items.splice(index, 1);
        
        self._changed = true;
        return true;
    },
    
    Render: function() {
        
        var self = this;
        
        this.base('fieldset', 'ui-fieldset ui-array ' + this._className + (this._field.className != undefined ? ' ' + this._field.className : ''));
        this._element.append('<legend>' + this._field.title + ((this._field.checkbox && this._field.checkbox == true) ? '<input type="checkbox" class="enabler" tabIndex="' + (UI.tabIndex++) + ' " />' : '') + '</legend><div class="items"></div>' + 
            (!this._field.readonly ? '<a href="#" id="add" tabIndex="' + (UI.tabIndex++) + '">' + (this._field.addtext ? this._field.addtext : 'Добавить') + '</a>' : ''));
        if(this._field.newline != undefined && (this._field.newline == 1 || this._field.newline == 'true')) {
            this._element.before('<br />');
        }        
        this.addEmptyMessage();
        this._element.find('#add').click(function() {
            if(!self.enable())
                return false;
                
            self.removeEmptyMessage();
            self.addItem();
        })
        
        this.raiseEvent('ready', []);
        
        this._element.find('.enabler').click(function() {
            self.enable(this.checked);
        });
        
        if( (this._field.checkbox && this._field.checkbox == true) )
            this.enable(false);
        
        return this;
        
    },
    
    enable: function(val) {
        if(val === undefined)
            return !this._element.hasClass('ui-disabled');
        else {
            
            if(!val) { 
                this._element.addClass('ui-disabled').attr('disabled', 'disabled');
                this._element.find('.enabler').removeAttr('checked');
            }
            else {
                this._element.removeClass('ui-disabled').removeAttr('disabled');
                this._element.find('.enabler').attr('checked', 'cheched');
            }
            
            var self = this;
            $(this._items).each(function(i, o) {
                $.map(o.controls(), function(control, i) {
                    control.enable(self.enable());    
                });
            });
            
            return this;
        }
    },
    
    container: function() {
        return this._element.find('>div.items');
    },
    
    field: function() {
        return this._field;
    },
        
    focus: function() {
        if(this._items.length > 0) {
            out(this._items[0]);
            this._items[0].controls('firstChild').focus();
        }
    },
    
    validate: function() {
        var validated = true;
        $(this._items).each(function(i, o) {
            $.map(o.controls(), function(v, ii) {
                if(!v.validate())
                    validated = false;
            });
        });
        
        return validated;
    },      

    val: function(value) {
        
        if(value === undefined) {
            
            var self = this;
            var val = [];
            $(this._items).each(function(i, o) {
                var vv = o.tag() ? o.tag() : {};
                Object.forEach(o.controls(), function(ii, v) {
                    if(v.field)
                        vv[v.field().field] = v.val();
                });
                val.push(vv);
            });
            return val;
            
        }
        else {                                  
            
            var self = this;                    
            self.removeEmptyMessage();
            for(var i=0; i<this._items.length; i++) {
                self.removeItem(i);
            }
            this._items = [];
            self.container().html('');
        
            if(value) {
                if(!(value instanceof Array)) {
                    value = JSON.parse(value);    
                }
                if(value instanceof Array) {
                    $(value).each(function(i, v) {
                        self.addItem(v);
                    });
                }    
                if(self._items.length == 0)
                    self.addEmptyMessage();
            }
        }
        
    },
    
    changed: function() {
        return this._changed;
    },
    
}, {});
                                                                    
UI.Controls.Form.Bool = UI.Controls.Form.FormField.extend({
    
    _controlType: 'radio',
    
    RenderControl: function(controlContainer) {
        
        var dt = (new Date()).getTime();
        
        var self = this;
        
        var def = self.field().def;
        
        if(!self._field.values) {
            self._field.values = {'true': 'Да', 'false': 'Нет'};
        }
        
        if(this._controlType != 'checkbox') {
            if(self.field().values != undefined) {
                $.map(self.field().values, function(value, i) {
                    controlContainer.append('<div class="ui-formfield-bool"><input type="radio"' + (def == i ? ' checked="checked"' : '') + ' id="' + self.field().field + '_' + (dt + i) + '" name="' + self.field().field + '_' + dt + '" ' + (self.field().readonly ? ' disabled="disabled"' : '') + ' value="' + i + '" tabIndex="' + (UI.tabIndex++) + '"/><label for="' + self.field().field + '_' + (dt + i) + '">' + value + '</label></div>');        
                });
            }
            else {
                controlContainer.append('<div class="ui-formfield-bool"><input type="radio"' + (def === 'true' || def === true ? ' checked="checked"' : '') +   ' name="' + self.field().field + '_' + (dt + 1) + '" id="' + self.field().field + '_' + (dt + 1) + '"' + (self.field().readonly ? ' disabled="disabled"' : '') + ' value="true" tabIndex="' + (UI.tabIndex++) + '"  /><label for="' + self.field().field + '_' + (dt + 1) + '">Да</label></div>');        
                controlContainer.append('<div class="ui-formfield-bool"><input type="radio"' + (def === 'false' || def === false ? ' checked="checked"' : '') + ' name="' + self.field().field + '_' + (dt + 2) + '" id="' + self.field().field + '_' + (dt + 2) + '"' + (self.field().readonly ? ' disabled="disabled"' : '') + ' value="false" tabIndex="' + (UI.tabIndex++) + '" /><label for="' + self.field().field + '_' + (dt + 2) + '">Нет</label></div>');
            }
        }
        else {
            controlContainer.append('<div class="ui-formfield-bool"><input type="checkbox"' + (def === 'true' || def === true ? ' checked="checked"' : '') + ' id="' + self.field().field + '_' + dt + '" name="' + self.field().field + '_' + dt + '" ' + (self.field().readonly ? ' disabled="disabled"' : '') + ' value="true" tabIndex="' + (UI.tabIndex++) + '" /><label for="' + self.field().field + '_' + dt + '">' + self.field().values['true'] + '</label></div>');        
        }
                
        controlContainer.find('input').change(function(e) { self.validate(); return self.raiseEvent('change', {domEvent: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {domEvent: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {domEvent: e}); }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}); });
        
        controlContainer.find('input:not(.enabler)').click(function(e) { e.stopPropagation(); e.stopImmediatePropagation(); return true; });
        controlContainer.find('input:not(.enabler)').change(function() { self.raiseEvent('change'); });
        
    },                                                  
    
    type: function(value) {
        if(value == undefined)
            return this._controlType;
        else
            this._controlType = value;
        return this;
    },
    
    focus: function() {                       
        this._element.find('input[type=' + this._controlType + ']:eq(0)').focus();
    },
    
    validate: function() {
        var ret = true;
        
        // проверяем на наличие проблем
        if(this.field().expression != undefined && !eval(this.field().expression)) {
            ret = false;
        }
        
        if(this.field().required == true && this.val() === undefined) {
            ret = false;
        }
        
        if(!ret) {
            this._element.find('input[type=' + this._controlType + ']').addClass('invalid');
        }
        else {
            this._element.find('input[type=' + this._controlType + ']').removeClass('invalid');
        }
        
        return ret;
    },    
    
    val: function(val) {
        if(this._controlType != 'checkbox') {
            if(val === undefined) 
                return this._element.find('.ui-formfield-bool input[type=radio]:checked').val();
            else {
                var self = this;
                self._element.find('.ui-formfield-bool input').removeAttr('checked');
                self._element.find('.ui-formfield-bool input').each(function(i, o) { $(o)[0].checked = false; });
                if(val !== null) {
                    var index = -1;
                    /*if(self.field().values) {
                        var ii = 0;
                        $.map(self.field().values, function(o, i) {
                            if(val == i)
                                index = ii;
                            ii++;
                        });
                    }
                    else*/
                        index = val === 'true' || val === true ? 0 : 1;
                        
                    this._element.find('.ui-formfield-bool input:eq(' + index + ')').attr('checked', 'checked').get(0).checked = true;
                    
                }
                
                this._changed = false;
                return this;
            }
        }
        else {
            if(val == undefined) {
                return this._element.find('.ui-formfield-bool input[type=checkbox]').is(':checked') ? 1 : 0;
            }
            else {
                this._element.find('.ui-formfield-bool input[type=checkbox]').prop('checked', !val || val == '0' ? false : true);
            }
        }
    },
    
}, {});       

UI.Controls.Form.Label = UI.Controls.Form.FormField.extend({
    
    RenderControl: function(controlContainer) {
        
        var self = this;
        
        controlContainer.append('<div class="ui-formfield-label"><div class="ui-formfield-label-content" name="' + self.field().field + '" id="' + self.field().field + '" style="width: ' + self.field().width + ';">' + (self.field().def != undefined ? self.field().def : '') + '</div></div>');
        controlContainer.find('input').change(function() { self.validate(); self.raiseEvent('change'); });
        
    },
    
    focus: function() {
        return false;
    },  
    
    validate: function() {
        return true;
    },
    
    val: function(val) {
        if(val === undefined) 
            return this._element.find('.ui-formfield-label .ui-formfield-label-content').html();
        else {
            this._element.find('.ui-formfield-label .ui-formfield-label-content').html(val);
            return this;
        }
    },
    
}, {});

UI.Controls.Form.Json = UI.Controls.Form.FormField.extend({
    
    _value: false,
    
    RenderControl: function(controlContainer) {
        
        var self = this;
        
        controlContainer.append('<div class="ui-formfield-json"><div class="ui-formfield-json-content" name="' + self.field().field + '" id="' + self.field().field + '" style="width: ' + self.field().width + ';height: ' + self.field().height + ';"></div></div>');
        
        var def = self.field().def;
        var el = controlContainer.find('.ui-formfield-json-content');
        if(def) {
            
            def = $.parseJSON(def);
            if(def instanceof Array) {
                $(def).each(function(i, o) {
                    
                    if(o instanceof Object) {
                        el.append('<div class="ui-formfield-json-item"><div>');
                            $(o.keys()).each(function(j, key) {
                                el.append('<div><strong>' + key + '</strong></div>');
                                el.append('<div>' + o[key] + '</div>');
                            });
                        el.append('</div></div>');
                    }
                    else {
                        el.append('<div class="ui-formfield-json-item"><div><div>' + o + '</div></div></div>');

                    }   
                    
                });
            }
            
        }        
    },
    
    focus: function() {
        return false;
    },  
    
    val: function(val) {
        if(val === undefined) 
            return this.value;
        else {
            var self = this;
            this.value = $.parseJSON(val);
            
            var el = this._element.find('.ui-formfield-json-content');
            el.html('');
            
            if(this.value instanceof Array) {
                $(this.value).each(function(i, o) {           
                    
                    var html = '';
                    if(o instanceof Object) {
                        html += ('<div class="ui-formfield-json-item">');
                            if(self.field().jsonFields) {             
                                for(var key in self.field().jsonFields) {
                                    html += ('<div><div><strong>' + self.field().jsonFields[key].title + '</strong></div>');
                                    var v = o[key];
                                    if(self.field().jsonFields[key].view)
                                        v = eval(self.field().jsonFields[key].view)
                                    html += ('<div>' + v + '</div></div>');
                                }
                            }
                            else {                                             
                                for(var key in o) {
                                    html += ('<div><div><strong>' + key + '</strong></div>');
                                    html += ('<div>' + o[key] + '</div></div>');
                                }
                            }
                        html += ('</div>');
                        el.append(html);
                    }
                    else {
                        el.append('<div class="ui-formfield-json-item"><div><div>' + o + '</div></div></div>');

                    }   
                    
                });
            }
            else if(this.value instanceof Object) {
                var o = this.value;
                var html = '';
                if(o instanceof Object) {
                    html += ('<div class="ui-formfield-json-item">');
                        if(self.field().jsonFields) {             
                            for(var key in self.field().jsonFields) {
                                if(!o[key]) continue;
                                
                                html += ('<div><div><strong>' + self.field().jsonFields[key].title + '</strong></div>');
                                var v = o[key];
                                if(self.field().jsonFields[key].view){ 
                                    v = eval(self.field().jsonFields[key].view)
                                }
                                html += ('<div>' + v + '</div></div>');
                            }
                        }
                        else {             
                            
                            if(o instanceof Array) {
                                $(o).each(function(i, oo) {

                                    for(var key in oo) {
                                        html += ('<div><div><strong>' + key + '</strong></div>');
                                        html += ('<div>' + o[key] + '</div></div>');
                                    }
                                    html += '<div><hr /></div>';
                                    
                                });
                            }
                            else {
                                for(var key in o) {
                                    html += ('<div><div><strong>' + key + '</strong></div>');
                                    html += ('<div>' + o[key] + '</div></div>');
                                }
                            }
                        }
                    html += ('</div></div>');
                    el.append(html);
                }
                else {
                    el.append('<div class="ui-formfield-json-item"><div><div>' + o + '</div></div></div>');

                }   
                
            }
            
            return this;
        }
    },
    
}, {});

UI.Controls.Form.Text = UI.Controls.Form.FormField.extend({
    
    RenderControl: function(controlContainer) {
        
        var self = this;
        
        controlContainer.append('<div class="ui-formfield-text"><input type="' + (self.field().inputType ? self.field().inputType : 'text') + '" ' + (self.field().readonly ? ' disabled="disabled"' : '') + ' tabIndex="' + (UI.tabIndex++) + '" value="' + (self.field().def != undefined ? self.field().def : '') + '" name="' + self.field().field + '" id="' + self.field().field + '" style="width: ' + self.field().width + ';" ' + (!self.field().note && self.field().placeholder ? ' placeholder="' + self.field().placeholder + '"' : '') + (self.field().mask ? ' data-mask="' + self.field().mask +'"' : '') + ' /></div>');
        controlContainer.find('input').change(function(e) { self.validate(); return self.raiseEvent('change', {domEvent: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {domEvent: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {domEvent: e}); }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}); });
        
        /*if(this.field().mask) {
            controlContainer.find('input').mask(this.field().mask);
        }*/
        
    },
    
    focus: function() {
        this._element.find('input[type=text]').focus();
        if(!this.validate())
            this._element.find('input[type=text]').addClass('invalid');
        else
            this._element.find('input[type=text]').removeClass('invalid');
    },  
    
    validate: function() {
        var ret = true;
        // проверяем на наличие проблем
        if(this.field().expression != undefined && !eval(this.field().expression)) {
            ret = false;
        }
        
        if(this.field().required == true && this.val() == '') {
            ret = false;
        }
        
        if(this.field().size != undefined && this.val().length > this.field().size) {
            ret = false;
        }         
        
        if(!ret) {
            this._element.find('input[type=text]').addClass('invalid');
        }
        else {
            this._element.find('input[type=text]').removeClass('invalid');
        }
        
        return ret;       
    
    },
    
    val: function(val) {
        if(val === undefined) 
            return this._element.find('.ui-formfield-text input[type=' + (this.field().inputType ? this.field().inputType : 'text') + ']').val();
        else {
            this._element.find('.ui-formfield-text input[type=' + (this.field().inputType ? this.field().inputType : 'text') + ']').val(val ? val : '');
            this._changed = false;
            return this;
        }
    },
    
}, {});

UI.Controls.Form.Date = UI.Controls.Form.FormField.extend({
    
    RenderControl: function(controlContainer) {
        
        var self = this;
        controlContainer.append('<div class="ui-formfield-date" style="width: ' + self.field().width + ';">' + 
            '<input type="hidden" name="' + self.field().field + '" value="' + (self.field().def != undefined && self.field().def != '0000-00-00 00:00:00' ? self.field().def.toDate().toDbDate() : Date.Now().toDbDate()) + '" />' + 
            '<input type="date" ' + (self.field().readonly ? ' disabled="disabled"' : '') + ' tabIndex="' + (UI.tabIndex++) + '" value="' + (self.field().def != undefined && self.field().def != '0000-00-00 00:00:00' ? self.field().def.toDate().toShortDateString() : Date.Now().toShortDateString()) + '" name="' + self.field().field + '_date" id="' + self.field().field + '_date" />&nbsp;' + 
            (self.field().hasTime ? '<input type="time" ' + (self.field().readonly ? ' disabled="disabled"' : '') + ' tabIndex="' + (UI.tabIndex++) + '" value="' + (self.field().def != undefined && self.field().def != '0000-00-00 00:00:00' ? self.field().def.toDate().toTimeString() : Date.Now().toTimeString()) + '" name="' + self.field().field + '_time" id="' + self.field().field + '_time" />' : '') + 
        '</div>');
        controlContainer.find('input').change(function(e) { self.validate(); return self.raiseEvent('change', {domEvent: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {domEvent: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {domEvent: e}); }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}); });        
    },
    
    focus: function() {
        this._element.find('input[type=text]').focus();
        if(!this.validate())
            this._element.find('input[type=text]').addClass('invalid');
        else
            this._element.find('input[type=text]').removeClass('invalid');
    },  
    
    validate: function() {
        var ret = true;
        // проверяем на наличие проблем
        if(this.field().expression != undefined && !eval(this.field().expression)) {
            ret = false;
        }
        
        if(this.field().required == true && this.val() == '') {
            ret = false;
        }
        
        if(this.field().size != undefined && this.val() && this.val().length > this.field().size) {
            ret = false;
        }         
        
        if(!ret) {
            this._element.find('input[type=text]').addClass('invalid');
        }
        else {
            this._element.find('input[type=text]').removeClass('invalid');
        }
        
        return ret;       
    
    },
    
    val: function(val) {
        if(val === undefined) { 
            if(this._element.find('.ui-formfield-date input[type=hidden]').val() == '')
                return null;
            return this._element.find('.ui-formfield-date input[type=hidden]').val();
        }
        else {
            if(val && val != '0000-00-00 00:00:00') {
                this._element.find('.ui-formfield-date input[type=hidden]').val( val.toDate().toDbDate() );
                this._element.find('.ui-formfield-date input[type=date]').val(val.toDate().toShortDateString());
                this._element.find('.ui-formfield-date input[type=time]').val(val.toDate().toTimeString());
            }
            else {
                
                var val = (this.field().def != undefined && this.field().def != '0000-00-00 00:00:00' ? this.field().def.toDate() : Date.Now());

                this._element.find('.ui-formfield-date input[type=hidden]').val(val.toDbDate());
                this._element.find('.ui-formfield-date input[type=date]').val(val.toShortDateString());
                this._element.find('.ui-formfield-date input[type=time]').val(val.toTimeString());
            }
            this._changed = false;
            return this;
        }
    },
    
}, {});

UI.Controls.Form.Files = UI.Controls.Form.FormField.extend({
    
    _uploadFolder: false,
    
    RenderControl: function(controlContainer) {
                        
        var self = this;
        
        controlContainer.append('<div class="ui-formfield-images">' + 
            '<input type="file" multiple style="display: none;" value="" id="chooser" />' + 
            '<div class="options">' + 
                '<a href="#" class="choose" title="Выбрать изображение" tabIndex="' + (UI.tabIndex++) + '"></a>' + 
                '<a href="#" class="choosefromserver" title="Выбрать из библиотеки" tabIndex="' + (UI.tabIndex++) + '"></a>' +
                '<a href="#" class="delete" title="Очистить список" tabIndex="' + (UI.tabIndex++) + '"></a>' + 
            '</div>' + 
            '<div class="ui-formfield-images-container"></div>' +
        '</div>');
        
        controlContainer.on('click', '.ui-formfield-images .ui-formfield-images-container a.delete', function() {
            var ret = window.confirm('Вы уверены, что xотите удалить выбор файла. Файл на сервере не будет удален.');
            if(ret) {
                var parent = $(this).closest('div');
                parent.remove();
            }
            return false;
        });
        controlContainer.on('click', '.ui-formfield-images .options a.delete', function() {
            var ret = window.confirm('Вы уверены, что xотите удалить очистить список. Файлы на сервере не будут удалены.');
            if(ret) {
                var parent = $(this).closest('.ui-formfield-images').find('.ui-formfield-images-container');
                parent.html('');
            }
            return false;
        });
        
        controlContainer.on('change', '.ui-formfield-images .ui-formfield-images-container input[type=text]', function() {
            
            var file = $(this).val();
            var self = this;
            var exts = file.split('.');
            var ext = exts[exts.length - 1];
            if(MimeType.isImage(ext)) { 
                $(this).closest('div').find('.img-view').removeAttr('rel').css('background-image', 'url(' + file + ')'); 
            }
            else {
                $(this).closest('div').find('.img-view').removeAttr('style').attr('rel', MimeType.icon(ext)); 
            }
            
            self.raiseEvent('change');
            
            return false;
        })
        .on('dblclick', '.ui-formfield-images .ui-formfield-images-container input[type=text]', function(e) { 
            return self.raiseEvent('dblclick', {domEvent: e}); 
        })
        .on('keydown', '.ui-formfield-images .ui-formfield-images-container input[type=text]', function(e) { 
            return self.raiseEvent('keydown', {domEvent: e}); 
        })
        .on('blur', '.ui-formfield-images .ui-formfield-images-container input[type=text]', function(e) { 
            self.raiseEvent('blur', {domEvent: e}) 
        })
        .on('focus', '.ui-formfield-images .ui-formfield-images-container input[type=text]', function(e) { 
            self.raiseEvent('focus', {domEvent: e}); 
        });

        
        var chooseHandler = controlContainer.find('.ui-formfield-images a.choose');
        chooseHandler.click(function() {
            window.app.GetFolders('', function(folders) {

                var position = chooseHandler.offset();
                position.top += chooseHandler.height();   

                var cm = new UI.Controls.ContextMenu('filecm', $(document.body));
                var menuitems = [];
                folders.forEach(function(folder) {
                    menuitems.push({key: folder.path.replaceAll('./res/', ''), text: folder.name, 'keyboard': false, icon: 'res/img/icons/folder.png', folder: folder});                    
                });
                
                cm.addHandler('menuItemOver', function(sender, args) {
                    if(args.itemData.folder.children > 0) {
                        window.app.GetFolders(args.itemData.folder.path.replaceAll('./res/', ''), function(folders) {  
                            var childs = [];
                            folders.forEach(function(folder) {
                                childs.push({key: folder.path, text: folder.name, 'keyboard': false, icon: 'res/img/icons/folder.png', folder: folder});                    
                            });
                            cm.removeChilds(args.itemData.folder.path.replaceAll('./res/', ''));
                            cm.addItems(childs, args.itemData.folder.path.replaceAll('./res/', ''));
                        });
                        
                    }
                }).addHandler('menuItemClicked', function(sender, args) {
                    cm.Hide().Dispose();
                    
                    self._uploadFolder = args.itemData.folder.path.replaceAll('./res/', '');
                    controlContainer.find('#chooser').click();
                    
                }).Render().width('250px').addItems(menuitems).Show(position);
                   
            });
            return false;
        })
        
        var chooseFromServerHandler = controlContainer.find('.ui-formfield-images a.choosefromserver');
        chooseFromServerHandler.click(function() {
            window.app.FileManager.Show({
                chooseCallback: function(rows) {
                    var val = [];
                    rows.forEach(function(file) {
                        val.push(file.path);
                    });
                    self.val(val.join(';'));
                }
            });
            return false;
        });
        
        controlContainer.on('click', '.ui-formfield-images a.download', function() {
            window.open($(this).closest('div').find('input[type=text]').val());
            return false;
        });
        
        controlContainer.find('#chooser').change(function(e) {
            var files = e.target.files;
            
            $(files).each(function(i, file) {
                self._uploadFile(file);
            });
            self._element.find('.ui-formfield-image input#chooser').val('');       
            
            e.preventDefault();
            return false;
        });
        
        this._element.find('.ui-formfield-images-container').sortable({
            handle: '.img-view',
            stop: function(event, ui) {
                var val = [];
                var items = self._element.find('.ui-images-item');
                $(items).each(function(i, el) {
                    val.push($(el).find('input[type=text]').val());
                });
            }
        });
        this._element.find('.ui-formfield-images-container').disableSelection();
        
    },
    
    _addItem: function(file) {
        var self = this;
        var exts = file.split('.');
        var ext = exts[exts.length - 1];
        var item = $('<div class="ui-images-item"><table><tr><td>' + 
                (MimeType.isImage(ext) ? 
                '<div class="img-view" style="' + (file == 'new' ? '' : 'background-image: url(' + file + ');') + '"></div>' 
                :
                '<div class="img-view" rel="' + MimeType.icon(ext) + '"></div>' 
                ) + 
            '</td><td>' + 
            '<table>' +
                '<tr><td><input type="text" value="' + file + '" name="' + self.field().field + '[]" tabIndex="' + (UI.tabIndex++) + '" /></td></tr>' + 
                '<tr><td>' + 
                    '<a href="#" class="delete" title="Удалить" tabIndex="' + (UI.tabIndex++) + '"></a>' + 
                    '<a href="#" class="choosefromserver" title="Выбрать из библиотеки" tabIndex="' + (UI.tabIndex++) + '"></a>' + 
                    '<a href="#" class="download" title="Скачать изображение" tabIndex="' + (UI.tabIndex++) + '"></a>' + 
                '</td></tr>' +
            '</table>' + 
        '</td></tr></table></div>').appendTo(self._element.find('.ui-formfield-images-container'));
        return item;
    },
    
    _uploadFile: function(file) {

        var self = this;
        
        var item = self._addItem('');
        
        item.find('input[type=text]').attr('placeholder', 'Подготовка к загрузке...');
        
        window.app._requestFileUpload('FilesAjaxHandler.UploadFiles', file, {filename: file.name, path: this._uploadFolder}, function(e, data) {
            var percent = 0;
            var position = event.loaded || event.position;
            var total = event.total;
            if (event.lengthComputable) {
                percent = Math.ceil(position / total * 100);
            }
            
            item.find('input[type=text]').attr('placeholder', file.name + ' Загрузка (' + percent + '%)');
            
        }, function(data) {
            if(data.error) {
                item.find('input[type=text]').attr('placeholder', file.name + ' Ошибка загрузки (' + data.message + ')');
                setTimeout(function() {
                    item.remove();
                }, 3000);
            }
            else {
                item.find('input[type=text]').val(data.file.path.replaceAll('./', '/')).change();
                item.find('input[type=text]').removeAttr('placeholder');
            }
        });
        
        
    },
    
    focus: function() {
        this._element.find('input[type=text]').focus();
    },
    
    val: function(val) {
        if(val === undefined) {
            var ret = [];
            this._element.find('.ui-formfield-images input[type=text]').each(function(i, o) {
                ret.push($(o).val());
            })
            return ret.join(';');
        }
        else {
            if(val == undefined || val == null || val === '') {
                this._element.find('.ui-formfield-images input[type=hidden]').val('');
                this._element.find('.ui-formfield-images input#chooser').val('');
                this._element.find('.ui-formfield-images-container').html('');
            }
            else {
                var self = this;
                
                this._element.find('.ui-formfield-images input[type=hidden]').val(val);
                this._element.find('.ui-formfield-images input#chooser').val('');
                this._element.find('.ui-formfield-images-container').html('');
                if(val != '') {
                    var vals = val.split(';');
                    $(vals).each(function(i, val) {
                        self._addItem(val);
                    });
                }
            }   
            this._changed = false;
            return this;
        }
    },
    
}, {});

UI.Controls.Form.File = UI.Controls.Form.FormField.extend({
    
    _uploadFolder: false,
    
    RenderControl: function(controlContainer) {
        
        var self = this;
        
        controlContainer.append('<div class="ui-formfield-image"><table><tr><td><div class="img-view"></div></td><td>' + 
            '<table>' +
                '<tr><td><input ' + (self.field().readonly ? ' disabled="disabled"' : '') + ' type="text" value="' + (self.field().def != undefined ? self.field().def : '') + '" name="' + self.field().field + '" style="' + (self.field().width ? 'width:' + self.field().width : '') + '" tabIndex="' + (UI.tabIndex++) + '" /></td></tr>' + 
                '<tr><td>' + 
                    '<a href="#" class="delete" title="Удалить" tabIndex="' + (UI.tabIndex++) + '"></a>' + 
                    '<a href="#" class="choosefromserver" title="Выбрать из библиотеки" tabIndex="' + (UI.tabIndex++) + '"></a>' + 
                    '<a href="#" class="choose" title="Загрузить изображение" tabIndex="' + (UI.tabIndex++) + '"></a>' + 
                    '<a href="#" class="download" title="Скачать изображение" tabIndex="' + (UI.tabIndex++) + '"></a>' + 
                    '<input type="file" style="display: none;" value="" id="chooser" tabIndex="' + (UI.tabIndex++) + '" />' +
                '</td></tr>' +
            '</table>' + 
        '</td></tr></table></div>');
        controlContainer.find('input').change(function(e) { self.validate(); return self.raiseEvent('change', {domEvent: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {domEvent: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {domEvent: e}); }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}); });
                                       
        var removeHandler = controlContainer.find('.ui-formfield-image a.delete');
        removeHandler.click(function() {
            var ret = window.confirm('Вы уверены, что xотите удалить выбор файла. Файл на сервере не будет удален.');
            if(ret)
                self.val('');
            return false;
        })
        
        var chooseHandler = controlContainer.find('.ui-formfield-image a.choose');
        chooseHandler.click(function() {
            
            
            window.app.GetFolders('', function(folders) {

                var position = chooseHandler.offset();
                position.top += chooseHandler.height();   

                var cm = new UI.Controls.ContextMenu('filecm', $(document.body));
                var menuitems = [];
                folders.forEach(function(folder) {
                    menuitems.push({key: folder.path.replaceAll('./res/', ''), text: folder.name, 'keyboard': false, icon: 'res/img/icons/folder.png', folder: folder});                    
                });
                
                cm.addHandler('menuItemOver', function(sender, args) {
                    if(args.itemData.folder.children > 0) {
                        window.app.GetFolders(args.itemData.folder.path.replaceAll('./res/', ''), function(folders) {  
                            var childs = [];
                            folders.forEach(function(folder) {
                                childs.push({key: folder.path, text: folder.name, 'keyboard': false, icon: 'res/img/icons/folder.png', folder: folder});                    
                            });
                            cm.removeChilds(args.itemData.folder.path.replaceAll('./res/', ''));
                            cm.addItems(childs, args.itemData.folder.path.replaceAll('./res/', ''));
                        });
                        
                    }
                }).addHandler('menuItemClicked', function(sender, args) {
                    cm.Hide().Dispose();
                    
                    self._uploadFolder = args.itemData.folder.path.replaceAll('./res/', '');
                    controlContainer.find('#chooser').click();
                    
                }).Render().width('250px').addItems(menuitems).Show(position);
                   
            });
            
        })
        
        var chooseFromServerHandler = controlContainer.find('.ui-formfield-image a.choosefromserver');
        chooseFromServerHandler.click(function() {
            window.app.FileManager.Show({
                chooseCallback: function(rows) {
                    self.val(rows[0].path);
                }
            });
            return false;
        });
        
        controlContainer.on('change', '.ui-formfield-image input[type=text]', function() {
            
            var file = $(this).val();
            var self = this;
            var exts = file.split('.');
            var ext = exts[exts.length - 1];
            if(MimeType.isImage(ext)) { 
                $(this).closest('div').find('.img-view').removeAttr('rel').css('background-image', 'url(' + file + ')'); 
            }
            else {
                $(this).closest('div').find('.img-view').removeAttr('style').attr('rel', MimeType.icon(ext)); 
            }
            
            return false;
        });
        
        var downloadHandler = controlContainer.find('.ui-formfield-image a.download');
        downloadHandler.click(function() {
            window.open(self.val());
            return false;
        });

        controlContainer.find('#chooser').change(function(e) {
            self._uploadFile(e.target.files[0]);
            e.preventDefault();
            return false;
        });
        
    },
    
    _uploadFile: function(file) {

        var self = this;
        
        self.val('');
        self._element.find('input[type=text]').attr('placeholder', 'Подготовка к загрузке...');
        
        window.app._requestFileUpload('FilesAjaxHandler.UploadFiles', file, {filename: file.name, path: this._uploadFolder}, function(e, data) {
            var percent = 0;
            var position = event.loaded || event.position;
            var total = event.total;
            if (event.lengthComputable) {
                percent = Math.ceil(position / total * 100);
            }
            
            self._element.find('input[type=text]').attr('placeholder', file.name + ' Загрузка (' + percent + '%)');
            
        }, function(data) {
            if(data.error) {
                self.val('');
                self._element.find('input[type=text]').attr('placeholder', file.name + ' Ошибка загрузки (' + data.message + ')');
                setTimeout(function() {
                    self._element.find('input[type=text]').removeAttr('placeholder');
                }, 3000);
            }
            else {
                self.val(data.file.path.replaceAll('./', '/'));
                self._element.find('input[type=text]').removeAttr('placeholder');
            }
        });
        
        
    },
    
    focus: function() {
        this._element.find('input[type=text]').focus();
    },
    
    val: function(val) {
        if(val === undefined) 
            return this._element.find('.ui-formfield-image input[type=text]').val();
        else {
            if(val == undefined || val == null || val === '') {
                this._element.find('.ui-formfield-image .img-view').removeAttr('rel').removeAttr('style');
                this._element.find('.ui-formfield-image input[type=text]').val('');
                this._element.find('.ui-formfield-image input#chooser').val('');
            }
            else {
                if(!val) val = '';
                var exts = val.split('.');
                var ext = 'png';
                if(exts.length < 2)
                    exts.push(ext);
                else 
                    ext = exts[exts.length - 1];
                val = exts.join('.');
                if(MimeType.isImage(ext))
                    this._element.find('.ui-formfield-image .img-view').css('background', 'url(' + val + ') center center no-repeat').css('background-size', 'contain');
                else
                    this._element.find('.ui-formfield-image .img-view').attr('rel', MimeType.icon(ext)).css('background-size', 'contain');
                this._element.find('.ui-formfield-image input[type=text]').val(val);
                try { this._element.find('.ui-formfield-image input#chooser').val(val); } catch(e) {}
            }
            this._changed = false;
            return this;
        }
    },
    
}, {});

UI.Controls.Form.Numeric = UI.Controls.Form.FormField.extend({
    
    RenderControl: function(controlContainer) {
        
        var self = this;
        var size = 20;
        if(self.field().size != undefined) {
            size = (self.field().size + '').split('.');
            size = parseInt(size[0]) + (size.length > 1 ? 1 + parseInt(size[1]) : 0);
            size += size * UI.Controls.Form.charWidth + 2 * UI.Controls.Form.inputPadding;
        }
        
        controlContainer.append('<div class="ui-formfield-numeric' + (self.field().slider ? ' slider' : '') + '" style="' + (self.field().width != undefined && self.field().slider ? ' width: ' + self.field().width : '') + '">' + 
            (self.field().slider ? '<div><div>' : '') + 
            '<input type="text" ' + (self.field().readonly ? ' disabled="disabled"' : '') + ' tabIndex="' + (UI.tabIndex++) + '" value="' + (self.field().def != undefined ? self.field().def : '') + '" name="' + self.field().field + '" id="' + self.field().field + '" style="width: ' + (self.field().width != undefined && !self.field().slider ? self.field().width : size + 'px') + '" ' + (!self.field().note && self.field().placeholder ? ' placeholder="' + self.field().placeholder + '"' : '') + ' />' + (self.field().post == undefined ? '' : '<span>' + self.field().post + '</span>') + 
            (self.field().slider ? '</div><div class="ui-formfield-slider" data-min="' + self.field().min + '" data-max="' + self.field().max + '"><span><em></em></span></div></div>' : '') + 
        '</div>');
        controlContainer.find('input').change(function(e) { self.validate(); return self.raiseEvent('change', {domEvent: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {domEvent: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {domEvent: e}); }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}); });
        
        if(self.field().slider) {
            self.__initSlider();
        }
        
    },
    
    __initSlider: function() {
        var self = this;
        this._element.find('.ui-formfield-slider span em').draggable({ 
            axis: "x", 
            containment: "parent", 
            start: function() {
                self.__dragging = true;
            },
            drag: function(ui, helper) {
                var x = helper.position.left;
                var w = self._element.find('.ui-formfield-slider span').width() - 10;
                y = (x * 100 / w) * (self.field().max - self.field().min) / 100;
                self.val(self.field().min + y);
            },
            end: function() {
                self.__dragging = false;
            }
        });
        
    },
    
    validate: function() {
        var ret = true;
        
        // проверяем на наличие проблем
        if(this.field().expression != undefined && !eval(this.field().expression)) {
            ret = false;
        }
        
        if((this.field().required == true || (this.val() !== '' && this.val() !== null)) && (isNaN(parseFloat(this.val())) || isNaN(parseInt(this.val())))) {
            ret = false;
        }
        
        if(this.field().required == true && (this.val() === '' || this.val() === null)) {
            ret = false;
        }
        
        // size = 000.00
        if((this.val() != '' && this.val() != null) && this.field().size != undefined) {
            
            var size = (this.field().size + '').split('.');
            if(size.length > 1) {
                // десятичные
                var val = this.val() + '';
                val = val.split('.');
                if(val.length > 1) {
                    // проверяем и целую и десятичную часть на длину
                    if(val[0].length > size[0] || val[1].length > size[1]) {
                        ret = false;
                    }
                }
                else {
                    if(val[0].length > size[0]) {
                        ret = false;
                    }
                }
            }
            else {
                // девятичные
                var val = this.val() + ''
                val = val.split('.');
                if(val.length > 1) {
                    ret = false;
                }
                else {
                    if(val[0].length > size[0]) {
                        ret = false;
                    }
                }
            }
            
        }      
        
        if(!ret) {
            this._element.find('input[type=text]').addClass('invalid');
        }
        else {
            this._element.find('input[type=text]').removeClass('invalid');
        }
        

        return ret;          
        
    },
    
    focus: function() {
        this._element.find('input[type=text]').focus();
        if(!this.validate())
            this._element.find('input[type=text]').addClass('invalid');
        else
            this._element.find('input[type=text]').removeClass('invalid');
    },
    
    val: function(val) {
        if(val === undefined) {
            if(this._element.find('.ui-formfield-numeric input[type=text]').val() == '')
                return null;
            return (this._element.find('.ui-formfield-numeric input[type=text]').val() + '').replaceAll(' ', '').replaceAll(',', '.');
        }
        else {
            var size = 20;
            var digits = 0;
            if(this.field().size != undefined) {
                size = (this.field().size + '').split('.');
                digits = size.length > 1 ? size[1] : 0;
                size = size[0];
            }
            this._element.find('.ui-formfield-numeric input[type=text]').val(val == null || isNaN(parseFloat(val)) ? '' : parseFloat(val).toMoney(digits, false));
            this._changed = false;
            return this;
        }
    },
    
}, {});

UI.Controls.Form.Coords = UI.Controls.Form.FormField.extend({
    
    _map: false,
    _placemark: false,
    
    RenderControl: function(controlContainer) {
        
        var self = this;
        $(document.body).append('<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>');
        // TODO: нужно ли сделать таб индекс, видимо надо добавить элемент, в котором можно будет вписать коррдинаты и на него поставить tabIndex " tabIndex="' + (UI.tabIndex++) + '"" 
        controlContainer.append('<div class="ui-formfield-coords"><input type="hidden" value="' + (self.field().def != undefined ? self.field().def : '') + '" name="' + self.field().field + '" id="' + self.field().field + '" ' + ' /></div>');
        controlContainer.find('.ui-formfield-coords').append('<div class="map" id="map-container-' + self.field().field + '" style="width: ' + self.field().width + '; height: ' + self.field().height + ';"></div>');
        controlContainer.find('input').change(function(e) { self.validate(); return self.raiseEvent('change', {domEvent: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {domEvent: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {domEvent: e}); }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}); });

        this._initMap();
    },
    
    _initMap: function() {
        var self = this;
        ymaps.ready(function() {
            var place = self.val();
            if(!place || place.split(',').length < 2) place = '55.76,37.64';
            var searchControl = new ymaps.control.SearchControl({
                options: {
                    noSuggestPanel: false    
                }
            })

            self._map = new ymaps.Map('map-container-' + self.field().field, {
                center: place.split(','), 
                controls: ['zoomControl', searchControl],
                zoom: 17
            });
            self._placemark = new ymaps.Placemark(place.split(','), {}, {
                draggable: true, 
                iconLayout: 'default#image',
                iconImageHref: _ROOTPATH + self.field().handleIcon,
                iconImageSize: [36, 36],
            });
            if(self._map.geoObjects) {
                self._map.geoObjects.add(self._placemark);
                self._placemark.events.add('dragend', function(e) {  
                   var thisPlacemark = e.get('target');
                   var coords = thisPlacemark.geometry.getCoordinates();
                   self.val(coords.join(','));
                });
            }
            searchControl.events.add('resultselect', function(e) {
                var result = searchControl.getResult(searchControl.getSelectedIndex());
                result.then(function (res) {
                    var coords = res.geometry.getCoordinates();
                    self.val(coords.join(','));
                    searchControl.hideResult();
                }, function (err) { });
                return false;
            })
        });
        
    },
    
    focus: function() {
        this._element.find('input[type=text]').focus();
        if(!this.validate())
            this._element.find('input[type=text]').addClass('invalid');
        else
            this._element.find('input[type=text]').removeClass('invalid');
    },  
    
    validate: function() {
        var ret = true;
        // проверяем на наличие проблем
        if(this.field().expression != undefined && !eval(this.field().expression)) {
            ret = false;
        }
        
        if(this.field().required == true && this.val() == '') {
            ret = false;
        }
        
        if(this.field().size != undefined && this.val().length > this.field().size) {
            ret = false;
        }         
        
        if(!ret) {
            this._element.find('input[type=text]').addClass('invalid');
        }
        else {
            this._element.find('input[type=text]').removeClass('invalid');
        }
        
        return ret;       
    
    },
    
    val: function(val) {
        if(val === undefined) 
            return this._element.find('.ui-formfield-coords input[type=hidden]').val();
        else {
            
            var self = this;
            if(val) {
                if(this._placemark)
                    this._map.geoObjects.remove(this._placemark);

                if(val.split(',').length < 2) val = '55.76,37.64';
                    
                this._placemark = new ymaps.Placemark(val.split(','), {}, {
                    draggable: true, 
                    iconLayout: 'default#image',
                    iconImageHref: _ROOTPATH + this.field().handleIcon,
                    iconImageSize: [36, 36],
                });      
                if(this._map.geoObjects) {
                    this._map.geoObjects.add(this._placemark);
                    this._map.setCenter(val.split(','));
                    this._map.setZoom(19);
                }
                this._placemark.events.add('dragend', function(e) {
                   var thisPlacemark = e.get('target');
                   var coords = thisPlacemark.geometry.getCoordinates();
                   self.val(coords.join(','));
                });
                
            }
            console.log(val);
            this._element.find('.ui-formfield-coords input[type=hidden]').val(val ? val : '');
            this._changed = false;
            return this;
        }
    },
    
}, {});

UI.Controls.Form.Memo = UI.Controls.Form.FormField.extend({
    
    _codemirror: false,
    
    RenderControl: function(controlContainer) {
        var self = this;
        if(this.field().styles != undefined) {
            controlContainer.append('<link rel="stylesheet" href="' + this.field().styles + '" type="text/css" />');
        }
        controlContainer.append('<div class="ui-formfield-memo"' + (this.field().code ? ' style="height: ' + this.field().height + '; width: ' + this.field().width + '"' : '') + '>' + 
            '<textarea ' + (self.field().readonly ? ' disabled="disabled"' : '') + ' tabIndex="' + (UI.tabIndex++) + '" name="' + self.field().field + '" id="' + self.field().field + '"' + (self.field().width || self.field().height ? ' style="' + (self.field().width ? 'width: ' + self.field().width + ';' : '') + (self.field().height ? 'height: ' + self.field().height + ';' : '') + '"' : '') + '  ' + (self.field().placeholder && !self.field().note ? ' placeholder="' + self.field().placeholder + '"' : '') + '>' + (self.field().def == undefined ? '' : self.field().def) + '</textarea>' + 
        '</div>');
        this.bindHTMLEvents();
        if(this.field().template) {
            $.get(this.field().template, function(data) {
                self._templateData = data;
            });
        }
    },
    
    bindHTMLEvents: function() {
        var self = this;
        
        /*if(this.field().virtual) {
            this._element.find('.textarea').unbind('change');
            this._element.find('.textarea').change(function(e) { self.validate(); self.raiseEvent('change', {event: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {event: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {event: e}); });
        }*/

        this._element.find('textarea').unbind('change');
        this._element.find('textarea').change(function(e) { self.validate(); return self.raiseEvent('change', {domEvent: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {domEvent: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {domEvent: e}); }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}); });
        
        /*this._element.find('.ui-formfield-memo .textarea').find('[contenteditable]').each(function() { 
            this.contentEditable = true; 
            window.ncedit.addInstance(this);
        });*/

        /*this._element.find('.textarea a').unbind('click');
        this._element.find('.textarea a').click(function() { return false; });
        
        this._element.find('.textarea').keydown(function(e) {
            if(e.keyCode == 13) {
                e.stopPropagation();
            }
            return true;
        });*/

        
        this.__initVisual();

        // this._element.find('.ui-formfield-memo .textarea [contenteditable]').editable({inlineMode: true, alwaysVisible: false, imageUpload: false});
    },
    
    __initVisual: function() {
        var self = this;
        if(this.field().visual) {
            
            if(tinymce.get(this.field().field))
                tinymce.get(this.field().field).remove(); 
                
            this.tinymceAditionalTools = [];
            this.tinymceAditionalToolbarButtons = '';
            if(window.app) {
                window.app.raiseEvent('application.tinymce.toolbar', {control: this});
            }
            
            tinymce.init({
                selector: '#' + this.field().field,
                /*skin: 'nulla',*/
                relative_urls : false,
                remove_script_host : true,
                allow_script_urls: true,
                language: 'ru',
                extended_valid_elements: "script[*],style[*]", 
                valid_children: "+body[script],pre[script|div|p|br|span|img|style|h1|h2|h3|h4|h5],*[*]",
                valid_elements : "*[*]",
                codemirror: {
                    indentOnInit: true, // Whether or not to indent code on init.
                    fullscreen: false,   // Default setting is false
                    path: _ROOTPATH + 'res/codemirror', // Path to CodeMirror distribution
                    config: {           // CodeMirror config object
                        mode: 'application/x-httpd-php',
                        lineNumbers: true
                    },
                    width: 800,         // Default value is 800
                    height: 600,        // Default value is 550
                    saveCursorPosition: true,    // Insert caret marker
                    jsFiles: [          // Additional JS files to load
                        'mode/clike/clike.js',
                        'mode/php/php.js'
                    ]
                },
                menubar: false,
                plugins: [
                    "advlist link image lists charmap print preview hr anchor pagebreak spellchecker",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "table contextmenu directionality emoticons template textcolor paste textcolor codemirror"
                ],
                toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
                toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media embed code | pastetext | forecolor backcolor",
                toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | visualchars visualblocks nonbreaking pagebreak restoredraft",
                toolbar4: this.tinymceAditionalToolbarButtons,
                file_picker_callback: function(callback, value, meta) {
                    var element = $('.mce-open:hover');
                    
                    var position = element.offset();
                    position.top += element.height();                    
                    
                    var hide = function() {
                        if(!cm._element.is(':hover'))
                            cm.Hide();
                    };
                    
                    var cm = (new UI.Controls.Tree(self.field().field + '_filepopup', $(document.body)))
                    .Render()
                    .width('250px')
                    .height('200px')
                    .styles({background: '#fff', border: '1px solid #c0c0c0', 'box-shadow': '5px 5px 10px rgba(0,0,0,.6)'})
                    .position(position)
                    .addHandler('shown', function() {       
                        $(document.body).bind('mousedown', hide);
                    })
                    .addHandler('hidden', function() {
                        $(document.body).unbind('mousedown', hide);
                    })
                    .addHandler('selectionChanged', function(sender, args) {
                        var node = args.node;
                        if(node && node.tag().type != 'none') {
                            if(node.tag().type == 'file' || node.tag().type == 'dir')
                                callback('/' + node.tag().path);
                            else if(node.tag().type == 'domain' || node.tag().type == 'page') {
                                callback('//' + node.tag().path);
                            }
                            cm.Hide();
                        }
                    })
                    .Show();
                    var _showItems = function(results, parent) {
                        results.forEach(function(result) {
                            var newnode = parent.nodes().Add(result.path).title(result.desc ? result.desc : result.name).icon(_ROOTPATH + (result.type == 'file' ? '/res/img/icons/files/' + result.ext.toLowerCase() + '.svg' : '/res/img/icons/folder.svg')).tag(result);
                            if(result.childs)
                                _showItems(result.childs, newnode);
                        });
                    };
                    window.app.ExecuteCommand('ApplicationAjaxHandler.Hrefs', {}, function(data) {
                        _showItems(data.results, cm);
                    });
              
                },
                setup: function(ed) {
                    
                    self.tinymceAditionalTools.forEach(function(button) {
                        button.editor = ed;
                        ed.addButton(button.name, button); 
                    });
                    
                    ed.on('change', function(e) {
                        $(e.target.editorContainer).next().change();
                    });

                },
            });

            // $('#' + this.field().field + '_ifr').attr('tabIndex', $('#' + this.field().field).attr('tabIndex'));


            // this._element.find('textarea').redactor({focus: true, lang: 'ru'});
        }
        else if(this.field().code) {
            var props = $.extend({}, this.field().codeProps);         
            // TODO проблема с таб индексом, не берет
            props.tabindex = this._element.find('.ui-formfield-memo textarea').attr('tabIndex');
            this._codemirror = CodeMirror.fromTextArea(this._element.find('.ui-formfield-memo textarea')[0], props);
        }
    },
    
    template: function() {
        return this._templateData;
    },
    
    focus: function() {
        this._element.find('textarea').focus();
        if(!this.validate())
            this._element.find('textarea').addClass('invalid');
        else
            this._element.find('textarea').removeClass('invalid');

    },
    
    validate: function() {
        var ret = true;
        
        // проверяем на наличие проблем
        if(this.field().expression != undefined && !eval(this.field().expression)) {
            ret = false;
        }
        
        if(this.field().required == true && this.val() == '') {
            ret = false;
        }
        
        if(this.field().size != undefined && this.val().length > this.field().size) {
            ret = false;
        }  
        
        if(!ret) {
            this._element.find('input[type=text]').addClass('invalid');
        }
        else {
            this._element.find('input[type=text]').removeClass('invalid');
        }
        
        return ret;              

    },
    
    val: function(val) {
        if(val === undefined) {
            if(this.field().visual) {
                try {
                    var html = tinymce.get(this.field().field).getContent();
                } catch(e) {
                    var html = '';
                }
                var ret = html
                    .replaceAll('<!DOCTYPE html>', '')
                    .replaceAll('<html>', '')
                    .replaceAll('</html>', '')
                    .replaceAll('<head>', '')
                    .replaceAll('</head>', '')
                    .replaceAll('<body>', '')
                    .replaceAll('</body>', '');
                return ret;
            }
            else if(this.field().code) {
                return this._codemirror.getValue();
            }
            else
                return this._element.find('.ui-formfield-memo textarea').length > 0 ? this._element.find('.ui-formfield-memo textarea').val() : this._element.find('.ui-formfield-memo .textarea').html();
        }
        else {
            if(this.field().visual) {      
                try {
                    // console.log(val);
                    // this._element.find('textarea').val(val ? val : '');
                    tinymce.get(this.field().field).setContent(val ? val : '', {format: 'raw'});
                }
                catch(e) {
                    this._element.find('textarea').val(val ? val : '');
                }
            }
            else if(this.field().code) {
                return this._codemirror.setValue(val);
            }
            else {
                this._element.find('textarea').val(val ? val : '');
            }   
            this._changed = false;
            return this;
        }
    },
    
}, {});

UI.Controls.Form.Lookup = UI.Controls.Form.FormField.extend({
    
    _lookupManager: false,
    
    _selected: false,
    
    constructor: function(name, container, title, className, field) {
        this.base(name, container, title, className, field);
        
        this._lookupManager = new Data.Lookup();
        
    },
    
    RenderControl: function(controlContainer) {
        
        var self = this;
        
        var def = self.field().def != undefined ? self.field().def : '';
        
        var div = $('<div class="ui-formfield-lookup"></div>').appendTo(controlContainer)
    
        var select = $('<select' + (self.field().multiple ? ' multiple="multiple"' : '') + (self.field().readonly ? ' disabled="disabled"' : '') + ' style="' + (self.field().width ? 'width: ' + self.field().width + ';' : '') + (self.field().height ? 'height: ' + self.field().height + ';' : '') + '" tabIndex="' + (UI.tabIndex++) + '"></select>').appendTo(div);
        select.change(function(e) { self.validate(); return self.raiseEvent('change', {domEvent: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {domEvent: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {domEvent: e}); }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}); });
        
        select.select2({minimumResultsForSearch: 10});
        
        if(this.field().values != undefined) {
            Object.forEach(self.field().values, function(i, value) {
                select.append('<option value="' + i + '"' + (def == i ? ' selected="selected"' : '') + '>' + value + '</option>');
            });
        }
        else if(this.field().lookup != undefined) {
            
            if(this.field().lookup.rows == undefined && (this.field().lookup.ondemand == undefined || this.field().lookup.ondemand == false)) {    
                var lookup = this.field().lookup;
                lookup.data = self.tag();
                select.attr('disabled', 'disabled');
                this._lookupManager.LookupData(this, lookup, function(data) {   
                    if(data.error) {
                        window.app.Alert.Show({
                            message: data.message,
                            removeCancelButton: true,
                            okButtonTitle: 'Хорошо'
                        });
                        return;
                    }
                        
                    var def = this.field().def != undefined ? this.field().def : '';  
                    
                    this.field().lookup.rows = toArray(data.rows);
                    if(!this.field().multiple && (this.field().nodef == undefined || this.field().nodef == false)) {
                        select.append('<option value="0"' + (def == 0 ? ' selected="selected"' : '') + '>...</option>');
                    }
                        
                    Object.forEach(this.field().lookup.rows, function(i, d) {
                        select.append('<option value="' + d.value + '"' + (def == d.value ? ' selected="selected"' : '') + '>' + d.title + '</option>');
                    });
                    
                    this.val(this._selected);
                    
                    if(!this.field().readonly && select.closest('.ui-formfield.ui-disabled').length == 0)
                        select.removeAttr('disabled');
                });
            }
            else {
                if(this.field().nodef == undefined || this.field().nodef == false) {
                    select.append('<option value="0"' + (def == 0 ? ' selected="selected"' : '') + '>...</option>');
                }
                    
                Object.forEach(this.field().lookup.rows, function(i, d) {
                    select.append('<option value="' + d.value + '"' + (def == d.value ? ' selected="selected"' : '') + '>' + d.title + '</option>');
                });
            }
        }            

    },
    
    reload: function(callback) {
        var self = this;
        var select = this._element.find('select');
        var lookup = this.field().lookup;
        lookup.data = self.tag();
        select.find('option').remove();
        select.attr('disabled', 'disabled');
        lookup.rows = undefined;
        this._lookupManager.LookupData(this, lookup, function(data) {   
            if(data.error) {
                window.app.Alert.Show({
                    message: data.message,
                    removeCancelButton: true,
                    okButtonTitle: 'Хорошо'
                });
                return;
            }
                
            var def = this.field().def != undefined ? this.field().def : '';  
            
            this.field().lookup.rows = toArray(data.rows);
            if(!this.field().multiple && (this.field().nodef == undefined || this.field().nodef == false)) {
                select.append('<option value="0"' + (def == 0 ? ' selected="selected"' : '') + '>...</option>');
            }
                
            Object.forEach(this.field().lookup.rows, function(i, d) {
                select.append('<option value="' + d.value + '"' + (def == d.value ? ' selected="selected"' : '') + '>' + d.title + '</option>');
            });
            
            this.val(this._selected);
            
            if(!this.field().readonly && select.closest('.ui-formfield.ui-disabled').length == 0)
                select.removeAttr('disabled');
            
            select.select2({minimumResultsForSearch: 10});
            
            if(callback)
                callback();
            
        });
    },
    
    values: function(values) {
        var select = this._element.find('select');
        select.html('');
        this.field().values = values;
        Object.forEach(this.field().values, function(i, value) {
            select.append('<option value="' + i + '">' + value + '</option>');
        });
    }, 
                            
    validate: function() {
        var ret = true;
        // проверяем на наличие проблем
        if(this.field().expression != undefined && !eval(this.field().expression)) {
            ret = false;
        }
                                                                                                
        if(this.field().values !== undefined) {
            if(this.field().required == true && (this.val() === undefined || this.val() === null || this.field().values[this.val()] === undefined)) {
                ret = false;
            }
        }
        else {
            if(this.field().required == true && (this.val() === undefined || this.val() === null || this.val() === 0 || this.val()+'' === '0')) {
                ret = false;
            }
        }
        
        if(!ret) {
            this._element.find('select').addClass('invalid');
        }
        else {
            this._element.find('select').removeClass('invalid');
        }
        
        return ret;
    },

    focus: function() {
        this._element.find('select').focus();
        this._element.find('select').select2('focus');
        this.validate();
    },
   
    val: function(val) {
        
        if(val === true) val = 1;
        if(val === false) val = 0;
        
        if(val === undefined) {
            if(this.field().multiple) {
                var ret = [];
                var selected = this._element.find('.ui-formfield-lookup select option:selected');
                selected.each(function() {
                    ret.push($(this).val());
                });
                return ret;
            }
            else
                return this._element.find('.ui-formfield-lookup select option:selected').val();
        }
        else {
            this._selected = val;
            
            if(this.field().multiple) {
                
                if(val instanceof Array) {} else {
                    val = val ? val.split(',') : [];
                }
                
                this._element.find('.ui-formfield-lookup select option:selected').removeAttr('selected');
                
                var self = this;
                
                $(val).each(function() {
                    var v = this;
                    if(self.field().type == 'numeric')
                        v = parseFloat(v);
                    try { self._element.find('.ui-formfield-lookup select option[value="' + v + '"]').prop('selected', true); } catch(e) {}
                });
                this._element.find('select').select2({minimumResultsForSearch: 10});
                
            }
            else {
                if(this.field().type == 'numeric')
                    val = parseFloat(val);

                this._element.find('.ui-formfield-lookup select option:selected').removeAttr('selected');
                if(this._element.find('.ui-formfield-lookup option[value="' + val + '"]').length > 0) {
                    this._element.find('.ui-formfield-lookup option[value="' + val + '"]').attr('selected', 'selected')[0].selected=true;
                    this._element.find('.ui-formfield-lookup select').trigger('change');
                }
            }

            this._changed = false;
            
            return this;
        }
    },
    
}, {});

UI.Controls.Form.FileSelect = UI.Controls.Form.FormField.extend({
    
    _path: false,
    _command: false,
    _createFileCommand: false,
    _createFolderCommand: false,
    
    _popup: false,
    
    RenderControl: function(controlContainer) {
        var self = this;
        controlContainer.append('<div class="ui-formfield-fileselect" style="width: ' + self.field().width + ';">' + 
            '<input type="text" readonly="readonly" value="' + (self.field().def != undefined ? self.field().def : '') + '" name="' + self.field().field + '" id="' + self.field().field + '" ' + (!self.field().note && self.field().placeholder ? ' placeholder="' + self.field().placeholder + '"' : '') + ' />' + 
            '<button id="' + self.field().field + '_selector" tabIndex="' + (UI.tabIndex++) + '">•••</button>' + 
        '</div>');
        
        this._element.find('.ui-formfield-fileselect').css({position: 'relative'});
        
        this._popup = (new UI.Controls.Tree(self.field().field + '_popup', this._element.find('.ui-formfield-fileselect'))).Render().Hide().width(self.field().width).height('200px').styles({background: '#fff', border: '1px solid #c0c0c0', 'box-shadow': '5px 5px 10px rgba(0,0,0,.6)'});
        this._popup.contextmenu(this.GetContextMenu());
        this._popup.tabIndex(true);
        this._popup.addHandler('selectionChanged', function(sender, args) {
            var node = args.node;
            if(node && !node.tag().isdir) {
                self.raiseEvent('selectionChanging', {file: node.tag().path});
                self._popup.Hide();
            }
        }).addHandler('contextMenuItemClicked', function(sender, args) {
            var node = args.node;
            var path = '/';
            if(node.tree) { // если есть функция tree значит это TreeNode, если нету значит Tree
                path = node.path();
            }
            
            if(args.menuItem == 'add-file') {
                
                node.nodes().AddEditable({icon: _ROOTPATH + '/res/img/icons/layout.svg'}, function(node, editableVal) {
                    window.app.ExecuteCommand(self._createFileCommand, {path: self._path + (node.tag().path ? node.tag().path : ''), name: editableVal}, function(data) { 
                        if(data.error) {
                            window.app.Alert.Show({
                                message: data.message,
                                removeCancelButton: true,
                                okButtonTitle: 'Хорошо'
                            });
                            return;
                        }
                        var result = data.result;
                        result.path = result.path.replaceAll(self._path, '');
                        node.nodes().Add(result.path).title(result.name).icon(_ROOTPATH + '/res/img/icons/layout.svg').tag(result);
                    });
                });                
            }
            else if(args.menuItem == 'add-folder') {
                
                node.nodes().AddEditable({icon: _ROOTPATH + '/res/img/icons/folder.svg'}, function(node, editableVal) {
                    window.app.ExecuteCommand(self._createFolderCommand, {path: self._path + (node.tag().path ? node.tag().path : ''), name: editableVal}, function(data) { 
                        if(data.error) {
                            window.app.Alert.Show({
                                message: data.message,
                                removeCancelButton: true,
                                okButtonTitle: 'Хорошо'
                            });
                            return;
                        }
                        var result = data.result;
                        result.path = result.path.replaceAll(self._path, '');
                        node.nodes().Add(result.path).title(result.name).icon(_ROOTPATH + '/res/img/icons/folder.svg').tag(result).contextmenu(self.GetContextMenu());
                    });
                });
                                
            }

        });
        
        this.bindHTMLEvents();
    },
    
    GetContextMenu: function() {
        return [
            {key: 'add-folder', text: 'Новыя папка', 'keyboard': false, icon: 'res/img/icons/add-folder.svg'},
            {key: 'add-file', text: 'Новый файл', 'keyboard': false, icon: 'res/img/icons/add-field.svg'},
        ];
    },
    
    LoadFiles: function(parent, path) {
        var self = this;
        
        if(parent.nodes().count() > 0)
            return;
        
        window.app.ExecuteCommand(this._command, {path: path}, function(data) {
            var results = data.results;
            results.forEach(function(result) {
                result.path = result.path.replaceAll(self._path, '');
                var newnode = parent.nodes().Add(result.path).title(result.name).icon(_ROOTPATH + (result.isdir ? '/res/img/icons/folder.svg' : '/res/img/icons/layout.svg')).tag(result);
                newnode.contextmenu(result.isdir ? self.GetContextMenu() : null);
                if(result.haschildren) {
                    self.LoadFiles(newnode, self._path + result.path);
                }
            });
            
        });
    },
    
    bindHTMLEvents: function() {
        var self = this;
        
        this._element.find('input').change(function(e) { self.validate(); return self.raiseEvent('change', {domEvent: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {domEvent: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {domEvent: e}); }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}); });
        this._element.resize(function() {
            self._popup.width(self.width());
        });
        this._element.find('button').click(function() {
            if(self._popup.visible()) { 
                self._popup.Hide();
            }
            else {                
                self._popup.position({left: 0, top: self._element.find('.ui-formfield-fileselect').outerHeight()}).Show().focus();
                self.parent().addHandler('click', function() {
                    self._popup.Hide();
                });
                if(self._command && self._path) {
                    self.LoadFiles(self._popup, self._path);
                }
            }
            return false;
        });
        
    },
    
    filesPath: function(value) {
        if(value == undefined)
            return this._path;
        else {
            this._path = value;
            this._popup.clearSelection();
            if(this._popup)
                this._popup.nodes().Dispose();
            return this;
        }
    },
    
    command: function(value) {
        if(value == undefined) 
            return this._command;
        else {
            this._command = value;
            return this;
        }
    },

    createFileCommand: function(value) {
        if(value == undefined) 
            return this._createFileCommand;
        else {
            this._createFileCommand = value;
            return this;
        }
    },
    
    createFolderCommand: function(value) {
        if(value == undefined) 
            return this._createFolderCommand;
        else {
            this._createFolderCommand = value;
            return this;
        }
    },
    
    val: function(value) {
        
        if(value == undefined) {
            return this._element.find('input').val();
        }
        else {
            this._element.find('input').val(value);
            return this;
        }
        
    },
     
}, {});

UI.Controls.Form.Href = UI.Controls.Form.FormField.extend({
    
    _path: false,
    _command: false,
    _createFileCommand: false,
    _createFolderCommand: false,
    
    _popup: false,
    
    RenderControl: function(controlContainer) {
        var self = this;
        controlContainer.append('<div class="ui-formfield-fileselect" style="width: ' + self.field().width + ';">' + 
            '<input type="text" tabIndex="' + (UI.tabIndex++) + '" value="' + (self.field().def != undefined ? self.field().def : '') + '" name="' + self.field().field + '" id="' + self.field().field + '" ' + (!self.field().note && self.field().placeholder ? ' placeholder="' + self.field().placeholder + '"' : '') + ' />' + 
            '<button id="' + self.field().field + '_selector" tabIndex="' + (UI.tabIndex++) + '">•••</button>' + 
        '</div>');
        
        this._element.find('.ui-formfield-fileselect').css({position: 'relative'});
        
        this._popup = (new UI.Controls.Tree(self.field().field + '_popup', this._element.find('.ui-formfield-fileselect'))).Render().Hide().width(self.field().width).height('200px').styles({background: '#fff', border: '1px solid #c0c0c0', 'box-shadow': '5px 5px 10px rgba(0,0,0,.6)'});
        this._popup.addHandler('selectionChanged', function(sender, args) {
            var node = args.node;
            if(node && node.tag().type != 'none') {
                if(node.tag().type == 'file' || node.tag().type == 'dir')
                    self.val('/' + node.tag().path);
                else if(node.tag().type == 'domain' || node.tag().type == 'page') {
                    self.val('//' + node.tag().path + '/');
                }
                self._popup.Hide();
            }
        });
        
        this.bindHTMLEvents();
    },
    
    _showItems: function(results, parent) {
        var self = this;
        results.forEach(function(result) {
            var newnode = parent.nodes().Add(result.path).title(result.desc ? result.desc : result.name).icon(_ROOTPATH + (result.type == 'file' ? '/res/img/icons/files/' + result.ext + '.svg' : '/res/img/icons/folder.svg')).tag(result);
            if(result.childs)
                self._showItems(result.childs, newnode);
        });
    },
    
    LoadItems: function(parent, path) {
        var self = this;
        
        if(parent.nodes().count() > 0)
            return;
        
        window.app.ExecuteCommand(this.field().command, {}, function(data) {
            self._showItems(data.results, self._popup);
        });
        
    },
    
    bindHTMLEvents: function() {
        var self = this;
        
        this._element.find('input').change(function(e) { self.validate(); return self.raiseEvent('change', {domEvent: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {domEvent: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {domEvent: e}); }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}); });
        this._element.resize(function() {
            self._popup.width(self.width());
        });
        this._element.find('button').click(function() {
            if(self._popup.visible()) { 
                self._popup.Hide();
            }
            else {                
                self._popup.position({left: 0, top: self._element.find('.ui-formfield-fileselect').outerHeight()}).Show().focus();
                self.parent().addHandler('click', function() {
                    self._popup.Hide();
                });
                if(self.field().command) {
                    self.LoadItems(self._popup, self._path);
                }
            }
            return false;
        });
        
    },

    
    val: function(value) {
        
        if(value == undefined) {
            return this._element.find('input').val();
        }
        else {
            this._element.find('input').val(value);
            return this;
        }
        
    },
     
}, {});

UI.Controls.Form.ChooseFile = UI.Controls.Form.FormField.extend({
    
    _path: false,
    _command: false,
    _createFileCommand: false,
    _createFolderCommand: false,
    
    _popup: false,
    
    RenderControl: function(controlContainer) {
        var self = this;
        controlContainer.append('<div class="ui-formfield-fileselect" style="width: ' + self.field().width + ';">' + 
            '<input type="text" tabIndex="' + (UI.tabIndex++) + '" value="' + (self.field().def != undefined ? self.field().def : '') + '" readonly="readonly" name="' + self.field().field + '" id="' + self.field().field + '" ' + (!self.field().note && self.field().placeholder ? ' placeholder="' + self.field().placeholder + '"' : '') + ' />' + 
            '<input type="file" style="display: none;" value="" id="chooser" tabIndex="' + (UI.tabIndex++) + '" /><input type="hidden" value="" name="filedata" id="filedata" /><button id="' + self.field().field + '_selector" class="choose"></button>' + 
        '</div>');
        
        this._element.find('.ui-formfield-fileselect').css({position: 'relative'});
        
        this.bindHTMLEvents();
    },
    
    bindHTMLEvents: function() {
        var self = this;
        
        this._element.find('input').change(function(e) { self.validate(); return self.raiseEvent('change', {domEvent: e}); }).dblclick(function(e) { return self.raiseEvent('dblclick', {domEvent: e}); }).keydown(function(e) { return self.raiseEvent('keydown', {domEvent: e}); }).blur(function(e) { self.raiseEvent('blur', {domEvent: e}) }).focus(function(e) { self.raiseEvent('focus', {domEvent: e}); });
        this._element.find('button').click(function() {
            self._element.find('[type=file]').click();
            return false;
        });
        
        this._element.find('[type=file]').change(function(e) {
            var file = e.target.files[0];

            self._element.find('[type=text]').val(file.name);

            var reader = new FileReader();
            reader.onload = function(ee) {
                self._element.find('[type=hidden]').val(ee.target.result.split('base64,')[1]);
            };
            reader.readAsDataURL(file);
            
            e.preventDefault();
            return false;
        });
        
    },

    
    val: function(value) {
        
        if(value == undefined) {
            return {name: this._element.find('input[type=text]').val(), data: this._element.find('input[type=hidden]').val()};
        }
        else {
            
            if(value.name !== undefined) {
                this._element.find('input[type=text]').val(value.name);
                this._element.find('input[type=hidden]').val(value.data);
                this._element.find('input[type=file]').val('');
            }
            else {
                this._element.find('input[type=text]').val('');
                this._element.find('input[type=hidden]').val('');
                this._element.find('input[type=file]').val('');
            }
            
            return this;
        }
        
    },
     
}, {});
