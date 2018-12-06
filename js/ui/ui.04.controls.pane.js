UI.Controls.Pane = UI.Controls.Control.extend({
    
    _align: false,

    constructor: function(name, container) {
        this.base(name, container);
    }, 
    
    Render: function(layout) {
        var self = this;
        this.base('div', 'ui-pane', layout);
        $(window).resize(function() { self.Resize(); });

        if(layout != undefined) {
            (new UI.LayoutManager(this, layout.length != undefined ?  _ROOTPATH + layout : layout))
                .addHandler('layoutmanager.ready', function(event, args) {
                    self.raiseEvent('ready', []);
                })
                .Render();
        }
        else {
            self.raiseEvent('ready', []);
        }

        return self;
    },
    
    Resize: function() {
        if(this._align) this.floatInParent(this._align);
        this.raiseEvent('resize', []);
    },
    
    floatInParent: function(val) {
        this._align = val;
        if(val == 'bottom') {
            //out('check', this._container.get(0) == $('#buttonsfixed').parent().get(0));
            this._element.css({position: 'absolute', marginTop: (this._container.height() - this.height()) + 'px'}).width(this._container.get(0).clientWidth);
        }
        else if(val == 'top') {
            this._element.css({position: 'absolute', marginTop: '0px'}).width(this._container.width());
        }
        else if(val == 'content') {
            this._element.css({position: 'absolute', marginTop: '0px', marginLeft: '0px'}).width(this._container.width()).height(this._container.height());
        }
        else {
            this._element.css({position: 'static', marginTop: 'none'}).width('auto');
            /*this._container.height(this._container.height() + this.height()).css({marginTop: 'none', marginBottom: 'none'});*/
        }
        return this;
    },
    
    val: function(val) {
        if(val == undefined)
            return this._element.find('>div').html();
        else
            this._element.find('>div').html(val);
    },
    
}, {});