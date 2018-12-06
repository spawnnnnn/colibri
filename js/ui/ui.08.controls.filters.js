UI.Controls.Filters = {
    charWidth: 12,
};

UI.Controls.Filters.FilterField = UI.Controls.Control.extend({

    _container: false,
    _title: false,
    _placeholder: false,
    
    constructor: function(name, container, title, placeholder) {
        this.base(name, container);
        this._title = title;
        this._placeholder = placeholder;
    }, 
    
    RenderControl: function(controlContainer) {
        
    }, 
    
    Render: function() {
        
        var self = this;
        
        this.base('div', 'ui-filterfield ' + this._className);
        
        this._element.append('<div><div class="ui-filterfield-title">' + this._title + '</div><div class="ui-filterfield-input"></div></div>');

        this.RenderControl(this._element.find('.ui-filterfield-input'));
        
        this.raiseEvent('ready', []);
        
        return this;
        
    },
    
});
  
// Язык поисковых запросов
// ("Фамилия" Григорян или Иванов) и ("Имя" Ваган) и ("Партнер" mycie или ничего)
// "Фамилия" Григорян
// "Номер">10
// Григорян
// берем и делим по () между ними заменяем "и"/"или" на "or"/"and"
// далее в каждом куске беррем все, что в ковычках - это название поля
// между последней ковычкой и первой буквой - это знак, если пустой значит ~ (like)
// далее смотрим, берем часть без поля и ковычек
// ищем и/или если есть то делаем split
// подставляем название поля из списка полей, и формируем [[field.name]] знак '[[typeExchange(значение)]]' операция ...
// собираем все вместе
// отдаем в руки шаблону, который делает правильные замены (должен быть дефолтный шаблон, который собирает все для mysql)
// нужно написать шаблон для публикаций, там есть ft фултекст
// когда пользователь тыркается в форму то нужно собирать запрос в нескомпилированном виде
UI.Controls.Filters.Search = UI.Controls.Filters.FilterField.extend({
    
    _layout: false,
    _fields: false,
    _fieldsList: false,
    
    _templates: false,
    
    _val: false,
    
    container: function(isinner) {
        return isinner ? this._element.find('.ui-filterfield-search') : this._element;
    },
    
    RenderControl: function(controlContainer) {
        var self = this;
        controlContainer.append('<div class="ui-filterfield-search" style="position: relative;">' + 
            '<input type="text" tabIndex="' + (UI.tabIndex++) + '" name="filter-search" placeholder="' + this._placeholder + '" />' + 
            '<button class="filter-search_clear">&#9932;</button>' + 
            '<button class="filter-search_selector">•••</button>' + 
        '</div>');
        
        controlContainer.find('.filter-search_clear').css({right: controlContainer.find('.filter-search_selector').outerWidth()});
                                                                                                                         
        controlContainer.find('.ui-filterfield-search>input').focus(function() {
            self.controls('layout').Hide();
        });
        
        (new UI.LayoutManager(this, {
            control: 'UI.Controls.Pane',
            container: [true],
            then: {
                width: ['550px'],
                height: ['auto'],
                styles: [{background: '#fff', border: '1px solid #c0c0c0', 'box-shadow': '5px 5px 10px rgba(0,0,0,.6)', padding: '10px'}],
                focus: [],
            },                     
            controls: {
                fulltext: {
                    control: 'UI.Controls.Form.Text',
                    create: ['Поиск', ' fulltext-search', {
                        field: 'fulltextsearch',
                        type: 'text',
                        placeholder: 'Введите текст для поиска',
                        width: '100%',
                        note: false,
                        required: false,
                        editor: true,
                        editable: false
                    }]
                },
                fields: {
                    control: 'UI.Controls.ListView',
                    then: {
                        styles: [{'margin-top': '10px'}],
                        height: ['200px']
                    },
                }
            }
        }, false))
        .addHandler('layoutmanager.ready', function(event, args) {
            
            var layout = self.controls('layout');
            var fulltext = layout.controls('fulltext');
            var fields = layout.controls('fields');  
            
            fields.headers().Add([
                {title: 'Поле',         name: 'name',  type: 'text', width: '30%', resizable: true}, 
                {title: 'Сравнение',    name: 'comp',  type: 'text', width: '20%', resizable: true, editable: true, values:{'like': '~', '=': '=', '>': '>', '<': '<', '>=': '>=', '<=': '<='}}, 
                {title: 'Значение',     name: 'value', type: function(row, header) { 
                    var d = row.itemData();
                    var h = $.extend({}, header.headerData());
                    switch(d['type']) {
                        case 'longtext':
                        case 'memo':
                        case 'html': 
                        case 'href':
                        case 'text': 
                            h.type = 'text';
                            break;
                        case 'file': 
                        case 'files': 
                            h.type = 'text';
                            h.values = {'': 'Не важно', 'empty': 'Не заполнено', 'filled': 'Заполнено'};
                            break;
                        case 'bool': 
                            h.values = {'': 'Не важно', '0': 'Нет', '1': 'Да'};
                            break;
                        case 'date': 
                        case 'datetime':
                            if(d['type'] == 'datetime')
                                h.hasTime = true;
                            h.type = 'date'; 
                            break;
                        default: 
                            h.type = d['type'];
                            break;
                    }
                    if(d['values'])
                        h.values = d['values'];
                    if(d['lookup'])
                        h.lookup = d['lookup'];
                    return h;
                }, width: '50%',  editable: true},                                                                                                  
            ]);
            
            fields.addHandler('editableChanged', function(sender, args) {
                
                args.row.itemData()[args.property] = args.value;
                var o = [];
                this.items().forEach(function(k, row) {
                    var row = row.itemData();
                    if(row.value)
                        o.push({field: row.field, comp: row.comp, value: row.value});
                });
                
                self._set(self._val.fulltext, o);
                
            })
            .addHandler('blur', function(sender, args) {
                // if(!args.domEvent && !args.domEvent.relatedTarget)    
                //     return true;
                // if(!isChildOf($(args.domEvent.relatedTarget), layout._element))
                //     layout.Hide();
            });
            
            fulltext
                .addHandler('blur', function(sender, args) {
                    if(!isChildOf($(args.domEvent.relatedTarget), layout._element))
                        layout.Hide();
                })
                .addHandler('change', function(sender, args) {
                    self._set(this.val(), self._val.fields);
                });
            
            self.bindHTMLEvents();
            
            layout.Hide();
            
        })
        .Render();
    
        
        
    },
    
    bindHTMLEvents: function() {
        var self = this;
        
        this._element.find('.ui-filterfield-search>input').change(function() {
            self.raiseEvent('filterChanged');
        });
        
        this._element.find('.ui-filterfield-search>.filter-search_selector').click(function() {
            if(self.controls('layout').visible()) { 
                self.controls('layout').Hide();
            }
            else {                
                self.controls('layout').position({left: 0, top: self._element.find('input').outerHeight()}).Show();
                self.controls('layout').controls('fulltext').focus();
            }
            return false;
        });
        
        this._element.find('.ui-filterfield-search>.filter-search_clear').click(function() {
            self._set(false, []);
            return false;
        });
        
    },
    
    fields: function(val) {
        if(val == undefined)
            return this._fields;
        else {
            
            this._fields = val;
            
            this._element.find('input[name=filter-search]').val('');
            this.controls('layout').controls('fields').items().Clear();

            var rows = [];
            this._fields.forEach(function(storage) {
                
                storage.fields.forEach(function(field) {
                    rows.push({
                        field: field.name,
                        type: field.type,
                        name: field.desc ? field.desc : field.title,
                        comp: 'like',
                        value: '',
                        values: field.values,
                        lookup: field.lookup
                    });
                });
                
            });
            
            this.controls('layout').controls('fields').items().Add(rows);
            
            
        }
    }, 
    
    templates: function(s, so) {
        this._templates = {s: s, so: so};
        return this;
    }, 
    
    hasFullText: function(val) {
        if(val) {
            this.controls('layout').controls('fulltext').Show();
            this.controls('layout').controls('fields').styles({'margin-top': '10px'});
        }
        else {
            this.controls('layout').controls('fulltext').Hide();
            this.controls('layout').controls('fields').styles({'margin-top': '0px'});
        }
        return this;
    },
    
    _set: function(fulltext, fields) {
        this._val = {
            fulltext: fulltext,
            fields: fields
        };
        
        var self = this;
        var val = '';
        
        if(this._val.fulltext) {
            val += this._templates.s.replaceAll('%ft', this._val.fulltext); //'(ft like \'%' + this._val.fulltext + '%\')';
        }
        
        if(this._val.fields && this._val.fields.length > 0) {
            var fv = [];
            this._val.fields.forEach(function(o) {
                fv.push(self._templates.so.replaceAll('%field', o.field).replaceAll('%comp', o.comp).replaceAll('%value', (o.comp == 'like' ? '%' + o.value + '%' : o.value)));
            });
            val += (this._val.fulltext ? ' and ' : '') + '(' + fv.join(' and ') + ')'
        }
        
        this._element.find('.ui-filterfield-search>input').val(val);
        
    },
    
    val: function(s, so) {
        return this._element.find('.ui-filterfield-search>input').val();
    },
    
}); 

UI.Controls.Filters.Order = UI.Controls.Filters.FilterField.extend({
    
    _fields: false,
    _order: false,
    _fieldsList: false,
    
    _templates: false,
    
    _val: false,
    
    container: function(isinner) {
        return isinner ? this._element.find('.ui-filterfield-order>div') : this._element;
    },
    
    RenderControl: function(controlContainer) {
        var self = this;
        controlContainer.append('<div class="ui-filterfield-order" style="position: relative;">' + 
            '<div></div>' + 
            '<button class="filter-order_clear">&#9932;</button>' + 
        '</div>');
        
        controlContainer.find('.filter-order_clear').css({right: controlContainer.find('.filter-order_selector').outerWidth()});
                                                                                                                         
        this._field = new UI.Controls.Form.Lookup('field', this.container(true), '', ' fulltext-order', {
            field: 'orderfield',
            type: 'text',
            values: {},
            width: '220px',
            note: false,
            required: false,
            editor: true,
            editable: false,
            def: 0
        }).Render().styles({width: '220px', display: 'inline-block'});
        
        this._order = new UI.Controls.Form.Lookup('order', this.container(true), '', ' fulltext-order', {
            field: 'orderorder',
            type: 'text',
            values: {'asc': 'ASC', 'desc': 'DESC'},
            width: '80px',
            note: false,
            required: false,
            editor: true,
            editable: false
        }).Render().styles({width: '80px', display: 'inline-block', 'margin-left': '10px'});

        this.bindHTMLEvents();
        
    },
    
    bindHTMLEvents: function() {
        var self = this;
        
        this._element.find('.ui-filterfield-order>.filter-order_clear').click(function() {
            self._field.val('-');
            return false;
        });
        
    },
    
    templates: function(s) {
        this._templates = {s: s};
    }, 
    
    fields: function(val) {
        if(val == undefined)
            return this._fieldsList;
        else {
            
            this._fieldsList = val;
            var rows = [];
            rows['-'] = 'Не важно';
            this._fieldsList.forEach(function(storage) {
                storage.fields.forEach(function(field) {
                    rows[field.name] = field.desc;
                });
            });
            this._field.values(rows);
            
        }
    }, 
    
    val: function(s) {
        if(this._field.val() == '-')
            return '';
        return this._templates.s.replaceAll('%field', this._field.val()).replaceAll('%order', this._order.val());
    },
    
});                   