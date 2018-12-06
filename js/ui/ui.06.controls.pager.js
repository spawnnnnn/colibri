UI.Controls.Pager = UI.Controls.Control.extend({
    
    _page: 1,
    _pagesize: 20,
    _affected: 0,
    
    _maxpages: 0,
    
    constructor: function(name, container) {
        this.base(name, container);
    }, 
    
    Render: function() {
        var self = this;
        this.base('div', 'ui-pager');
        
        this._element.append('<div>Страницы</div><div class="ui-pager-left"><em>&lt;</em></div><div class="ui-pager-content"></div><div class="ui-pager-right"><em>&gt;</em></div><div>показывать&nbsp;</div><div class="ui-pager-pages"><select></select></div>');
        $(window).resize(function() {
            self.Resize();
        });
        
        self._element.find('.ui-pager-left>em').click(function() {
            if(self._element.is('.ui-disabled'))
                return false;
            self._page = Math.max(--self._page, 1);
            self.val(self._page);
            self.raiseEvent('changed');
            return false;
        });
        self._element.find('.ui-pager-right>em').click(function() {
            if(self._element.is('.ui-disabled'))
                return false;
            self._page = Math.min(++self._page, self._maxpages);
            self.val(self._page);
            self.raiseEvent('changed');
            return false;
        });
        
        self._element.find('.ui-pager-pages select').append('<option value="10">по 10</option>');
        self._element.find('.ui-pager-pages select').append('<option value="20">по 20</option>');
        self._element.find('.ui-pager-pages select').append('<option value="30">по 30</option>');
        self._element.find('.ui-pager-pages select').append('<option value="40">по 40</option>');
        self._element.find('.ui-pager-pages select').append('<option value="50">по 50</option>');
        
        self._element.find('.ui-pager-pages select option[value="' + self._pagesize + '"]').prop('selected', true);

        self._element.find('.ui-pager-pages>select').change(function() {
            if(self._element.is('.ui-disabled'))
                return false;
            self.pagesize($(this).find('option:selected').val());
            self.val(1);
            self.raiseEvent('changed');
            return false;
        });        
        
        self.raiseEvent('ready', []);
        return self;
    },
    
    Resize: function() {
        this.raiseEvent('resize', []);
    },
    
    Init: function(pagesize, affected) {
        this._page = 1;
        this._pagesize = pagesize;
        this._affected = affected;
        this._maxpages = affected == -1 ? Number.MAX_VALUE : Math.ceil(this._affected/this._pagesize);
        return this;
    }, 
    
    val: function(page) {
        
        if(page == undefined)
            return this._page;
        else {
            this._page = page;
            this._element.find('.ui-pager-content').html(this._page + (this._affected != -1 ? ' из ' + this._maxpages + ', всего: ' + this._affected : ''));
            return this;
        }
    },
    
    pagesize: function(value) {
        if(value == undefined) 
            return this._pagesize;
        else {
            this._pagesize = value;                                                                                           
            this._maxpages = this._affected == -1 ? Number.MAX_VALUE : Math.ceil(this._affected/this._pagesize);
            this._element.find('.ui-pager-pages select option[value="' + this._pagesize + '"]').prop('selected', true);
            return this;
        }
    }, 
    
    affected: function(value) {
        if(value == undefined) 
            return this._affected;
        else {
            this._affected = value;
            return this;
        }
    }, 

    maxpages: function(value) {
        return this._maxpages;
    },

     
}, {});