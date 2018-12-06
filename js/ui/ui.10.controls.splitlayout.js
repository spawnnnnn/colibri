UI.Controls.SplitLayout = UI.Controls.Control.extend({
    
    _key: false,
    _last: false,
    
    _controls: false,

    constructor: function(name, container, key) {

        this.base(name, container);

        this._key = key;
        this._last = JSON.parse(localStorage.getItem('ui-splitlayout-' + this._key));
        if(!this._last) {
            this._last = {left: '50%', right: '50%'};
        }
    
    }, 
    
    _hideBadTags: function(val) {
        if(val) {
            this.container('left').css({'pointer-events': 'none'});
            this.container('right').css({'pointer-events': 'none'});        
            this.container('left').find('iframe').css({visibility: 'hidden'});
            this.container('right').find('iframe').css({visibility: 'hidden'});
        }
        else {
            this.container('left').find('iframe').css({visibility: 'visible'});
            this.container('right').find('iframe').css({visibility: 'visible'});
            this.container('left').css({'pointer-events': 'all'});
            this.container('right').css({'pointer-events': 'all'});        
        }
    },
    
    Render: function(orientation) {
        var self = this;
        
        if(!orientation) orientation = UI.Controls.SplitLayout.Horizontal;
        this.base('div', 'ui-splitlayout ' + orientation);
        
        this._element.append('<div class="left"></div><div class="sp"></div><div class="right"></div>');
        
        if(orientation == UI.Controls.SplitLayout.Horizontal) {
            self._element.find('>.left').resizable({
                handleSelector: self._element.find('.sp'),
                resizeHeight: false,
                onStartDragging: function() {
                    self._hideBadTags(true);
                    self.raiseEvent('start');
                },
                onStopDragging: function() {
                    var left = parseInt(self.width(undefined, 'left') * 100 / self.width());
                    var right = 100 - left; //parseInt(self.width(undefined, 'right') * 100 / self.width());
                    localStorage.setItem('ui-splitlayout-' + self._key, JSON.stringify({left: left + '%', right: right + '%'}));
                    self._hideBadTags(false);
                    self.raiseEvent('end');
                },
            });
        }
        else if(orientation == UI.Controls.SplitLayout.Vertical) {
            self._element.find('>.left').resizable({
                handleSelector: self._element.find('.sp'),
                resizeWidth: false,
                onStartDragging: function() {
                    self._hideBadTags(true);
                    self.raiseEvent('start');
                },
                onStopDragging: function() {
                    var left = parseInt(self.height(undefined, 'left') * 100 / self.height());
                    var right = 100 - left; // parseInt(self.height(undefined, 'right') * 100 / self.height());
                    localStorage.setItem('ui-splitlayout-' + self._key, JSON.stringify({left: left + '%', right: right + '%'}));
                    self.raiseEvent('end');
                    self._hideBadTags(false);
                },
            });
        }
        
        $(window).bind('resizeend', function() { self.Resize(); });
        self.raiseEvent('ready', []);
        return self;
    },
    
    Resize: function() {
        this.raiseEvent('resize', []);
    },
    
    container: function(wich) {
        if(wich == 'left')
            return this._element.find('>.left');
        else if(wich == 'right')   
            return this._element.find('>.right');
        else
            return this._element;
    },
    
    width: function(val, wich) {
        if(val == undefined)
            return wich == 'left' ? this._element.find('>.left').outerWidth() : (wich == 'right' ? this._element.find('>.right').outerWidth() : this._element.outerWidth());
        else {
            if(val instanceof Object) {
                this._element.find('>.left').width(val.left == 'auto' ? this._last.left : val.left);
                this._element.find('>.right').width(val.right == 'auto' ? this._last.right : val.right);
            }
            else
                this._element.outerWidth(val);
            return this;
        }
    }, 
    
    height: function(val, wich) {
        if(val == undefined)
            return wich == 'left' ? this._element.find('>.left').outerHeight() : (wich == 'right' ? this._element.find('>.right').outerHeight() : this._element.outerHeight());
        else {
            if(val instanceof Object) {
                this._element.find('>.left').height(val.left == 'auto' ? this._last.left : val.left);
                this._element.find('>.right').height(val.right == 'auto' ? this._last.right : val.right);
                
                // localStorage.setItem('ui-splitlayout-' + this._key, JSON.stringify({left: (val.left == 'auto' ? this._last.left : val.left), right: (val.right == 'auto' ? this._last.right : val.right)}));
            }
            else
                this._element.outerHeight(val);
            return this;
        }
    },
    
    last: function() {
        return this._last && this._last.left ? this._last : {left: '50%', right: '50%'};
    },
    
}, {});

UI.Controls.SplitLayout.Horizontal = 'hr';
UI.Controls.SplitLayout.Vertical = 'vr';